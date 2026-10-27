<?php
require_once __DIR__ . '/config.php';

if (empty($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit;
}

// Upload ka result (zip-upload.php / bulk-upload.php se redirect hone par)
$bulkResult = null;
if (!empty($_SESSION['bulk_result'])) {
    $bulkResult = $_SESSION['bulk_result'];
    unset($_SESSION['bulk_result']);
}

// Abhi tak upload hue results
$existingFiles = [];
if (is_dir(RESULT_DIR)) {
    foreach (scandir(RESULT_DIR) as $f) {
        if (preg_match('/\.pdf$/i', $f)) {
            $existingFiles[] = $f;
        }
    }
    sort($existingFiles);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin — Upload Results | MIM Result Portal</title>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@600;700;800&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
  :root{
    --red:#C0392B; --red-dark:#A5301F; --bg:#F7F5F5; --card:#fff; --border:#EDE0E0;
    --text:#222; --muted:#6B7280; --green:#2E7D32; --danger:#DC2626;
  }
  *{ margin:0; padding:0; box-sizing:border-box; }
  body{ font-family:'Poppins', sans-serif; background:var(--bg); color:var(--text); padding:30px 16px 60px; }
  main{ max-width:640px; margin:0 auto; }
  .top-row{ display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
  .top-row h1{ font-family:'Sora', sans-serif; font-size:19px; }
  .logout{ font-size:13px; color:var(--red); text-decoration:none; font-weight:600; }

  .card{
    background:var(--card); border:1px solid var(--border); border-radius:18px;
    box-shadow:0 8px 26px -12px rgba(0,0,0,0.10); padding:28px clamp(18px,4vw,34px) 32px; margin-bottom:22px;
  }
  .card h2{ font-family:'Sora', sans-serif; font-size:17px; margin-bottom:18px; }

  .field{ margin-bottom:16px; display:flex; flex-direction:column; gap:6px; }
  .field label{ font-size:12.5px; font-weight:600; }
  .field input{
    padding:12px 14px; border:1px solid #DDD; border-radius:12px; background:#FAFAFA;
    font-family:'Poppins', sans-serif; font-size:14.5px;
  }
  .field input:focus{ outline:none; border-color:var(--red); background:#fff; }
  .hint{ font-size:11.5px; color:var(--muted); margin-top:2px; }

  button{
    width:100%; padding:14px; border:none; border-radius:12px; cursor:pointer;
    background:linear-gradient(135deg, var(--red), var(--red-dark)); color:#fff;
    font-weight:600; font-size:14.5px; letter-spacing:.02em;
  }

  .msg{ padding:12px 14px; border-radius:10px; font-size:13.5px; font-weight:600; margin-bottom:18px; }
  .msg.success{ background:#E8F5E9; color:var(--green); }
  .msg.error{ background:#FDECEA; color:var(--danger); }

  table{ width:100%; border-collapse:collapse; font-size:13.5px; }
  th, td{ text-align:left; padding:10px 8px; border-bottom:1px solid var(--border); }
  th{ color:var(--muted); font-weight:600; font-size:12px; text-transform:uppercase; }
  .empty{ color:var(--muted); font-size:13.5px; text-align:center; padding:14px 0; }
</style>
</head>
<body>
<main>

  <div class="top-row">
    <h1>Result Upload Panel</h1>
    <a class="logout" href="admin-logout.php">Logout</a>
  </div>

  <?php if ($bulkResult): ?>
    <div class="card">
      <h2>Upload Result</h2>
      <div class="msg success"><?php echo count($bulkResult['success']); ?> result(s) successfully uploaded.</div>
      <?php if (!empty($bulkResult['failed'])): ?>
        <div class="msg error"><?php echo count($bulkResult['failed']); ?> file(s) skipped:</div>
        <table>
          <tr><th>File</th><th>Reason</th></tr>
          <?php foreach ($bulkResult['failed'] as $f): ?>
            <?php $parts = explode(' — ', $f, 2); ?>
            <tr><td><?php echo htmlspecialchars($parts[0]); ?></td><td><?php echo htmlspecialchars($parts[1] ?? ''); ?></td></tr>
          <?php endforeach; ?>
        </table>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <div class="card">
    <h2>ZIP Upload (500+ Results in One File)</h2>
    <p class="hint" style="margin-bottom:14px;">
      Saare PDFs ko ek ZIP mein daalo. ZIP ka naam kuch bhi ho sakta hai
      (jaise results.zip), lekin andar har PDF ka naam REG.pdf pattern mein
      hona chahiye (jaise MIM2026PG001.pdf).
    </p>
    <form method="POST" action="zip-upload.php" enctype="multipart/form-data">
      <div class="field">
        <label>Select ZIP File</label>
        <input type="file" name="zipFile" accept=".zip,application/zip" required>
      </div>
      <button type="submit">Upload ZIP</button>
    </form>
  </div>

  <div class="card">
    <h2>Normal Upload (Multiple PDFs)</h2>
    <p class="hint" style="margin-bottom:14px;">
      Har PDF ka naam REG.pdf pattern mein hona chahiye (jaise MIM2026PG001.pdf).
      Registration number filename se hi liya jaayega. Same naam ki purani file
      automatically replace ho jaayegi.
    </p>
    <form method="POST" action="bulk-upload.php" enctype="multipart/form-data">
      <div class="field">
        <label>Select PDF Files (ya niche se poora folder chuno)</label>
        <input type="file" name="bulkFiles[]" id="bulkFilesInput" accept="application/pdf" multiple required>
        <div class="hint">
          Ctrl (ya Cmd) dabaa ke ek saath kai files select kar sakte ho, ya
          <a href="#" id="chooseFolderLink" style="color:var(--red); font-weight:600;">poora folder select karo</a>.
          Zyada files (20+) ho to ZIP upload use karo.
        </div>
      </div>
      <button type="submit">Upload All</button>
    </form>
  </div>

  <div class="card">
    <h2>Currently Uploaded Results (<?php echo count($existingFiles); ?>)</h2>
    <?php if (empty($existingFiles)): ?>
      <div class="empty">No results uploaded yet.</div>
    <?php else: ?>
      <table>
        <tr><th>File Name</th></tr>
        <?php foreach ($existingFiles as $f): ?>
          <tr><td><?php echo htmlspecialchars($f); ?></td></tr>
        <?php endforeach; ?>
      </table>
    <?php endif; ?>
  </div>

</main>

<script>
document.getElementById('chooseFolderLink').addEventListener('click', function(e){
  e.preventDefault();
  var input = document.getElementById('bulkFilesInput');
  input.setAttribute('webkitdirectory', '');
  input.setAttribute('directory', '');
  input.click();
});
</script>
</body>
</html>
