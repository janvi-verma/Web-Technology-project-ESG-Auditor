<?php
// All ESG questions live here. w = weight (importance), tip = recommendation if not fully met.
function get_questions(): array {
    return [
        'E' => ['title' => 'Environmental', 'icon' => '🌍', 'items' => [
            ['id'=>'e1','w'=>3,'q'=>'Do you track energy consumption regularly?',
             'tip'=>'Install energy meters and review monthly usage to find savings.'],
            ['id'=>'e2','w'=>3,'q'=>'Do you use renewable energy (solar, wind, green power)?',
             'tip'=>'Start with rooftop solar or buy a green power plan for part of your usage.'],
            ['id'=>'e3','w'=>3,'q'=>'Do you measure and report your carbon emissions?',
             'tip'=>'Use the GHG Protocol to calculate Scope 1 and 2 emissions every year.'],
            ['id'=>'e4','w'=>2,'q'=>'Do you have a waste segregation and recycling programme?',
             'tip'=>'Set up separate bins for wet, dry and e-waste and tie up with a recycler.'],
            ['id'=>'e5','w'=>2,'q'=>'Do you monitor and conserve water usage?',
             'tip'=>'Fit water meters and low-flow fixtures; consider rainwater harvesting.'],
        ]],
        'S' => ['title' => 'Social', 'icon' => '🤝', 'items' => [
            ['id'=>'s1','w'=>3,'q'=>'Do you give workplace safety training and keep an incident log?',
             'tip'=>'Run safety drills twice a year and record every incident with its fix.'],
            ['id'=>'s2','w'=>3,'q'=>'Do you ensure fair wages and no child or forced labour?',
             'tip'=>'Audit payroll and supplier contracts against labour laws.'],
            ['id'=>'s3','w'=>2,'q'=>'Do you have a diversity and inclusion policy?',
             'tip'=>'Publish a D&I policy and set measurable hiring and promotion goals.'],
            ['id'=>'s4','w'=>2,'q'=>'Is there a grievance redressal system for employees?',
             'tip'=>'Create an anonymous complaint channel with a clear response timeline.'],
            ['id'=>'s5','w'=>2,'q'=>'Do you run community or CSR programmes?',
             'tip'=>'Partner with a local NGO and track the hours or funds contributed.'],
        ]],
        'G' => ['title' => 'Governance', 'icon' => '⚖️', 'items' => [
            ['id'=>'g1','w'=>3,'q'=>'Does the board include independent members?',
             'tip'=>'Add independent directors to improve oversight and decision quality.'],
            ['id'=>'g2','w'=>3,'q'=>'Do you have an anti-corruption and ethics policy?',
             'tip'=>'Write a code of conduct, train staff, and add a whistleblower process.'],
            ['id'=>'g3','w'=>3,'q'=>'Are internal or external audits done regularly?',
             'tip'=>'Schedule an annual audit and track corrective actions to closure.'],
            ['id'=>'g4','w'=>2,'q'=>'Do you have data privacy and cybersecurity policies?',
             'tip'=>'Adopt a data protection policy, access controls and incident response plan.'],
            ['id'=>'g5','w'=>2,'q'=>'Do you publish transparent ESG or financial disclosures?',
             'tip'=>'Publish an annual sustainability report with clear targets and results.'],
        ]],
    ];
}

// Category weights in the overall score (must match js/app.js)
const CAT_WEIGHTS = ['E' => 0.4, 'S' => 0.3, 'G' => 0.3];

function rating_for(int $total): string {
    if ($total >= 75) return 'Compliant';
    if ($total >= 50) return 'Needs Improvement';
    return 'Non-compliant';
}

function rating_class(string $rating): string {
    return $rating === 'Compliant' ? 'good' : ($rating === 'Needs Improvement' ? 'mid' : 'bad');
}

// answers = ['e1' => 'yes'|'partial'|'no', ...]
function calculate_scores(array $answers): array {
    $value = ['yes' => 1, 'partial' => 0.5, 'no' => 0];
    $scores = [];
    $recs = [];

    foreach (get_questions() as $cat => $group) {
        $earned = 0; $max = 0;
        foreach ($group['items'] as $item) {
            $a = $answers[$item['id']] ?? 'no';
            if (!isset($value[$a])) $a = 'no';
            $earned += $value[$a] * $item['w'];
            $max += $item['w'];
            if ($a !== 'yes') {
                $recs[] = ['cat' => $cat, 'q' => $item['q'], 'tip' => $item['tip'], 'level' => $a];
            }
        }
        $scores[$cat] = (int) round($earned / $max * 100);
    }

    $total = 0;
    foreach (CAT_WEIGHTS as $cat => $w) $total += $scores[$cat] * $w;
    $total = (int) round($total);

    return ['E' => $scores['E'], 'S' => $scores['S'], 'G' => $scores['G'],
            'total' => $total, 'rating' => rating_for($total), 'recs' => $recs];
}
