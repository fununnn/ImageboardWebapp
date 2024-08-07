# Online Image Hosting Service

このプロジェクトは、ユーザーアカウントを必要とせずに画像をアップロード、共有、表示できるオンライン画像ホスティングサービスです。

## 主な機能

- 画像のアップロード
- 画像の表示
- 画像の削除

## 使用方法

### メインページへのアクセス

サービスのメインページにアクセスするには、以下のURLを使用してください：

    http://[あなたのドメインまたはIPアドレス]/

例えば、ローカル環境で実行している場合：

    http://localhost/

このページから画像のアップロードを行うことができます。

### 画像のアップロード

1. メインページにアクセスします。
2. 「Choose File」ボタンをクリックして画像を選択します。
3. 「Upload」ボタンをクリックしてアップロードを開始します。
4. アップロード成功後、画像のURLが表示されます。

### 画像の表示

アップロードされた画像を表示するには、以下のURLフォーマットを使用します：

    http://[あなたのドメインまたはIPアドレス]/image/[画像のユニークURL]

### 画像の削除

画像表示ページの「Delete Image」ボタンをクリックすることで、画像を削除できます。

### スニペットの作成

スニペットを作成するには、以下のURLにアクセスします：

    http://[あなたのドメインまたはIPアドレス]/snippet/create

### スニペットの表示

作成されたスニペットを表示するには、以下のURLフォーマットを使用します：

    http://[あなたのドメインまたはIPアドレス]/snippet/view/[スニペットのユニークURL]

## 技術スタック

- PHP 8.0+
- MySQL
- HTML/CSS
- JavaScript (フロントエンドの機能用)

## セットアップ

1. プロジェクトをクローンまたはダウンロードします。
2. Webサーバー（Apache、Nginxなど）とPHPをセットアップします。
3. MySQLデータベースを作成し、必要なテーブルをセットアップします。
4. `.env`ファイルを作成し、データベース接続情報を設定します。
5. Webサーバーのドキュメントルートを`public`ディレクトリに設定します。

## 注意事項

- このサービスは、デモンストレーション目的で作成されています。実際の運用には、さらなるセキュリティ対策が必要です。
- アップロードされた画像やスニペットは、定期的に削除されることがあります。


https://github.com/user-attachments/assets/34b2baf1-f5d0-4a95-9d0a-8dca7aa4a44f

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

- cronジョブの設定:
  ```
  php console setup:cron --time 00:00
  ```
  これにより、毎日午前0時にCleanupOldImagesコマンドが実行されます。

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