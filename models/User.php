<?php
class User extends Model
{
    protected string $table = 'users';

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("
            SELECT u.*, r.nom AS role_nom, reg.nom AS region_nom
            FROM users u
            JOIN roles r ON u.role_id = r.id
            LEFT JOIN regions reg ON u.region_id = reg.id
            WHERE u.email = ? AND u.statut = 'actif'
        ");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function register(array $data): int
    {
        return $this->create([
            'nom'           => $data['nom'],
            'prenom'        => $data['prenom'] ?? null,
            'email'         => strtolower($data['email']),
            'telephone'     => $data['telephone'] ?? null,
            'password_hash' => password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => (int) Env::get('BCRYPT_COST', 10)]),
            'role_id'       => 1, // Agriculteur par défaut
            'region_id'     => $data['region_id'] ?? null,
        ]);
    }

    public function verifyPassword(string $plain, string $hash): bool
    {
        return password_verify($plain, $hash);
    }

    public function touchLogin(int $id): void
    {
        $this->db->prepare("UPDATE users SET derniere_connexion = NOW() WHERE id = ?")->execute([$id]);
    }

    public function allWithStats(int $limit = 50): array
    {
        $stmt = $this->db->prepare("
            SELECT u.id, u.nom, u.prenom, u.email, u.telephone, u.statut, u.created_at,
                   u.role_id, r.libelle AS role, r.nom AS role_nom,
                   reg.nom AS region,
                   (SELECT COUNT(*) FROM diagnostics d WHERE d.user_id = u.id) AS nb_diag
            FROM users u
            JOIN roles r ON u.role_id = r.id
            LEFT JOIN regions reg ON u.region_id = reg.id
            WHERE u.statut <> 'supprime'
            ORDER BY u.created_at DESC LIMIT $limit
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function changeRole(int $id, int $roleId): bool
    {
        if (!in_array($roleId, [1, 2, 3], true)) return false;
        return $this->db->prepare("UPDATE users SET role_id = ? WHERE id = ?")
                        ->execute([$roleId, $id]);
    }

    public function setStatus(int $id, string $statut): bool
    {
        if (!in_array($statut, ['actif', 'suspendu', 'supprime'], true)) return false;
        return $this->db->prepare("UPDATE users SET statut = ? WHERE id = ?")
                        ->execute([$statut, $id]);
    }

    public function countByRole(): array
    {
        $stmt = $this->db->query("
            SELECT r.libelle, COUNT(u.id) AS total
            FROM roles r LEFT JOIN users u ON u.role_id = r.id
            GROUP BY r.id
        ");
        return $stmt->fetchAll();
    }
}
