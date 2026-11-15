// Live score preview on the audit form (same maths as includes/score.php)
(function () {
  const form = document.getElementById('auditForm');
  if (!form) return;

  const W = { E: 0.4, S: 0.3, G: 0.3 };
  const VAL = { yes: 1, partial: 0.5, no: 0 };

  function update() {
    const t = { E: [0, 0], S: [0, 0], G: [0, 0] };
    form.querySelectorAll('.q').forEach(function (q) {
      const cat = q.dataset.cat;
      const w = Number(q.dataset.w);
      t[cat][1] += w;
      const picked = q.querySelector('input:checked');
      if (picked) t[cat][0] += VAL[picked.value] * w;
    });

    let total = 0;
    ['E', 'S', 'G'].forEach(function (c) {
      const pct = Math.round((t[c][0] / t[c][1]) * 100);
      document.getElementById('prev' + c).textContent = pct;
      total += pct * W[c];
    });
    document.getElementById('prevT').textContent = Math.round(total);
  }

  form.addEventListener('change', update);
  update();
})();
