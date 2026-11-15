<?php
require_once 'includes/auth.php';
require_once 'includes/score.php';
require_login();

$id = (int)($_GET['id'] ?? 0);
$st = db()->prepare('SELECT * FROM audits WHERE id = ? AND user_id = ?');
$st->execute([$id, $_SESSION['user_id']]);
$audit = $st->fetch();
if (!$audit) { header('Location: dashboard.php'); exit; }

$r = calculate_scores(json_decode($audit['answers'], true) ?: []);
$cls = rating_class($audit['rating']);
$groups = get_questions();

$title = 'Report';
include 'includes/header.php';
?>
<main class="container">
  <div class="report-head">
    <div>
      <h2><?= e($audit['company_name']) ?> &mdash; ESG Report</h2>
      <p class="muted">Period: <?= e($audit['period']) ?> &middot; Generated: <?= e($audit['created_at']) ?></p>
    </div>
    <div class="no-print">
      <button class="btn" onclick="window.print()">Download PDF</button>
      <a class="btn btn-outline-dark" href="dashboard.php">Back</a>
    </div>
  </div>

  <div class="card overall">
    <div class="big-score <?= $cls ?>"><?= (int)$audit['total'] ?><small>/100</small></div>
    <div><span class="badge <?= $cls ?>"><?= e($audit['rating']) ?></span>
      <p class="muted">Overall = 40% Environmental + 30% Social + 30% Governance</p></div>
  </div>

  <div class="card">
    <?php foreach (['E','S','G'] as $c): ?>
      <div class="bar-row">
        <span><?= $groups[$c]['icon'] ?> <?= e($groups[$c]['title']) ?></span>
        <div class="bar"><div class="fill" style="width: <?= (int)$r[$c] ?>%"></div></div>
        <strong><?= (int)$r[$c] ?>%</strong>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="card">
    <h3>Recommendations</h3>
    <?php if (!$r['recs']): ?>
      <p>Excellent! Every area is fully compliant. Keep it up.</p>
    <?php else: ?>
      <ul class="recs">
        <?php foreach ($r['recs'] as $rec): ?>
          <li>
            <span class="tag"><?= e($groups[$rec['cat']]['title']) ?></span>
            <span class="tag <?= $rec['level'] === 'no' ? 'bad' : 'mid' ?>"><?= $rec['level'] === 'no' ? 'Not met' : 'Partly met' ?></span>
            <strong><?= e($rec['q']) ?></strong><br>
            <span class="muted">&rarr; <?= e($rec['tip']) ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</main>
<?php include 'includes/footer.php'; ?>
