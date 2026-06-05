<?php
class Feedback extends Model
{
    protected string $table = 'feedbacks';

    public function findByDiagnostic(int $diagnosticId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM feedbacks WHERE diagnostic_id = ? LIMIT 1");
        $stmt->execute([$diagnosticId]);
        return $stmt->fetch() ?: null;
    }

    public function save(int $diagnosticId, int $userId, bool $correct, ?int $note, ?string $commentaire): bool
    {
        // Upsert : si feedback existe déjà pour ce user+diag, on met à jour
        $existing = $this->db->prepare("SELECT id FROM feedbacks WHERE diagnostic_id = ? AND user_id = ?");
        $existing->execute([$diagnosticId, $userId]);
        $row = $existing->fetch();

        if ($row) {
            $stmt = $this->db->prepare("UPDATE feedbacks SET est_correct = ?, note = ?, commentaire = ? WHERE id = ?");
            return $stmt->execute([$correct ? 1 : 0, $note, $commentaire, $row['id']]);
        }

        $stmt = $this->db->prepare("INSERT INTO feedbacks (diagnostic_id, user_id, est_correct, note, commentaire) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$diagnosticId, $userId, $correct ? 1 : 0, $note, $commentaire]);
    }
}
