// Draws the dashboard charts using Chart.js (data comes from dashboard.php)
(function () {
  const raw = document.getElementById('chartData');
  if (!raw || typeof Chart === 'undefined') return;
  const d = JSON.parse(raw.textContent);

  new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
      labels: d.labels,
      datasets: [{
        label: 'Overall score',
        data: d.totals,
        borderColor: '#2e7d32',
        backgroundColor: 'rgba(46,125,50,0.15)',
        fill: true,
        tension: 0.3
      }]
    },
    options: { scales: { y: { min: 0, max: 100 } } }
  });

  new Chart(document.getElementById('catChart'), {
    type: 'bar',
    data: {
      labels: ['Environmental', 'Social', 'Governance'],
      datasets: [{
        label: 'Score',
        data: d.latest,
        backgroundColor: ['#43a047', '#1e88e5', '#8e24aa']
      }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { min: 0, max: 100 } } }
  });
})();
