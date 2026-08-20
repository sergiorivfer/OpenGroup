<?php
/**
 * Markdown negotiation for AI agents.
 * When a request arrives with "Accept: text/markdown", serve a markdown version
 * of the requested page. Browsers never send that Accept header, so normal
 * visitors are unaffected.
 */
header('Content-Type: text/markdown; charset=UTF-8');

$path = isset($_GET['path']) ? $_GET['path'] : '';
$path = trim($path, '/');

// Sanitize path: block traversal and disallow weird characters
if ($path !== '' && (preg_match('/\.\./', $path) || preg_match('/[^a-zA-Z0-9\/\-_]/', $path))) {
    http_response_code(400);
    echo "# Bad request\n";
    exit;
}

$file = ($path === '' || $path === 'index') ? 'index.html' : $path . '.html';
$full = __DIR__ . '/' . $file;

if (!is_file($full) || pathinfo($full, PATHINFO_EXTENSION) !== 'html') {
    http_response_code(404);
    echo "# 404 - Page not found\n";
    exit;
}

$html = file_get_contents($full);

$title = 'Open Group';
if (preg_match('/<title>(.*?)<\/title>/is', $html, $m)) {
    $title = trim(strip_tags($m[1]));
}

$t = $html;
$t = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $t);
$t = preg_replace('/<style\b[^>]*>.*?<\/style>/is', '', $t);
$t = preg_replace('/<h1\b[^>]*>(.*?)<\/h1>/is', "\n# $1\n", $t);
$t = preg_replace('/<h2\b[^>]*>(.*?)<\/h2>/is', "\n## $1\n", $t);
$t = preg_replace('/<h3\b[^>]*>(.*?)<\/h3>/is', "\n### $1\n", $t);
$t = preg_replace('/<br\s*\/?>/i', "\n", $t);
$t = preg_replace('/<\/(p|div|li|section|h1|h2|h3)>/i', "\n", $t);
$t = preg_replace('/<a\b[^>]*href=["\']([^"\']*)["\'][^>]*>(.*?)<\/a>/is', '[$2]($1)', $t);
$t = preg_replace('/<li\b[^>]*>(.*?)<\/li>/is', '- $1', $t);
$t = strip_tags($t);
$t = html_entity_decode($t, ENT_QUOTES, 'UTF-8');
$t = preg_replace('/[ \t]+/m', ' ', $t);
$t = preg_replace('/\n{3,}/', "\n\n", $t);
$t = trim($t);

echo '# ' . $title . "\n\n";
echo "> Open Group SAS - Soluciones TIC para empresas en Colombia.\n\n";
echo $t . "\n";
