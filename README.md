```markdown
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

1. マイグレーションの作成:
   ```
   php console code-gen migration <migration_name>
   ```
   例: `php console code-gen migration CreateUsersTable`

2. マイグレーションの実行:
   ```
   php console migrate
   ```

3. ロールバック:
   ```
   php console migrate --rollback [n]
   ```
   `n` は省略可能で、ロールバックするマイグレーションの数を指定します。

4. マイグレーションテーブルの初期化:
   ```
   php console migrate --init
   ```

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
```