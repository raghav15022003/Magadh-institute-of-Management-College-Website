<?php
/**
 * result-download.php
 * RESULT folder se PDF stream karta hai.
 *   ?file=REG.pdf            -> download
 *   ?file=REG.pdf&view=1     -> page ke andar preview (inline)
 */

$resultDir = __DIR__ . '/RESULT';
$file = isset($_GET['file']) ? basename($_GET['file']) : '';

if (!$file || !preg_match('/^[A-Z0-9]+\.pdf$/i', $file)) {
    http_response_code(400);
    exit('Invalid request.');
}

$filePath = $resultDir . '/' . $file;

if (!is_file($filePath)) {
    http_response_code(404);
    exit('File not found.');
}

$inline = !empty($_GET['view']);

header('Content-Type: application/pdf');
header('Content-Disposition: ' . ($inline ? 'inline' : 'attachment') . '; filename="' . $file . '"');
header('Content-Length: ' . filesize($filePath));
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-cache, must-revalidate');
readfile($filePath);
exit;
