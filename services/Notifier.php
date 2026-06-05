<?php
/**
 * Service de notifications e-mail.
 *
 * Deux pilotes (config mail.driver) :
 *  - 'mail' : utilise la fonction mail() de PHP (nécessite un serveur SMTP).
 *  - 'log'  : écrit le message dans logs/mail.log (idéal en local WAMP sans SMTP).
 *
 * L'échec d'envoi n'interrompt jamais le parcours utilisateur : on journalise
 * simplement l'erreur. Conçu pour être remplacé par PHPMailer en production.
 */
class Notifier
{
    /**
     * Envoi générique d'un e-mail HTML.
     */
    public static function send(string $to, string $subject, string $html): bool
    {
        $cfg     = config('mail');
        $from     = $cfg['from'];
        $fromName = $cfg['from_name'];
        $driver   = $cfg['driver'] ?? 'log';

        // Pilote SMTP via PHPMailer (envoi réel)
        if ($driver === 'smtp' && class_exists(\PHPMailer\PHPMailer\PHPMailer::class)) {
            try {
                $mailer = new \PHPMailer\PHPMailer\PHPMailer(true);
                $mailer->isSMTP();
                $mailer->Host       = Env::get('SMTP_HOST', 'smtp.gmail.com');
                $mailer->SMTPAuth   = true;
                $mailer->Username   = Env::get('SMTP_USER');
                $mailer->Password   = Env::get('SMTP_PASS');
                $mailer->SMTPSecure = Env::get('SMTP_SECURE', 'tls'); // 'tls' (587) ou 'ssl' (465)
                $mailer->Port       = (int) Env::get('SMTP_PORT', 587);
                $mailer->CharSet    = 'UTF-8';

                $mailer->setFrom($from, $fromName);
                $mailer->addAddress($to);
                $mailer->isHTML(true);
                $mailer->Subject = $subject;
                $mailer->Body    = $html;
                $mailer->AltBody = strip_tags(str_replace(['<br>', '</p>'], "\n", $html));

                $mailer->send();
                Logger::info('E-mail SMTP envoyé', ['to' => $to, 'subject' => $subject]);
                return true;
            } catch (\Throwable $e) {
                Logger::warn('Échec SMTP, bascule sur journal', ['to' => $to, 'erreur' => $e->getMessage()]);
                self::writeLog($to, $subject, $html, $from);
                return true;
            }
        }

        // Pilote 'mail' (fonction native PHP — rare en local Windows)
        if ($driver === 'mail' && function_exists('mail')) {
            $headers = [
                'MIME-Version: 1.0',
                'Content-Type: text/html; charset=UTF-8',
                'From: ' . sprintf('%s <%s>', $fromName, $from),
                'Reply-To: ' . $from,
                'X-Mailer: PlantDoc',
            ];
            $ok = @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $html, implode("\r\n", $headers));
            if ($ok) {
                Logger::info('E-mail envoyé via mail()', ['to' => $to, 'subject' => $subject]);
                return true;
            }
            Logger::warn('Échec mail(), bascule sur journal', ['to' => $to]);
        }

