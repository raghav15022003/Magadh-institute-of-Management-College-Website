<?php
/**
 * verify.php
 * Sirf Registration Number se result dhoondta hai.
 * PDF naam rule:  {REGISTRATION_NUMBER}.pdf   (e.g. MIM2026PG001.pdf)
 */

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$regRaw = isset($_POST['reg']) ? trim($_POST['reg']) : '';
$reg    = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $regRaw));

$fail = ['success' => false, 'message' => 'Result not found — Please check your Registration Number'];

if ($reg === '') {
    echo json_encode($fail);
    exit;
}

$resultDir     = __DIR__ . '/RESULT';
$fileName      = $reg . '.pdf';
$realResultDir = realpath($resultDir);
$realFilePath  = $realResultDir ? realpath($resultDir . '/' . $fileName) : false;

if ($realFilePath && strpos($realFilePath, $realResultDir) === 0 && is_file($realFilePath)) {
    $q = urlencode($fileName);
    echo json_encode([
        'success'     => true,
        'reg'         => $reg,
        'fileName'    => $fileName,
        'viewUrl'     => 'result-download.php?view=1&file=' . $q,
        'downloadUrl' => 'result-download.php?file=' . $q
    ]);
} else {
    echo json_encode($fail);
}
