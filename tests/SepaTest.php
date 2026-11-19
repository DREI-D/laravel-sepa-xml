<?php

/**
 * AI GENERATED TESTS
 *
 * @noinspection PhpUnhandledExceptionInspection
 */

use DREID\LaravelSepaXml\DataTransferObjects\ConfigurationDto;
use DREID\LaravelSepaXml\DataTransferObjects\TransactionDto;
use DREID\LaravelSepaXml\Exceptions\ConfigurationException;
use DREID\LaravelSepaXml\Exceptions\LimitExceededException;
use DREID\LaravelSepaXml\Facades\Sepa;
use DREID\LaravelSepaXml\SepaBuilder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Config::set('sepa.from', 'FROM EXAMPLE');
    Config::set('sepa.iban', 'IBAN EXAMPLE');
    Config::set('sepa.bic', 'BIC EXAMPLE');
    Config::set('sepa.prefix', 'PREFIX-EXAMPLE-');
    Config::set('sepa.batch_booking', false);
    Config::set('sepa.max_number_of_transactions', null);
    Config::set('sepa.max_value_of_transactions', null);
});

describe('Sepa facade', function () {
    it('creates a sepa builder instance', function () {
        $builder = Sepa::create('T1-2026-');

        expect($builder)->toBeInstanceOf(SepaBuilder::class);
    });

    it('can save a sepa xml file to default storage', function () {
        Storage::fake('local');

        Sepa::create('T1-2026-')
            ->addTransaction(
                subject: 'Test-Subject for Transaction',
                amount: 49.95,
                accountOwner: 'Max Mustermann',
                iban: 'DE02120300000000202051',
                bic: 'BYLADEM1001',
            )
            ->save('text.xml');

        Storage::disk('local')->assertExists('text.xml');

        $content = Storage::disk('local')->get('text.xml');

        expect($content)
            ->toContain('<?xml version="1.0" encoding="UTF-8"?>')
            ->toContain('pain.001.001.09')
            ->toContain('<MsgId>PREFIX-EXAMPLE-T1-2026-</MsgId>')
            ->toContain('<InstdAmt Ccy="EUR">49.95</InstdAmt>')
            ->toContain('<Nm>Max Mustermann</Nm>')
            ->toContain('<IBAN>DE02120300000000202051</IBAN>')
            ->toContain('<BICFI>BYLADEM1001</BICFI>')
            ->toContain('<Ustrd>Test-Subject for Transaction</Ustrd>');
    });

    it('can save a sepa xml file to a custom storage disk', function () {
        Storage::fake('custom_disk');

        Sepa::create('T1-2026-')
            ->addTransaction(
                subject: 'Test-Subject for Transaction',
                amount: 49.95,
                accountOwner: 'Max Mustermann',
                iban: 'DE02120300000000202051',
                bic: 'BYLADEM1001',
            )
            ->save('custom.xml', 'custom_disk');

        Storage::disk('custom_disk')->assertExists('custom.xml');
    });

    it('can get xml content as a string', function () {
        $creator = Sepa::create('T1-2026-')
            ->addTransaction(
                subject: 'Test-Subject for Transaction',
                amount: 49.95,
                accountOwner: 'Max Mustermann',
                iban: 'DE02120300000000202051',
                bic: 'BYLADEM1001',
            )
            ->getContent();

        expect($creator)
            ->toBeString()
            ->toContain('<?xml version="1.0" encoding="UTF-8"?>')
            ->toContain('<Document xmlns="urn:iso:std:iso:20022:tech:xsd:pain.001.001.09"')
            ->toContain('<MsgId>PREFIX-EXAMPLE-T1-2026-</MsgId>')
            ->toContain('<NbOfTxs>1</NbOfTxs>')
            ->toContain('<CtrlSum>49.95</CtrlSum>')
            ->toContain('<InitgPty>')
            ->toContain('<Nm>FROM EXAMPLE</Nm>')
            ->toContain('<PmtInf>')
            ->toContain('<PmtInfId>PREFIX-EXAMPLE-T1-2026-</PmtInfId>')
            ->toContain('<PmtMtd>TRF</PmtMtd>')
            ->toContain('<BtchBookg>false</BtchBookg>')
            ->toContain('<SvcLvl>')
            ->toContain('<Cd>SEPA</Cd>')
            ->toContain('<ReqdExctnDt>')
            ->toContain('<Dt>' . today()->format('Y-m-d') . '</Dt>')
            ->toContain('<Dbtr>')
            ->toContain('<Nm>FROM EXAMPLE</Nm>')
            ->toContain('<DbtrAcct>')
            ->toContain('<IBAN>IBANEXAMPLE</IBAN>')
            ->toContain('<DbtrAgt>')
            ->toContain('<BICFI>BIC EXAMPLE</BICFI>')
            ->toContain('<ChrgBr>SLEV</ChrgBr>')
            ->toContain('<CdtTrfTxInf>')
            ->toContain('<EndToEndId>PREFIX-EXAMPLE-T1-2026--1</EndToEndId>')
            ->toContain('<InstdAmt Ccy="EUR">49.95</InstdAmt>')
            ->toContain('<CdtrAgt>')
            ->toContain('<BICFI>BYLADEM1001</BICFI>')
            ->toContain('<Cdtr>')
            ->toContain('<Nm>Max Mustermann</Nm>')
            ->toContain('<CdtrAcct>')
            ->toContain('<IBAN>DE02120300000000202051</IBAN>')
            ->toContain('<RmtInf>')
            ->toContain('<Ustrd>Test-Subject for Transaction</Ustrd>');
    });

    it('handles multiple transactions correctly', function () {
        $xml = Sepa::create('BATCH-1')
            ->addTransaction(
                subject: 'Payment 1',
                amount: 10.50,
                accountOwner: 'Alice',
                iban: 'DE02120300000000202051',
                bic: 'BYLADEM1001',
            )
            ->addTransaction(
                subject: 'Payment 2',
                amount: 20.25,
                accountOwner: 'Bob',
                iban: 'DE02120300000000202052',
                bic: 'BYLADEM1002',
            )
            ->getContent();

        expect($xml)
            ->toContain('<NbOfTxs>2</NbOfTxs>')
            ->toContain('<CtrlSum>30.75</CtrlSum>')
            ->toContain('<EndToEndId>PREFIX-EXAMPLE-BATCH-1-1</EndToEndId>')
            ->toContain('<EndToEndId>PREFIX-EXAMPLE-BATCH-1-2</EndToEndId>')
            ->toContain('<Nm>Alice</Nm>')
            ->toContain('<Nm>Bob</Nm>');
    });

    it('defaults bic to NOTPROVIDED when null', function () {
        $xml = Sepa::create('T1-2026-')
            ->addTransaction(
                subject: 'No BIC payment',
                amount: 15.00,
                accountOwner: 'Charlie',
                iban: 'DE02120300000000202051',
            )
            ->getContent();

        expect($xml)->toContain('<BICFI>NOTPROVIDED</BICFI>');
    });

    it('sanitizes the inputs when sanitized is turned on', function () {
        $creator = Sepa::create('T1-2026-')
            ->sanitized()
            ->addTransaction(
                subject: 'Überweisung / Test #123!',
                amount: 49.95,
                accountOwner: 'Mäx & Mörïtz Söhne',
                iban: 'DE02 1203 0000 0000 2020 51',
                bic: 'byla dem1 001',
            )
            ->getContent();

        expect($creator)
            ->toContain('<Nm>MAEX  MOERITZ SOEHNE</Nm>')
            ->toContain('<Ustrd>UEBERWEISUNG - TEST 123</Ustrd>')
            ->toContain('<IBAN>DE02120300000000202051</IBAN>')
            ->toContain('<BICFI>BYLADEM1001</BICFI>');
    });

    it('does not sanitize inputs by default', function () {
        $creator = Sepa::create('T1-2026-')
            ->addTransaction(
                subject: 'Überweisung / Test',
                amount: 49.95,
                accountOwner: 'Max Mustermann',
                iban: 'DE02120300000000202051',
                bic: 'BYLADEM1001',
            )
            ->getContent();

        expect($creator)
            ->toContain('<Ustrd>Überweisung / Test</Ustrd>')
            ->toContain('<Nm>Max Mustermann</Nm>');
    });

    it('can disable sanitization explicitly', function () {
        $creator = Sepa::create('T1-2026-')
            ->sanitized(false)
            ->addTransaction(
                subject: 'Überweisung / Test',
                amount: 49.95,
                accountOwner: 'Max Mustermann',
                iban: 'DE02120300000000202051',
                bic: 'BYLADEM1001',
            )
            ->getContent();

        expect($creator)->toContain('<Ustrd>Überweisung / Test</Ustrd>');
    });

    it('minifies the output when minified is turned on', function () {
        $creator = Sepa::create('T1-2026-')
            ->minified()
            ->addTransaction(
                subject: 'Test-Subject for Transaction',
                amount: 49.95,
                accountOwner: 'Max Mustermann',
                iban: 'DE02120300000000202051',
                bic: 'BYLADEM1001',
            )
            ->getContent();

        expect($creator)
            ->toBeString()
            ->toContain('<Document xmlns="urn:iso:std:iso:20022:tech:xsd:pain.001.001.09"')
            ->and(str_contains($creator, "\n  <CstmrCdtTrfInitn>"))->toBeFalse();
    });

    it('formats the output by default when minified is not turned on', function () {
        $creator = Sepa::create('T1-2026-')
            ->addTransaction(
                subject: 'Test-Subject for Transaction',
                amount: 49.95,
                accountOwner: 'Max Mustermann',
                iban: 'DE02120300000000202051',
                bic: 'BYLADEM1001',
            )
            ->getContent();

        expect($creator)->toContain("\n");
    });

    it('can disable minification explicitly', function () {
        $creator = Sepa::create('T1-2026-')
            ->minified(false)
            ->addTransaction(
                subject: 'Test-Subject for Transaction',
                amount: 49.95,
                accountOwner: 'Max Mustermann',
                iban: 'DE02120300000000202051',
                bic: 'BYLADEM1001',
            )
            ->getContent();

        expect($creator)->toContain("\n");
    });

    it('can use a different configuration per created file', function () {
        $creator = Sepa::create(
            transactionNumber: 'T1-2026-',
            configuration: new ConfigurationDto(
                from: 'MAX MUSTERMANN',
                iban: 'DE02120300000000202051',
                bic: 'BYLADEM1001',
                prefix: 'TEST-',
                batchBooking: true,
            )
        );

        expect($creator)->toBeInstanceOf(SepaBuilder::class);

        $xml = $creator
            ->addTransaction(
                subject: 'Custom Config Transaction',
                amount: 99.99,
                accountOwner: 'Jane Doe',
                iban: 'DE02120300000000202052',
                bic: 'BYLADEM1002',
            )
            ->getContent();

        expect($xml)
            ->toContain('<MsgId>TEST-T1-2026-</MsgId>')
            ->toContain('<PmtInfId>TEST-T1-2026-</PmtInfId>')
            ->toContain('<BtchBookg>true</BtchBookg>')
            ->toContain('<Dbtr>')
            ->toContain('<Nm>MAX MUSTERMANN</Nm>')
            ->toContain('<IBAN>DE02120300000000202051</IBAN>')
            ->toContain('<BICFI>BYLADEM1001</BICFI>')
            ->toContain('<EndToEndId>TEST-T1-2026--1</EndToEndId>')
            ->toContain('<InstdAmt Ccy="EUR">99.99</InstdAmt>');
    });

    it('can use a different configuration passed positionally', function () {
        $creator = Sepa::create(
            'T1-2026-',
            new ConfigurationDto(
                from: 'MAX MUSTERMANN',
                iban: 'DE02120300000000202051',
                bic: 'BYLADEM1001',
                prefix: 'TEST-',
                batchBooking: true,
            )
        );

        $xml = $creator
            ->addTransaction(
                subject: 'Custom Config Transaction',
                amount: 50.00,
                accountOwner: 'Jane Doe',
                iban: 'DE02120300000000202052',
            )
            ->getContent();

        expect($xml)->toContain('<MsgId>TEST-T1-2026-</MsgId>');
    });

    it('allows adding transaction dtos directly and retrieving them', function () {
        $builder = Sepa::create('T1-2026-');

        $dto = new TransactionDto(
            accountOwner: 'Max Mustermann',
            subject: 'Subject',
            iban: 'DE02120300000000202051',
            bic: 'BYLADEM1001',
            amount: 25.00,
        );

        $builder->addTransactionDto($dto);

        expect($builder->getTransactions())->toHaveCount(1)
            ->and($builder->getTransactions()[0])->toBe($dto);
    });

    it('throws configuration exception when required default config is empty', function (string $key) {
        Config::set("sepa.{$key}", '');

        Sepa::create('T1-2026-');
    })->with(['from', 'iban', 'bic', 'prefix'])->throws(ConfigurationException::class);

    it('throws configuration exception when custom configuration has invalid limit', function () {
        Sepa::create(
            transactionNumber: 'T1-2026-',
            configuration: new ConfigurationDto(
                from: 'FROM',
                iban: 'IBAN',
                bic: 'BIC',
                prefix: 'PREFIX',
                maxNumberOfTransactions: 0,
            )
        );
    })->throws(ConfigurationException::class);

    it('throws limit exceeded exception when max number of transactions is exceeded', function () {
        $builder = Sepa::create(
            transactionNumber: 'T1-2026-',
            configuration: new ConfigurationDto(
                from: 'FROM',
                iban: 'IBAN',
                bic: 'BIC',
                prefix: 'PREFIX',
                maxNumberOfTransactions: 1,
            )
        );

        $builder->addTransaction(
            subject: 'First',
            amount: 10.00,
            accountOwner: 'Owner',
            iban: 'DE02120300000000202051',
        );

        $builder->addTransaction(
            subject: 'Second',
            amount: 10.00,
            accountOwner: 'Owner',
            iban: 'DE02120300000000202051',
        );
    })->throws(LimitExceededException::class);

    it('throws limit exceeded exception when max value of transactions is exceeded', function () {
        $builder = Sepa::create(
            transactionNumber: 'T1-2026-',
            configuration: new ConfigurationDto(
                from: 'FROM',
                iban: 'IBAN',
                bic: 'BIC',
                prefix: 'PREFIX',
                maxValueOfTransactions: 50.00,
            )
        );

        $builder->addTransaction(
            subject: 'Too large',
            amount: 50.01,
            accountOwner: 'Owner',
            iban: 'DE02120300000000202051',
        );
    })->throws(LimitExceededException::class);
});
