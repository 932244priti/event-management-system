<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
requireAdmin();

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM events WHERE id=?")->execute([$_GET['delete']]);
    header("Location: /admin/manage-events.php"); exit();
}

$events = $pdo->query("SELECT e.*, (SELECT COUNT(*) FROM bookings b WHERE b.event_id=e.id AND b.status='confirmed') as bookings_count FROM events e ORDER BY event_date DESC")->fetchAll();
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Manage Events</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}body{font-family:'Segoe UI',sans-serif;background:#0a0a14;color:#fff;display:flex}
.sidebar{width:220px;background:rgba(255,255,255,0.03);border-right:1px solid rgba(255,255,255,0.08);padding:1.5rem;min-height:100vh;flex-shrink:0}
.logo{font-size:1.2rem;font-weight:700;background:linear-gradient(135deg,#f59e0b,#ef4444);-webkit-background-clip:text;-webkit-text-fill-color:transparent;margin-bottom:2rem;display:block}
.nav-item{display:block;padding:0.7rem 1rem;border-radius:8px;color:#888;text-decoration:none;margin-bottom:0.3rem;font-size:0.9rem;transition:all 0.2s}
.nav-item:hover,.nav-item.active{background:rgba(255,255,255,0.08);color:#fff}
.main{flex:1;padding:2rem}
h1{font-size:1.8rem;font-weight:700;margin-bottom:1.5rem}
.table-card{background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:12px;overflow:hidden}
table{width:100%;border-collapse:collapse;font-size:0.85rem}
th{background:rgba(255,255,255,0.05);text-align:left;color:#888;font-weight:500;padding:0.8rem 1rem}
td{padding:0.8rem 1rem;border-bottom:1px solid rgba(255,255,255,0.05)}
tr:hover td{background:rgba(255,255,255,0.02)}
.event-dot{width:12px;height:12px;border-radius:50%;display:inline-block;margin-right:0.5rem}
.btn-sm{padding:0.3rem 0.8rem;border-radius:6px;font-size:0.8rem;font-weight:600;cursor:pointer;text-decoration:none;border:none;display:inline-block}
.btn-edit{background:rgba(99,102,241,0.2);color:#a5b4fc}
.btn-del{background:rgba(239,68,68,0.2);color:#f87171}
.btn-add{padding:0.6rem 1.2rem;background:linear-gradient(135deg,#f59e0b,#ef4444);border:none;border-radius:8px;color:#fff;font-size:0.9rem;font-weight:600;cursor:pointer;text-decoration:none}
.progress{background:rgba(255,255,255,0.1);border-radius:10px;height:6px;margin-top:4px}
.progress-fill{height:100%;border-radius:10px;background:linear-gradient(90deg,#10b981,#6366f1)}
</style></head><body>
<div class="sidebar">
  <span class="logo">⚙️ EventHub Admin</span>
  <a href="/admin/dashboard.php" class="nav-item">📊 Dashboard</a>
  <a href="/admin/create-event.php" class="nav-item">➕ Create Event</a>
  <a href="/admin/manage-events.php" class="nav-item active">🎉 Events</a>
  <a href="/admin/bookings.php" class="nav-item">🎟️ Bookings</a>
  <a href="/admin/reports.php" class="nav-item">📈 Reports</a>
  <a href="/admin/logout.php" class="nav-item" style="margin-top:2rem;color:#ef4444">🚪 Logout</a>
</div>
<div class="main">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem">
    <h1>🎉 All Events</h1>
    <a href="/admin/create-event.php" class="btn-add">+ Create New Event</a>
  </div>
  <div class="table-card">
    <table>
      <tr><th>Event</th><th>Date</th><th>Venue</th><th>Price</th><th>Tickets Sold</th><th>Actions</th></tr>
      <?php foreach($events as $e):
        $sold = $e['total_tickets'] - $e['available_tickets'];
        $pct = $e['total_tickets']>0 ? round($sold/$e['total_tickets']*100) : 0;
      ?>
      <tr>
        <td><span class="event-dot" style="background:<?= $e['color'] ?>"></span><?= htmlspecialchars($e['title']) ?></td>
        <td><?= date('d M Y', strtotime($e['event_date'])) ?></td>
        <td style="color:#888"><?= htmlspecialchars(substr($e['venue'],0,25)) ?></td>
        <td>₹<?= number_format($e['price'],0) ?></td>
        <td>
          <?= $sold ?>/<?= $e['total_tickets'] ?> (<?= $pct ?>%)
          <div class="progress"><div class="progress-fill" style="width:<?= $pct ?>%"></div></div>
        </td>
        <td>
          <a href="/admin/create-event.php?edit=<?= $e['id'] ?>" class="btn-sm btn-edit">Edit</a> &nbsp;
          <a href="?delete=<?= $e['id'] ?>" class="btn-sm btn-del" onclick="return confirm('Event delete karo?')">Delete</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>
  </div>
</div>
</body></html>
