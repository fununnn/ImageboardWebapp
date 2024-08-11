<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($thread->getSubject()) ?></title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <h1><?= htmlspecialchars($thread->getSubject()) ?></h1>
    <div class="thread-content">
        <p><?= nl2br(htmlspecialchars($thread->getContent())) ?></p>
        <p>作成日時: <?= $thread->getCreatedAt() ?></p>
    </div>

    <h2>返信</h2>
    <ul class="reply-list">
        <?php foreach ($replies as $reply): ?>
            <li class="reply-item">
                <p><?= nl2br(htmlspecialchars($reply->getContent())) ?></p>
                <p>作成日時: <?= $reply->getCreatedAt() ?></p>
            </li>
        <?php endforeach; ?>
    </ul>

    <h2>返信を投稿</h2>
    <form id="reply-form" action="/thread/reply/<?= $thread->getId() ?>" method="POST">
        <div>
            <label for="content">返信内容：</label>
            <textarea id="content" name="content" required></textarea>
        </div>
        <button type="submit">返信を投稿</button>
    </form>

    <a href="/threads">スレッド一覧に戻る</a>
    <script src="/js/app.js"></script>
</body>
</html>