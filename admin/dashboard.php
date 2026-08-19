<?php
/**
 * Open Group — Admin Dashboard
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../blog/data/init.php';

require_login();

$all_posts = get_all_posts();
$message = $_GET['msg'] ?? '';
$success = $_GET['success'] ?? '';

// Handle delete
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['slug'])) {
    if (delete_post($_GET['slug'])) {
        header('Location: dashboard.php?success=deleted');
        exit;
    }
}

// Handle toggle status
if (isset($_GET['action']) && $_GET['action'] === 'toggle' && isset($_GET['slug'])) {
    $post = load_post($_GET['slug']);
    if ($post) {
        $new_status = ($post['status'] === 'published') ? 'draft' : 'published';
        $post['status'] = $new_status;
        save_post($post);
        header('Location: dashboard.php?success=toggled');
        exit;
    }
}

function admin_date($date) {
    return date('d M, Y', strtotime($date));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Dashboard — Blog Open Group</title>
    <link rel="stylesheet" href="../assets/css/fontawesome.min.css" />
    <link rel="shortcut icon" type="image/png" href="https://www.opengroupsa.com/assets/img/logo/logos/faviconopen.png" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
            background: #0a0d14;
            color: #fff;
            min-height: 100vh;
        }
        /* Top bar */
        .admin-header {
            background: #11151C;
            border-bottom: 1px solid #1E2228;
            padding: 16px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .admin-header .logo-area {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            font-size: 18px;
        }
        .admin-header .logo-area span {
            color: #73bd1e;
        }
        .admin-header .user-area {
            display: flex;
            align-items: center;
            gap: 20px;
            font-size: 14px;
            color: #B0B2B7;
        }
        .admin-header .btn-logout {
            background: transparent;
            color: #e74c3c;
            border: 1px solid rgba(231, 76, 60, 0.3);
            padding: 8px 16px;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        .admin-header .btn-logout:hover {
            background: rgba(231, 76, 60, 0.1);
        }
        /* Main */
        .admin-main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 30px;
        }
        .admin-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }
        .admin-toolbar h1 {
            font-size: 28px;
            font-weight: 700;
        }
        .admin-toolbar h1 span {
            color: #73bd1e;
        }
        .btn-new {
            background: #73bd1e;
            color: #000;
            text-decoration: none;
            padding: 14px 28px;
            font-size: 15px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-transform: uppercase;
            transition: background 0.3s;
        }
        .btn-new:hover {
            background: #8cd44a;
        }
        /* Messages */
        .msg {
            padding: 14px 20px;
            margin-bottom: 25px;
            font-size: 14px;
            font-weight: 600;
        }
        .msg-success {
            background: rgba(115, 189, 30, 0.1);
            border: 1px solid rgba(115, 189, 30, 0.3);
            color: #73bd1e;
        }
        /* Table */
        .posts-table {
            width: 100%;
            border-collapse: collapse;
            background: #11151C;
            border: 1px solid #1E2228;
        }
        .posts-table th {
            text-align: left;
            padding: 16px 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #B0B2B7;
            background: #0d1015;
            border-bottom: 1px solid #1E2228;
        }
        .posts-table td {
            padding: 16px 20px;
            font-size: 14px;
            border-bottom: 1px solid #1E2228;
            vertical-align: middle;
        }
        .posts-table .col-title {
            font-weight: 700;
            max-width: 350px;
        }
        .posts-table .col-title a {
            color: #fff;
            text-decoration: none;
        }
        .posts-table .col-title a:hover {
            color: #73bd1e;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .badge-published {
            background: rgba(115, 189, 30, 0.15);
            color: #73bd1e;
            border: 1px solid rgba(115, 189, 30, 0.3);
        }
        .badge-draft {
            background: rgba(243, 156, 18, 0.15);
            color: #f39c12;
            border: 1px solid rgba(243, 156, 18, 0.3);
        }
        .actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .btn-sm {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 6px 14px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid #1E2228;
            background: transparent;
            color: #B0B2B7;
            transition: all 0.2s;
            font-family: inherit;
        }
        .btn-sm:hover { color: #fff; border-color: #444; }
        .btn-sm.btn-edit:hover { border-color: #73bd1e; color: #73bd1e; }
        .btn-sm.btn-toggle:hover { border-color: #f39c12; color: #f39c12; }
        .btn-sm.btn-delete:hover { border-color: #e74c3c; color: #e74c3c; background: rgba(231, 76, 60, 0.05); }
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: #B0B2B7;
        }
        .empty-state i {
            font-size: 48px;
            color: #73bd1e;
            margin-bottom: 20px;
            display: block;
        }
        .empty-state p {
            font-size: 16px;
            margin-bottom: 20px;
        }
        /* Responsive */
        @media (max-width: 768px) {
            .posts-table { font-size: 13px; }
            .posts-table th, .posts-table td { padding: 12px 10px; }
            .actions { flex-direction: column; }
            .admin-header { padding: 12px 15px; }
            .admin-main { padding: 20px 15px; }
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="logo-area">
            Blog <span>Open Group</span>
        </div>
        <div class="user-area">
            <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'admin'); ?>
            <a href="logout.php" class="btn-logout">Cerrar sesión</a>
        </div>
    </header>

    <main class="admin-main">
        <div class="admin-toolbar">
            <h1>Dashboard <span>— Posts</span></h1>
            <a href="editor.php" class="btn-new">
                <i class="fa-regular fa-plus"></i> Nuevo Post
            </a>
        </div>

        <?php if ($success === 'deleted'): ?>
        <div class="msg msg-success">Post eliminado correctamente.</div>
        <?php elseif ($success === 'toggled'): ?>
        <div class="msg msg-success">Estado del post actualizado.</div>
        <?php elseif ($success === 'saved'): ?>
        <div class="msg msg-success">Post guardado correctamente.</div>
        <?php endif; ?>

        <?php if (count($all_posts) === 0): ?>
        <div class="empty-state">
            <i class="fa-regular fa-newspaper"></i>
            <p>No hay posts todavía. ¡Crea tu primer artículo!</p>
            <a href="editor.php" class="btn-new" style="display:inline-flex;">Crear primer post</a>
        </div>
        <?php else: ?>
        <table class="posts-table">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Categoría</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($all_posts as $p): ?>
                <tr>
                    <td class="col-title">
                        <a href="/open-2026/blog/?slug=<?php echo htmlspecialchars($p['slug']); ?>" target="_blank" title="Ver en el sitio">
                            <?php echo htmlspecialchars($p['title']); ?>
                        </a>
                    </td>
                    <td><?php echo htmlspecialchars($p['category'] ?? '—'); ?></td>
                    <td><?php echo admin_date($p['date']); ?></td>
                    <td>
                        <span class="badge <?php echo ($p['status'] ?? 'draft') === 'published' ? 'badge-published' : 'badge-draft'; ?>">
                            <?php echo ($p['status'] ?? 'draft') === 'published' ? 'Publicado' : 'Borrador'; ?>
                        </span>
                    </td>
                    <td>
                        <div class="actions">
                            <a href="editor.php?slug=<?php echo urlencode($p['slug']); ?>" class="btn-sm btn-edit">
                                <i class="fa-regular fa-pen-to-square"></i> Editar
                            </a>
                            <a href="?action=toggle&slug=<?php echo urlencode($p['slug']); ?>" class="btn-sm btn-toggle" onclick="return confirm('¿Cambiar estado de este post?')">
                                <?php echo ($p['status'] ?? 'draft') === 'published' ? 'Despublicar' : 'Publicar'; ?>
                            </a>
                            <a href="?action=delete&slug=<?php echo urlencode($p['slug']); ?>" class="btn-sm btn-delete" onclick="return confirm('¿Eliminar este post? Esta acción no se puede deshacer.')">
                                <i class="fa-regular fa-trash"></i> Eliminar
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </main>
</body>
</html>

