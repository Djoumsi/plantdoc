<?php
class DiagnosticController extends Controller
{
    public function dashboard(): void
    {
        $this->requireAuth();
        $userId = (int) $_SESSION['user_id'];

        $diagModel = new Diagnostic();
        $stats = $diagModel->statsUser($userId);
        $recents = $diagModel->byUser($userId, 5);

        $this->view('diagnostic/dashboard', [
            'title'   => 'Tableau de bord',
            'stats'   => $stats,
            'recents' => $recents,
        ]);
    }

    public function newForm(): void
    {
        $this->requireAuth();
        $this->view('diagnostic/new', ['title' => 'Nouveau diagnostic']);
    }

    public function create(): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $userId = (int) $_SESSION['user_id'];

        // Rate limit
        if (!RateLimit::check('diagnostic', "user:$userId", config('rate_limits')['diagnostic'], 60)) {
            set_flash('error', 'Limite horaire atteinte. Réessayez dans 1 heure.');
            $this->redirect('/diagnostic/new');
        }

        // Upload
        $upload = Upload::image($_FILES['photo'] ?? []);
        if (!$upload['ok']) {
            set_flash('error', $upload['error']);
            $this->redirect('/diagnostic/new');
        }

        // IA
        $ai = new AIService();
        $result = $ai->analyzeImage($upload['absolute']);

        if (!$result['ok']) {
            @unlink($upload['absolute']);
            set_flash('error', $result['error'] ?? 'Erreur d\'analyse IA');
            $this->redirect('/diagnostic/new');
        }

        $diag = $result['diagnostic'];

        // Chercher maladie en base par nom
        $maladieId = null;
        if (!empty($diag['maladie_nom_commun'])) {
            $maladie = (new Maladie())->findByName($diag['maladie_nom_commun']);
            if ($maladie) $maladieId = (int) $maladie['id'];
        }

        // Statut selon confiance
        $confiance = (int) ($diag['confiance'] ?? 0);
        $statut = $confiance >= 70 ? 'valide' : 'a_verifier';

        $diagModel = new Diagnostic();
        $id = $diagModel->create([
            'user_id'             => $userId,
            'maladie_id'          => $maladieId,
            'photo_path'          => $upload['path'],
            'maladie_detectee'    => $diag['maladie_nom_commun'] ?? null,
            'nom_scientifique'    => $diag['maladie_nom_scientifique'] ?? null,
            'confiance'           => $confiance,
            'gravite'             => $diag['gravite'] ?? 'inconnue',
            'plante_saine'        => !empty($diag['plante_saine']) ? 1 : 0,
            'traitement_propose'  => $diag['traitement'] ?? null,
            'prevention_proposee' => $diag['prevention'] ?? null,
            'statut'              => $statut,
            'ia_raw_response'     => json_encode($result['raw'] ?? $diag, JSON_UNESCAPED_UNICODE),
            'ia_model'            => $result['model'] ?? null,
            'ia_duration_ms'      => $result['duration_ms'] ?? null,
        ]);

        Logger::info('Diagnostic créé', ['id' => $id, 'user' => $userId, 'maladie' => $diag['maladie_nom_commun'] ?? 'sain']);

        $this->redirect("/diagnostic/$id");
    }

    public function show($id): void
    {
        $this->requireAuth();
        $diag = (new Diagnostic())->findFullById((int) $id);

        if (!$diag) {
            http_response_code(404);
            die('Diagnostic introuvable');
        }
        if ((int) $diag['user_id'] !== (int) $_SESSION['user_id'] && !is_admin() && !is_expert()) {
            http_response_code(403);
            die('Accès interdit');
        }

        $feedback = (new Feedback())->findByDiagnostic((int) $diag['id']);

        $this->view('diagnostic/result', [
            'title'    => 'Diagnostic #' . $diag['id'],
            'diag'     => $diag,
            'feedback' => $feedback,
        ]);
    }

    /**
     * Fiche PDF imprimable d'un diagnostic (mise en page A4 + QR de partage).
     * L'utilisateur enregistre en PDF via l'impression navigateur.
     */
    public function pdf($id): void
    {
        $this->requireAuth();
        $diag = (new Diagnostic())->findFullById((int) $id);

        if (!$diag) {
            http_response_code(404);
            die('Diagnostic introuvable');
        }
        if ((int) $diag['user_id'] !== (int) $_SESSION['user_id'] && !is_admin() && !is_expert()) {
            http_response_code(403);
            die('Accès interdit');
        }

        // Vue autonome (pas de layout applicatif)
        extract(['diag' => $diag, 'shareUrl' => url('/diagnostic/' . $diag['id'])]);
        require APP_ROOT . '/views/diagnostic/pdf.php';
    }

    public function feedback($id): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $diagModel = new Diagnostic();
        $diag = $diagModel->find((int) $id);
        if (!$diag || (int) $diag['user_id'] !== (int) $_SESSION['user_id']) {
            set_flash('error', 'Accès interdit');
            $this->redirect('/dashboard');
        }

        $correct = $this->input('correct') === '1';
        $note    = (int) $this->input('note') ?: null;
        $commentaire = trim((string) $this->input('commentaire')) ?: null;

        (new Feedback())->save((int) $id, (int) $_SESSION['user_id'], $correct, $note, $commentaire);

        Logger::info('Feedback enregistré', ['diag' => $id, 'correct' => $correct, 'note' => $note]);
        set_flash('success', 'Merci pour votre retour ! Cela nous aide à améliorer l\'IA.');
        $this->redirect("/diagnostic/$id");
    }

    public function history(): void
    {
        $this->requireAuth();
        $diagnostics = (new Diagnostic())->byUser((int) $_SESSION['user_id'], 100);
        $this->view('diagnostic/history', [
            'title'       => 'Mon historique',
            'diagnostics' => $diagnostics,
        ]);
    }
}
