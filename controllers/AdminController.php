<?php
class AdminController extends Controller
{
    public function dashboard(): void
    {
        $this->requireRole('admin', 'expert');

        $diagModel = new Diagnostic();
        $userModel = new User();
        $maladieModel = new Maladie();

        $this->view('admin/dashboard', [
            'title'        => 'Tableau de bord admin',
            'stats'        => $diagModel->statsGlobal(),
            'recents'      => $diagModel->recent(10),
            'top_maladies' => $maladieModel->top(5),
            'roles_stats'  => $userModel->countByRole(),
            'per_day'      => $diagModel->perDay(30),
            'top10'        => $diagModel->topMaladies(10),
            'by_region'    => $diagModel->byRegion(),
        ], 'admin');
    }

    /**
     * Export CSV de l'ensemble des diagnostics (téléchargement direct).
     */
    public function exportCsv(): void
    {
        $this->requireRole('admin', 'expert');

        $rows = (new Diagnostic())->allForExport();

        $filename = 'plantdoc_diagnostics_' . date('Y-m-d_His') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $out = fopen('php://output', 'w');
        // BOM UTF-8 pour Excel
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, ['ID', 'Date', 'Agriculteur', 'Email', 'Région', 'Culture', 'Maladie', 'Confiance (%)', 'Gravité', 'Statut'], ';');
        foreach ($rows as $r) {
            fputcsv($out, [
                $r['id'],
                $r['created_at'],
                trim($r['agriculteur']),
                $r['email'],
                $r['region'] ?? '',
                $r['culture'] ?? '',
                $r['maladie'],
                $r['confiance'],
                $r['gravite'],
                $r['statut'],
            ], ';');
        }
        fclose($out);

        Logger::info('Export CSV diagnostics', ['by' => $_SESSION['user_id'], 'count' => count($rows)]);
        exit;
    }

    public function users(): void
    {
        $this->requireRole('admin');
        $users = (new User())->allWithStats(100);
        $this->view('admin/users', [
            'title' => 'Utilisateurs',
            'users' => $users,
        ], 'admin');
    }

    public function diagnostics(): void
    {
        $this->requireRole('admin', 'expert');
        $diagnostics = (new Diagnostic())->recent(50);
        $this->view('admin/diagnostics', [
            'title'       => 'Diagnostics',
            'diagnostics' => $diagnostics,
        ], 'admin');
    }

    public function validate($id): void
    {
        $this->requireRole('admin', 'expert');
        $this->validateCsrf();

        $statut = $this->input('decision'); // valide | rejete
        $commentaire = trim((string) $this->input('commentaire'));

        if (!in_array($statut, ['valide', 'rejete'], true)) {
            set_flash('error', 'Décision invalide');
            $this->redirect("/admin/diagnostics");
        }

        $diagModel = new Diagnostic();
        $ok = $diagModel->validate(
            (int) $id,
            (int) $_SESSION['user_id'],
            $statut,
            $commentaire ?: null
        );

        if ($ok) {
            Logger::info('Diagnostic validé', ['id' => $id, 'expert' => $_SESSION['user_id'], 'statut' => $statut]);

            // Notifier l'agriculteur propriétaire du diagnostic
            $diag = $diagModel->findFullById((int) $id);
            if ($diag && !empty($diag['user_email'])) {
                Notifier::diagnosticValidated([
                    'email'  => $diag['user_email'],
                    'prenom' => $diag['user_prenom'] ?? '',
                    'nom'    => $diag['user_nom'] ?? '',
                ], (int) $id, $statut, $commentaire ?: null);
            }

            set_flash('success', 'Diagnostic ' . ($statut === 'valide' ? 'validé' : 'rejeté'));
        }

        $this->redirect("/admin/diagnostics");
    }

    public function maladies(): void
    {
        $this->requireRole('admin', 'expert');
        $maladies = (new Maladie())->allWithCulture();
        $this->view('admin/maladies', [
            'title'    => 'Catalogue maladies',
            'maladies' => $maladies,
        ], 'admin');
    }
}
