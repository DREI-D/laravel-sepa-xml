<?php

declare(strict_types=1);

namespace DREID\LaravelSepaXml\DataTransferObjects;

readonly class TransactionDto
{
    public function __construct(
        public string $accountOwner,
        public string $subject,
        public string $iban,
        public string $bic,
        public float $amount,
    ) {}
}
