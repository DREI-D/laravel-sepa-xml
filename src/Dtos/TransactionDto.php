<?php

namespace DREID\LaravelSepaXml\Dtos;

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
