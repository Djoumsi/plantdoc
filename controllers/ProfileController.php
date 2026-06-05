<?php
class ProfileController extends Controller
{
    public function show(): void
    {
        $this->requireAuth();

        $userModel = new User();
        $user = $userModel->find((int) $_SESSION['user_id']);
        $regions = (new Region())->allOrdered();

        $diagModel = new Diagnostic();
        $stats = $diagModel->statsUser((int) $_SESSION['user_id']);

        $this->view('profile/index', [
            'title'   => 'Mon profil',
            'user'    => $user,
            'regions' => $regions,
            'stats'   => $stats,
        ]);
    }

    public function update(): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $db = Database::connect();
        $userId = (int) $_SESSION['user_id'];

        $data = [
            'nom'       => trim((string) $this->input('nom')),
            'prenom'    => trim((string) $this->input('prenom')),
            'telephone' => trim((string) $this->input('telephone')),
            'region_id' => (int) $this->input('region_id') ?: null,
            'langue'    => $this->input('langue') === 'en' ? 'en' : 'fr',
        ];

        if (strlen($data['nom']) < 2) {
            set_flash('error', 'Nom requis');
            $this->redirect('/profile');
        }

        $stmt = $db->prepare("UPDATE users SET nom = ?, prenom = ?, telephone = ?, region_id = ?, langue = ? WHERE id = ?");
        $stmt->execute([$data['nom'], $data['prenom'] ?: null, $data['telephone'] ?: null, $data['region_id'], $data['langue'], $userId]);

        // MAJ session
        $_SESSION['user']['nom']    = $data['nom'];
        $_SESSION['user']['prenom'] = $data['prenom'];

        Logger::info('Profil mis à jour', ['user_id' => $userId]);
        set_flash('success', 'Profil mis à jour avec succès');
        $this->redirect('/profile');
    }

    public function password(): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $userId = (int) $_SESSION['user_id'];
        $current = (string) $this->input('current_password');
        $new     = (string) $this->input('new_password');
        $confirm = (string) $this->input('confirm_password');

        if (strlen($new) < 8) {
            set_flash('error', 'Le nouveau mot de passe doit faire au moins 8 caractères');
            $this->redirect('/profile');
        }
        if ($new !== $confirm) {
            set_flash('error', 'Les mots de passe ne correspondent pas');
            $this->redirect('/profile');
        }

        $userModel = new User();
        $user = $userModel->find($userId);
        if (!password_verify($current, $user['password_hash'])) {
            set_flash('error', 'Mot de passe actuel incorrect');
            $this->redirect('/profile');
        }

        $hash = password_hash($new, PASSWORD_BCRYPT);
        Database::connect()->prepare("UPDATE users SET password_hash = ? WHERE id = ?")->execute([$hash, $userId]);

        Logger::info('Mot de passe changé', ['user_id' => $userId]);
        set_flash('success', 'Mot de passe modifié avec succès');
        $this->redirect('/profile');
    }
}
