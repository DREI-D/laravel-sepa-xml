<?php

namespace DREID\LaravelSepaXml\Factories;

use DREID\LaravelSepaXml\Dtos\TransactionDto;
use DREID\LaravelSepaXml\Services\SepaStringSanitizeService;

class TransactionFactory
{
    public function __construct(private readonly SepaStringSanitizeService $sanitizer) {}

    public function transform(
        string $accountOwner,
        string $subject,
        string $iban,
        string $bic,
        float $amount
    ): TransactionDto {
        return new TransactionDto(
            $this->sanitizer->sanitize($accountOwner),
            $this->sanitizer->sanitize($subject),
            $this->sanitizer->sanitizeBankDetails($iban),
            $this->sanitizer->sanitizeBankDetails($bic),
            $amount
        );
    }
}
