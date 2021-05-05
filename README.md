# ToDoList

Base du projet #8 : Améliorez un projet existant

https://openclassrooms.com/projects/ameliorer-un-projet-existant-1


## Environnement

* Symfony 5.2
* Composer 2.0.7
* PHP 7.2.1
* MYSQL  8.0.19
* PHPUnit 9.5.4

## Installation

1. Clonez le répertoire

    ```
        git clone https://github.com/asainama/P8-ToDo-Co.git
    ```

2. Configuration du env.local

    Créer un fichier .env.local qui devra avoir:

    ```
        APP_ENV=dev
        APP_SECRET=
        DATABASE_URL="mysql://db_user:db_password@database_address/db_name?serverVersion=VERSION"
        MAILER_URL=smtp://localhost:1025
    ```

3. Installer le projet à l'aide de la commande

    ```
        composer install
    ```

4. Créer la base de données

    ```
        php bin/console doctrine:database:create
    ```

5. Utiliser les migrations pour créer les tables

    ```
        php bin/console doctrine:migrations:migrate
    ```

6. Afin de ne pas avoir un projet vierge installer les fixtures

    ```
        php bin/console doctrine:fixtures:load
    ```

7. Si vous avez rajouter les fixtures l'utilisateur
    > admin@admin.fr avec le mot de passe admin est crée

8. Lancer le jeu de test à l'aide de phpUnit
    ```
        php bin/console doctrine:database:drop --force --env=test
        php bin/console doctrine:database:create --env=test
        php bin/console doctrine:migrations:migrate --env=test
        php bin/console doctrine:fixtures:load --env=test
        php ./vendor/bin/phpunit --testdox
        php ./vendor/bin/phpunit --coverage-html ./report  # Génère un rapport de couverture   
    ```

9.  Félicitation le projet est maintenant installé.
