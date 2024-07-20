<?php
use Database\MySQLWrapper;

echo "Starting database setup...\n";
$mysqli = new MySQLWrapper();

try {
    // トランザクション開始
    $mysqli->begin_transaction();

    echo "Attempting to create tables...\n";
    $sqlFiles = glob(__DIR__ . '/Examples/*.sql');
    foreach ($sqlFiles as $sqlFile) {
        $filename = basename($sqlFile);
        echo "Attempting to execute $filename...\n";
        $sql = file_get_contents($sqlFile);
        $result = $mysqli->query($sql);
        if ($result === false) {
            throw new Exception('Could not execute query: ' . $mysqli->error);
        } else {
            echo "Successfully executed $filename\n";
        }
    }

    // 外部キーの追加
    $fk_add_query = "
        ALTER TABLE Post
        ADD CONSTRAINT fk_userID_Post
        FOREIGN KEY (userID)
        REFERENCES User(userID);

        ALTER TABLE Comment
        ADD CONSTRAINT fk_userID_Comment
        FOREIGN KEY (userID)
        REFERENCES User(userID);

        ALTER TABLE Comment
        ADD CONSTRAINT fk_postID_Comment
        FOREIGN KEY (postID)
        REFERENCES Post(postID);

        ALTER TABLE PostLike
        ADD CONSTRAINT fk_userID_PostLike
        FOREIGN KEY (userID)
        REFERENCES User(userID);

        ALTER TABLE PostLike
        ADD CONSTRAINT fk_PostID_PostLike
        FOREIGN KEY (postID)
        REFERENCES Post(postID);

        ALTER TABLE CommentLike
        ADD CONSTRAINT fk_userID_CommentLike
        FOREIGN KEY (userID)
        REFERENCES User(userID);

        ALTER TABLE CommentLike
        ADD CONSTRAINT fk_commentID_CommentLike
        FOREIGN KEY (commentID)
        REFERENCES Comment(commentID);
    ";

    $mysqli->multi_query($fk_add_query);
    do {
        if ($result = $mysqli->store_result()) {
            $result->free();
        }
    } while ($mysqli->more_results() && $mysqli->next_result());

    // トランザクションのコミット
    $mysqli->commit();
    echo "All queries executed successfully. Changes committed.\n";

} catch (Exception $e) {
    // エラーが発生した場合、トランザクションをロールバック
    $mysqli->rollback();
    echo "An error occurred. All changes have been rolled back: " . $e->getMessage() . "\n";
} finally {
    // $mysqliオブジェクトをクローズしない
    // データベース接続は init-app.php で閉じる
}