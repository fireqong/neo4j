## Introduction

> This is neo4j sdk for php


## Requirement

- PHP >= 7.4

## Installation

```shell
composer require church/neo4j
```

## Usage

### Init
```php
$app = new \Church\Neo4j\Application("http://127.0.0.1:7474", "neo4j", "neo4j");
$app->discovery();

```

### query

```php
$statement = (new \Church\Neo4j\Statement('CREATE (n $props) RETURN n)'))->params([
    'props' => [
        'name' => 'test'
    ]   
]);
```

### begin

```php
$statements = \Church\Neo4j\StatementRepository::add($statement);
$transaction = $app->transaction($statements);
$transaction->begin();
```

### commit

```php
$result = $transaction->commit();

if ($result->getRawResponse()->getStatusCode() == 200) {
    print_r($result->getData());
}

```

### keepAlive

> default expiry time is 60 seconds.

```php 
$transaction->keepAlive(); 
```

### rollback

```php 
$transaction->rollback();
```

### beginAndCommit

```php 
$result = $transaction->beginAndCommit();
print_r($result);
```

## Test

```shell
composer install
./vendor/bin/phpunit
```

## Protocol

> MIT