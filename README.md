# Distributed Worker

distributed worker using a database table. The worker requests each URL inside the table and stores the resulting response code.

## Install

Fill database credentials in .env

Then you need to install application

```bash
$ composer install
```
Then create table
```bash
$  php bin/console doctrine:migrations:migrate
```

## Run

```bash
$ php bin/console app:start-worker
```