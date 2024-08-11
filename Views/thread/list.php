<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>スレッド一覧</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <h1>スレッド一覧</h1>
    <a href="/thread/create">新しいスレッドを作成</a>
    <ul class="thread-list">
        <?php foreach ($threads as $thread): ?>
            <li class="thread-item">
                <a href="/thread/view/<?= $thread->getId() ?>">
                    <h2><?= htmlspecialchars($thread->getSubject()) ?></h2>
                </a>
                <p>作成日時: <?= $thread->getCreatedAt() ?></p>
            </li>
        <?php endforeach; ?>
    </ul>
    <script src="/js/app.js"></script>
</body>
</html>