<?php
class MaladieController extends Controller
{
    public function index(): void
    {
        $maladieModel = new Maladie();
        $maladies = $maladieModel->allWithCulture();

        $cultures = Database::connect()->query("SELECT * FROM cultures ORDER BY nom")->fetchAll();

        $search    = trim((string) $this->input('q'));
        $cultureId = (int) $this->input('culture');
        $type      = (string) $this->input('type');

        // Filtrage côté PHP (volume faible)
        if ($search) {
            $s = strtolower($search);
            $maladies = array_filter($maladies, fn($m) =>
                str_contains(strtolower($m['nom_commun']), $s) ||
                str_contains(strtolower($m['nom_scientifique'] ?? ''), $s) ||
                str_contains(strtolower($m['symptomes']), $s)
            );
        }
        if ($cultureId) {
            $maladies = array_filter($maladies, fn($m) => (int) $m['culture_id'] === $cultureId);
        }
        if ($type) {
            $maladies = array_filter($maladies, fn($m) => $m['type_pathologie'] === $type);
        }

        $layout = auth() ? 'main' : 'public';
        $this->view('maladies/index', [
            'title'    => 'Catalogue des maladies',
            'maladies' => array_values($maladies),
            'cultures' => $cultures,
            'search'   => $search,
            'filters'  => ['culture' => $cultureId, 'type' => $type],
        ], $layout);
    }

    public function show($id): void
    {
        $maladie = (new Maladie())->find((int) $id);
        if (!$maladie) {
            http_response_code(404);
            die('Maladie introuvable');
        }

        $culture = Database::connect()->prepare("SELECT * FROM cultures WHERE id = ?");
        $culture->execute([$maladie['culture_id']]);
        $culture = $culture->fetch();

        $layout = auth() ? 'main' : 'public';
        $this->view('maladies/show', [
            'title'   => $maladie['nom_commun'],
            'maladie' => $maladie,
            'culture' => $culture,
        ], $layout);
    }
}
