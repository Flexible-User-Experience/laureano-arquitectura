Laureano Arquitectura v1.0
==========================

A fresh new Symfony 5.4 LTS webapp project to manage Laureano Arquitectura enterprise.

---

#### Installation requirements

 * PHP 8.2+
 * MySQL 8.0+
 * Composer 2.0+
 * Node 18.0+
 * Yarn 1.0+
 * Git 2.0+

#### Installation instructions

```bash
$ git git@github.com:Flexible-User-Experience/laureano-arquitectura.git
$ cd laureano-arquitectura
$ cp env.dist .env
$ nano .env
$ composer install
$ yarn install
$ yarn encore prod
$ php bin/console doctrine:database:create --env=prod
$ php bin/console doctrine:migrations:migrate --env=prod
$ php bin/console messenger:consume async
```

Remember to edit `.env` config file according to your system environment needs.

#### Testing suite commands

```bash
$ ./scripts/developer-tools/test-database-reset.sh
$ ./scripts/developer-tools/run-test.sh
```

#### Developer important notes

* Remember to properly configure Supervisor message queue consumers https://symfony.com/doc/current/messenger.html#messenger-supervisor

#### Code Style notes

Execute following link to be sure that php-cs-fixer will be applied automatically before every commit. Please, check https://github.com/FriendsOfPHP/PHP-CS-Fixer to install it globally (manual) in your system.

```bash
$ ln -s ../../scripts/githooks/pre-commit .git/hooks/pre-commit
```
