<?php
/**
 * Service IA — Claude Vision (Anthropic)
 * Analyse une image de plante et retourne un diagnostic structuré
 */
class AIService
{
    private string $apiKey;
    private string $model;
    private int $maxTokens;

    public function __construct()
    {
        $cfg = config('ai');
        $this->apiKey   = $cfg['api_key'] ?? '';
        $this->model    = $cfg['model'];
        $this->maxTokens = $cfg['max_tokens'];
    }

    public function analyzeImage(string $imagePath): array
    {
        if (!$this->apiKey || str_starts_with($this->apiKey, 'sk-ant-xxxxx')) {
            Logger::warn('API key non configurée — simulation');
            return $this->simulate();
        }

        if (!file_exists($imagePath)) {
            return ['ok' => false, 'error' => 'Image introuvable'];
        }

        $mime = mime_content_type($imagePath);
        $base64 = base64_encode(file_get_contents($imagePath));

        $prompt = $this->buildPrompt();

        $payload = [
            'model'      => $this->model,
            'max_tokens' => $this->maxTokens,
            'messages'   => [[
                'role'    => 'user',
                'content' => [
                    ['type' => 'image', 'source' => ['type' => 'base64', 'media_type' => $mime, 'data' => $base64]],
                    ['type' => 'text', 'text' => $prompt],
                ],
            ]],
        ];

        $start = microtime(true);
        $response = $this->callApi($payload);
        $duration = (int) ((microtime(true) - $start) * 1000);

        if (!$response['ok']) {
            Logger::warn('API IA indisponible, fallback simulation', ['err' => $response['error'] ?? '']);
            $sim = $this->simulate();
            $sim['fallback_reason'] = $response['error'] ?? 'API error';
            return $sim;
        }

        $parsed = $this->parseResponse($response['data']);
        if (!$parsed['ok']) {
            Logger::warn('Parsing IA échoué, fallback simulation');
            return $this->simulate();
        }
        $parsed['duration_ms'] = $duration;
        $parsed['model'] = $this->model;
        $parsed['raw'] = $response['data'];

        return $parsed;
    }

    private function buildPrompt(): string
    {
        return <<<PROMPT
Tu es un phytopathologiste expert en agriculture tropicale africaine, spécialisé dans les cultures du Cameroun (cacao, café, banane plantain, manioc, maïs, tomate, arachide, mil).

Analyse cette image et identifie si la plante est malade. Réponds UNIQUEMENT en JSON valide avec ce format strict :

{
  "plante_saine": false,
  "culture_identifiee": "nom de la culture",
  "maladie_nom_commun": "nom français usuel",
  "maladie_nom_scientifique": "nom latin",
  "confiance": 87,
  "gravite": "severe",
  "symptomes_observes": "description courte des symptômes visibles",
  "traitement": "traitements biologiques prioritaires, puis chimiques",
  "prevention": "mesures préventives"
}

Règles :
- "confiance" : entier 0-100
- "gravite" : "legere", "moderee" ou "severe"
- "plante_saine" : true si aucun signe de maladie
- Si plante saine, mettre maladie_nom_commun à null
- Privilégier les traitements biologiques adaptés au contexte camerounais
PROMPT;
    }

    private function callApi(array $payload): array
    {
        $ch = curl_init('https://api.anthropic.com/v1/messages');
        $caCert = APP_ROOT . '/config/cacert.pem';
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_UNICODE),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'x-api-key: ' . $this->apiKey,
                'anthropic-version: 2023-06-01',
            ],
            CURLOPT_TIMEOUT        => 60,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_CAINFO         => $caCert,
        ]);
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);

        if ($err || $code !== 200) {
            Logger::error('API Claude failed', ['code' => $code, 'err' => $err, 'body' => substr((string) $body, 0, 500)]);
            return ['ok' => false, 'error' => 'Erreur IA (code ' . $code . ')'];
        }

        return ['ok' => true, 'data' => json_decode($body, true)];
    }

    private function parseResponse(array $apiResponse): array
    {
        $text = $apiResponse['content'][0]['text'] ?? '';
        // Extraire le bloc JSON
        if (preg_match('/\{.*\}/s', $text, $m)) {
            $json = json_decode($m[0], true);
            if ($json) {
                return ['ok' => true, 'diagnostic' => $json];
            }
        }
        return ['ok' => false, 'error' => 'Réponse IA invalide', 'raw_text' => $text];
    }

    private function simulate(): array
    {
        $samples = [
            ['plante_saine' => false, 'culture_identifiee' => 'Tomate', 'maladie_nom_commun' => 'Mildiou de la tomate', 'maladie_nom_scientifique' => 'Phytophthora infestans', 'confiance' => 87, 'gravite' => 'severe', 'symptomes_observes' => 'Taches brun-noir, moisissure blanche au revers', 'traitement' => 'Bouillie bordelaise, retrait des feuilles atteintes', 'prevention' => 'Arrosage au pied, paillage'],
            ['plante_saine' => false, 'culture_identifiee' => 'Cacao', 'maladie_nom_commun' => 'Pourriture brune du cacao', 'maladie_nom_scientifique' => 'Phytophthora megakarya', 'confiance' => 92, 'gravite' => 'severe', 'symptomes_observes' => 'Cabosses brunâtres', 'traitement' => 'Élimination des cabosses, fongicide cuprique', 'prevention' => 'Drainage, taille'],
            ['plante_saine' => true, 'culture_identifiee' => 'Manioc', 'maladie_nom_commun' => null, 'confiance' => 95, 'gravite' => 'legere', 'symptomes_observes' => 'Aucun signe de maladie', 'traitement' => null, 'prevention' => 'Surveillance continue'],
        ];
        return ['ok' => true, 'diagnostic' => $samples[array_rand($samples)], 'simulated' => true, 'duration_ms' => 800, 'model' => 'simulation'];
    }
}
