<?php

declare(strict_types=1);

namespace DREID\LaravelSepaXml;

use DREID\LaravelSepaXml\DataTransferObjects\ConfigurationDto;
use DREID\LaravelSepaXml\Exceptions\ConfigurationException;
use Illuminate\Support\Facades\Config;

class Sepa
{
    /**
     * @throws ConfigurationException
     */
    public function create(string $transactionNumber, ?ConfigurationDto $configuration = null): SepaBuilder
    {
        $configuration ??= new ConfigurationDto(
            from: Config::string('sepa.from'),
            iban: Config::string('sepa.iban'),
            bic: Config::string('sepa.bic'),
            prefix: Config::string('sepa.prefix'),
            batchBooking: Config::boolean('sepa.batch_booking', false),
            maxNumberOfTransactions: Config::integer('sepa.max_number_of_transactions') ?: null,
            maxValueOfTransactions: Config::integer('sepa.max_value_of_transactions') ?: null,
        );

        if (
            empty($configuration->from)
            || empty($configuration->iban)
            || empty($configuration->bic)
            || empty($configuration->prefix)
            || (
                $configuration->maxNumberOfTransactions !== null
                && $configuration->maxNumberOfTransactions <= 0
            )
            || (
                $configuration->maxValueOfTransactions !== null
                && $configuration->maxValueOfTransactions < 0
            )
        ) {
            throw new ConfigurationException();
        }

        return new SepaBuilder($transactionNumber, $configuration);
    }
}
