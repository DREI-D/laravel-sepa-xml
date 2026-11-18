<?php

declare(strict_types=1);

namespace DREID\LaravelSepaXml\DataTransferObjects;

readonly class ConfigurationDto
{
    public function __construct(
        public string $from,
        public string $iban,
        public string $bic,
        public string $prefix,
        public bool $batchBooking = false,
        public ?int $maxNumberOfTransactions = null,
        public ?float $maxValueOfTransactions = null,
    ) {}
}
