<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
requireUser();

$event_id = (int)($_GET['id'] ?? 0);
$event = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$event->execute([$event_id]);
$event = $event->fetch();
if (!$event) { header("Location: /user/events.php"); exit(); }

$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $qty = (int)$_POST['quantity'];
    if ($qty < 1 || $qty > 10) { $error = "1 se 10 tickets book kar sakte ho."; }
    elseif ($qty > $event['available_tickets']) { $error = "Itne tickets available nahi hain."; }
    else {
        $total = $qty * $event['price'];
        $pdo->beginTransaction();
        try {
            $pdo->prepare("INSERT INTO bookings (user_id,event_id,tickets_booked,total_amount) VALUES (?,?,?,?)")
                ->execute([$_SESSION['user_id'], $event_id, $qty, $total]);
            $pdo->prepare("UPDATE events SET available_tickets = available_tickets - ? WHERE id = ?")
                ->execute([$qty, $event_id]);
            $pdo->commit();
            $success = "Tickets successfully booked! 🎉";
            $event['available_tickets'] -= $qty;
        } catch(Exception $ex) { $pdo->rollBack(); $error = "Booking failed. Try again."; }
    }
}
$icons=['Tech'=>'💻','Music'=>'🎵','Business'=>'💼','Sports'=>'⚽','Art'=>'🎨','Food'=>'🍴'];
$icon=$icons[$event['category']]??'🎉';
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Book Ticket — EventHub</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}body{font-family:'Segoe UI',sans-serif;background:#0f0f1a;color:#fff;min-height:100vh}
nav{background:rgba(255,255,255,0.05);padding:1rem 2rem;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid rgba(255,255,255,0.1)}
.logo{font-size:1.5rem;font-weight:700;background:linear-gradient(135deg,#6366f1,#a855f7);-webkit-background-clip:text;-webkit-text-fill-color:transparent;text-decoration:none}
.nav-links a{color:#ccc;text-decoration:none;margin-left:1.5rem}
main{max-width:800px;margin:2rem auto;padding:0 1rem;display:grid;grid-template-columns:1fr 1fr;gap:2rem}
.event-preview{background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:16px;overflow:hidden}
.banner{height:180px;display:flex;align-items:center;justify-content:center;font-size:5rem;background:<?= $event['color'] ?>22}
.info{padding:1.5rem}
.title{font-size:1.3rem;font-weight:700;margin-bottom:1rem}
.meta{font-size:0.9rem;color:#888;line-height:2}
.book-card{background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:16px;padding:1.5rem}
h2{font-size:1.3rem;font-weight:700;margin-bottom:1.5rem}
label{display:block;font-size:0.85rem;color:#ccc;margin-bottom:0.3rem;margin-top:1rem}
input,select{width:100%;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.15);border-radius:8px;padding:0.7rem;color:#fff;font-size:0.95rem}
.total-box{background:rgba(99,102,241,0.1);border:1px solid rgba(99,102,241,0.3);border-radius:8px;padding:1rem;margin:1rem 0;text-align:center}
.total-label{font-size:0.85rem;color:#888}
.total-amount{font-size:2rem;font-weight:700;color:#a855f7}
.btn{width:100%;padding:0.9rem;background:linear-gradient(135deg,#6366f1,#a855f7);border:none;border-radius:8px;color:#fff;font-size:1rem;font-weight:600;cursor:pointer;margin-top:0.5rem}
.msg{padding:0.8rem;border-radius:8px;margin-bottom:1rem;font-size:0.9rem}
.success{background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.3);color:#34d399}
.error{background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#f87171}
@media(max-width:600px){main{grid-template-columns:1fr}}
</style></head><body>
<nav>
  <a href="/" class="logo">🎟️ EventHub</a>
  <div class="nav-links">
    <a href="/user/events.php">← Events</a>
    <a href="/user/my-tickets.php">My Tickets</a>
    <a href="/user/logout.php">Logout</a>
  </div>
</nav>
<main>
  <div class="event-preview">
    <div class="banner"><?= $icon ?></div>
    <div class="info">
      <div class="title"><?= htmlspecialchars($event['title']) ?></div>
      <div class="meta">
        📅 <?= date('D, d M Y', strtotime($event['event_date'])) ?><br>
        🕐 <?= date('h:i A', strtotime($event['event_time'])) ?><br>
        📍 <?= htmlspecialchars($event['venue']) ?><br>
        🎟️ <?= $event['available_tickets'] ?> tickets available<br>
        💰 ₹<?= number_format($event['price'],2) ?> per ticket
      </div>
    </div>
  </div>
  <div class="book-card">
    <h2>Book Tickets</h2>
    <?php if($success): ?><div class="msg success"><?= $success ?> <a href="/user/my-tickets.php" style="color:#34d399">My Tickets dekho →</a></div><?php endif; ?>
    <?php if($error): ?><div class="msg error"><?= $error ?></div><?php endif; ?>
    <form method="POST" id="bookForm">
      <label>Kitne Tickets Chahiye?</label>
      <input type="number" name="quantity" min="1" max="<?= min(10,$event['available_tickets']) ?>" value="1" id="qty" onchange="updateTotal()">
      <div class="total-box">
        <div class="total-label">Total Amount</div>
        <div class="total-amount" id="totalAmt">₹<?= number_format($event['price'],2) ?></div>
      </div>
      <button type="submit" class="btn">Confirm Booking 🎟️</button>
    </form>
    <a href="/user/events.php" style="display:block;text-align:center;margin-top:1rem;color:#888;text-decoration:none;font-size:0.9rem">← Back to Events</a>
  </div>
</main>
<script>
var price = <?= $event['price'] ?>;
function updateTotal(){
  var qty=parseInt(document.getElementById('qty').value)||1;
  document.getElementById('totalAmt').textContent='₹'+(qty*price).toLocaleString('en-IN',{minimumFractionDigits:2});
}
</script>
</body></html>
