<?php

require_once __DIR__ . '/../vendor/autoload.php';

$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);

// API endpoints
if (str_starts_with($path, '/api/')) {
    header('Content-Type: application/json');
    // TODO: Add your API endpoints here
    exit;
}

// Regular site routes
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="BigfootCMS - A modern content management system">
    <title><?= $title ?? 'BigfootCMS' ?></title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; line-height: 1.6; max-width: 1200px; margin: 0 auto; padding: 2rem; }
        .navigation { margin-bottom: 2rem; padding: 1rem 0; border-bottom: 1px solid #eee; }
        .navigation a { margin-right: 1rem; color: #333; text-decoration: none; }
        .navigation a:hover { text-decoration: underline; }
        .content { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .sidebar { padding: 1rem; }
        .footer { margin-top: 2rem; padding: 1rem 0; border-top: 1px solid #eee; text-align: center; }
    </style>
</head>
<body>
    <!-- Navigation container -->
    <nav id="main-nav" class="navigation">
        <a href="/">Home</a>
        <a href="/about">About</a>
        <a href="/contact">Contact</a>
    </nav>

    <!-- Main content area -->
    <main id="main-content" class="content">
        <?php
        switch ($path) {
            case '/':
                echo '<h1>Welcome to BigfootCMS</h1>
                     <p>Your site is ready! This is a modern revival of the classic CMS.</p>
                     <h2>Features</h2>
                     <ul>
                         <li>Simple and fast</li>
                         <li>SQLite database</li>
                         <li>PHP 8.3 compatible</li>
                         <li>Modern architecture</li>
                     </ul>';
                break;
            default:
                http_response_code(404);
                echo '<h1>404 Not Found</h1><p>The requested page could not be found.</p>';
                break;
        }
        ?>
    </main>

    <!-- Admin widget mount point -->
    <div id="app" style="position:fixed;top:0;right:0;bottom:0;width:300px;background:white;box-shadow:-2px 0 5px rgba(0,0,0,0.1);z-index:1000;overflow-y:auto"></div>

    <!-- Debug output -->
    <script>
        console.log('Debug: Mount point exists:', !!document.getElementById('app'));
        window.ADMIN_CONFIG = {
            apiEndpoint: '/api',
            currentPath: <?= json_encode($path) ?>,
            pageData: {
                path: <?= json_encode($path) ?>,
                title: <?= json_encode($title ?? 'BigfootCMS') ?>,
                content: ''
            }
        };
    </script>

    <!-- Vite dev server scripts -->
    <script type="module">
        console.log('Debug: Loading Vite client...');
        import RefreshRuntime from "http://192.168.50.126:5173/@vite/client";
        console.log('Debug: Vite client loaded');
        RefreshRuntime.injectIntoGlobalHook(window);
        window.$RefreshReg$ = () => {};
        window.$RefreshSig$ = () => (type) => type;
        window.__vite_plugin_react_preamble_installed__ = true;
    </script>
    <script type="module">
        console.log('Debug: Loading main app...');
        import("http://192.168.50.126:5173/src/main.ts")
            .then(() => console.log('Debug: Main app loaded'))
            .catch(err => console.error('Debug: Failed to load main app:', err));
    </script>
</body>
</html>
<?php
echo ob_get_clean(); 