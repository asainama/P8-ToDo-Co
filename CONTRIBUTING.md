# Install for dev environment

## Fork the project

First, to contribute to this project, fork it.

## Install without Docker

### Prerequisites

| Stack      |
| ---------- |
| PHP7.1+    |
| MySQL      |
| composer   |

### Server

This web app needs a server. You could use any server like Nginx, Apache, [PHP server](https://www.php.net/manual/fr/features.commandline.webserver.php) or [Symfony server](https://symfony.com/doc/current/setup/symfony_server.html).

NB: The public HTML must be the public root (`/path/to/todo_co/public`).

## Configuration

[instructions](README.md)
Copy `./.env` to `./.env.local` and fill following parameters
    - APP_ENV
    - APP_SECRET
    - LOCALE_LANGUAGE
    - MAILER_DSN
    - DATABASE_URL

### Install libs and bundles

From the project root launch `composer install` to install composer libs and bundles.

### Create schema

From the project root, run the following commands:

```shell
    php bin/console doctrine:schema:create
```

# Merge requests

Please, use a clear and explicit title for your merge request. Same for the MR description.

To the development appears on the changelog, use gitmoji-changelog

See the changelog config for more.

NB: For commit's name, use a clear and sample name or squash it.

1. Create new branch

```shell
    git checkout -b nouvelle-branch
```

2. Push new branch sur votre fork

```shell
    git push origin nouvelle-branch
```

3. Open a new pull request

# Checks

For all your MR, run this from the root project:
Before commit please launches some tools to checks code like phpcs, phpstan, and more.
