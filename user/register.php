<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

$error = $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];

    if (strlen($password) < 6) {
        $error = "Password minimum 6 characters hona chahiye.";
    } else {
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            $error = "Yeh email already registered hai.";
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password) VALUES (?,?,?,?)");
            $stmt->execute([$name, $email, $phone, $hash]);
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['user_name'] = $name;
            header("Location: /user/events.php");
            exit();
        }
    }
}
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Register — EventHub</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}body{font-family:'Segoe UI',sans-serif;background:#0f0f1a;color:#fff;min-height:100vh;display:flex;align-items:center;justify-content:center}
.card{background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:16px;padding:2.5rem;width:100%;max-width:440px}
h1{font-size:1.8rem;font-weight:700;margin-bottom:0.5rem;background:linear-gradient(135deg,#6366f1,#a855f7);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
p{color:#888;margin-bottom:1.5rem}
label{display:block;font-size:0.85rem;color:#ccc;margin-bottom:0.3rem}
input{width:100%;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.15);border-radius:8px;padding:0.7rem 1rem;color:#fff;font-size:0.95rem;margin-bottom:1rem}
input:focus{outline:none;border-color:#6366f1}
.btn{width:100%;padding:0.8rem;background:linear-gradient(135deg,#6366f1,#a855f7);border:none;border-radius:8px;color:#fff;font-size:1rem;font-weight:600;cursor:pointer;margin-top:0.5rem}
.btn:hover{opacity:0.85}
.error{background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);border-radius:8px;padding:0.7rem;color:#f87171;margin-bottom:1rem;font-size:0.9rem}
.link{text-align:center;margin-top:1rem;color:#888;font-size:0.9rem}
.link a{color:#a855f7;text-decoration:none}
</style></head><body>
<div class="card">
  <h1>Create Account</h1>
  <p>EventHub pe events book karo</p>
  <?php if($error): ?><div class="error"><?= $error ?></div><?php endif; ?>
  <form method="POST">
    <label>Full Name</label><input type="text" name="name" required placeholder="Aapka naam">
    <label>Email Address</label><input type="email" name="email" required placeholder="email@example.com">
    <label>Phone Number</label><input type="tel" name="phone" placeholder="9876543210">
    <label>Password</label><input type="password" name="password" required placeholder="Min 6 characters">
    <button type="submit" class="btn">Register Karo</button>
  </form>
  <div class="link">Already have account? <a href="/user/login.php">Login karo</a></div>
  <div class="link" style="margin-top:0.5rem"><a href="/">← Back to Home</a></div>
</div>
</body></html>
