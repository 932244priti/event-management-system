<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        header("Location: /admin/dashboard.php");
        exit();
    } else {
        $error = "Admin credentials galat hain.";
    }
}
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Admin Login</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}body{font-family:'Segoe UI',sans-serif;background:#0a0a14;color:#fff;min-height:100vh;display:flex;align-items:center;justify-content:center}
.card{background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:16px;padding:2.5rem;width:100%;max-width:400px}
.badge{background:rgba(239,68,68,0.15);color:#f87171;border:1px solid rgba(239,68,68,0.3);padding:0.3rem 0.8rem;border-radius:20px;font-size:0.8rem;display:inline-block;margin-bottom:1rem}
h1{font-size:1.8rem;font-weight:700;margin-bottom:0.3rem;background:linear-gradient(135deg,#f59e0b,#ef4444);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
p{color:#888;margin-bottom:1.5rem;font-size:0.9rem}
label{display:block;font-size:0.85rem;color:#ccc;margin-bottom:0.3rem}
input{width:100%;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.15);border-radius:8px;padding:0.7rem;color:#fff;font-size:0.95rem;margin-bottom:1rem}
.btn{width:100%;padding:0.8rem;background:linear-gradient(135deg,#f59e0b,#ef4444);border:none;border-radius:8px;color:#fff;font-size:1rem;font-weight:600;cursor:pointer}
.error{background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);border-radius:8px;padding:0.7rem;color:#f87171;margin-bottom:1rem;font-size:0.9rem}
.link{text-align:center;margin-top:1rem;font-size:0.9rem}<a href="/">color:#888;text-decoration:none</a>
</style></head><body>
<div class="card">
  <div class="badge">🔐 Admin Portal</div>
  <h1>Admin Login</h1>
  <p>EventHub Management Console</p>
  <?php if($error): ?><div class="error"><?= $error ?></div><?php endif; ?>
  <form method="POST">
    <label>Admin Email</label><input type="email" name="email" required placeholder="admin@events.com">
    <label>Password</label><input type="password" name="password" required placeholder="••••••••">
    <button type="submit" class="btn">Login to Dashboard</button>
  </form>
  <div style="text-align:center;margin-top:1rem"><a href="/" style="color:#888;font-size:0.9rem;text-decoration:none">← Back to Site</a></div>
</div>
</body></html>
