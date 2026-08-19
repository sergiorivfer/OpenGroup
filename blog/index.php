<?php
/**
 * Open Group Blog Single Router
 * Handles: listing (/blog/) and individual posts (/blog/?slug=xxx)
 * Structure identica a las demas paginas de Open Group
 */
require_once __DIR__ . '/data/init.php';

$slug = $_GET['slug'] ?? null;
$is_single = !empty($slug);

$published = get_published_posts();

$categories = [];
foreach ($published as $p) {
    $cat = $p['category'] ?? 'General';
    $categories[$cat] = ($categories[$cat] ?? 0) + 1;
}

function blog_date($date) {
    $months = ['', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
               'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
    $d = new DateTime($date);
    return $d->format('d') . ' de ' . $months[intval($d->format('m'))] . ', ' . $d->format('Y');
}

if ($is_single) {
    $post = load_post($slug);
    if (!$post) $is_single = false;
}
?>
<!DOCTYPE html>
<html class="no-js" lang="es">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

<?php if ($is_single && $post): ?>
    <?php
    /* Imagen absoluta (schema + OG exigen URL completa) */
    $og_img = $post['featured_image'] ?? '';
    if ($og_img !== '' && strpos($og_img, 'http') !== 0) {
        $og_img = 'https://www.opengroupsa.com' . $og_img;
    }
    /* Descripción limpia (sin HTML ni entidades) */
    $og_desc = trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($post['excerpt'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
    if ($og_desc === '') {
        $og_desc = trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($post['content'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
    }
    ?>
    <title><?php echo htmlspecialchars($post['title']); ?> | Blog Open Group</title>
    <meta name="description" content="<?php echo htmlspecialchars(mb_substr($og_desc, 0, 160)); ?>" />
    <meta property="og:title" content="<?php echo htmlspecialchars($post['title']); ?>" />
    <meta property="og:description" content="<?php echo htmlspecialchars($og_desc); ?>" />
    <?php if (!empty($og_img)): ?>
    <meta property="og:image" content="<?php echo htmlspecialchars($og_img); ?>" />
    <?php endif; ?>
    <meta property="og:url" content="https://www.opengroupsa.com/blog/?slug=<?php echo htmlspecialchars($slug); ?>" />
    <link rel="canonical" href="https://www.opengroupsa.com/blog/?slug=<?php echo htmlspecialchars($slug); ?>" />
    <!-- JSON-LD BlogPosting: schema para motores de IA y rich results -->
    <script type="application/ld+json">
    <?php echo json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'BlogPosting',
        'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => 'https://www.opengroupsa.com/blog/?slug=' . $slug],
        'headline' => $post['title'],
        'description' => mb_substr($og_desc, 0, 200),
        'image' => $og_img !== '' ? $og_img : 'https://www.opengroupsa.com/assets/img/opengroup-og.webp',
        'author' => ['@type' => 'Organization', 'name' => $post['author'] ?? 'Open Group S.A.S'],
        'publisher' => ['@type' => 'Organization', 'name' => 'Open Group S.A.S', 'logo' => ['@type' => 'ImageObject', 'url' => 'https://www.opengroupsa.com/assets/img/logo/logos/faviconopen.png']],
        'datePublished' => substr($post['date'], 0, 10),
        'dateModified' => substr($post['date'], 0, 10),
        'inLanguage' => 'es',
        'articleSection' => $post['category'] ?? 'Blog'
    ], JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
    </script>
<?php else: ?>
    <title>Blog | Open Group - Soluciones TIC para Empresas en Colombia</title>
    <meta name="description" content="Blog de Open Group: noticias, tendencias y conocimiento sobre tecnología empresarial." />
    <meta property="og:title" content="Blog | Open Group - Soluciones TIC" />
    <meta property="og:description" content="Noticias y conocimiento sobre tecnología empresarial." />
    <link rel="canonical" href="https://www.opengroupsa.com/blog/" />
<?php endif; ?>

    <meta name="robots" content="index, follow" />
    <meta property="og:type" content="<?php echo $is_single ? 'article' : 'website'; ?>" />
    <meta name="author" content="Open Group S.A.S" />
    <meta name="language" content="es" />

    <!-- Open Graph -->
    <meta property="og:url" content="https://www.opengroupsa.com/blog/" />
    <meta property="og:image" content="https://www.opengroupsa.com/assets/img/opengroup-og.webp" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />

    <!-- CSS -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/fontawesome.min.css" />
    <link rel="stylesheet" href="../assets/css/venobox.min.css" />
    <link rel="stylesheet" href="../assets/css/animate.min.css" />
    <link rel="stylesheet" href="../assets/css/keyframe-animation.css" />
    <link rel="stylesheet" href="../assets/css/odometer.min.css" />
    <link rel="stylesheet" href="../assets/css/nice-select.css" />
    <link rel="stylesheet" href="../assets/css/swiper.min.css" />
    <link rel="stylesheet" href="../assets/css/og-slider.css" />
    <link rel="stylesheet" href="../assets/css/main.css" />
    <link rel="stylesheet" href="../assets/css/og-menu.css" />
    <link rel="stylesheet" href="../assets/css/forms-bitrix.css" />
    <link rel="stylesheet" href="../assets/css/chatbot.css" />
    <link rel="shortcut icon" type="image/png" href="https://www.opengroupsa.com/assets/img/logo/logos/faviconopen.png" />

    <!-- GTM -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','GTM-WQ3JNH9T');</script>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-9T8VN0XF39"></script>
    <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','G-9T8VN0XF39');</script>

    <style>
        :root{--og-green:#73bd1e;--og-dark:#11151C;--og-darker:#0a0d14;--og-border:#1E2228;--og-text:#B0B2B7;--og-white:#ffffff;}
        body{background:var(--og-darker);}
        .blog-hero{position:relative;background:var(--og-darker);background-size:cover;background-position:center;padding:160px 0 80px;text-align:center;overflow:hidden;}
        .blog-hero .overlay{position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(2,5,10,0.72);z-index:1;}
        .blog-hero .container{position:relative;z-index:1;}
        .blog-hero h1{font-size:clamp(32px,5vw,52px);font-weight:800;color:var(--og-white);margin-bottom:12px;letter-spacing:-0.5px;}
        .blog-hero h1 span{color:var(--og-green);}
        .blog-hero p{font-size:18px;color:var(--og-text);max-width:560px;margin:0 auto;}
        .blog-hero .bread{font-size:14px;color:#666;margin-top:20px;}
        .blog-hero .bread a{color:var(--og-green);text-decoration:none;}
        .blog-hero .bread span{color:#555;}
        .blog-main{padding:60px 0 100px;}
        .posts-grid{display:flex;flex-direction:column;gap:24px;}
        .post-card{background:var(--og-dark);border:1px solid var(--og-border);border-radius:12px;overflow:hidden;display:flex;flex-direction:row;transition:border-color .3s,transform .3s;}
        .post-card:hover{border-color:var(--og-green);transform:translateY(-2px);}
        .post-card .post-thumb{width:320px;min-height:220px;flex-shrink:0;overflow:hidden;position:relative;}
        .post-card .post-thumb img{width:100%;height:100%;object-fit:cover;transition:transform .4s;}
        .post-card:hover .post-thumb img{transform:scale(1.05);}
        .post-card .post-thumb .no-img{width:100%;height:100%;background:linear-gradient(135deg,#1a1e28,#11151C);display:flex;align-items:center;justify-content:center;font-size:48px;color:var(--og-green);}
        .post-card .post-body{padding:28px 30px;flex:1;display:flex;flex-direction:column;justify-content:center;}
        .post-card .post-meta{display:flex;flex-wrap:wrap;gap:16px;margin-bottom:12px;font-size:13px;color:#888;}
        .post-card .post-meta span{display:flex;align-items:center;gap:5px;}
        .post-card .post-meta i{color:var(--og-green);}
        .post-card .post-meta .cat{background:rgba(115,189,30,0.12);color:var(--og-green);padding:2px 10px;border-radius:4px;font-weight:600;font-size:11px;text-transform:uppercase;letter-spacing:.5px;}
        .post-card h2{font-size:22px;font-weight:700;line-height:1.4;margin-bottom:10px;}
        .post-card h2 a{color:var(--og-white);text-decoration:none;transition:color .3s;}
        .post-card h2 a:hover{color:var(--og-green);}
        .post-card p{color:var(--og-text);font-size:15px;line-height:1.7;margin-bottom:14px;}
        .post-card .read-more{display:inline-flex;align-items:center;gap:6px;color:var(--og-green);font-weight:600;font-size:14px;text-decoration:none;transition:gap .3s;}
        .post-card .read-more:hover{gap:10px;}
        .single-post-wrap{max-width:860px;margin:0 auto;}
        .single-post-wrap .back-link{display:inline-flex;align-items:center;gap:6px;color:var(--og-green);font-weight:600;font-size:14px;text-decoration:none;margin-bottom:24px;transition:gap .3s;}
        .single-post-wrap .back-link:hover{gap:10px;}
        .single-post-header{margin-bottom:30px;}
        .single-post-header h1{font-size:clamp(26px,3.5vw,40px);font-weight:800;color:var(--og-white);line-height:1.3;margin-bottom:16px;}
        .single-post-header .meta{display:flex;flex-wrap:wrap;gap:16px;font-size:14px;color:#888;}
        .single-post-header .meta span{display:flex;align-items:center;gap:6px;}
        .single-post-header .meta i{color:var(--og-green);}
        .single-post-header .meta .cat{background:rgba(115,189,30,0.12);color:var(--og-green);padding:2px 10px;border-radius:4px;font-weight:600;font-size:11px;text-transform:uppercase;letter-spacing:.5px;}
        /* featured image now used as hero background - removed from body to avoid duplication */
        .post-content{color:#d0d0d0;font-size:16px;line-height:1.9;}
        .post-content h2{font-size:28px;font-weight:700;color:var(--og-white);margin:40px 0 16px;}
        .post-content h3{font-size:22px;font-weight:700;color:var(--og-white);margin:32px 0 12px;}
        .post-content p{margin-bottom:18px;}
        .post-content img{max-width:100%;height:auto;border-radius:8px;margin:20px 0;}
        .post-content ul,.post-content ol{margin:16px 0;padding-left:22px;}
        .post-content li{margin-bottom:8px;}
        .post-content a{color:var(--og-green);text-decoration:underline;}
        .post-content blockquote{border-left:4px solid var(--og-green);padding:16px 24px;margin:24px 0;background:rgba(115,189,30,0.05);font-style:italic;color:#bbb;font-size:18px;}
        .no-posts{text-align:center;padding:80px 20px;}
        .no-posts i{font-size:48px;color:var(--og-green);margin-bottom:16px;display:block;}
        .no-posts p{font-size:16px;color:var(--og-text);}
        .blog-sidebar{padding-left:30px;}
        @media(max-width:991px){.blog-sidebar{padding-left:0;margin-top:40px;}}
        .sidebar-card{background:var(--og-dark);border:1px solid var(--og-border);border-radius:12px;padding:24px;margin-bottom:20px;}
        .sidebar-card h3{font-size:18px;font-weight:700;color:var(--og-white);margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid var(--og-border);}
        .sidebar-card .search-form{position:relative;}
        .sidebar-card .search-form input{width:100%;background:var(--og-darker);border:1px solid var(--og-border);border-radius:8px;color:var(--og-white);padding:12px 40px 12px 14px;font-size:14px;font-family:inherit;}
        .sidebar-card .search-form input:focus{outline:none;border-color:var(--og-green);}
        .sidebar-card .search-form button{position:absolute;right:8px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--og-green);cursor:pointer;font-size:16px;}
        .sidebar-card .cat-list{list-style:none;padding:0;margin:0;}
        .sidebar-card .cat-list li{display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid rgba(255,255,255,0.04);}
        .sidebar-card .cat-list li:last-child{border-bottom:none;}
        .sidebar-card .cat-list li a{color:var(--og-text);text-decoration:none;font-size:14px;transition:color .3s;}
        .sidebar-card .cat-list li a:hover{color:var(--og-green);}
        .sidebar-card .cat-list li .count{background:rgba(115,189,30,0.1);color:var(--og-green);font-size:11px;padding:2px 8px;border-radius:4px;font-weight:600;}
        .sidebar-card .recent-item{display:flex;gap:12px;padding:10px 0;border-bottom:1px solid rgba(255,255,255,0.04);}
        .sidebar-card .recent-item:last-child{border-bottom:none;}
        .sidebar-card .recent-item img{width:60px;height:60px;border-radius:8px;object-fit:cover;flex-shrink:0;}
        .sidebar-card .recent-item .ri-content{flex:1;}
        .sidebar-card .recent-item .ri-content a{color:var(--og-white);text-decoration:none;font-size:14px;font-weight:600;line-height:1.4;display:block;transition:color .3s;}
        .sidebar-card .recent-item .ri-content a:hover{color:var(--og-green);}
        .sidebar-card .recent-item .ri-content .ri-date{font-size:12px;color:#666;display:block;margin-top:4px;}
        .sidebar-card .tags-list{display:flex;flex-wrap:wrap;gap:8px;list-style:none;padding:0;margin:0;}
        .sidebar-card .tags-list li a{display:block;padding:6px 14px;background:var(--og-darker);border:1px solid var(--og-border);border-radius:6px;color:var(--og-text);font-size:12px;font-weight:500;text-decoration:none;transition:all .3s;}
        .sidebar-card .tags-list li a:hover{background:var(--og-green);color:#000;border-color:var(--og-green);}
        .pagination-wrap{display:flex;justify-content:center;gap:8px;margin-top:40px;list-style:none;padding:0;}
        .pagination-wrap li a{display:flex;align-items:center;justify-content:center;width:42px;height:42px;background:var(--og-dark);border:1px solid var(--og-border);border-radius:10px;color:var(--og-text);font-weight:600;font-size:15px;text-decoration:none;transition:all .3s;}
        .pagination-wrap li a:hover,.pagination-wrap li a.active{background:var(--og-green);color:#000;border-color:var(--og-green);}
        @media(max-width:767px){.post-card{flex-direction:column;}.post-card .post-thumb{width:100%;height:200px;}.post-card .post-body{padding:20px;}.post-card h2{font-size:18px;}.blog-hero{padding:140px 0 50px;}}
    </style>
</head>
<body>

    <!-- GTM noscript -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WQ3JNH9T" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>

    <script src="../assets/js/forms.js"></script>

    <div id="og-contact-modal-root"></div>
    <script>
    fetch("../contact-modal.html").then(function(r){if(!r.ok)throw new Error("Error al cargar el modal: "+r.status);return r.text();}).then(function(h){document.getElementById("og-contact-modal-root").innerHTML=h;}).catch(function(e){console.error(e);});
    </script>

    <!-- preloader (comentado como en las demas paginas) -->
    <div id="menu"></div>
    <script>
    fetch("../menu.html").then(function(r){if(!r.ok)throw new Error("Error HTTP: "+r.status);return r.text();}).then(function(d){var bp="../";d=d.replace(/(?:href|src)="(?!\/|https?:|#|mailto|javascript:|tel:)([^"]+)"/g,function(m,a){var q=m.indexOf('src=')===0?'src':'href';return q+'="'+bp+a+'"';});d=d.replace(/(?:href|src)="\/(?!\/)([^"]*)"/g,function(m,a){var q=m.indexOf('src=')===0?'src':'href';return q+'="'+bp+a+'"';});document.getElementById("menu").innerHTML=d;}).catch(function(e){console.error("No se pudo cargar el menu:",e);});
    </script>

    <div id="smooth-wrapper">
    <div id="smooth-content">

    <!-- HERO (con background image + overlay tenue) -->
    <section class="blog-hero"<?php if ($is_single && $post && !empty($post['featured_image'])): ?> style="background-image:url(<?php echo htmlspecialchars($post['featured_image']); ?>)"<?php endif; ?>>
        <div class="overlay"></div>
        <div class="container">
            <?php if ($is_single && $post): ?>
                <div class="bread"><a href="./">Blog</a> <span>/</span> <?php echo htmlspecialchars($post['title']); ?></div>
                <h1><?php echo htmlspecialchars($post['title']); ?></h1>
            <?php else: ?>
                <div class="bread"><a href="../">Inicio</a> <span>/</span> Blog</div>
                <h1>Blog <span>Open Group</span></h1>
                <p>Conocimiento, tendencias y casos de éxito sobre tecnología empresarial</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- CONTENT -->
    <section class="blog-main">
        <div class="container">
            <div class="row">

            <?php if ($is_single && $post): ?>
            <!-- SINGLE POST -->
            <div class="col-12">
                <div class="single-post-wrap">
                    <a href="./" class="back-link"><i class="fa-regular fa-arrow-left"></i> Volver al Blog</a>
                    
                    <div class="single-post-header">
                        <div class="meta">
                            <span><i class="fa-regular fa-clock"></i> <?php echo blog_date($post['date']); ?></span>
                            <span><i class="fa-light fa-user"></i> <?php echo htmlspecialchars($post['author'] ?? 'Open Group'); ?></span>
                            <?php if (!empty($post['category'])): ?>
                            <span class="cat"><?php echo htmlspecialchars($post['category']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="post-content"><?php echo $post['content']; ?></div>
                </div>
            </div>

            <?php else: ?>
            <!-- BLOG LISTING -->
            <div class="col-lg-8">
                <div class="posts-grid">
                    <?php
                    $page = max(1, intval($_GET['page'] ?? 1));
                    $per_page = 5;
                    $total = count($published);
                    $total_pages = max(1, ceil($total / $per_page));
                    $offset = ($page - 1) * $per_page;
                    $posts = array_slice($published, $offset, $per_page);

                    if (count($posts) === 0): ?>
                    <div class="no-posts">
                        <i class="fa-regular fa-newspaper"></i>
                        <p>Pronto publicaremos nuestro primer articulo. Vuelve pronto!</p>
                    </div>
                    <?php else: ?>
                    <?php foreach ($posts as $p): ?>
                    <article class="post-card">
                        <div class="post-thumb">
                            <?php if (!empty($p['featured_image'])): ?>
                            <a href="?slug=<?php echo htmlspecialchars($p['slug']); ?>"><img src="<?php echo htmlspecialchars($p['featured_image']); ?>" alt="<?php echo htmlspecialchars($p['title']); ?>" loading="lazy" /></a>
                            <?php else: ?>
                            <div class="no-img"><i class="fa-regular fa-file-lines"></i></div>
                            <?php endif; ?>
                        </div>
                        <div class="post-body">
                            <div class="post-meta">
                                <span><i class="fa-regular fa-clock"></i> <?php echo blog_date($p['date']); ?></span>
                                <?php if (!empty($p['category'])): ?><span class="cat"><?php echo htmlspecialchars($p['category']); ?></span><?php endif; ?>
                            </div>
                            <h2><a href="?slug=<?php echo htmlspecialchars($p['slug']); ?>"><?php echo htmlspecialchars($p['title']); ?></a></h2>
                            <p><?php echo htmlspecialchars($p['excerpt'] ?? mb_substr(strip_tags($p['content'] ?? ''), 0, 200) . '...'); ?></p>
                            <a href="?slug=<?php echo htmlspecialchars($p['slug']); ?>" class="read-more">Leer articulo <i class="fa-regular fa-arrow-right"></i></a>
                        </div>
                    </article>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <?php if ($total_pages > 1): ?>
                <ul class="pagination-wrap">
                    <?php if ($page > 1): ?><li><a href="?page=<?php echo $page - 1; ?>"><i class="fa-solid fa-chevron-left"></i></a></li><?php endif; ?>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li><a href="?page=<?php echo $i; ?>" class="<?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a></li>
                    <?php endfor; ?>
                    <?php if ($page < $total_pages): ?><li><a href="?page=<?php echo $page + 1; ?>"><i class="fa-solid fa-chevron-right"></i></a></li><?php endif; ?>
                </ul>
                <?php endif; ?>
            </div>

            <!-- SIDEBAR -->
            <div class="col-lg-4">
                <aside class="blog-sidebar">
                    <div class="sidebar-card">
                        <h3>Buscar</h3>
                        <form action="./" method="get" class="search-form">
                            <input type="text" name="q" placeholder="Buscar articulos..." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>" />
                            <button type="submit"><i class="fa-regular fa-magnifying-glass"></i></button>
                        </form>
                    </div>
                    <?php if (count($categories) > 0): ?>
                    <div class="sidebar-card">
                        <h3>Categorias</h3>
                        <ul class="cat-list"><?php foreach ($categories as $cat => $count): ?><li><a href="?category=<?php echo urlencode($cat); ?>"><?php echo htmlspecialchars($cat); ?></a><span class="count"><?php echo $count; ?></span></li><?php endforeach; ?></ul>
                    </div>
                    <?php endif; ?>
                    <?php $recent = array_slice($published, 0, 3); if (count($recent) > 0): ?>
                    <div class="sidebar-card">
                        <h3>Recientes</h3>
                        <?php foreach ($recent as $rp): ?>
                        <div class="recent-item">
                            <a href="?slug=<?php echo htmlspecialchars($rp['slug']); ?>"><?php if (!empty($rp['featured_image'])): ?><img src="<?php echo htmlspecialchars($rp['featured_image']); ?>" alt="" loading="lazy" /><?php endif; ?></a>
                            <div class="ri-content"><a href="?slug=<?php echo htmlspecialchars($rp['slug']); ?>"><?php echo htmlspecialchars($rp['title']); ?></a><span class="ri-date"><?php echo blog_date($rp['date']); ?></span></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <?php if (count($categories) > 0): ?>
                    <div class="sidebar-card">
                        <h3>Tags</h3>
                        <ul class="tags-list"><?php foreach ($categories as $cat => $c): ?><li><a href="?category=<?php echo urlencode($cat); ?>"><?php echo htmlspecialchars($cat); ?></a></li><?php endforeach; ?></ul>
                    </div>
                    <?php endif; ?>
                </aside>
            </div>
            <?php endif; ?>

            </div>
        </div>
    </section>

    </div>
    </div>
    <!-- ./smooth-wrapper /smooth-content -->

    <!-- footer -->
    <div id="footer"></div>
    <script>
    fetch("../footer.html").then(function(r){if(!r.ok)throw new Error("Error HTTP: "+r.status);return r.text();}).then(function(d){var bp="../";d=d.replace(/(?:href|src)="(?!\/|https?:|#|mailto|javascript:|tel:)([^"]+)"/g,function(m,a){var q=m.indexOf('src=')===0?'src':'href';return q+'="'+bp+a+'"';});d=d.replace(/(?:href|src)="\/(?!\/)([^"]*)"/g,function(m,a){var q=m.indexOf('src=')===0?'src':'href';return q+'="'+bp+a+'"';});document.getElementById("footer").innerHTML=d;}).catch(function(e){console.error("No se pudo cargar el footer:",e);});
    </script>

    <div id="theme-toogle" class="switcher-button">
        <div class="switcher-button-inner-left"></div>
        <div class="switcher-button-inner"></div>
    </div>

    <!-- GSAP -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>

    <!-- Vendor JS -->
    <script src="../assets/js/vendor/jquary-3.6.0.min.js"></script>
    <script src="../assets/js/vendor/bootstrap-bundle.js"></script>
    <script src="../assets/js/vendor/imagesloaded-pkgd.js"></script>
    <script src="../assets/js/vendor/waypoints.min.js"></script>
    <script src="../assets/js/vendor/venobox.min.js"></script>
    <script src="../assets/js/vendor/odometer.min.js"></script>
    <script src="../assets/js/vendor/meanmenu.js"></script>
    <script src="../assets/js/vendor/jquery.isotope.js"></script>
    <script src="../assets/js/vendor/wow.min.js"></script>
    <script src="../assets/js/vendor/swiper.min.js"></script>
    <script src="../assets/js/vendor/split-type.min.js"></script>

    <script src="../assets/js/vendor/jquery.carouselTicker.js"></script>
    <script src="../assets/js/vendor/nice-select.js"></script>
    <script src="../assets/js/ajax-form.js"></script>
    <script src="../assets/js/contact.js"></script>
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/og-menu.js"></script>
    <script src="../assets/js/og-slider.js"></script>
    <script src="../assets/js/chatbot.js"></script>
    <script async defer src="https://chat.dialvox.io/js/widget/m9iooto1vhl4hdce/float.js"></script>

</body>
</html>
