<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" data-name="vs/editor/editor.main" href="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.30.1/min/vs/editor/editor.main.min.css">
    <link rel="stylesheet" href="../public/css/style.css">
    <style>
        body { padding-top: 60px; }
        .navbar { background-color: #f8f9fa; }
        .footer { position: fixed; bottom: 0; width: 100%; height: 60px; line-height: 60px; background-color: #f5f5f5; }
    </style>
    <title><?= $title ?? 'Image Board' ?></title>
</head>
<body>
    <nav class="navbar navbar-expand-md navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="/">Upload an Image</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/snippet/create">Create Snippet</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/threads">掲示板</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/thread/create">新規スレッド作成</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <main class="container mt-5 mb-5">
        <!-- Main content -->
    </main>
    <script>
        var require = { paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.30.1/min/vs' } };
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.30.1/min/vs/loader.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../public/js/app.js"></script>
</body>
</html>