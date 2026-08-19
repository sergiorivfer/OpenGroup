<?php
/**
 * Open Group Blog — Sitemap Dinamico
 * Genera las URLs de /blog/ y de cada post publicado.
 * Se auto-actualiza con cada publicación: no requiere tocar archivos.
 * Referenciado en robots.txt como sitemap adicional.
 */
require_once __DIR__ . '/data/init.php';

header('Content-Type: application/xml; charset=UTF-8');

function esc_xml($s) {
    return htmlspecialchars($s ?? '', ENT_XML1 | ENT_QUOTES, 'UTF-8');
}

$posts = get_published_posts();
$base  = 'https://www.opengroupsa.com';

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc><?php echo $base; ?>/blog/</loc>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
<?php foreach ($posts as $p): $d = substr($p['date'] ?? '', 0, 10); ?>
    <url>
        <loc><?php echo $base; ?>/blog/?slug=<?php echo esc_xml($p['slug']); ?></loc>
        <?php if ($d !== ''): ?><lastmod><?php echo esc_xml($d); ?></lastmod><?php endif; ?>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
<?php endforeach; ?>
</urlset>
