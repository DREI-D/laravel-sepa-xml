<?php

declare(strict_types=1);

namespace DREID\LaravelSepaXml;

use DREID\LaravelSepaXml\DataTransferObjects\ConfigurationDto;
use DREID\LaravelSepaXml\DataTransferObjects\TransactionDto;
use DREID\LaravelSepaXml\Exceptions\FormatException;
use DREID\LaravelSepaXml\Exceptions\MissingExtensionException;
use DREID\LaravelSepaXml\Exceptions\RenderException;
use DREID\LaravelSepaXml\Interfaces\Formatter;
use DREID\LaravelSepaXml\Interfaces\Sanitizer;
use Illuminate\Support\Facades\Storage;
use Throwable;

class SepaFileBuilder
{
    /** @var TransactionDto[] */
    private array $transactions = [];

    private bool $shouldSanitize = false;

    private bool $shouldMinify = false;

    public function __construct(
        private readonly string $transactionNumber,
        private readonly ConfigurationDto $configuration,
    ) {}

    public function minified(bool $shouldMinify = true): self
    {
        $this->shouldMinify = $shouldMinify;

        return $this;
    }

    public function sanitized(bool $shouldSanitize = true): self
    {
        $this->shouldSanitize = $shouldSanitize;

        return $this;
    }

    /**
     * @throws MissingExtensionException
     */
    public function addTransaction(
        string $subject,
        float $amount,
        string $accountOwner,
        string $iban,
        ?string $bic = null
    ): self {
        $sanitizer = app(Sanitizer::class);
        $bic ??= 'NOTPROVIDED';

        if ($this->shouldSanitize) {
            $accountOwner = $sanitizer->sanitize($accountOwner);
            $subject = $sanitizer->sanitize($subject);
            $iban = $sanitizer->sanitizeBankDetails($iban);
            $bic = $sanitizer->sanitizeBankDetails($bic);
        }

        $this->addTransactionDto(
            new TransactionDto(
                accountOwner: $accountOwner,
                subject: $subject,
                iban: $iban,
                bic: $bic,
                amount: $amount
            )
        );

        return $this;
    }

    public function addTransactionDto(TransactionDto $dto): self
    {
        $this->transactions[] = $dto;

        return $this;
    }

    /**
     * @return TransactionDto[]
     */
    public function getTransactions(): array
    {
        return $this->transactions;
    }

    /**
     * @throws MissingExtensionException
     * @throws FormatException
     * @throws RenderException
     */
    public function getContent(): string
    {
        $formatter = app(Formatter::class);
        $content = $this->renderXml();

        if ($this->shouldMinify) {
            $content = $formatter->minify($content);
        } else {
            $content = $formatter->format($content);
        }

        return $content;
    }

    /**
     * @throws MissingExtensionException
     * @throws FormatException
     * @throws RenderException
     */
    public function save(string $path, ?string $disk = null): void
    {
        Storage::disk($disk ?? 'local')->put(
            $path,
            $this->getContent()
        );
    }

    /**
     * @throws RenderException
     */
    protected function renderXml(): string
    {
        $sum = array_reduce($this->transactions, static function (float $carry, TransactionDto $dto) {
            return $carry + $dto->amount;
        }, 0);

        try {
            return view('laravel-sepa-xml::sepa', [
                'messageId'      => $this->configuration->prefix . $this->transactionNumber,
                'transactions'   => $this->transactions,
                'transactionSum' => $sum,
                'configuration'  => $this->configuration,
            ])->render();
        } catch (Throwable $exception) {
            throw new RenderException($exception);
        }
    }
}
