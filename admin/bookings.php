<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
requireAdmin();

$bookings = $pdo->query("SELECT b.*, u.name as user_name, u.email as user_email, e.title as event_title, e.event_date FROM bookings b JOIN users u ON b.user_id=u.id JOIN events e ON b.event_id=e.id ORDER BY b.booking_date DESC")->fetchAll();
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Bookings — Admin</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}body{font-family:'Segoe UI',sans-serif;background:#0a0a14;color:#fff;display:flex}
.sidebar{width:220px;background:rgba(255,255,255,0.03);border-right:1px solid rgba(255,255,255,0.08);padding:1.5rem;min-height:100vh;flex-shrink:0}
.logo{font-size:1.2rem;font-weight:700;background:linear-gradient(135deg,#f59e0b,#ef4444);-webkit-background-clip:text;-webkit-text-fill-color:transparent;margin-bottom:2rem;display:block}
.nav-item{display:block;padding:0.7rem 1rem;border-radius:8px;color:#888;text-decoration:none;margin-bottom:0.3rem;font-size:0.9rem}
.nav-item:hover,.nav-item.active{background:rgba(255,255,255,0.08);color:#fff}
.main{flex:1;padding:2rem}
h1{font-size:1.8rem;font-weight:700;margin-bottom:1.5rem}
.table-card{background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:12px;overflow:hidden}
table{width:100%;border-collapse:collapse;font-size:0.83rem}
th{background:rgba(255,255,255,0.05);text-align:left;color:#888;font-weight:500;padding:0.8rem 1rem}
td{padding:0.7rem 1rem;border-bottom:1px solid rgba(255,255,255,0.05)}
tr:hover td{background:rgba(255,255,255,0.02)}
.badge{padding:0.2rem 0.6rem;border-radius:20px;font-size:0.75rem;font-weight:600}
.confirmed{background:rgba(16,185,129,0.15);color:#34d399}
</style></head><body>
<div class="sidebar">
  <span class="logo">⚙️ EventHub Admin</span>
  <a href="/admin/dashboard.php" class="nav-item">📊 Dashboard</a>
  <a href="/admin/create-event.php" class="nav-item">➕ Create Event</a>
  <a href="/admin/manage-events.php" class="nav-item">🎉 Events</a>
  <a href="/admin/bookings.php" class="nav-item active">🎟️ Bookings</a>
  <a href="/admin/reports.php" class="nav-item">📈 Reports</a>
  <a href="/admin/logout.php" class="nav-item" style="margin-top:2rem;color:#ef4444">🚪 Logout</a>
</div>
<div class="main">
  <h1>🎟️ All Bookings (<?= count($bookings) ?>)</h1>
  <div class="table-card">
    <table>
      <tr><th>#</th><th>User</th><th>Email</th><th>Event</th><th>Event Date</th><th>Tickets</th><th>Amount</th><th>Booked On</th><th>Status</th></tr>
      <?php foreach($bookings as $i=>$b): ?>
      <tr>
        <td style="color:#888"><?= $i+1 ?></td>
        <td><?= htmlspecialchars($b['user_name']) ?></td>
        <td style="color:#888"><?= htmlspecialchars($b['user_email']) ?></td>
        <td><?= htmlspecialchars(substr($b['event_title'],0,25)) ?>...</td>
        <td><?= date('d M Y', strtotime($b['event_date'])) ?></td>
        <td style="text-align:center"><?= $b['tickets_booked'] ?></td>
        <td style="color:#f59e0b">₹<?= number_format($b['total_amount'],2) ?></td>
        <td style="color:#888"><?= date('d M Y H:i', strtotime($b['booking_date'])) ?></td>
        <td><span class="badge confirmed"><?= ucfirst($b['status']) ?></span></td>
      </tr>
      <?php endforeach; ?>
    </table>
  </div>
</div>
</body></html>
