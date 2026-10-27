<?php
require_once __DIR__ . '/config.php';

if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: admin-upload.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    if (password_verify($password, ADMIN_PASSWORD_HASH)) {
        session_regenerate_id(true);
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin-upload.php');
        exit;
    } else {
        $error = 'Incorrect password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login | MIM Result Portal</title>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@600;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
  :root{ --red:#C0392B; --red-dark:#A5301F; --bg:#F7F5F5; --text:#222; --danger:#DC2626; }
  *{ margin:0; padding:0; box-sizing:border-box; }
  body{
    font-family:'Poppins', sans-serif; background:var(--bg); color:var(--text);
    min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px;
  }
  .card{
    background:#fff; border:1px solid #EDE0E0; border-radius:18px;
    box-shadow:0 8px 26px -12px rgba(0,0,0,0.12); padding:38px 32px; max-width:380px; width:100%;
  }
  h1{ font-family:'Sora', sans-serif; font-size:20px; text-align:center; margin-bottom:6px; }
  p.sub{ text-align:center; color:#6B7280; font-size:13px; margin-bottom:24px; }
  input[type=password]{
    width:100%; padding:13px 14px; border:1px solid #DDD; border-radius:12px;
    background:#FAFAFA; font-family:'Poppins', sans-serif; font-size:14.5px; margin-bottom:16px;
  }
  input[type=password]:focus{ outline:none; border-color:var(--red); background:#fff; }
  button{
    width:100%; padding:14px; border:none; border-radius:12px; cursor:pointer;
    background:linear-gradient(135deg, var(--red), var(--red-dark)); color:#fff;
    font-weight:600; font-size:14.5px; letter-spacing:.02em;
  }
  .error{ color:var(--danger); font-size:13px; text-align:center; margin-bottom:14px; font-weight:600; }
</style>
</head>
<body>
  <div class="card">
    <h1>Admin Login</h1>
    <p class="sub">MIM Result Upload Panel</p>
    <?php if ($error): ?>
      <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <form method="POST">
      <input type="password" name="password" placeholder="Enter admin password" required autofocus>
      <button type="submit">Login</button>
    </form>
  </div>
</body>
</html>
