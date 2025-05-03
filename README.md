## 環境構築  

Dockerビルド  

1.git clone https://github.com/usersugita/Attendance_App.git  
2.docker-compose up -d --build  
＊MYSQLは、OSによって起動しない場合がありますのでそれぞれのPCに合わせて docker-compose.yml ファイルを編集してください。  

Laravel環境構築  
1.docker-compose exec php bash  
2.composer install  
3.「.env.example」ファイルを 「.env」ファイルに命名を変更。または、新しく.envファイルを作成  
4..envに以下の環境変数を追加  
DB_CONNECTION=mysql  
DB_HOST=mysql  
DB_PORT=3306  
DB_DATABASE=laravel_db  
DB_USERNAME=laravel_user  
DB_PASSWORD=laravel_pass  

MAIL_MAILER=smtp  
MAIL_HOST=mailhog  
MAIL_PORT=1025  
MAIL_USERNAME=null  
MAIL_PASSWORD=null  
MAIL_ENCRYPTION=null  
MAIL_FROM_ADDRESS=no-reply@example.com  
MAIL_FROM_NAME="Attendance App"  
5.アプリケーションキーの作成  
php artisan key:generate  
6.マイグレーションの実行  
php artisan migrate  
7.シーディングの実行  
php artisan db:seed  
  
## 使用技術  

・PHP 7.4.9  
・Laravel 8.83.27  
・Mysql 8.0.26
## ER図  
![スクリーンショット 2025-05-03 144931](https://github.com/user-attachments/assets/8356196c-f79a-4c51-ada1-d76e8cf4ef74)


## URL  
・スタッフログイン：http://localhost/login  
・管理者ログイン：http://localhost/admin/login 
・phpMyadmin：http://localhost:8080/  
