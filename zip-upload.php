<?php
require_once __DIR__ . '/config.php';

if (empty($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit;
}

@set_time_limit(0);
@ini_set('memory_limit', '512M');

$success = [];
$failed  = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['zipFile'])) {

    $f = $_FILES['zipFile'];

    if ($f['error'] !== UPLOAD_ERR_OK) {
        $failed[] = 'ZIP file — upload nahi hui (error code ' . $f['error'] . '). Server ki upload_max_filesize / post_max_size badhao.';
    } elseif (strtolower(pathinfo($f['name'], PATHINFO_EXTENSION)) !== 'zip') {
        $failed[] = basename($f['name']) . ' — ZIP file nahi hai';
    } elseif (!class_exists('ZipArchive')) {
        $failed[] = 'ZIP file — server par ZipArchive (php-zip) enabled nahi hai';
    } else {

        if (!is_dir(RESULT_DIR)) {
            mkdir(RESULT_DIR, 0755, true);
        }

        $zip = new ZipArchive();
        if ($zip->open($f['tmp_name']) !== true) {
            $failed[] = basename($f['name']) . ' — ZIP khul nahi paayi (corrupt ho sakti hai)';
        } else {

            for ($i = 0; $i < $zip->numFiles; $i++) {
                $stat  = $zip->statIndex($i);
                $entry = str_replace('\\', '/', $stat['name']);

                if (substr($entry, -1) === '/') continue;                 // folders skip

                $name = basename($entry);                                  // zip-slip se bachaav

                if ($name === '' || $name[0] === '.' || strpos($entry, '__MACOSX/') !== false) continue;

                // Filename pattern: REG.pdf
                if (!preg_match('/^([A-Za-z0-9]+)\.pdf$/i', $name, $m)) {
                    $failed[] = $name . ' — naam sahi pattern mein nahi hai (REG.pdf)';
                    continue;
                }

                if ($stat['size'] > MAX_UPLOAD_SIZE) {
                    $failed[] = $name . ' — 5 MB se bada hai';
                    continue;
                }

                $data = $zip->getFromIndex($i);
                if ($data === false || strncmp($data, '%PDF', 4) !== 0) {
                    $failed[] = $name . ' — valid PDF file nahi hai';
                    continue;
                }

                $normalizedName = strtoupper($m[1]) . '.pdf';
                $destPath = RESULT_DIR . '/' . $normalizedName;

                if (file_put_contents($destPath, $data) !== false) {
                    $success[] = $normalizedName;
                } else {
                    $failed[] = $name . ' — save nahi ho paya (folder permission issue?)';
                }
                unset($data);
            }
            $zip->close();

            if (!$success && !$failed) {
                $failed[] = 'ZIP mein koi PDF nahi mili';
            }
        }
    }
} else {
    $failed[] = 'ZIP file select nahi hui (ya file server limit se badi hai)';
}

$_SESSION['bulk_result'] = ['success' => $success, 'failed' => $failed];
header('Location: admin-upload.php');
exit;
