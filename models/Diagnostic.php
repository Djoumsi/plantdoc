<?php
class Diagnostic extends Model
{
    protected string $table = 'diagnostics';

    public function findFullById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT d.*,
                   u.nom AS user_nom, u.prenom AS user_prenom, u.email AS user_email,
                   m.nom_commun AS maladie_nom, m.nom_scientifique AS maladie_sci,
                   m.traitements_bio, m.traitements_chim, m.prevention AS prevention_ref,
                   c.nom AS culture_nom, c.icone AS culture_icone, c.couleur AS culture_couleur,
                   reg.nom AS region_nom
            FROM diagnostics d
            JOIN users u ON d.user_id = u.id
            LEFT JOIN maladies m ON d.maladie_id = m.id
            LEFT JOIN cultures c ON m.culture_id = c.id
            LEFT JOIN regions reg ON d.region_id = reg.id
            WHERE d.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function byUser(int $userId, int $limit = 50): array
    {
        $stmt = $this->db->prepare("
            SELECT d.*, m.nom_commun AS maladie_nom, c.nom AS culture_nom, c.icone, c.couleur
            FROM diagnostics d
            LEFT JOIN maladies m ON d.maladie_id = m.id
            LEFT JOIN cultures c ON m.culture_id = c.id
            WHERE d.user_id = ?
            ORDER BY d.created_at DESC
            LIMIT $limit
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function recent(int $limit = 20): array
    {
        $stmt = $this->db->query("
            SELECT d.*, u.nom AS user_nom, u.prenom AS user_prenom,
                   m.nom_commun AS maladie_nom
            FROM diagnostics d
            JOIN users u ON d.user_id = u.id
            LEFT JOIN maladies m ON d.maladie_id = m.id
            ORDER BY d.created_at DESC LIMIT $limit
        ");
        return $stmt->fetchAll();
    }

    public function statsGlobal(): array
    {
        $db = $this->db;
        return [
            'total'        => (int) $db->query("SELECT COUNT(*) FROM diagnostics")->fetchColumn(),
            'aujourdhui'   => (int) $db->query("SELECT COUNT(*) FROM diagnostics WHERE DATE(created_at) = CURDATE()")->fetchColumn(),
            'a_valider'    => (int) $db->query("SELECT COUNT(*) FROM diagnostics WHERE statut = 'a_verifier'")->fetchColumn(),
            'severes'      => (int) $db->query("SELECT COUNT(*) FROM diagnostics WHERE gravite = 'severe'")->fetchColumn(),
            'precision'    => (float) ($db->query("SELECT AVG(confiance) FROM diagnostics WHERE confiance IS NOT NULL")->fetchColumn() ?: 0),
        ];
    }

    public function statsUser(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT
                COUNT(*) AS total,
                SUM(gravite = 'severe') AS severes,
                SUM(plante_saine = 1) AS saines,
                COUNT(DISTINCT maladie_id) AS maladies_distinctes
            FROM diagnostics WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch() ?: [];
    }

    public function validate(int $id, int $expertId, string $statut, ?string $commentaire): bool
    {
        $stmt = $this->db->prepare("
            UPDATE diagnostics
            SET statut = ?, validateur_id = ?, commentaire_expert = ?, validated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$statut, $expertId, $commentaire, $id]);
    }

    /**
     * Série temporelle : nombre de diagnostics par jour sur N jours.
     * Renvoie un tableau [['jour'=>'2026-05-30','total'=>5], ...] complété
     * pour tous les jours (zéro inclus) afin d'avoir une courbe continue.
     */
    public function perDay(int $days = 30): array
    {
        $stmt = $this->db->prepare("
            SELECT DATE(created_at) AS jour, COUNT(*) AS total
            FROM diagnostics
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
            GROUP BY DATE(created_at)
            ORDER BY jour ASC
        ");
        $stmt->execute([$days]);
        $rows = $stmt->fetchAll();

        $map = [];
        foreach ($rows as $r) {
            $map[$r['jour']] = (int) $r['total'];
        }

        $series = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-$i day"));
            $series[] = ['jour' => $d, 'total' => $map[$d] ?? 0];
        }
        return $series;
    }

    /**
     * Top maladies en tendance (occurrences détectées).
     */
    public function topMaladies(int $limit = 10): array
    {
        $stmt = $this->db->prepare("
            SELECT COALESCE(m.nom_commun, d.maladie_detectee, 'Non identifiée') AS nom,
                   c.nom AS culture,
                   COUNT(*) AS occurrences,
                   ROUND(AVG(d.confiance), 0) AS confiance_moy
            FROM diagnostics d
            LEFT JOIN maladies m ON d.maladie_id = m.id
            LEFT JOIN cultures c ON m.culture_id = c.id
            WHERE d.plante_saine = 0 OR d.plante_saine IS NULL
            GROUP BY nom, culture
            ORDER BY occurrences DESC
            LIMIT $limit
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Répartition des diagnostics par région (carte de chaleur Cameroun).
     */
    public function byRegion(): array
    {
        return $this->db->query("
            SELECT reg.id, reg.nom,
                   COALESCE(reg.latitude, NULL) AS latitude,
                   COALESCE(reg.longitude, NULL) AS longitude,
                   COUNT(d.id) AS total,
                   SUM(d.gravite = 'severe') AS severes
            FROM regions reg
            LEFT JOIN diagnostics d ON d.region_id = reg.id
            GROUP BY reg.id, reg.nom, reg.latitude, reg.longitude
            ORDER BY total DESC
        ")->fetchAll();
    }

    /**
     * Toutes les lignes pour export CSV (jointures lisibles).
     */
    public function allForExport(): array
    {
        return $this->db->query("
            SELECT d.id,
                   d.created_at,
                   CONCAT(COALESCE(u.prenom,''),' ',u.nom) AS agriculteur,
                   u.email,
                   reg.nom AS region,
                   c.nom AS culture,
                   COALESCE(m.nom_commun, d.maladie_detectee, 'Plante saine') AS maladie,
                   d.confiance,
                   d.gravite,
                   d.statut
            FROM diagnostics d
            JOIN users u ON d.user_id = u.id
            LEFT JOIN maladies m ON d.maladie_id = m.id
            LEFT JOIN cultures c ON m.culture_id = c.id
            LEFT JOIN regions reg ON d.region_id = reg.id
            ORDER BY d.created_at DESC
        ")->fetchAll();
    }
}
