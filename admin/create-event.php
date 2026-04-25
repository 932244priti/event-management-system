<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
requireAdmin();

$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $desc = trim($_POST['description']);
    $cat = $_POST['category'];
    $venue = trim($_POST['venue']);
    $date = $_POST['event_date'];
    $time = $_POST['event_time'];
    $tickets = (int)$_POST['total_tickets'];
    $price = (float)$_POST['price'];
    $color = $_POST['color'];

    if (!$title || !$venue || !$date) { $error = "Saare required fields fill karo."; }
    else {
        $pdo->prepare("INSERT INTO events (title,description,category,venue,event_date,event_time,total_tickets,available_tickets,price,color) VALUES (?,?,?,?,?,?,?,?,?,?)")
            ->execute([$title,$desc,$cat,$venue,$date,$time,$tickets,$tickets,$price,$color]);
        $success = "Event successfully create ho gaya! 🎉";
    }
}
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Create Event — Admin</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}body{font-family:'Segoe UI',sans-serif;background:#0a0a14;color:#fff;display:flex}
.sidebar{width:220px;background:rgba(255,255,255,0.03);border-right:1px solid rgba(255,255,255,0.08);padding:1.5rem;min-height:100vh;flex-shrink:0}
.logo{font-size:1.2rem;font-weight:700;background:linear-gradient(135deg,#f59e0b,#ef4444);-webkit-background-clip:text;-webkit-text-fill-color:transparent;margin-bottom:2rem;display:block}
.nav-item{display:block;padding:0.7rem 1rem;border-radius:8px;color:#888;text-decoration:none;margin-bottom:0.3rem;font-size:0.9rem;transition:all 0.2s}
.nav-item:hover,.nav-item.active{background:rgba(255,255,255,0.08);color:#fff}
.main{flex:1;padding:2rem;max-width:800px}
h1{font-size:1.8rem;font-weight:700;margin-bottom:1.5rem}
.form-card{background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:12px;padding:2rem}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
.form-group{margin-bottom:1rem}
label{display:block;font-size:0.85rem;color:#ccc;margin-bottom:0.4rem}
input,select,textarea{width:100%;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.15);border-radius:8px;padding:0.7rem 1rem;color:#fff;font-size:0.9rem}
input:focus,select:focus,textarea:focus{outline:none;border-color:#f59e0b}
select option{background:#1a1a2e}textarea{height:100px;resize:vertical}
.full-width{grid-column:1/-1}
.btn{padding:0.8rem 2rem;background:linear-gradient(135deg,#f59e0b,#ef4444);border:none;border-radius:8px;color:#fff;font-size:1rem;font-weight:600;cursor:pointer}
.msg{padding:0.8rem;border-radius:8px;margin-bottom:1rem;font-size:0.9rem}
.success{background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.3);color:#34d399}
.error{background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#f87171}
.color-preview{width:40px;height:40px;border-radius:8px;display:inline-block;vertical-align:middle;margin-left:0.5rem}
</style></head><body>
<div class="sidebar">
  <span class="logo">⚙️ EventHub Admin</span>
  <a href="/admin/dashboard.php" class="nav-item">📊 Dashboard</a>
  <a href="/admin/create-event.php" class="nav-item active">➕ Create Event</a>
  <a href="/admin/manage-events.php" class="nav-item">🎉 Events</a>
  <a href="/admin/bookings.php" class="nav-item">🎟️ Bookings</a>
  <a href="/admin/reports.php" class="nav-item">📈 Reports</a>
  <a href="/admin/logout.php" class="nav-item" style="margin-top:2rem;color:#ef4444">🚪 Logout</a>
</div>
<div class="main">
  <h1>➕ Create New Event</h1>
  <?php if($success): ?><div class="msg success"><?= $success ?></div><?php endif; ?>
  <?php if($error): ?><div class="msg error"><?= $error ?></div><?php endif; ?>
  <div class="form-card">
    <form method="POST">
      <div class="form-grid">
        <div class="form-group full-width"><label>Event Title *</label><input type="text" name="title" required placeholder="Eg: Tech Summit 2025"></div>
        <div class="form-group full-width"><label>Description</label><textarea name="description" placeholder="Event ke baare mein batao..."></textarea></div>
        <div class="form-group"><label>Category *</label>
          <select name="category" required>
            <option value="Tech">💻 Tech</option><option value="Music">🎵 Music</option>
            <option value="Business">💼 Business</option><option value="Sports">⚽ Sports</option>
            <option value="Art">🎨 Art</option><option value="Food">🍴 Food</option>
          </select>
        </div>
        <div class="form-group"><label>Venue *</label><input type="text" name="venue" required placeholder="Event location"></div>
        <div class="form-group"><label>Event Date *</label><input type="date" name="event_date" required></div>
        <div class="form-group"><label>Event Time</label><input type="time" name="event_time" value="10:00"></div>
        <div class="form-group"><label>Total Tickets</label><input type="number" name="total_tickets" value="100" min="1"></div>
        <div class="form-group"><label>Price per Ticket (₹)</label><input type="number" name="price" value="0" min="0" step="0.01"></div>
        <div class="form-group full-width">
          <label>Card Color &nbsp;<span id="colorPreview" class="color-preview" style="background:#6366f1"></span></label>
          <input type="color" name="color" value="#6366f1" style="width:80px;height:40px;cursor:pointer" oninput="document.getElementById('colorPreview').style.background=this.value">
        </div>
        <div class="form-group full-width">
          <button type="submit" class="btn">🚀 Create Event</button>
        </div>
      </div>
    </form>
  </div>
</div>
</body></html>
