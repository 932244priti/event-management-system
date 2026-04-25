<?php
require_once 'config/db.php';
$events = $pdo->query("SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 6")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EventHub — Book Amazing Events</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Segoe UI',sans-serif;background:#0f0f1a;color:#fff}
nav{background:rgba(255,255,255,0.05);backdrop-filter:blur(10px);padding:1rem 2rem;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;z-index:100;border-bottom:1px solid rgba(255,255,255,0.1)}
.logo{font-size:1.5rem;font-weight:700;background:linear-gradient(135deg,#6366f1,#a855f7);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.nav-links a{color:#ccc;text-decoration:none;margin-left:1.5rem;transition:color 0.3s}
.nav-links a:hover{color:#fff}
.btn{padding:0.5rem 1.2rem;border-radius:8px;text-decoration:none;font-weight:600;transition:all 0.3s;cursor:pointer;border:none;font-size:0.95rem}
.btn-primary{background:linear-gradient(135deg,#6366f1,#a855f7);color:#fff}
.btn-primary:hover{opacity:0.85;transform:translateY(-1px)}
.btn-outline{border:1px solid #6366f1;color:#6366f1;background:transparent}
.btn-outline:hover{background:#6366f1;color:#fff}
.hero{text-align:center;padding:5rem 2rem;background:radial-gradient(ellipse at center,rgba(99,102,241,0.15) 0%,transparent 70%)}
.hero h1{font-size:3rem;font-weight:800;margin-bottom:1rem;background:linear-gradient(135deg,#fff,#a855f7);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.hero p{font-size:1.2rem;color:#888;margin-bottom:2rem}
.events-section{padding:3rem 2rem;max-width:1200px;margin:0 auto}
.section-title{font-size:1.8rem;font-weight:700;margin-bottom:2rem;text-align:center}
.events-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:1.5rem}
.event-card{background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:16px;overflow:hidden;transition:transform 0.3s,box-shadow 0.3s}
.event-card:hover{transform:translateY(-5px);box-shadow:0 20px 40px rgba(0,0,0,0.4)}
.event-banner{height:160px;display:flex;align-items:center;justify-content:center;font-size:3rem;position:relative;overflow:hidden}
.event-banner::before{content:'';position:absolute;inset:0;opacity:0.3}
.event-info{padding:1.2rem}
.event-category{font-size:0.75rem;text-transform:uppercase;letter-spacing:1px;opacity:0.7;margin-bottom:0.5rem}
.event-title{font-size:1.1rem;font-weight:700;margin-bottom:0.5rem}
.event-meta{font-size:0.85rem;color:#888;margin-bottom:0.8rem}
.event-footer{display:flex;justify-content:space-between;align-items:center}
.event-price{font-weight:700;font-size:1rem}
.event-tickets{font-size:0.8rem;color:#888}
.features{background:rgba(255,255,255,0.02);padding:4rem 2rem;text-align:center}
.features-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:2rem;max-width:900px;margin:2rem auto 0}
.feature-card{padding:2rem;border:1px solid rgba(255,255,255,0.08);border-radius:12px}
.feature-icon{font-size:2rem;margin-bottom:1rem}
footer{text-align:center;padding:2rem;color:#555;border-top:1px solid rgba(255,255,255,0.05)}
</style>
</head>
<body>
<nav>
  <div class="logo">🎟️ EventHub</div>
  <div class="nav-links">
    <a href="/">Home</a>
    <a href="/user/events.php">Events</a>
    <?php if(isset($_SESSION['user_id'])): ?>
      <a href="/user/my-tickets.php">My Tickets</a>
      <a href="/user/logout.php">Logout</a>
    <?php else: ?>
      <a href="/user/login.php" class="btn btn-outline" style="margin-left:1rem">Login</a>
      <a href="/user/register.php" class="btn btn-primary" style="margin-left:0.5rem">Register</a>
    <?php endif; ?>
  </div>
</nav>

<div class="hero">
  <h1>Discover & Book<br>Amazing Events</h1>
  <p>Concerts, Tech Talks, Exhibitions & More — All in One Place</p>
  <a href="/user/events.php" class="btn btn-primary" style="font-size:1.1rem;padding:0.8rem 2rem">Browse Events</a>
</div>

<div class="events-section">
  <h2 class="section-title">Upcoming Events</h2>
  <div class="events-grid">
    <?php
    $icons = ['Tech'=>'💻','Music'=>'🎵','Business'=>'💼','Sports'=>'⚽','Art'=>'🎨','Food'=>'🍴'];
    foreach($events as $e):
      $icon = $icons[$e['category']] ?? '🎉';
    ?>
    <div class="event-card">
      <div class="event-banner" style="background:<?= $e['color'] ?>22;">
        <span style="font-size:4rem"><?= $icon ?></span>
      </div>
      <div class="event-info">
        <div class="event-category" style="color:<?= $e['color'] ?>"><?= htmlspecialchars($e['category']) ?></div>
        <div class="event-title"><?= htmlspecialchars($e['title']) ?></div>
        <div class="event-meta">📅 <?= date('D, d M Y', strtotime($e['event_date'])) ?> &nbsp;|&nbsp; 📍 <?= htmlspecialchars($e['venue']) ?></div>
        <div class="event-footer">
          <div class="event-price" style="color:<?= $e['color'] ?>">₹<?= number_format($e['price'],2) ?></div>
          <div class="event-tickets">🎟️ <?= $e['available_tickets'] ?> left</div>
        </div>
        <a href="/user/book.php?id=<?= $e['id'] ?>" class="btn btn-primary" style="display:block;text-align:center;margin-top:1rem">Book Now</a>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<div class="features">
  <h2 class="section-title">Why Choose EventHub?</h2>
  <div class="features-grid">
    <div class="feature-card"><div class="feature-icon">⚡</div><h3>Instant Booking</h3><p style="color:#888;margin-top:0.5rem">Book tickets in seconds</p></div>
    <div class="feature-card"><div class="feature-icon">🔒</div><h3>Secure Payments</h3><p style="color:#888;margin-top:0.5rem">100% safe & encrypted</p></div>
    <div class="feature-card"><div class="feature-icon">📱</div><h3>Mobile Friendly</h3><p style="color:#888;margin-top:0.5rem">Works on all devices</p></div>
  </div>
</div>

<footer><p>© 2025 EventHub. All Rights Reserved.</p></footer>
</body>
</html>
