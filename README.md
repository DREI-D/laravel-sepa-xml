# Laravel Sepa XML

This project was made to create SEPA xml files in Laravel.

## Dependencies

These dependencies are required:

- Laravel 10 or higher
- PHP 8.4 or higher

... and for some features, these are needed:

- ext-dom
- ext-iconv

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
    'prefix' => 'PREFIX-EXAMPLE-',

    // group transactions as a single transfer
    'batch_booking' => false
];
```

- Replace `FROM EXAMPLE` with your companies name. You should use caps lock and no special characters.
- Replace `IBAN EXAMPLE` with the IBAN of the bank account you want to send money from. Do not include spaces.
- Replace `BIC EXAMPLE` with the BIC of the bank account you want to send money from. Do not include spaces.
- Replace `PREFIX` with a unique identifier for your project. This prefix is used to generate unique End-to-End-IDs for your transactions.
- If you want to group your transactions as a single transfer, you can set `batch_booking` to true. If you are not familiar with batch booking, feel free to check out [this post](https://www.sepaforcorporates.com/sepa-payments/why-sepa-batch-booking-is-important/).

An example for our company would be:
```php
return [
    'from'   => 'DREID-D DIREKTWERBUNG GMBH CO KG',
    'iban'   => 'DE02120300000000202051',
    'bic'    => 'BYLADEM1001',
    'prefix' => '3D-INTERN-',

    // group transactions as a single transfer
    'batch_booking' => false
];
```

## Usage

### Saving to a file

You can create a new SEPA file using the provided `Sepa` facade, like this:

```php
use DREID\LaravelSepaXml\Sepa;

$creator = Sepa::create('T1-2026-')
    ->addTransaction(
        subject: 'Test-Subject for Transaction',
        amount: 49.95, // amount in EUR
        accountOwner: 'Max Mustermann',
        iban: 'DE02120300000000202051', // IBAN
        bic: 'BYLADEM1001', // BIC
    )
    ->save('text.xml');
```

### Getting the XML content as a string

Instead of saving to a file, you can also get the generated XML content like this:

```php
use DREID\LaravelSepaXml\Sepa;

$creator = Sepa::create('T1-2026-')
    ->addTransaction(
        // ..
    )
    ->getContent();
```

### Sanitizing the inputs

If you are not sure, whether your data contains (mostly forbidden) special characters or not, you can turn on the built-in sanitizer like this:

```php
use DREID\LaravelSepaXml\Sepa;

$creator = Sepa::create('T1-2026-')
    ->sanitized()
    ->addTransaction(
        // ..
    )
    ->getContent();
```

### Minifying the output

By default, the output gets formatted. You can also minify the XML instead:

```php
use DREID\LaravelSepaXml\Sepa;

$creator = Sepa::create('T1-2026-')
    ->minified()
    ->addTransaction(
        // ..
    )
    ->getContent();
```

### Using a different configuration per created file

If you need to create transactions from multiple accounts, you can pass a configuration class to the `Sepa` facade as well:

```php
use DREID\LaravelSepaXml\Sepa;
use DREID\LaravelSepaXml\DataTransferObjects\ConfigurationDto;

$creator = Sepa::create(
    transactionNumber: 'T1-2026-',
    configurationDto: new ConfigurationDto(
        from: 'MAX MUSTERMANN',
        iban: 'DE02120300000000202051',
        bic: 'BYLADEM1001',
        prefix: 'TEST-',
        batchBooking: true,
    )
);
```
