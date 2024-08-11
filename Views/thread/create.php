<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新しいスレッドを作成</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <h1>新しいスレッドを作成</h1>
    <form id="create-thread-form" action="/thread/create" method="POST">
        <div>
            <label for="subject">タイトル：</label>
            <input type="text" id="subject" name="subject" required>
        </div>
        <div>
            <label for="content">本文：</label>
            <textarea id="content" name="content" required></textarea>
        </div>
        <button type="submit">スレッドを作成</button>
    </form>
    <a href="/threads">スレッド一覧に戻る</a>
    <script src="/js/app.js"></script>
</body>
</html>