<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
requireAdmin();

$total_events = $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_bookings = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status='confirmed'")->fetchColumn();
$total_revenue = $pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM bookings WHERE status='confirmed'")->fetchColumn();
$recent_bookings = $pdo->query("SELECT b.*, u.name as user_name, e.title as event_title FROM bookings b JOIN users u ON b.user_id=u.id JOIN events e ON b.event_id=e.id ORDER BY b.booking_date DESC LIMIT 8")->fetchAll();
$monthly = $pdo->query("SELECT DATE_FORMAT(booking_date,'%b') as month, COUNT(*) as cnt, COALESCE(SUM(total_amount),0) as rev FROM bookings WHERE booking_date >= DATE_SUB(NOW(),INTERVAL 6 MONTH) GROUP BY DATE_FORMAT(booking_date,'%Y-%m'), DATE_FORMAT(booking_date,'%b') ORDER BY MIN(booking_date)")->fetchAll();
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Admin Dashboard</title>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Segoe UI',sans-serif;background:#0a0a14;color:#fff;display:flex}
.sidebar{width:220px;background:rgba(255,255,255,0.03);border-right:1px solid rgba(255,255,255,0.08);padding:1.5rem;min-height:100vh;flex-shrink:0}
.logo{font-size:1.1rem;font-weight:700;color:#f59e0b;margin-bottom:2rem;display:block}
.nav-item{display:block;padding:0.7rem 1rem;border-radius:8px;color:#888;text-decoration:none;margin-bottom:0.3rem;font-size:0.9rem}
.nav-item:hover,.nav-item.active{background:rgba(255,255,255,0.08);color:#fff}
.main{flex:1;padding:2rem;overflow-x:hidden}
h1{font-size:1.8rem;font-weight:700;margin-bottom:1.5rem}
.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:2rem}
.stat-card{background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:12px;padding:1.2rem}
.stat-label{font-size:0.8rem;color:#888;text-transform:uppercase;letter-spacing:1px;margin-bottom:0.5rem}
.stat-value{font-size:2rem;font-weight:700}
.charts-grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:2rem}
.chart-card{background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:12px;padding:1.2rem}
.chart-title{font-size:1rem;font-weight:600;margin-bottom:1rem}
.table-card{background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:12px;padding:1.5rem}
table{width:100%;border-collapse:collapse;font-size:0.85rem}
th{text-align:left;color:#888;font-weight:500;padding:0.5rem 0.8rem;border-bottom:1px solid rgba(255,255,255,0.08)}
td{padding:0.7rem 0.8rem;border-bottom:1px solid rgba(255,255,255,0.05)}
.badge{padding:0.2rem 0.6rem;border-radius:20px;font-size:0.75rem;font-weight:600;background:rgba(16,185,129,0.15);color:#34d399}
.btn-sm{padding:0.4rem 1rem;background:linear-gradient(135deg,#6366f1,#a855f7);border:none;border-radius:6px;color:#fff;font-size:0.8rem;font-weight:600;cursor:pointer;text-decoration:none}
</style></head><body>
<div class="sidebar">
  <div class="logo">⚙️ EventHub Admin</div>
  <a href="/admin/dashboard.php" class="nav-item active">📊 Dashboard</a>
  <a href="/admin/create-event.php" class="nav-item">➕ Create Event</a>
  <a href="/admin/manage-events.php" class="nav-item">🎉 Events</a>
  <a href="/admin/bookings.php" class="nav-item">🎟️ Bookings</a>
  <a href="/admin/reports.php" class="nav-item">📈 Reports</a>
  <a href="/admin/logout.php" class="nav-item" style="margin-top:2rem;color:#ef4444">🚪 Logout</a>
</div>
<div class="main">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem">
    <h1>📊 Dashboard</h1>
    <span style="color:#888;font-size:0.9rem">👋 Welcome, <?= htmlspecialchars($_SESSION['admin_name']) ?></span>
  </div>
  <div class="stats-grid">
    <div class="stat-card"><div class="stat-label">Total Events</div><div class="stat-value" style="color:#6366f1"><?= $total_events ?></div></div>
    <div class="stat-card"><div class="stat-label">Registered Users</div><div class="stat-value" style="color:#a855f7"><?= $total_users ?></div></div>
    <div class="stat-card"><div class="stat-label">Total Bookings</div><div class="stat-value" style="color:#10b981"><?= $total_bookings ?></div></div>
    <div class="stat-card"><div class="stat-label">Total Revenue</div><div class="stat-value" style="color:#f59e0b">₹<?= number_format($total_revenue) ?></div></div>
  </div>
  <div class="charts-grid">
    <div class="chart-card"><div class="chart-title">📊 Monthly Bookings</div><canvas id="bookingChart" height="200"></canvas></div>
    <div class="chart-card"><div class="chart-title">💰 Monthly Revenue</div><canvas id="revenueChart" height="200"></canvas></div>
  </div>
  <div class="table-card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
      <div class="chart-title">🎟️ Recent Bookings</div>
      <a href="/admin/bookings.php" class="btn-sm">View All</a>
    </div>
    <?php if(empty($recent_bookings)): ?>
      <p style="color:#888;text-align:center;padding:2rem">Abhi koi booking nahi hai</p>
    <?php else: ?>
    <table>
      <tr><th>User</th><th>Event</th><th>Tickets</th><th>Amount</th><th>Date</th><th>Status</th></tr>
      <?php foreach($recent_bookings as $b): ?>
      <tr>
        <td><?= htmlspecialchars($b['user_name']) ?></td>
        <td><?= htmlspecialchars(substr($b['event_title'],0,25)) ?>...</td>
        <td style="text-align:center"><?= $b['tickets_booked'] ?></td>
        <td style="color:#f59e0b">₹<?= number_format($b['total_amount'],2) ?></td>
        <td style="color:#888"><?= date('d M Y', strtotime($b['booking_date'])) ?></td>
        <td><span class="badge">Confirmed</span></td>
      </tr>
      <?php endforeach; ?>
    </table>
    <?php endif; ?>
  </div>
</div>
<script>
var months=<?= json_encode(array_column($monthly,'month')) ?>;
var counts=<?= json_encode(array_column($monthly,'cnt')) ?>;
var revs=<?= json_encode(array_column($monthly,'rev')) ?>;
var opts={responsive:true,plugins:{legend:{display:false}},scales:{x:{grid:{color:'rgba(255,255,255,0.05)'},ticks:{color:'#888'}},y:{grid:{color:'rgba(255,255,255,0.05)'},ticks:{color:'#888'}}}};
new Chart(document.getElementById('bookingChart'),{type:'bar',data:{labels:months.length?months:['No Data'],datasets:[{data:counts.length?counts:[0],backgroundColor:'rgba(99,102,241,0.6)',borderColor:'#6366f1',borderWidth:1,borderRadius:6}]},options:opts});
new Chart(document.getElementById('revenueChart'),{type:'line',data:{labels:months.length?months:['No Data'],datasets:[{data:revs.length?revs:[0],borderColor:'#f59e0b',backgroundColor:'rgba(245,158,11,0.1)',fill:true,tension:0.4,pointBackgroundColor:'#f59e0b'}]},options:opts});
</script>
</body></html>
