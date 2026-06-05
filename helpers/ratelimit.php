<?php
class RateLimit
{
    public static function check(string $action, string $key, int $maxAttempts, int $windowMinutes = 60): bool
    {
        $db = Database::connect();
        $cutoff = date('Y-m-d H:i:s', time() - $windowMinutes * 60);

        $db->prepare("DELETE FROM rate_limits WHERE window_start < ?")->execute([$cutoff]);

        $stmt = $db->prepare("SELECT id, count FROM rate_limits WHERE cle = ? AND action = ? AND window_start >= ?");
        $stmt->execute([$key, $action, $cutoff]);
        $row = $stmt->fetch();

        if ($row) {
            if ($row['count'] >= $maxAttempts) return false;
            $db->prepare("UPDATE rate_limits SET count = count + 1 WHERE id = ?")->execute([$row['id']]);
        } else {
            $db->prepare("INSERT INTO rate_limits (cle, action, count) VALUES (?, ?, 1)")
               ->execute([$key, $action]);
        }
        return true;
    }
}
