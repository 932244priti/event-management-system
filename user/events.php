<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

$sql = "SELECT * FROM events WHERE event_date >= CURDATE()";
$params = [];
if ($search) { $sql .= " AND (title LIKE ? OR venue LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
if ($category) { $sql .= " AND category = ?"; $params[] = $category; }
$sql .= " ORDER BY event_date ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$events = $stmt->fetchAll();
$categories = $pdo->query("SELECT DISTINCT category FROM events")->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Events — EventHub</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}body{font-family:'Segoe UI',sans-serif;background:#0f0f1a;color:#fff}
nav{background:rgba(255,255,255,0.05);backdrop-filter:blur(10px);padding:1rem 2rem;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;z-index:100;border-bottom:1px solid rgba(255,255,255,0.1)}
.logo{font-size:1.5rem;font-weight:700;background:linear-gradient(135deg,#6366f1,#a855f7);-webkit-background-clip:text;-webkit-text-fill-color:transparent;text-decoration:none}
.nav-links a{color:#ccc;text-decoration:none;margin-left:1.5rem}.nav-links a:hover{color:#fff}
.btn{padding:0.5rem 1.2rem;border-radius:8px;text-decoration:none;font-weight:600;transition:all 0.3s;cursor:pointer;border:none;font-size:0.9rem}
.btn-primary{background:linear-gradient(135deg,#6366f1,#a855f7);color:#fff}.btn-primary:hover{opacity:0.85}
main{max-width:1200px;margin:0 auto;padding:2rem}
h1{font-size:2rem;font-weight:700;margin-bottom:1.5rem}
.filters{display:flex;gap:1rem;margin-bottom:2rem;flex-wrap:wrap}
.filters input,.filters select{background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.15);border-radius:8px;padding:0.6rem 1rem;color:#fff;font-size:0.9rem}
.filters input{flex:1;min-width:200px}.filters select option{background:#1a1a2e}
.events-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:1.5rem}
.event-card{background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:16px;overflow:hidden;transition:transform 0.3s}
.event-card:hover{transform:translateY(-4px)}
.event-banner{height:140px;display:flex;align-items:center;justify-content:center;font-size:3.5rem}
.event-info{padding:1.2rem}
.event-cat{font-size:0.75rem;text-transform:uppercase;letter-spacing:1px;opacity:0.7;margin-bottom:0.4rem}
.event-title{font-size:1.05rem;font-weight:700;margin-bottom:0.5rem}
.event-meta{font-size:0.82rem;color:#888;margin-bottom:0.8rem;line-height:1.8}
.event-footer{display:flex;justify-content:space-between;align-items:center;margin-bottom:0.8rem}
.price{font-weight:700}.tickets{font-size:0.8rem;color:#888}
.empty{text-align:center;padding:4rem;color:#555}
</style></head><body>
<nav>
  <a href="/" class="logo">🎟️ EventHub</a>
  <div class="nav-links">
    <a href="/user/events.php">Events</a>
    <?php if(isUserLoggedIn()): ?>
      <a href="/user/my-tickets.php">My Tickets</a>
      <a href="/user/logout.php">Logout (<?= htmlspecialchars($_SESSION['user_name']) ?>)</a>
    <?php else: ?>
      <a href="/user/login.php" class="btn" style="border:1px solid #6366f1;color:#6366f1">Login</a>
      <a href="/user/register.php" class="btn btn-primary">Register</a>
    <?php endif; ?>
  </div>
</nav>
<main>
  <h1>🎉 All Events</h1>
  <form method="GET" class="filters">
    <input type="text" name="search" placeholder="Search events..." value="<?= htmlspecialchars($search) ?>">
    <select name="category">
      <option value="">All Categories</option>
      <?php foreach($categories as $cat): ?>
        <option value="<?= $cat ?>" <?= $category==$cat?'selected':'' ?>><?= $cat ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-primary">Search</button>
    <a href="/user/events.php" class="btn" style="border:1px solid rgba(255,255,255,0.2);color:#ccc">Reset</a>
  </form>
  <?php if(empty($events)): ?>
    <div class="empty"><p style="font-size:3rem">😔</p><p>Koi event nahi mila</p></div>
  <?php else: ?>
  <div class="events-grid">
    <?php
    $icons=['Tech'=>'💻','Music'=>'🎵','Business'=>'💼','Sports'=>'⚽','Art'=>'🎨','Food'=>'🍴'];
    foreach($events as $e):
      $icon=$icons[$e['category']]??'🎉';
    ?>
    <div class="event-card">
      <div class="event-banner" style="background:<?= $e['color'] ?>22"><?= $icon ?></div>
      <div class="event-info">
        <div class="event-cat" style="color:<?= $e['color'] ?>"><?= htmlspecialchars($e['category']) ?></div>
        <div class="event-title"><?= htmlspecialchars($e['title']) ?></div>
        <div class="event-meta">
          📅 <?= date('D, d M Y', strtotime($e['event_date'])) ?><br>
          🕐 <?= date('h:i A', strtotime($e['event_time'])) ?><br>
          📍 <?= htmlspecialchars($e['venue']) ?><br>
          📝 <?= htmlspecialchars(substr($e['description'],0,80)) ?>...
        </div>
        <div class="event-footer">
          <span class="price" style="color:<?= $e['color'] ?>">₹<?= number_format($e['price'],2) ?></span>
          <span class="tickets">🎟️ <?= $e['available_tickets'] ?> available</span>
        </div>
        <?php if($e['available_tickets'] > 0): ?>
          <a href="/user/book.php?id=<?= $e['id'] ?>" class="btn btn-primary" style="display:block;text-align:center">Book Now</a>
        <?php else: ?>
          <button class="btn" style="width:100%;background:rgba(255,255,255,0.05);color:#666;cursor:not-allowed">Sold Out</button>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</main>
</body></html>
