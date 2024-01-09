# Laravel Sepa XML

This project was made to create SEPA xml files in Laravel.

## Requirements

- Laravel 10 or higher
- PHP 8.2 or higher
- ext-dom & ext-iconv enabled

## Installation

This project is available via composer.
<br>To install it, run:

```shell
composer install drei-d/laravel-sepa-xml
```

## Configuration

This project comes with a predefined config.
<br>In order to use this package, you need to customize it to your needs.

Publish the configuration by running:

```shell
php artisan vendor:publish --provider=DREID\\LaravelSepaXml\\Providers\\ServiceProvider
```

The configuration file should look like the following:

```php
return [
    'from'   => 'FROM EXAMPLE',
    'iban'   => 'IBAN EXAMPLE',
    'bic'    => 'BIC EXAMPLE',
    'prefix' => 'PREFIX-EXAMPLE-'
];
```

- Replace `FROM EXAMPLE` with your companies name. You should use caps lock and no special characters.
- Replace `IBAN EXAMPLE` with the IBAN of the bank account you want to send money from. Do not include spaces.
- Replace `BIC EXAMPLE` with the BIC of the bank account you want to send money from. Do not include spaces.
- Replace `PREFIX` with a unique identifier for your project. This prefix is used to generate unique End-to-End-IDs for your transactions.

An example for our company would be:
```php
return [
    'from'   => 'DREID-D DIREKTWERBUNG GMBH CO KG',
    'iban'   => 'DE02120300000000202051',
    'bic'    => 'BYLADEM1001',
    'prefix' => '3D-INTERN-'
];
```

## Usage

This project uses dependency injection. To get access to its services, you should inject them using the app() function, or, if possible, as function parameter.

Example:

```php
use DREID\LaravelSepaXml\Factories\TransactionFactory;
use DREID\LaravelSepaXml\Services\SepaFileCreationService;

$factory = app(TransactionFactory::class);
$service = app(SepaFileCreationService::class);

$transaction = $factory->transform(
    'Max Mustermann', // account owner
    'Test-Subject for Transaction', // subject
    'DE02120300000000202051', // IBAN
    'BYLADEM1001', // BIC
    49.95 // amount in EUR
);

$service->save(
    'local', // storage disk
    'sepa.xml', // file name
    '1', // transaction number, should be unique per export
    [
        $transaction
    ] // array of transactions you want to export
);
```

When using the factory, your values are automatically sanitized. You can see the changes made by dumping the DTO.

```php
dump($transaction);
```

The result should look like this:

```php
[
    'accountOwner' => 'MAX MUSTERMANN',
    'subject'      => 'TEST-SUBJECT FOR TRANSACTION',
    'iban'         => 'DE02120300000000202051',
    'bic'          => 'BYLADEM1001',
    'amount'       => 49.95
]
```
