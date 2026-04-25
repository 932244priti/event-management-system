<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
requireUser();

$bookings = $pdo->prepare("SELECT b.*, e.title, e.venue, e.event_date, e.event_time, e.color, e.category FROM bookings b JOIN events e ON b.event_id=e.id WHERE b.user_id=? ORDER BY b.booking_date DESC");
$bookings->execute([$_SESSION['user_id']]);
$bookings = $bookings->fetchAll();
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>My Tickets — EventHub</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}body{font-family:'Segoe UI',sans-serif;background:#0f0f1a;color:#fff}
nav{background:rgba(255,255,255,0.05);padding:1rem 2rem;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid rgba(255,255,255,0.1)}
.logo{font-size:1.5rem;font-weight:700;background:linear-gradient(135deg,#6366f1,#a855f7);-webkit-background-clip:text;-webkit-text-fill-color:transparent;text-decoration:none}
.nav-links a{color:#ccc;text-decoration:none;margin-left:1.5rem}
main{max-width:900px;margin:2rem auto;padding:0 1rem}
h1{font-size:2rem;font-weight:700;margin-bottom:1.5rem}
.ticket{background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:16px;overflow:hidden;margin-bottom:1rem;display:flex}
.ticket-left{width:120px;display:flex;align-items:center;justify-content:center;font-size:3rem;flex-shrink:0}
.ticket-info{padding:1.2rem;flex:1;border-left:1px dashed rgba(255,255,255,0.15)}
.ticket-title{font-size:1.1rem;font-weight:700;margin-bottom:0.5rem}
.ticket-meta{font-size:0.85rem;color:#888;line-height:1.8}
.ticket-right{padding:1rem;text-align:right;display:flex;flex-direction:column;justify-content:space-between;border-left:1px dashed rgba(255,255,255,0.15);min-width:130px}
.ticket-count{font-size:2rem;font-weight:700}
.ticket-label{font-size:0.75rem;color:#888}
.ticket-amount{font-size:1rem;font-weight:700}
.status{display:inline-block;padding:0.3rem 0.8rem;border-radius:20px;font-size:0.75rem;font-weight:600;margin-top:0.5rem}
.confirmed{background:rgba(16,185,129,0.15);color:#34d399;border:1px solid rgba(16,185,129,0.3)}
.cancelled{background:rgba(239,68,68,0.15);color:#f87171;border:1px solid rgba(239,68,68,0.3)}
.empty{text-align:center;padding:4rem;color:#555}
.btn{padding:0.6rem 1.2rem;background:linear-gradient(135deg,#6366f1,#a855f7);border:none;border-radius:8px;color:#fff;text-decoration:none;font-weight:600;font-size:0.9rem}
</style></head><body>
<nav>
  <a href="/" class="logo">🎟️ EventHub</a>
  <div class="nav-links">
    <a href="/user/events.php">Events</a>
    <a href="/user/logout.php">Logout (<?= htmlspecialchars($_SESSION['user_name']) ?>)</a>
  </div>
</nav>
<main>
  <h1>🎟️ My Tickets</h1>
  <?php if(empty($bookings)): ?>
    <div class="empty">
      <p style="font-size:4rem;margin-bottom:1rem">🎭</p>
      <p style="font-size:1.2rem;margin-bottom:1rem">Koi ticket nahi mila</p>
      <a href="/user/events.php" class="btn">Events Dekho</a>
    </div>
  <?php else: ?>
    <?php
    $icons=['Tech'=>'💻','Music'=>'🎵','Business'=>'💼','Sports'=>'⚽','Art'=>'🎨','Food'=>'🍴'];
    foreach($bookings as $b):
      $icon=$icons[$b['category']]??'🎉';
    ?>
    <div class="ticket">
      <div class="ticket-left" style="background:<?= $b['color'] ?>22"><?= $icon ?></div>
      <div class="ticket-info">
        <div class="ticket-title"><?= htmlspecialchars($b['title']) ?></div>
        <div class="ticket-meta">
          📅 <?= date('D, d M Y', strtotime($b['event_date'])) ?><br>
          🕐 <?= date('h:i A', strtotime($b['event_time'])) ?><br>
          📍 <?= htmlspecialchars($b['venue']) ?><br>
          🗓️ Booked on: <?= date('d M Y', strtotime($b['booking_date'])) ?>
        </div>
        <span class="status <?= $b['status'] ?>"><?= ucfirst($b['status']) ?></span>
      </div>
      <div class="ticket-right">
        <div>
          <div class="ticket-count"><?= $b['tickets_booked'] ?></div>
          <div class="ticket-label">Ticket<?= $b['tickets_booked']>1?'s':'' ?></div>
        </div>
        <div>
          <div class="ticket-amount" style="color:<?= $b['color'] ?>">₹<?= number_format($b['total_amount'],2) ?></div>
          <div class="ticket-label">Total Paid</div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  <?php endif; ?>
</main>
</body></html>
