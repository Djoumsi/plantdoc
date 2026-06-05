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

    /**
     * Change le rôle d'un utilisateur (admin uniquement).
     * Empêche l'admin de se rétrograder lui-même pour éviter de perdre l'accès.
     */
    public function changeUserRole($id): void
    {
        $this->requireRole('admin');
        $this->validateCsrf();

        $id     = (int) $id;
        $roleId = (int) $this->input('role_id');

        if ($id === (int) $_SESSION['user_id']) {
            set_flash('error', 'Vous ne pouvez pas modifier votre propre rôle.');
            $this->redirect('/admin/users');
        }
        if (!in_array($roleId, [1, 2, 3], true)) {
            set_flash('error', 'Rôle invalide.');
            $this->redirect('/admin/users');
        }

        $ok = (new User())->changeRole($id, $roleId);
        if ($ok) {
            Logger::info('Rôle utilisateur modifié', ['id' => $id, 'role_id' => $roleId, 'by' => $_SESSION['user_id']]);
            set_flash('success', 'Rôle mis à jour avec succès.');
        } else {
            set_flash('error', 'Échec de la mise à jour.');
        }
        $this->redirect('/admin/users');
    }

    /**
     * Suspend ou réactive un compte utilisateur.
     */
    public function toggleUserStatus($id): void
    {
        $this->requireRole('admin');
        $this->validateCsrf();

        $id     = (int) $id;
        $statut = (string) $this->input('statut');

        if ($id === (int) $_SESSION['user_id']) {
            set_flash('error', 'Vous ne pouvez pas modifier votre propre statut.');
            $this->redirect('/admin/users');
        }
        if (!in_array($statut, ['actif', 'suspendu'], true)) {
            set_flash('error', 'Statut invalide.');
            $this->redirect('/admin/users');
        }

        $ok = (new User())->setStatus($id, $statut);
        if ($ok) {
            Logger::info('Statut utilisateur modifié', ['id' => $id, 'statut' => $statut, 'by' => $_SESSION['user_id']]);
            set_flash('success', $statut === 'actif' ? 'Compte réactivé.' : 'Compte suspendu.');
        }
        $this->redirect('/admin/users');
    }

    /**
     * Suppression logique d'un utilisateur (statut = supprime).
     */
    public function deleteUser($id): void
    {
        $this->requireRole('admin');
        $this->validateCsrf();

        $id = (int) $id;
        if ($id === (int) $_SESSION['user_id']) {
            set_flash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
            $this->redirect('/admin/users');
        }

        $ok = (new User())->setStatus($id, 'supprime');
        if ($ok) {
            Logger::info('Utilisateur supprimé', ['id' => $id, 'by' => $_SESSION['user_id']]);
            set_flash('success', 'Compte supprimé.');
        }
        $this->redirect('/admin/users');
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
