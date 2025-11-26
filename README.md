# first-contact

# 環境構築

## Dockerビルド

 ・ git clone <git@github.com>:haruki-saitou/first-contact.git <br>
 ・ docker-compose up -d --build

## Laravel環境構築

 ・ docker-compose exec php bash <br>
 ・ composer install <br>
 ・ cp .env.exemple. .env , 環境変数を適宣変更 <br>
 ・ php artisan key:generate <br>
 ・ php artisan migrate <br>
 ・ php artisan db:seed <br>

## 開発環境

 ・ お問合せ画面 : <http://localhost/> <br>
 ・ ユーザー登録 : <http://localhost/register> <br>
 ・ ログイン : <http://localhost/login> <br>
 ・ お問合せ内容確認 : <http://localhost/contacts/confirm> <br>
 ・ 送信完了画面 : <http://localhost/complete> <br>
 ・ 送信履歴 : <http://localhost/contact/history> <br>
 ・ phpMyAdmin : <http://localhost:8080/> <br>

## 使用技術(実行環境)

・ PHP 8.1.33 <br>
・ MySQL 8.0 <br>
・ nginx 1.21.1 <br>
・ laravel : 8.83.8 <br>

## ER図

![ER Diagram](assets/er_diagram.png) <br>

#### users Table (User Authentication Information)

| Column Name | Data Type | Key/Constraints | Description |
| :--- | :--- | :--- | :--- |
| **id** | BIGINT | Primary Key | User ID |
| name | VARCHAR | | User Name |
| email | VARCHAR | UNIQUE | Email Address (Login ID) |
| password | VARCHAR | | Password (Hashed) |
| email_verified_at | TIMESTAMP | Nullable | Email Verification Timestamp |
| remember_token | VARCHAR | | Login Persistence Token |
| created_at/updated_at | TIMESTAMP | | Record Creation/Update Timestamps |

#### contacts Table (Contact Form Submissions)

| Column Name | Data Type | Key/Constraints | Description |
| :--- | :--- | :--- | :--- |
| **id** | BIGINT | Primary Key | Contact ID |
| name | VARCHAR | | Submitter's Name |
| email | VARCHAR | | Submitter's Email |
| tel | VARCHAR | Length 11 | Phone Number |
| content | TEXT | Nullable | Submission Content/Message |
| created_at/updated_at | TIMESTAMP | | Record Creation/Update Timestamps |
