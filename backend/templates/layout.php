<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'BigfootCMS' ?></title>
</head>
<body>
    <?= $content ?? '' ?>
    <div id="app" style="position:fixed;top:0;right:0;bottom:0;width:300px;background:white;box-shadow:-2px 0 5px rgba(0,0,0,0.1);z-index:1000;overflow-y:auto"></div>
    <script type="module" src="http://192.168.50.126:5173/@vite/client"></script>
    <script type="module" src="http://192.168.50.126:5173/src/main.ts"></script>
</body>
</html> 