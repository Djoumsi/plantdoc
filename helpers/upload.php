<?php
class Upload
{
    public static function image(array $file, string $subdir = 'diagnostics'): array
    {
        $cfg = config('upload');

        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'error' => 'Erreur lors de l\'envoi du fichier'];
        }
        if ($file['size'] > $cfg['max_size']) {
            return ['ok' => false, 'error' => 'Fichier trop volumineux (max ' . round($cfg['max_size']/1024/1024) . ' Mo)'];
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        if (!in_array($mime, $cfg['allowed'], true)) {
            return ['ok' => false, 'error' => 'Type de fichier non autorisé'];
        }

        $dims = @getimagesize($file['tmp_name']);
        if (!$dims || $dims[0] < 100 || $dims[1] < 100) {
            return ['ok' => false, 'error' => 'Image trop petite (min 100x100)'];
        }

        $ext = match($mime) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        };

        $filename = bin2hex(random_bytes(16)) . '.' . $ext;
        $dir = APP_ROOT . "/public/uploads/$subdir";
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $dest = "$dir/$filename";

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            return ['ok' => false, 'error' => 'Impossible de sauvegarder'];
        }

        return [
            'ok' => true,
            'path' => "uploads/$subdir/$filename",
            'absolute' => $dest,
            'mime' => $mime,
            'size' => $file['size'],
        ];
    }
}
