<?php
spl_autoload_register(function($className) {
    $file = __DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use Database\MySQLWrapper;

function constraintExists($mysqli, $tableName, $constraintName) {
    $query = "SELECT COUNT(*) as count FROM information_schema.table_constraints 
              WHERE table_schema = DATABASE() 
              AND table_name = '$tableName' 
              AND constraint_name = '$constraintName'";
    $result = $mysqli->query($query);
    $row = $result->fetch_assoc();
    return $row['count'] > 0;
}

function ensureTableExists($mysqli, $tableName, $createTableSQL) {
    $query = "SHOW TABLES LIKE '$tableName'";
    $result = $mysqli->query($query);
    if ($result->num_rows == 0) {
        if ($mysqli->query($createTableSQL) === FALSE) {
            throw new Exception("Could not create table $tableName: " . $mysqli->error);
        }
        echo "Created table $tableName\n";
    } else {
        echo "Table $tableName already exists, skipping creation\n";
    }
}

function ensureColumnExists($mysqli, $tableName, $columnName, $columnDefinition) {
    $query = "SHOW COLUMNS FROM $tableName LIKE '$columnName'";
    $result = $mysqli->query($query);
    if ($result->num_rows == 0) {
        $alterQuery = "ALTER TABLE $tableName ADD COLUMN $columnName $columnDefinition";
        // 主キーの場合は別の処理を行う
        if (strpos($columnDefinition, 'PRIMARY KEY') !== false) {
            // 主キーが既に存在するか確認
            $pkQuery = "SHOW KEYS FROM $tableName WHERE Key_name = 'PRIMARY'";
            $pkResult = $mysqli->query($pkQuery);
            if ($pkResult->num_rows == 0) {
                $alterQuery = "ALTER TABLE $tableName ADD COLUMN $columnName " . str_replace(' PRIMARY KEY', '', $columnDefinition);
                $alterQuery .= ", ADD PRIMARY KEY ($columnName)";
            } else {
                echo "Primary key already exists in table $tableName, skipping\n";
                return;
            }
        }
        if ($mysqli->query($alterQuery) === FALSE) {
            throw new Exception("Could not add column $columnName to table $tableName: " . $mysqli->error);
        }
        echo "Added column $columnName to table $tableName\n";
    } else {
        echo "Column $columnName already exists in table $tableName, skipping\n";
    }
}

try {
    $opts = getopt('', ['migrate']);
    if (isset($opts['migrate'])) {
        printf("Database migration started.\n");
        $mysqli = new MySQLWrapper();

        // トランザクション開始
        $mysqli->begin_transaction();

        echo "Starting database setup...\n";
        echo "Attempting to create tables...\n";
        
        // Categoryテーブルが存在することを確認
        ensureTableExists($mysqli, 'Category', "
            CREATE TABLE Category (
                categoryID INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL
            )
        ");

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
        $fk_queries = [
            ["Category", "categoryID", "INT AUTO_INCREMENT PRIMARY KEY", ""],
            ["Post", "userID", "INT", "ALTER TABLE Post ADD CONSTRAINT fk_userID_Post FOREIGN KEY (userID) REFERENCES User(userID)"],
            ["Comment", "userID", "INT", "ALTER TABLE Comment ADD CONSTRAINT fk_userID_Comment FOREIGN KEY (userID) REFERENCES User(userID)"],
            ["Comment", "postID", "INT", "ALTER TABLE Comment ADD CONSTRAINT fk_postID_Comment FOREIGN KEY (postID) REFERENCES Post(postID)"],
            ["PostLike", "userID", "INT", "ALTER TABLE PostLike ADD CONSTRAINT fk_userID_PostLike FOREIGN KEY (userID) REFERENCES User(userID)"],
            ["PostLike", "postID", "INT", "ALTER TABLE PostLike ADD CONSTRAINT fk_PostID_PostLike FOREIGN KEY (postID) REFERENCES Post(postID)"],
            ["CommentLike", "userID", "INT", "ALTER TABLE CommentLike ADD CONSTRAINT fk_userID_CommentLike FOREIGN KEY (userID) REFERENCES User(userID)"],
            ["CommentLike", "commentID", "INT", "ALTER TABLE CommentLike ADD CONSTRAINT fk_commentID_CommentLike FOREIGN KEY (commentID) REFERENCES Comment(commentID)"],
            ["UserSetting", "userID", "INT", "ALTER TABLE UserSetting ADD CONSTRAINT fk_userID_UserSetting FOREIGN KEY (userID) REFERENCES User(userID)"],
            ["Post", "CategoryID", "INT", "ALTER TABLE Post ADD CONSTRAINT fk_CategoryID_Post FOREIGN KEY (CategoryID) REFERENCES Category(categoryID)"],
            ["PostTag", "postID", "INT", "ALTER TABLE PostTag ADD CONSTRAINT fk_postID_PostTag FOREIGN KEY (postID) REFERENCES Post(postID)"],
            ["PostTag", "tagID", "INT", "ALTER TABLE PostTag ADD CONSTRAINT fk_tagID_PostTag FOREIGN KEY (tagID) REFERENCES Tag(tagID)"]
        ];

        foreach ($fk_queries as $query) {
            $tableName = $query[0];
            $columnName = $query[1];
            $columnDefinition = $query[2];
            $constraintQuery = $query[3];

            ensureColumnExists($mysqli, $tableName, $columnName, $columnDefinition);

            if (!empty($constraintQuery)) {
                preg_match('/ADD CONSTRAINT (\w+)/', $constraintQuery, $matches);
                $constraintName = $matches[1];
                
                if (!constraintExists($mysqli, $tableName, $constraintName)) {
                    if ($mysqli->query($constraintQuery) === FALSE) {
                        throw new Exception('Could not add constraint: ' . $mysqli->error);
                    }
                    echo "Added constraint $constraintName to table $tableName\n";
                } else {
                    echo "Constraint $constraintName already exists on table $tableName, skipping...\n";
                }
            }
        }

        // トランザクションのコミット
        $mysqli->commit();
        echo "All queries executed successfully. Changes committed.\n";
        
        printf("Database migration completed.\n");
    }
    
    $mysqli = new MySQLWrapper();
    $charset = $mysqli->get_charset();
    if ($charset === null) {
        throw new \Exception('Charset could not be read');
    }
    printf("%s's charset: %s%s", $mysqli->getDatabaseName(), $charset->charset, PHP_EOL);
    printf("collation: %s%s", $charset->collation, PHP_EOL);
} catch (\Exception $e) {
    if (isset($mysqli)) {
        $mysqli->rollback();
    }
    printf("An error occurred: %s%s", $e->getMessage(), PHP_EOL);
    exit(1); // エラーが発生した場合はスクリプトを終了
} finally {
    if (isset($mysqli) && $mysqli instanceof MySQLWrapper) {
        $mysqli->close();
    }
}