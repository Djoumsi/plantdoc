<?php
class HomeController extends Controller
{
    public function index(): void
    {
        if (auth()) {
            $this->redirect(is_admin() ? '/admin' : '/dashboard');
        }
        $this->view('home/index', ['title' => 'Accueil'], 'public');
    }

    public function about(): void
    {
        $this->view('home/about', ['title' => 'À propos'], 'public');
    }

    /**
     * Changement de langue (FR/EN), puis retour à la page précédente.
     */
    public function setLanguage($code): void
    {
        set_lang((string) $code);

        // Cible de retour : referer si présent ET non vide ET sur le même hôte ;
        // sinon, on retombe sur une page sûre selon le rôle.
        $back    = trim((string) ($_SERVER['HTTP_REFERER'] ?? ''));
        $appHost = parse_url(config('url'), PHP_URL_HOST) ?: 'localhost';
        $refHost = $back ? (parse_url($back, PHP_URL_HOST) ?: '') : '';
        $sameHost = $refHost === '' || strcasecmp($refHost, $appHost) === 0;

        if ($back === '' || !$sameHost) {
            if (auth()) {
                $back = url(is_admin() || is_expert() ? '/admin' : '/dashboard');
            } else {
                $back = url('/');
            }
        }

        header('Location: ' . $back);
        exit;
    }

    public function contactForm(): void
    {
        $this->view('home/contact', ['title' => 'Nous contacter'], 'public');
    }

    public function contactSend(): void
    {
        $this->validateCsrf();

        $data = [
            'nom'     => trim((string) $this->input('nom')),
            'email'   => trim((string) $this->input('email')),
            'sujet'   => trim((string) $this->input('sujet')),
            'message' => trim((string) $this->input('message')),
        ];

        $errors = [];
        if (strlen($data['nom']) < 2) $errors[] = 'Nom requis';
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Email invalide';
        if (strlen($data['message']) < 10) $errors[] = 'Message trop court';

        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        if (!RateLimit::check('contact', $ip, 5, 60)) {
            $errors[] = 'Trop de messages envoyés. Réessayez plus tard.';
        }

        if ($errors) {
            set_flash('error', implode(' • ', $errors));
            $this->redirect('/contact');
        }

        Logger::info('Message contact', $data);
        set_flash('success', 'Merci ! Votre message a bien été envoyé. Nous vous répondrons sous 48h.');
        $this->redirect('/contact');
    }
}
