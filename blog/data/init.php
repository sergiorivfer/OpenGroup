<?php
/**
 * Open Group Blog — Data initialization
 * Called once, creates data structure
 */
define('BLOG_DATA', __DIR__);
define('POSTS_DIR', BLOG_DATA . '/posts');
define('POSTS_INDEX', BLOG_DATA . '/posts.json');

// Create directories if they don't exist
if (!is_dir(BLOG_DATA)) mkdir(BLOG_DATA, 0755, true);
if (!is_dir(POSTS_DIR)) mkdir(POSTS_DIR, 0755, true);

// Create index file if it doesn't exist
if (!file_exists(POSTS_INDEX)) {
    file_put_contents(POSTS_INDEX, json_encode(['posts' => []], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Load all posts
function load_posts() {
    if (!file_exists(POSTS_INDEX)) return [];
    $data = json_decode(file_get_contents(POSTS_INDEX), true);
    return $data['posts'] ?? [];
}

// Save posts index
function save_posts($posts) {
    $data = ['posts' => $posts];
    file_put_contents(POSTS_INDEX, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Load a single post by slug
function load_post($slug) {
    $slug = basename($slug); // security
    $file = POSTS_DIR . '/' . $slug . '.json';
    if (!file_exists($file)) return null;
    return json_decode(file_get_contents($file), true);
}

// Get published posts only
function get_published_posts() {
    $all = load_posts();
    return array_filter($all, function($p) { return ($p['status'] ?? 'draft') === 'published'; });
}

// Get posts for admin (all)
function get_all_posts() {
    return load_posts();
}

// Save a post (create or update)
function save_post($data) {
    $posts = load_posts();
    $slug = $data['slug'];
    $idx = -1;
    
    foreach ($posts as $i => $p) {
        if ($p['slug'] === $slug) { $idx = $i; break; }
    }
    
    $post_data = [
        'slug' => $slug,
        'title' => $data['title'],
        'content' => $data['content'],
        'excerpt' => $data['excerpt'] ?? mb_substr(strip_tags($data['content']), 0, 200) . '...',
        'author' => $data['author'] ?? 'Open Group',
        'date' => $data['date'] ?? date('Y-m-d'),
        'status' => $data['status'] ?? 'draft',
        'featured_image' => $data['featured_image'] ?? '',
        'category' => $data['category'] ?? 'General'
    ];
    
    // Save individual post file
    $file = POSTS_DIR . '/' . $slug . '.json';
    file_put_contents($file, json_encode($post_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    // Update index
    $index_entry = [
        'slug' => $slug,
        'title' => $data['title'],
        'excerpt' => $post_data['excerpt'],
        'author' => $post_data['author'],
        'date' => $post_data['date'],
        'status' => $post_data['status'],
        'featured_image' => $post_data['featured_image'],
        'category' => $post_data['category']
    ];
    
    if ($idx >= 0) {
        $posts[$idx] = $index_entry;
    } else {
        array_unshift($posts, $index_entry); // newest first
    }
    
    save_posts($posts);
    return true;
}

// Delete a post
function delete_post($slug) {
    $slug = basename($slug);
    $file = POSTS_DIR . '/' . $slug . '.json';
    if (file_exists($file)) unlink($file);
    
    $posts = load_posts();
    $posts = array_filter($posts, function($p) use ($slug) { return $p['slug'] !== $slug; });
    $posts = array_values($posts);
    save_posts($posts);
    return true;
}
