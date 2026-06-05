<?php
class Region extends Model
{
    protected string $table = 'regions';

    public function allOrdered(): array
    {
        return $this->db->query("SELECT * FROM regions ORDER BY nom")->fetchAll();
    }

    public function allWithStats(): array
    {
        return $this->db->query("
            SELECT r.id, r.nom, r.chef_lieu, r.latitude, r.longitude, r.climat,
                   COUNT(d.id) AS total_diagnostics,
                   SUM(CASE WHEN d.gravite = 'severe' THEN 1 ELSE 0 END) AS cas_severes,
                   SUM(CASE WHEN d.plante_saine = 1 THEN 1 ELSE 0 END) AS plantes_saines,
                   COUNT(DISTINCT d.maladie_id) AS maladies_distinctes,
                   (SELECT m.nom_commun FROM diagnostics d2
                    JOIN maladies m ON d2.maladie_id = m.id
                    WHERE d2.region_id = r.id
                    GROUP BY d2.maladie_id ORDER BY COUNT(*) DESC LIMIT 1) AS maladie_top
            FROM regions r
            LEFT JOIN diagnostics d ON d.region_id = r.id
            GROUP BY r.id
            ORDER BY r.nom
        ")->fetchAll();
    }
}
