# Simple-blog-in-symfony-4

 Installation process
 
 1. Clone the blog from github to your computer
 2. Run `composer install`
 3. Add database configuration `.env` file
 4. Run `php bin/console doctrine:database:create` to create the database
 5. Run `php bin/console doctrine:migrations:diff` to create migration
 6. Run `php bin/console doctrine:migrations:migrate` to execute the migration
 7. Run `php bin/console doctrine:fixtures:load` to insert the default user into database
 
 #### Wow
 Building completed now open your browser and login with username `aimal` and password `password`.
 
 If you are not using apache or Nginx just start the server by runing `php bin/console server:run` command.
