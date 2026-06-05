<?php
class Maladie extends Model
{
    protected string $table = 'maladies';

    public function findByName(string $name): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM maladies
            WHERE nom_commun LIKE ? OR nom_scientifique LIKE ?
            LIMIT 1
        ");
        $like = "%$name%";
        $stmt->execute([$like, $like]);
        return $stmt->fetch() ?: null;
    }

    public function allWithCulture(): array
    {
        return $this->db->query("
            SELECT m.*, c.nom AS culture_nom, c.icone, c.couleur
            FROM maladies m
            JOIN cultures c ON m.culture_id = c.id
            ORDER BY c.nom, m.nom_commun
        ")->fetchAll();
    }

    public function top(int $limit = 5): array
    {
        return $this->db->query("SELECT * FROM v_top_maladies LIMIT $limit")->fetchAll();
    }
}
