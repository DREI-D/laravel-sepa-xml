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
        $maxNumberOfTransactions = Config::get('sepa.max_number_of_transactions');
        $maxValueOfTransactions = Config::get('sepa.max_value_of_transactions');

        if (
            (!is_float($maxValueOfTransactions) && $maxValueOfTransactions !== null)
            || (!is_int($maxNumberOfTransactions) && $maxNumberOfTransactions !== null)
        ) {
            throw new ConfigurationException();
        }

        $configuration ??= new ConfigurationDto(
            from: Config::string('sepa.from'),
            iban: Config::string('sepa.iban'),
            bic: Config::string('sepa.bic'),
            prefix: Config::string('sepa.prefix'),
            batchBooking: Config::boolean('sepa.batch_booking', false),
            maxNumberOfTransactions: $maxNumberOfTransactions,
            maxValueOfTransactions: $maxValueOfTransactions,
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
