## 環境構築  

Dockerビルド  

1.git clone https://github.com/usersugita/flea-market_app.git  
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
![スクリーンショット 2025-02-23 100526](https://github.com/user-attachments/assets/f5e18373-6df7-42ee-96dd-4948d3d60918)

## URL  
・開発環境：http://localhost/  
・phpMyadmin：http://localhost:8080/  
