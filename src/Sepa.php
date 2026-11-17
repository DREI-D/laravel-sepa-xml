<?php

declare(strict_types=1);

namespace DREID\LaravelSepaXml;

use DREID\LaravelSepaXml\DataTransferObjects\ConfigurationDto;
use Illuminate\Support\Facades\Facade;

/**
 * @method static SepaFileBuilder create(string $transactionNumber, ?ConfigurationDto $configuration = null)
 */
class Sepa extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SepaFileCreator::class;
    }
}
