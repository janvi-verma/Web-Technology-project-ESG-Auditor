<?php
require_once 'includes/auth.php';
require_once 'includes/score.php';
require_login();

// Delete an audit (POST only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $del = db()->prepare('DELETE FROM audits WHERE id = ? AND user_id = ?');
    $del->execute([(int)$_POST['delete_id'], $_SESSION['user_id']]);
    header('Location: dashboard.php');
    exit;
}

$st = db()->prepare('SELECT * FROM audits WHERE user_id = ? ORDER BY id DESC');
$st->execute([$_SESSION['user_id']]);
$audits = $st->fetchAll();

$count = count($audits);
$latest = $count ? $audits[0] : null;
$avg = $count ? round(array_sum(array_column($audits, 'total')) / $count) : 0;

// Data for charts (oldest first so the trend reads left to right)
$chrono = array_reverse($audits);
$chartData = [
    'labels' => array_map(fn($a) => $a['period'], $chrono),
    'totals' => array_map(fn($a) => (int)$a['total'], $chrono),
    'latest' => $latest ? [(int)$latest['e_score'], (int)$latest['s_score'], (int)$latest['g_score']] : [0,0,0],
];

$title = 'Dashboard';
include 'includes/header.php';
?>
<main class="container">
  <div class="report-head">
    <h2>Dashboard</h2>
    <a class="btn" href="audit.php">+ New audit</a>
  </div>

  <div class="stats">
    <div class="card stat"><span>Total audits</span><strong><?= $count ?></strong></div>
    <div class="card stat"><span>Latest score</span><strong><?= $latest ? (int)$latest['total'] : '-' ?></strong></div>
    <div class="card stat"><span>Average score</span><strong><?= $count ? $avg : '-' ?></strong></div>
  </div>

  <?php if ($count): ?>
    <div class="grid2">
      <div class="card"><h3>Score trend</h3><canvas id="trendChart"></canvas></div>
      <div class="card"><h3>Latest E / S / G</h3><canvas id="catChart"></canvas></div>
    </div>

    <div class="card">
      <h3>Audit history</h3>
      <div class="table-wrap">
        <table>
          <tr><th>Company</th><th>Period</th><th>E</th><th>S</th><th>G</th><th>Total</th><th>Rating</th><th></th></tr>
          <?php foreach ($audits as $a): ?>
            <tr>
              <td><?= e($a['company_name']) ?></td>
              <td><?= e($a['period']) ?></td>
              <td><?= (int)$a['e_score'] ?></td>
              <td><?= (int)$a['s_score'] ?></td>
              <td><?= (int)$a['g_score'] ?></td>
              <td><strong><?= (int)$a['total'] ?></strong></td>
              <td><span class="badge <?= rating_class($a['rating']) ?>"><?= e($a['rating']) ?></span></td>
              <td class="actions">
                <a href="report.php?id=<?= (int)$a['id'] ?>">View</a>
                <form method="post" onsubmit="return confirm('Delete this audit?');">
                  <input type="hidden" name="delete_id" value="<?= (int)$a['id'] ?>">
                  <button class="link-btn" type="submit">Delete</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>
      </div>
    </div>

    <script id="chartData" type="application/json"><?= json_encode($chartData) ?></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="js/charts.js"></script>
  <?php else: ?>
    <div class="card empty"><p>No audits yet. Click <strong>New audit</strong> to create your first one.</p></div>
  <?php endif; ?>
</main>
<?php include 'includes/footer.php'; ?>
