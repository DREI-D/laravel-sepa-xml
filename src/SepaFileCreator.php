<?php

declare(strict_types=1);

namespace DREID\LaravelSepaXml;

use DREID\LaravelSepaXml\DataTransferObjects\ConfigurationDto;
use DREID\LaravelSepaXml\Exceptions\ConfigurationException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Facade;

class SepaFileCreator extends Facade
{
    /**
     * @throws ConfigurationException
     */
    public function create(string $transactionNumber, ?ConfigurationDto $configuration = null): SepaFileBuilder
    {
        $configuration ??= new ConfigurationDto(
            from: Config::string('sepa.from'),
            iban: Config::string('sepa.iban'),
            bic: Config::string('sepa.bic'),
            prefix: Config::string('sepa.prefix'),
            batchBooking: Config::boolean('sepa.batch_booking', false),
        );

        if (
            empty($configuration->from)
            || empty($configuration->iban)
            || empty($configuration->bic)
            || empty($configuration->prefix)
        ) {
            throw new ConfigurationException();
        }

        return new SepaFileBuilder($transactionNumber, $configuration);
    }
}
