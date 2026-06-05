<?php
class AuthController extends Controller
{
    public function loginForm(): void
    {
        if (auth()) $this->redirect('/dashboard');
        $this->view('auth/login', ['title' => 'Connexion'], 'auth');
    }

    public function login(): void
    {
        $this->validateCsrf();

        $email = trim((string) $this->input('email'));
        $password = (string) $this->input('password');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
            set_flash('error', 'Email ou mot de passe invalide');
            $this->redirect('/login');
        }

        // Rate limit
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        if (!RateLimit::check('login', "$ip:$email", config('rate_limits')['login'], 15)) {
            set_flash('error', 'Trop de tentatives. Réessayez dans 15 minutes.');
            $this->redirect('/login');
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (!$user || !$userModel->verifyPassword($password, $user['password_hash'])) {
            Logger::warn('Échec login', ['email' => $email, 'ip' => $ip]);
            set_flash('error', 'Identifiants incorrects');
            $this->redirect('/login');
        }

        // Régénérer session ID (anti-fixation)
        session_regenerate_id(true);

        $_SESSION['user_id']   = (int) $user['id'];
        $_SESSION['user_role'] = $user['role_nom'];
        $_SESSION['user'] = [
            'id'      => (int) $user['id'],
            'nom'     => $user['nom'],
            'prenom'  => $user['prenom'],
            'email'   => $user['email'],
            'role'    => $user['role_nom'],
            'region'  => $user['region_nom'],
        ];

        $userModel->touchLogin((int) $user['id']);
        Logger::info('Login OK', ['user_id' => $user['id']]);

        $this->redirect($user['role_nom'] === 'admin' ? '/admin' : '/dashboard');
    }

    public function registerForm(): void
    {
        if (auth()) $this->redirect('/dashboard');
        $regions = (new Region())->allOrdered();
        $this->view('auth/register', ['title' => 'Inscription', 'regions' => $regions], 'auth');
    }

    public function register(): void
    {
        $this->validateCsrf();

        $data = [
            'nom'       => trim((string) $this->input('nom')),
            'prenom'    => trim((string) $this->input('prenom')),
            'email'     => strtolower(trim((string) $this->input('email'))),
            'telephone' => trim((string) $this->input('telephone')),
            'password'  => (string) $this->input('password'),
            'region_id' => (int) $this->input('region_id') ?: null,
        ];

        $errors = [];
        if (strlen($data['nom']) < 2) $errors[] = 'Nom requis';
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Email invalide';
        if (strlen($data['password']) < 8) $errors[] = 'Mot de passe : 8 caractères minimum';

        $userModel = new User();
        if ($userModel->findByEmail($data['email'])) {
            $errors[] = 'Cet email est déjà utilisé';
        }

        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        if (!RateLimit::check('register', $ip, config('rate_limits')['register'], 60)) {
            $errors[] = 'Trop d\'inscriptions. Réessayez plus tard.';
        }

        if ($errors) {
            set_flash('error', implode(' • ', $errors));
            $this->redirect('/register');
        }

        $id = $userModel->register($data);
        Logger::info('Nouveau compte', ['user_id' => $id, 'email' => $data['email']]);

        // E-mail de bienvenue (n'interrompt pas le parcours en cas d'échec)
        Notifier::welcome($data);

        set_flash('success', 'Compte créé avec succès ! Connectez-vous.');
        $this->redirect('/login');
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        $this->redirect('/login');
    }
}
