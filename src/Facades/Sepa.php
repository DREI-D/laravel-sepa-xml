<?php

declare(strict_types=1);

namespace DREID\LaravelSepaXml\Facades;

use DREID\LaravelSepaXml\DataTransferObjects\ConfigurationDto;
use DREID\LaravelSepaXml\SepaBuilder;
use Illuminate\Support\Facades\Facade;

/**
 * @method static SepaBuilder create(string $transactionNumber, ?ConfigurationDto $configuration = null)
 */
class Sepa extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \DREID\LaravelSepaXml\Sepa::class;
    }
}
