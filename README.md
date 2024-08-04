![Videotogif](https://github.com/user-attachments/assets/1b4dfbec-d3ea-413b-8825-e5ab36944c16)

# コードスニペット共有サービス

これは、Pastebinに似たシンプルなウェブアプリケーションで、ユーザーが簡単にコードスニペットやプレーンテキストを共有できるようにします。

## 特徴

- ユーザーアカウント不要でコードスニペットの作成と共有が可能
- 様々なプログラミング言語に対応したシンタックスハイライト
- スニペットの有効期限設定
- 各スニペットに対するユニークURL生成
- 共有されたスニペットの閲覧

## 使用技術

- PHP 8.0
- MySQL
- HTML/CSS（Bootstrap 5）
- JavaScript（シンタックスハイライト用Highlight.js）

## インストール

1. リポジトリをクローン：
   git clone https://github.com/yourusername/code-snippet-sharing.git

2. プロジェクトディレクトリに移動：
   cd code-snippet-sharing

3. 依存関係のインストール（Composerを使用している場合）：
   composer install

4. データベースのセットアップ：
   - MySQLデータベースを作成
   - `Database/MySQLWrapper.php`のデータベース設定を更新
   - マイグレーションを実行：
     php console migrate

5. Webサーバー（Apache、Nginxなど）を設定し、`public`ディレクトリを指すようにする。

6. ウェブブラウザからアプリケーションにアクセス。

## 使用方法

1. ホームページにアクセスして新しいスニペットを作成。
2. テキストエリアにコードまたはテキストを入力。
3. プログラミング言語を選択（シンタックスハイライト用）。
4. 有効期限を選択（任意）。
5. 「スニペットを作成」をクリックしてユニークURLを生成。
6. URLを他の人と共有してスニペットを表示。

## アクセスURL

- ホームページ: http://yourdomain.com/
- スニペット作成: http://yourdomain.com/snippet/create
- スニペット閲覧: http://yourdomain.com/snippet/view/{unique_id}











# BlogBook マイグレーション管理ツール

## セットアップ

1. プロジェクトのルートディレクトリに `.env` ファイルを作成し、以下のようにデータベース接続情報を設定します：

   ```
   DATABASE_USER="your_username"
   DATABASE_USER_PASSWORD="your_password"
   DATABASE_NAME="your_database_name"
   DATABASE_HOST="localhost"
   ```

   必ず、実際の値に置き換えてください。

2. データベースを作成します（存在しない場合）：

   MySQLにログインし、以下のコマンドを実行します：
   ```sql
   CREATE DATABASE your_database_name;
   ```

3. 初期セットアップを実行します：

   ```
   php init-app.php
   ```

   これにより、必要なテーブルが作成されます。

## 基本的な使い方

すべてのコマンドは `console` ファイルを通じて実行します。

1. SQLファイルの作成:
   /Database/Examplesの中に、新しく作成したいSQLファイルを作成します。SQLファイルは常に01_〇〇.sqlとします。数字の若い順番にマイグレーションします。次に読むファイルは02_...です。
   例として、01_User.sqlを作成します。
   ```
   CREATE TABLE IF NOT EXISTS User (
    userID INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
    subscription_status VARCHAR(255),
    subscriptionCreatedAt DATETIME,
    subscriptionEndAt DATETIME);
   ```

2. マイグレーションの作成:
   ```
   php console code-gen migration --name <migration_name>
   ```
   例: `php console code-gen migration --name User`

   なお、以下のようにすると全ファイルを一気にマイグレーションできます。
   ```
   php console code-gen migration --name all
   ```
   
3. マイグレーションテーブルの初期化:
   ```
   php console migrate --init
   ```


4. マイグレーションの実行:
   ```
   php console migrate
   ```

5. ロールバック:
   ```
   php console migrate --rollback [n]
   ```
   `n` は省略可能で、ロールバックするマイグレーションの数を指定します。

## その他の機能

- データベースバックアップ:
  ```
  php console db-backup [--output <path>]
  ```

- データベースのワイプ:
  ```
  php console db-wipe [--backup]
  ```

- 新しいコマンドの生成:
  ```
  php console generate-commands <command_name>
  ```

## ヘルプの表示

各コマンドの詳細なヘルプを表示するには:

```
php console <command> --help
```

例:
```
php console migrate --help
```

## ファイル構造の説明

- `Commands/`: すべてのコマンドクラスが格納されています。
- `Database/`: データベース関連のファイルが格納されています。
  - `Examples/`: SQLファイルの例が格納されています。
  - `Migrations/`: 生成されたマイグレーションファイルが格納されます。
- `Exceptions/`: カスタム例外クラスが格納されています。
- `Helpers/`: ユーティリティクラスが格納されています。
- `backups/`: データベースバックアップが保存されます。
- `console`: コマンドを実行するためのエントリーポイントです。
- `init-app.php`: 初期セットアップを行うスクリプトです。

注意: コマンドを実行する前に、必ず正しいディレクトリ（プロジェクトのルートディレクトリ）にいることを確認してください。

このツールを使用する際は、データベースのバックアップを定期的に取ることをお勧めします。