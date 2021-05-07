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

## How to Contribute

1. Create a issue with a description. Ensuring you answer the questions: Why? What? How?
2. Check if the problem already exists.
3. Make sure the project compile.
4. Create test both the code (unit/functional tests), UI tests and document your code.
5. Make sur tests coverage superior 80%
6. Test the quality of the code with Codacy and obtain a minimum grade of B.
7. Open a merge request
8. Participate in the code review: Once you submit the merge branch your code will be analyzed. Be sure to respond to comments.

## Checks

For all your MR, run this from the root project:
Before commit please launches some tools to checks code like phpcs, phpstan, and more.
