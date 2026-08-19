<?php
/**
 * Open Group — Editor WYSIWYG (TinyMCE)
 * Experiencia WordPress para Hillary
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../blog/data/init.php';

require_login();

$edit_slug = $_GET['slug'] ?? null;
$existing = $edit_slug ? load_post($edit_slug) : null;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = $_POST['content'] ?? '';
    $category = trim($_POST['category'] ?? 'General');
    $featured_image = trim($_POST['featured_image'] ?? '');
    $status = $_POST['status'] ?? 'draft';

    if (empty($title)) {
        $error = 'El título es obligatorio.';
    } else {
        $slug = $edit_slug ?: slugify($title);
        if (!$edit_slug) {
            $existing_slugs = array_column(load_posts(), 'slug');
            $base_slug = $slug;
            $counter = 1;
            while (in_array($slug, $existing_slugs)) {
                $slug = $base_slug . '-' . $counter;
                $counter++;
            }
        }

        save_post([
            'slug' => $slug,
            'title' => $title,
            'content' => $content,
            'author' => 'Open Group',
            'date' => $edit_slug ? ($existing['date'] ?? date('Y-m-d')) : date('Y-m-d'),
            'status' => $status,
            'featured_image' => $featured_image,
            'category' => $category
        ]);

        if ($edit_slug && $edit_slug !== $slug) {
            $old_file = POSTS_DIR . '/' . $edit_slug . '.json';
            if (file_exists($old_file)) unlink($old_file);
            $posts = load_posts();
            $posts = array_filter($posts, fn($p) => $p['slug'] !== $edit_slug);
            save_posts(array_values($posts));
        }

        header('Location: dashboard.php?success=saved');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?php echo $edit_slug ? 'Editar' : 'Nuevo'; ?> Post — Blog OpenGroup</title>
    <link rel="stylesheet" href="../assets/css/fontawesome.min.css" />
    <link rel="shortcut icon" type="image/png" href="https://www.opengroupsa.com/assets/img/logo/logos/faviconopen.png" />
    <!-- TinyMCE CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js" referrerpolicy="origin"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
            background: #0a0d14;
            color: #fff;
            min-height: 100vh;
        }
        .admin-header {
            background: #11151C;
            border-bottom: 1px solid #1E2228;
            padding: 14px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .admin-header .back {
            color: #B0B2B7;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .admin-header .back:hover { color: #73bd1e; }
        .admin-header h1 { font-size: 18px; font-weight: 700; }
        .admin-main { max-width: 1100px; margin: 0 auto; padding: 30px; }
        .form-group { margin-bottom: 25px; }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #B0B2B7;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        .form-group input[type="text"],
        .form-group select {
            width: 100%;
            background: #11151C;
            border: 1px solid #1E2228;
            color: #fff;
            padding: 14px 16px;
            font-size: 16px;
            font-family: inherit;
        }
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #73bd1e;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        @media (max-width: 768px) { .form-row { grid-template-columns: 1fr; } }
        
        /* Upload */
        .upload-box {
            padding: 20px;
            background: #0d1015;
            border: 1px dashed #1E2228;
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        .upload-box input[type="file"] {
            color: #B0B2B7;
            font-size: 14px;
            font-family: inherit;
        }
        .btn {
            padding: 12px 28px;
            font-size: 14px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            text-transform: uppercase;
            font-family: inherit;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-gray { background: #1E2228; color: #B0B2B7; }
        .btn-gray:hover { background: #2a2d35; color: #fff; }
        .btn-green { background: #73bd1e; color: #000; }
        .btn-green:hover { background: #8cd44a; }
        .btn-cancel { background: transparent; color: #B0B2B7; border: 1px solid #1E2228; }
        .btn-cancel:hover { border-color: #444; color: #fff; }
        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 30px;
            flex-wrap: wrap;
        }
        .msg-error {
            background: rgba(231, 76, 60, 0.12);
            border: 1px solid rgba(231, 76, 60, 0.3);
            color: #e74c3c;
            padding: 12px 20px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        #uploadStatus { font-size: 13px; color: #73bd1e; }
        .upload-hint { font-size: 12px; color: #666; margin-top: 4px; }

        /* GEO Checklist */
        .editor-layout { display: grid; grid-template-columns: 1fr 330px; gap: 24px; align-items: start; }
        @media (max-width: 992px) { .editor-layout { grid-template-columns: 1fr; } }
        .geo-panel {
            background: #11151C;
            border: 1px solid #1E2228;
            border-radius: 12px;
            padding: 22px;
            position: sticky;
            top: 20px;
        }
        .geo-panel h2 { font-size: 15px; font-weight: 800; color: #fff; margin-bottom: 6px; }
        .geo-panel .geo-sub { font-size: 12px; color: #666; margin-bottom: 14px; }
        .geo-progress { font-size: 13px; font-weight: 700; color: #73bd1e; margin-bottom: 8px; }
        .geo-bar-wrap { height: 6px; background: #1E2228; border-radius: 4px; overflow: hidden; margin-bottom: 16px; }
        .geo-bar { height: 100%; width: 0; background: #73bd1e; transition: width .3s; }
        .geo-list { list-style: none; padding: 0; margin: 0; }
        .geo-list li {
            display: flex; gap: 10px; align-items: flex-start;
            padding: 9px 0; border-bottom: 1px solid rgba(255,255,255,0.04);
            font-size: 13px; color: #B0B2B7; line-height: 1.5; cursor: default;
        }
        .geo-list li:last-child { border-bottom: none; }
        .geo-list li.geo-manual { cursor: pointer; }
        .geo-list li.geo-manual:hover { color: #fff; }
        .geo-list li.geo-ok { color: #9ad158; }
        .geo-icon { width: 20px; flex-shrink: 0; }
        .geo-note { font-size: 11px; color: #666; margin-top: 14px; line-height: 1.6; }
        .geo-cta-btn {
            width: 100%; margin-top: 12px; padding: 10px; background: rgba(115,189,30,0.12);
            border: 1px solid rgba(115,189,30,0.35); color: #73bd1e; font-weight: 700; font-size: 12px;
            cursor: pointer; font-family: inherit; text-transform: uppercase; letter-spacing: .5px;
        }
        .geo-cta-btn:hover { background: rgba(115,189,30,0.22); }
    </style>
</head>
<body>
    <header class="admin-header">
        <a href="dashboard.php" class="back"><i class="fa-regular fa-arrow-left"></i> Dashboard</a>
        <h1><?php echo $edit_slug ? 'Editar Post' : 'Nuevo Post'; ?></h1>
        <div></div>
    </header>

    <main class="admin-main">
        <?php if ($error): ?>
        <div class="msg-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" id="postForm">
            <div class="editor-layout">
            <div class="editor-main">
            <div class="form-group">
                <label>Título del artículo</label>
                <input type="text" name="title" id="postTitle" placeholder="Ej: 5 Tendencias de Ciberseguridad para 2026" value="<?php echo htmlspecialchars($existing['title'] ?? ''); ?>" required />
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Categoría</label>
                    <select name="category" id="postCategory">
                        <?php
                        $cats = ['Ciberseguridad', 'MultiCloud', 'Comunicaciones', 'Transformación Digital', 'Infraestructura', 'General'];
                        $current_cat = $existing['category'] ?? 'General';
                        foreach ($cats as $c):
                        ?>
                        <option value="<?php echo $c; ?>" <?php echo $current_cat === $c ? 'selected' : ''; ?>><?php echo $c; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Imagen destacada (URL)</label>
                    <input type="text" name="featured_image" id="featuredImage" placeholder="O súbela abajo y se copia sola" value="<?php echo htmlspecialchars($existing['featured_image'] ?? ''); ?>" />
                </div>
            </div>

            <!-- Upload -->
            <div class="form-group">
                <label>📷 Subir imágenes</label>
                <div class="upload-box">
                    <input type="file" id="imageUploader" accept="image/*" />
                    <button type="button" id="uploadBtn" class="btn btn-gray" style="padding:10px 20px;">Subir al editor</button>
                    <span id="uploadStatus"></span>
                </div>
                <div class="upload-hint">JPG, PNG, WebP o GIF — máx 5MB. La imagen se inserta donde está el cursor en el editor.</div>
            </div>

            <!-- Editor TinyMCE -->
            <div class="form-group">
                <label>Contenido del artículo</label>
                <textarea name="content" id="editor" rows="20" style="width:100%;min-height:500px;">
                    <?php echo htmlspecialchars($existing['content'] ?? '<p>Empieza a escribir aquí tu artículo...</p>'); ?>
                </textarea>
            </div>

            </div><!-- /editor-main -->

            <aside class="geo-panel">
                <h2>📋 Checklist GEO</h2>
                <div class="geo-sub">Para que Google y las IA entiendan y citen tu artículo</div>
                <div class="geo-progress" id="geoProgress">0 de 8</div>
                <div class="geo-bar-wrap"><div class="geo-bar" id="geoBar"></div></div>
                <ul class="geo-list">
                    <li class="geo-item" data-check="title-len"><span class="geo-icon">⬜</span> Título entre 40 y 70 caracteres</li>
                    <li class="geo-item geo-manual" data-check="first-para" onclick="toggleManual(this)"><span class="geo-icon">⬜</span> El primer párrafo responde la pregunta directamente</li>
                    <li class="geo-item geo-manual" data-check="data" onclick="toggleManual(this)"><span class="geo-icon">⬜</span> Incluye datos, cifras o estadísticas</li>
                    <li class="geo-item" data-check="h2"><span class="geo-icon">⬜</span> Usa subtítulos (H2) para organizar</li>
                    <li class="geo-item" data-check="lists"><span class="geo-icon">⬜</span> Incluye listas o tablas</li>
                    <li class="geo-item" data-check="image"><span class="geo-icon">⬜</span> Imagen destacada definida</li>
                    <li class="geo-item" data-check="cta"><span class="geo-icon">⬜</span> Incluye llamado a la acción (WhatsApp / contacto)</li>
                    <li class="geo-item" data-check="words"><span class="geo-icon">⬜</span> Mínimo 300 palabras</li>
                </ul>
                <button type="button" class="geo-cta-btn" onclick="insertCTA()"><i class="fa-regular fa-comment-dots"></i> Insertar CTA de WhatsApp</button>
                <div class="geo-note">💡 Al publicar, el blog genera solo el schema BlogPosting y actualiza el sitemap. Este checklist es para que el contenido sea "extractable" por los motores de IA.</div>
            </aside>

            </div><!-- /editor-layout -->

            <div class="form-actions">
                <button type="button" class="btn btn-gray" onclick="savePost('draft')">💾 Guardar Borrador</button>
                <button type="button" class="btn btn-green" onclick="savePost('published')">🚀 Publicar</button>
                <a href="dashboard.php" class="btn btn-cancel">Cancelar</a>
            </div>

            <input type="hidden" name="status" id="statusField" value="<?php echo $existing['status'] ?? 'draft'; ?>" />
        </form>
    </main>

    <script>
    // ============================================================
    // CONFIGURACIÓN TINYMCE
    // ============================================================
    
    // TinyMCE language: Spanish
    tinymce.addI18n('es', {
        'Rich Text Area': 'Editor de texto',
        'Bold': 'Negrita',
        'Italic': 'Cursiva',
        'Underline': 'Subrayado',
        'Strikethrough': 'Tachado',
        'Bullet list': 'Lista con viñetas',
        'Numbered list': 'Lista numerada',
        'Link': 'Enlace',
        'Unlink': 'Quitar enlace',
        'Image': 'Imagen',
        'Source code': 'Código fuente',
        'Blockquote': 'Cita',
        'Heading 1': 'Título 1',
        'Heading 2': 'Título 2',
        'Heading 3': 'Título 3',
        'Heading 4': 'Título 4',
        'Paragraph': 'Párrafo'
    });

    tinymce.init({
        selector: '#editor',
        height: 550,
        menubar: false,
        branding: false,
        promotion: false,
        statusbar: true,
        language: 'es',
        content_style: `
            body {
                font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
                font-size: 16px;
                line-height: 1.8;
                color: #e0e0e0;
                background: #11151C;
                padding: 20px;
            }
            h2 { font-size: 28px; color: #fff; font-weight: 700; margin: 30px 0 15px; }
            h3 { font-size: 22px; color: #fff; font-weight: 700; margin: 25px 0 12px; }
            p { margin-bottom: 16px; }
            img { max-width: 100%; height: auto; }
            a { color: #73bd1e; }
            blockquote {
                border-left: 4px solid #73bd1e;
                padding: 15px 20px;
                margin: 20px 0;
                background: rgba(115, 189, 30, 0.05);
                font-style: italic;
                color: #ccc;
            }
            ul, ol { margin: 10px 0; padding-left: 25px; }
            li { margin-bottom: 8px; }
        `,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap',
            'preview', 'anchor', 'searchreplace', 'visualblocks', 'code',
            'fullscreen', 'insertdatetime', 'media', 'table', 'help',
            'wordcount'
        ],
        toolbar: [
            'undo redo | blocks | bold italic underline strikethrough | forecolor backcolor',
            'alignleft aligncenter alignright alignjustify | bullist numlist outdent indent',
            'link image media | blockquote | table | code fullscreen | help'
        ].join(' | '),
        // Setup: when user saves from TinyMCE, sync to our form
        setup: function(editor) {
            editor.on('change', function() {
                tinymce.triggerSave();
                updateChecklist();
            });
        },
        init_instance_callback: function() { updateChecklist(); },
        // Image upload handler
        images_upload_handler: function(blobInfo, progress) {
            return new Promise(function(resolve, reject) {
                var formData = new FormData();
                formData.append('image', blobInfo.blob(), blobInfo.filename());
                
                fetch('upload.php', {
                    method: 'POST',
                    body: formData
                })
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    if (data.success) {
                        // Also set featured image if empty
                        var fi = document.getElementById('featuredImage');
                        if (!fi.value) fi.value = data.url;
                        resolve(data.url);
                    } else {
                        reject(data.error || 'Error al subir imagen');
                    }
                })
                .catch(function(err) {
                    reject('Error de conexión: ' + err.message);
                });
            });
        }
    });

    // ============================================================
    // SAVE FUNCTIONS
    // ============================================================
    
    function savePost(status) {
        document.getElementById('statusField').value = status;
        // TinyMCE auto-syncs to the textarea, but let's force it
        tinymce.triggerSave();
        document.getElementById('postForm').submit();
    }

    // ============================================================
    // IMAGE UPLOAD (external button, inserts via TinyMCE)
    // ============================================================
    
    document.getElementById('uploadBtn').addEventListener('click', function() {
        var fileInput = document.getElementById('imageUploader');
        var statusEl = document.getElementById('uploadStatus');

        if (!fileInput.files || !fileInput.files[0]) {
            statusEl.textContent = '❌ Selecciona un archivo primero.';
            statusEl.style.color = '#e74c3c';
            return;
        }

        var file = fileInput.files[0];
        var formData = new FormData();
        formData.append('image', file);

        statusEl.textContent = '⏳ Subiendo...';
        statusEl.style.color = '#f39c12';

        fetch('upload.php', {
            method: 'POST',
            body: formData
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                // Insert image into TinyMCE
                if (tinymce.activeEditor) {
                    tinymce.activeEditor.insertContent('<img src="' + data.url + '" alt="" style="max-width:100%;height:auto;" />');
                }
                // Set featured image if empty
                var fi = document.getElementById('featuredImage');
                if (!fi.value) fi.value = data.url;

                statusEl.textContent = '✅ Imagen insertada: ' + data.url;
                statusEl.style.color = '#73bd1e';
                fileInput.value = '';
            } else {
                statusEl.textContent = '❌ ' + (data.error || 'Error');
                statusEl.style.color = '#e74c3c';
            }
        })
        .catch(function(err) {
            statusEl.textContent = '❌ Error de conexión';
            statusEl.style.color = '#e74c3c';
        });
    });
    // ============================================================
    // CHECKLIST GEO
    // ============================================================

    function updateChecklist() {
        var title = (document.getElementById('postTitle').value || '').trim();
        var content = tinymce.get('editor') ? tinymce.get('editor').getContent() : (document.getElementById('editor').value || '');
        var img = (document.getElementById('featuredImage').value || '').trim();
        var text = content.replace(/<[^>]+>/g, ' ');
        var words = text.trim().split(/\s+/).filter(Boolean).length;

        var checks = {
            'title-len': title.length >= 40 && title.length <= 70,
            'first-para': false,
            'data': false,
            'h2': content.indexOf('<h2') !== -1,
            'lists': content.indexOf('<ul') !== -1 || content.indexOf('<ol') !== -1,
            'image': img !== '',
            'cta': content.indexOf('wa.me') !== -1 || content.indexOf('/contacto') !== -1,
            'words': words >= 300
        };

        var done = 0, total = 0;
        document.querySelectorAll('.geo-item').forEach(function(item) {
            var id = item.getAttribute('data-check');
            var isManual = item.classList.contains('geo-manual');
            var ok = isManual ? item.classList.contains('geo-done') : !!checks[id];
            var icon = item.querySelector('.geo-icon');
            if (ok) { icon.textContent = '✅'; item.classList.add('geo-ok'); done++; }
            else { icon.textContent = '⬜'; item.classList.remove('geo-ok'); }
            total++;
        });
        document.getElementById('geoProgress').textContent = done + ' de ' + total;
        document.getElementById('geoBar').style.width = Math.round(done / total * 100) + '%';
    }

    function toggleManual(item) {
        item.classList.toggle('geo-done');
        updateChecklist();
    }

    function insertCTA() {
        if (!tinymce.activeEditor) return;
        tinymce.activeEditor.insertContent('<p><a href="https://wa.me/573336026020?text=Hola%2C%20quiero%20informaci%C3%B3n%20sobre%20sus%20servicios">Escríbanos por WhatsApp</a></p>');
        updateChecklist();
    }

    document.getElementById('postTitle').addEventListener('input', updateChecklist);
    document.getElementById('featuredImage').addEventListener('input', updateChecklist);
    </script>
</body>
</html>
