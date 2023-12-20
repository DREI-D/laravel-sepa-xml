<?php

namespace DREID\LaravelSepaXml\Services;

use DOMDocument;
use DREID\LaravelSepaXml\Dtos\TransactionDto;
use Illuminate\Support\Str;
use Storage;

class SepaFileCreationService
{
    public function save(
        string $disk,
        string $file,
        string $transactionNumber,
        array $transactions
    ): bool
    {
        $content = $this->create($transactionNumber, $transactions);

        return Storage::disk($disk)->put(
            $file,
            $content
        );
    }

    public function create(string $transactionNumber, array $transactions): string
    {
        $sum = array_reduce($transactions, static function (float $carry, TransactionDto $dto) {
            return $carry + $dto->amount;
        }, 0);

        $sepa = view('laravel-sepa-xml::sepa', [
            'messageId'      => $this->createMessageId($transactionNumber),
            'transactions'   => $transactions,
            'transactionSum' => $sum
        ])->render();

        $dom = new DOMDocument("1.0");
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($sepa);

        /** @var string $minifiedXml */
        $minifiedXml = $dom->saveXML();

        return Str::replace(["\n", "\r"], '', $minifiedXml);
    }

    protected function createMessageId(string $transactionNumber): string
    {
        return config('sepa.prefix') . $transactionNumber;
    }
}