        // Pilote 'log' par défaut (toujours disponible)
        self::writeLog($to, $subject, $html, $from);
        return true;
    }

    private static function writeLog(string $to, string $subject, string $html, string $from): void
    {
        $file = APP_ROOT . '/logs/mail.log';
        $entry = sprintf(
            "\n========== %s ==========\nDe : %s\nÀ : %s\nObjet : %s\n--------------------------------------\n%s\n",
            date('Y-m-d H:i:s'),
            $from,
            $to,
            $subject,
            strip_tags(str_replace(['<br>', '<br/>', '<br />', '</p>'], "\n", $html))
        );
        @file_put_contents($file, $entry, FILE_APPEND);
        Logger::info('E-mail journalisé (driver log)', ['to' => $to, 'subject' => $subject]);
    }

    /**
     * Gabarit HTML commun (en-tête + pied de page PlantDoc).
     */
    private static function template(string $title, string $body): string
    {
        $year = date('Y');
        return <<<HTML
<div style="font-family:Arial,sans-serif;max-width:560px;margin:0 auto;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden">
  <div style="background:linear-gradient(135deg,#2d6a4f,#52b788);padding:22px 28px;color:#fff">
    <span style="font-size:22px;font-weight:800">Plant<span style="color:#f4a261">Doc</span></span>
    <div style="font-size:12px;opacity:.9">Diagnostic phytosanitaire intelligent</div>
  </div>
  <div style="padding:26px 28px;color:#1b4332">
    <h2 style="margin:0 0 14px;font-size:18px">$title</h2>
    $body
  </div>
  <div style="background:#f9fafb;padding:16px 28px;font-size:11px;color:#9ca3af;border-top:1px solid #e5e7eb">
    PlantDoc &copy; $year — Au service des agriculteurs camerounais.<br>
    Cet e-mail vous est envoyé automatiquement, merci de ne pas y répondre.
  </div>
</div>
HTML;
    }

    /**
     * E-mail de bienvenue à l'inscription.
     */
    public static function welcome(array $user): bool
    {
        $prenom = htmlspecialchars($user['prenom'] ?: $user['nom']);
        $body = "
          <p>Bonjour <strong>$prenom</strong>,</p>
          <p>Bienvenue sur <strong>PlantDoc</strong> ! Votre compte a bien été créé.</p>
          <p>Vous pouvez dès maintenant photographier vos cultures pour obtenir un diagnostic
          des maladies en quelques secondes grâce à notre intelligence artificielle.</p>
          <p style='margin-top:22px'>
            <a href='" . url('/login') . "' style='background:#2d6a4f;color:#fff;text-decoration:none;padding:12px 22px;border-radius:8px;font-weight:600;display:inline-block'>
              Me connecter
            </a>
          </p>
          <p style='margin-top:18px;font-size:13px;color:#6b7280'>Ensemble, protégeons les récoltes du Cameroun. 🌱</p>
        ";
        return self::send($user['email'], 'Bienvenue sur PlantDoc 🌱', self::template('Votre compte est prêt', $body));
    }

    /**
     * Alerte à l'agriculteur quand un expert valide/rejette son diagnostic.
     */
    public static function diagnosticValidated(array $user, int $diagId, string $statut, ?string $commentaire = null): bool
    {
        $prenom = htmlspecialchars($user['prenom'] ?? $user['nom'] ?? '');
        $valide = $statut === 'valide';
        $titre  = $valide ? 'Votre diagnostic a été confirmé' : 'Votre diagnostic a été réévalué';
        $verdict = $valide
            ? "<span style='color:#2d6a4f;font-weight:700'>confirmé</span> par notre expert agronome"
            : "<span style='color:#b91c1c;font-weight:700'>réévalué</span> par notre expert agronome";

        $comment = $commentaire
            ? "<div style='background:#f9fafb;border-left:4px solid #52b788;padding:12px 16px;margin:16px 0;border-radius:6px;font-size:14px'>
                 <strong>Avis de l'expert :</strong><br>" . nl2br(htmlspecialchars($commentaire)) . "</div>"
            : "";

        $body = "
          <p>Bonjour <strong>$prenom</strong>,</p>
          <p>Le diagnostic <strong>#$diagId</strong> que vous avez soumis a été $verdict.</p>
          $comment
          <p style='margin-top:22px'>
            <a href='" . url('/diagnostic/' . $diagId) . "' style='background:#2d6a4f;color:#fff;text-decoration:none;padding:12px 22px;border-radius:8px;font-weight:600;display:inline-block'>
              Voir mon diagnostic
            </a>
          </p>
        ";
        return self::send($user['email'], $titre . ' — PlantDoc', self::template($titre, $body));
    }
}
