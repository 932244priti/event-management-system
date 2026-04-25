<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        header("Location: /user/events.php");
        exit();
    } else {
        $error = "Email ya password galat hai.";
    }
}
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Login — EventHub</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}body{font-family:'Segoe UI',sans-serif;background:#0f0f1a;color:#fff;min-height:100vh;display:flex;align-items:center;justify-content:center}
.card{background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:16px;padding:2.5rem;width:100%;max-width:420px}
h1{font-size:1.8rem;font-weight:700;margin-bottom:0.5rem;background:linear-gradient(135deg,#6366f1,#a855f7);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
p{color:#888;margin-bottom:1.5rem}
label{display:block;font-size:0.85rem;color:#ccc;margin-bottom:0.3rem}
input{width:100%;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.15);border-radius:8px;padding:0.7rem 1rem;color:#fff;font-size:0.95rem;margin-bottom:1rem}
input:focus{outline:none;border-color:#6366f1}
.btn{width:100%;padding:0.8rem;background:linear-gradient(135deg,#6366f1,#a855f7);border:none;border-radius:8px;color:#fff;font-size:1rem;font-weight:600;cursor:pointer}
.btn:hover{opacity:0.85}
.error{background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);border-radius:8px;padding:0.7rem;color:#f87171;margin-bottom:1rem;font-size:0.9rem}
.link{text-align:center;margin-top:1rem;color:#888;font-size:0.9rem}.link a{color:#a855f7;text-decoration:none}
</style></head><body>
<div class="card">
  <h1>Welcome Back</h1>
  <p>Apne account mein login karo</p>
  <?php if($error): ?><div class="error"><?= $error ?></div><?php endif; ?>
  <form method="POST">
    <label>Email Address</label><input type="email" name="email" required placeholder="email@example.com">
    <label>Password</label><input type="password" name="password" required placeholder="Aapka password">
    <button type="submit" class="btn">Login Karo</button>
  </form>
  <div class="link">Account nahi hai? <a href="/user/register.php">Register karo</a></div>
  <div class="link" style="margin-top:0.5rem"><a href="/">← Back to Home</a></div>
</div>
</body></html>
