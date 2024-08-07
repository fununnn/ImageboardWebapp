https://github.com/user-attachments/assets/5188fa6f-b840-4d03-987a-57a8f4f1ada6

# オンライン画像ホスティングサービス & コードスニペット共有サービス

このプロジェクトは、ユーザーアカウントを必要とせずに画像をアップロード、共有、表示できるオンライン画像ホスティングサービスと、コードスニペットを共有できるサービスを組み合わせたものです。また、データベースマイグレーション管理ツールも含まれています。

## 主な機能

- 画像のアップロード、表示、削除
- コードスニペットの作成、共有、表示
- データベースマイグレーション管理
- cronジョブによる定期的な古い画像の自動削除

## 技術スタック

- PHP 8.0+
- MySQL
- HTML/CSS (Bootstrap 5)
- JavaScript (フロントエンドの機能用、Highlight.js for シンタックスハイライト)

## セットアップ

1. プロジェクトをクローンまたはダウンロードします。
2. Webサーバー（Apache、Nginxなど）とPHPをセットアップします。
3. MySQLデータベースを作成します。
4. プロジェクトのルートディレクトリに `.env` ファイルを作成し、以下のように設定します：

```
DATABASE_USER="your_username"
DATABASE_USER_PASSWORD="your_password"
DATABASE_NAME="your_database_name"
DATABASE_HOST="localhost"
```

5. 初期セットアップを実行します：

```
php init-app.php
```

6. Webサーバーのドキュメントルートを `public` ディレクトリに設定します。

## 使用方法

### 画像ホスティングサービス

- メインページ: `http://[domain]/`
- 画像アップロード: メインページから行います
- 画像表示: `http://[domain]/media/image/[unique_id]`
- 画像削除: 画像表示ページの「Delete Image」ボタンをクリック

### コードスニペット共有サービス

- スニペット作成: `http://[domain]/snippet/create`
- スニペット表示: `http://[domain]/snippet/view/[unique_id]`

## データベースマイグレーション管理

すべてのコマンドは `console` ファイルを通じて実行します。

### 基本的なマイグレーションコマンド

1. マイグレーションの作成:
```
php console code-gen migration --name <migration_name>
```
または全ファイルを一度にマイグレーション:
```
php console code-gen migration --name all
```

2. マイグレーションテーブルの初期化:
```
php console migrate --init
```

3. マイグレーションの実行:
```
php console migrate
```

4. ロールバック:
```
php console migrate --rollback [n]
```

### その他のデータベース関連コマンド

- データベースバックアップ:
```
php console db-backup [--output <path>]
```

- データベースのワイプ:
```
php console db-wipe [--backup]
```

## cronジョブの設定

古い画像を自動的に削除するcronジョブを設定するには：

```
php console setup:cron --time HH:MM
```

例: 毎日午前0時に実行する場合
```
php console setup:cron --time 00:00
```

## 新しいコマンドの生成

新しいコマンドを生成するには：

```
php console generate-commands <command_name>
```

## ヘルプの表示

各コマンドの詳細なヘルプを表示するには：

```
php console <command> --help
```

## ファイル構造

- `Commands/`: すべてのコマンドクラス
- `Database/`: データベース関連ファイル
  - `Examples/`: SQLファイルの例
  - `Migrations/`: 生成されたマイグレーションファイル
- `Exceptions/`: カスタム例外クラス
- `Helpers/`: ユーティリティクラス
- `Models/`: データモデルクラス
- `Response/`: レスポンス関連クラス
- `Routing/`: ルーティング設定
- `Views/`: ビューテンプレート
- `backups/`: データベースバックアップ
- `public/`: Webサーバーのドキュメントルート
- `uploads/`: アップロードされた画像ファイル
- `console`: コマンド実行のエントリーポイント
- `init-app.php`: 初期セットアップスクリプト


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