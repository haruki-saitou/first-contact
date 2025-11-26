# 環境構築

## Dockerビルド
 ・ git clone <git@github.com>:haruki-saitou/first-contact.git
 ・ docker-compose up -d --build

## Laravel環境構築
 ・ docker-compose exec php bash
 ・ composer install
 ・ cp .env.exemple. .env , 環境変数を適宣変更
 ・ php artisan key:generate
 ・ php artisan migrate
 ・ php artisan db:seed

 ## 開発環境
 ・ お問合せ画面 : http://localhost/
 ・ ユーザー登録 : http://localhost/register
 ・ ログイン : http://localhost/login
 ・ お問合せ内容確認 : http://localhost/contacts/confirm
 ・ 送信完了画面 : http://localhost/complete
 ・ 送信履歴 : http://localhost/contact/history
 ・ phpMyAdmin : http://localhost:8080/

## 使用技術(実行環境)
・ PHP 8.1.33
・ MySQL 8.0
・ nginx 1.21.1
・ laravel : 8.83.8

## ER図

#### users テーブル (ユーザー認証情報)

| カラム名 | データ型 | 特徴 | 役割 |
| :--- | :--- | :--- | :--- |
| **id** | BIGINT | プライマリキー | ユーザーID |
| name | VARCHAR | | ユーザー名 |
| email | VARCHAR | UNIQUE | メールアドレス（ログインID） |
| password | VARCHAR | | パスワード（ハッシュ化） |
| email_verified_at | TIMESTAMP | NULL許可 | メール確認日時 |
| remember_token | VARCHAR | | ログイン維持トークン |
| created_at/updated_at | TIMESTAMP | | 作成・更新日時 |

#### contacts テーブル (お問い合わせ内容)

| カラム名 | データ型 | 特徴 | 役割 |
| :--- | :--- | :--- | :--- |
| **id** | BIGINT | プライマリキー | お問い合わせID |
| name | VARCHAR | | 問い合わせ主の名前 |
| email | VARCHAR | | 問い合わせ主のメール |
| tel | VARCHAR | 長さ11 | 電話番号 |
| content | TEXT | NULL許可 | 問い合わせ内容 |
| created_at/updated_at | TIMESTAMP | | 作成・更新日時 |
