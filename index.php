<?php
$title = 'Home';
include 'includes/header.php';
?>
<header class="hero">
  <div class="hero-text">
    <h1>Audit your company's ESG performance in minutes</h1>
    <p>Answer simple Environmental, Social and Governance questions, get an automatic compliance score, and download a ready report.</p>
    <?php if (is_logged_in()): ?>
      <a class="btn btn-light" href="audit.php">Start a new audit</a>
    <?php else: ?>
      <a class="btn btn-light" href="register.php">Get started</a>
      <a class="btn btn-outline" href="login.php">Login</a>
    <?php endif; ?>
  </div>
  <!-- Simple SVG illustration (replace with your own picture if you like) -->
  <svg class="hero-art" viewBox="0 0 200 200" aria-hidden="true">
    <circle cx="100" cy="100" r="80" fill="#a5d6a7"/>
    <circle cx="100" cy="100" r="80" fill="none" stroke="#fff" stroke-width="4"/>
    <path d="M100 150 C 60 120, 60 70, 100 40 C 140 70, 140 120, 100 150 Z" fill="#2e7d32"/>
    <path d="M100 150 L100 70" stroke="#e8f5e9" stroke-width="4" stroke-linecap="round"/>
    <path d="M100 110 L80 90 M100 95 L120 78" stroke="#e8f5e9" stroke-width="3" stroke-linecap="round"/>
  </svg>
</header>

<section class="features">
  <div class="card feature"><div class="ficon">🌍</div><h3>Environmental</h3><p>Energy, emissions, waste and water.</p></div>
  <div class="card feature"><div class="ficon">🤝</div><h3>Social</h3><p>Safety, fair labour, inclusion and community.</p></div>
  <div class="card feature"><div class="ficon">⚖️</div><h3>Governance</h3><p>Ethics, board oversight, audits and privacy.</p></div>
  <div class="card feature"><div class="ficon">📄</div><h3>Report</h3><p>Auto-scored results, tips and PDF export.</p></div>
</section>
<?php include 'includes/footer.php'; ?>
