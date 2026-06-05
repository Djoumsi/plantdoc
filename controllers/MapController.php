<?php
class MapController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        $regions = (new Region())->allWithStats();

        // Top 5 maladies en circulation
        $db = Database::connect();
        $topMaladies = $db->query("
            SELECT m.nom_commun, m.nom_scientifique, c.nom AS culture,
                   COUNT(d.id) AS total
            FROM maladies m
            JOIN diagnostics d ON d.maladie_id = m.id
            LEFT JOIN cultures c ON m.culture_id = c.id
            GROUP BY m.id ORDER BY total DESC LIMIT 5
        ")->fetchAll();

        $globalStats = [
            'total'      => (int) $db->query("SELECT COUNT(*) FROM diagnostics")->fetchColumn(),
            'severes'    => (int) $db->query("SELECT COUNT(*) FROM diagnostics WHERE gravite = 'severe'")->fetchColumn(),
            'regions_actives' => (int) $db->query("SELECT COUNT(DISTINCT region_id) FROM diagnostics WHERE region_id IS NOT NULL")->fetchColumn(),
        ];

        $this->view('map/index', [
            'title'         => 'Carte épidémiologique',
            'regions'       => $regions,
            'top_maladies'  => $topMaladies,
            'global_stats'  => $globalStats,
        ]);
    }
}
