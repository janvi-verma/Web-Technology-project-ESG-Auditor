
    <?php
require_once 'includes/auth.php';
require_once 'includes/score.php';
require_login();

$questions = get_questions();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company = trim($_POST['company_name'] ?? '');
    $period  = trim($_POST['period'] ?? '');
    $ans     = $_POST['ans'] ?? [];

    if ($company === '') $errors[] = 'Company name is required.';
    if ($period === '')  $errors[] = 'Audit period is required.';

    foreach ($questions as $group) {
        foreach ($group['items'] as $item) {
            if (!isset($ans[$item['id']])) {
                $errors[] = 'Please answer every question.';
                break 2;
            }
        }
    }

    if (!$errors) {
        $r = calculate_scores($ans);
        $st = db()->prepare('INSERT INTO audits
            (user_id, company_name, period, e_score, s_score, g_score, total, rating, answers)
            VALUES (?,?,?,?,?,?,?,?,?)');
        $st->execute([$_SESSION['user_id'], $company, $period,
            $r['E'], $r['S'], $r['G'], $r['total'], $r['rating'], json_encode($ans)]);
        header('Location: report.php?id=' . db()->lastInsertId());
        exit;
    }
}

$title = 'New Audit';
include 'includes/header.php';
?>
<main class="container">
  <h2>New ESG Audit</h2>
  <?php foreach ($errors as $err): ?><div class="alert error"><?= e($err) ?></div><?php endforeach; ?>

  <form method="post" id="auditForm">
    <div class="card grid2">
      <div><label>Company name</label>
        <input type="text" name="company_name" required value="<?= e($_POST['company_name'] ?? '') ?>"></div>
      <div><label>Audit period (e.g. FY 2025-26)</label>
        <input type="text" name="period" required value="<?= e($_POST['period'] ?? '') ?>"></div>
    </div>

    <?php foreach ($questions as $cat => $group): ?>
      <div class="card">
        <h3><?= $group['icon'] ?> <?= e($group['title']) ?></h3>
        <?php foreach ($group['items'] as $item): ?>
          <div class="q" data-cat="<?= $cat ?>" data-w="<?= $item['w'] ?>">
            <p><?= e($item['q']) ?></p>
            <div class="choices">
              <?php foreach (['yes' => 'Yes', 'partial' => 'Partly', 'no' => 'No'] as $val => $label): ?>
                <label class="pill">
                  <input type="radio" name="ans[<?= $item['id'] ?>]" value="<?= $val ?>"
                    <?= (($_POST['ans'][$item['id']] ?? '') === $val) ? 'checked' : '' ?> required>
                  <span><?= $label ?></span>
                </label>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>

    <div class="card preview">
      <strong>Live score preview:</strong>
      E <span id="prevE">0</span> &middot; S <span id="prevS">0</span> &middot; G <span id="prevG">0</span>
      &rarr; Overall <span id="prevT">0</span>/100
    </div>

    <button class="btn" type="submit">Submit audit &amp; generate report</button>
  </form>
</main>
<?php include 'includes/footer.php'; ?>
