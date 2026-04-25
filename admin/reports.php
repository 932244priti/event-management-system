<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
requireAdmin();

$revenue_by_event = $pdo->query("SELECT e.title, e.color, COUNT(b.id) as bookings, SUM(b.total_amount) as revenue FROM events e LEFT JOIN bookings b ON e.id=b.event_id AND b.status='confirmed' GROUP BY e.id ORDER BY revenue DESC LIMIT 10")->fetchAll();
$top_users = $pdo->query("SELECT u.name, u.email, COUNT(b.id) as bookings, SUM(b.total_amount) as spent FROM users u LEFT JOIN bookings b ON u.id=b.user_id AND b.status='confirmed' GROUP BY u.id ORDER BY bookings DESC LIMIT 5")->fetchAll();
$monthly = $pdo->query("SELECT DATE_FORMAT(booking_date,'%Y-%m') as ym, DATE_FORMAT(booking_date,'%b %Y') as label, COUNT(*) as cnt, SUM(total_amount) as rev FROM bookings WHERE status='confirmed' GROUP BY ym ORDER BY ym DESC LIMIT 6")->fetchAll();
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Reports — Admin</title>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
<style>
*{margin:0;padding:0;box-sizing:border-box}body{font-family:'Segoe UI',sans-serif;background:#0a0a14;color:#fff;display:flex}
.sidebar{width:220px;background:rgba(255,255,255,0.03);border-right:1px solid rgba(255,255,255,0.08);padding:1.5rem;min-height:100vh;flex-shrink:0}
.logo{font-size:1.2rem;font-weight:700;background:linear-gradient(135deg,#f59e0b,#ef4444);-webkit-background-clip:text;-webkit-text-fill-color:transparent;margin-bottom:2rem;display:block}
.nav-item{display:block;padding:0.7rem 1rem;border-radius:8px;color:#888;text-decoration:none;margin-bottom:0.3rem;font-size:0.9rem}
.nav-item:hover,.nav-item.active{background:rgba(255,255,255,0.08);color:#fff}
.main{flex:1;padding:2rem}
h1{font-size:1.8rem;font-weight:700;margin-bottom:1.5rem}
.grid2{display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem}
.card{background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:12px;padding:1.5rem}
.card-title{font-size:1rem;font-weight:600;margin-bottom:1rem}
table{width:100%;border-collapse:collapse;font-size:0.85rem}
th{text-align:left;color:#888;font-weight:500;padding:0.5rem 0}
td{padding:0.7rem 0;border-bottom:1px solid rgba(255,255,255,0.05)}
.bar-container{background:rgba(255,255,255,0.08);border-radius:6px;height:8px;margin-top:4px}
.bar-fill{height:100%;border-radius:6px}
</style></head><body>
<div class="sidebar">
  <span class="logo">⚙️ EventHub Admin</span>
  <a href="/admin/dashboard.php" class="nav-item">📊 Dashboard</a>
  <a href="/admin/create-event.php" class="nav-item">➕ Create Event</a>
  <a href="/admin/manage-events.php" class="nav-item">🎉 Events</a>
  <a href="/admin/bookings.php" class="nav-item">🎟️ Bookings</a>
  <a href="/admin/reports.php" class="nav-item active">📈 Reports</a>
  <a href="/admin/logout.php" class="nav-item" style="margin-top:2rem;color:#ef4444">🚪 Logout</a>
</div>
<div class="main">
  <h1>📈 Analytics & Reports</h1>
  <div class="grid2">
    <div class="card">
      <div class="card-title">🏆 Top Events by Revenue</div>
      <?php $max = $revenue_by_event[0]['revenue'] ?? 1; foreach($revenue_by_event as $r): ?>
      <div style="margin-bottom:0.8rem">
        <div style="display:flex;justify-content:space-between;font-size:0.85rem">
          <span><?= htmlspecialchars(substr($r['title'],0,25)) ?></span>
          <span style="color:#f59e0b">₹<?= number_format($r['revenue']) ?></span>
        </div>
        <div class="bar-container"><div class="bar-fill" style="width:<?= $max>0?round($r['revenue']/$max*100):0 ?>%;background:<?= $r['color'] ?>"></div></div>
        <div style="font-size:0.75rem;color:#666;margin-top:2px"><?= $r['bookings'] ?> bookings</div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="card">
      <div class="card-title">👥 Top Users</div>
      <table>
        <tr><th>User</th><th>Bookings</th><th>Spent</th></tr>
        <?php foreach($top_users as $u): ?>
        <tr>
          <td><?= htmlspecialchars($u['name']) ?><br><span style="font-size:0.75rem;color:#666"><?= htmlspecialchars($u['email']) ?></span></td>
          <td style="text-align:center"><?= $u['bookings'] ?></td>
          <td style="color:#f59e0b">₹<?= number_format($u['spent']) ?></td>
        </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </div>
  <div class="card">
    <div class="card-title">📅 Monthly Summary</div>
    <table>
      <tr><th>Month</th><th>Bookings</th><th>Revenue</th></tr>
      <?php foreach($monthly as $m): ?>
      <tr>
        <td><?= $m['label'] ?></td>
        <td><?= $m['cnt'] ?></td>
        <td style="color:#10b981">₹<?= number_format($m['rev'],2) ?></td>
      </tr>
      <?php endforeach; ?>
    </table>
  </div>
</div>
</body></html>
