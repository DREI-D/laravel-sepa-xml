@php
    $sanitizer = app(\DREID\LaravelSepaXml\Services\SepaStringSanitizeService::class);
@endphp
<?xml version="1.0" encoding="UTF-8"?>
<Document xmlns="urn:iso:std:iso:20022:tech:xsd:pain.001.002.03" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="urn:iso:std:iso:20022:tech:xsd:pain.001.002.03 pain.001.002.03.xsd">
    <CstmrCdtTrfInitn>
        <GrpHdr>
            <MsgId>{{ $messageId }}</MsgId>
            <CreDtTm>{{ now()->toISOString() }}</CreDtTm>
            <NbOfTxs>{{ count($transactions) }}</NbOfTxs>
            <CtrlSum>{{ number_format($transactionSum, 2, '.', '') }}</CtrlSum>
            <InitgPty>
                <Nm>{{ config('sepa.from') }}</Nm>
            </InitgPty>
        </GrpHdr>
        <PmtInf>
            <PmtInfId>{{ $messageId }}</PmtInfId>
            <PmtMtd>TRF</PmtMtd>
            <NbOfTxs>{{ count($transactions) }}</NbOfTxs>
            <CtrlSum>{{ number_format($transactionSum, 2, '.', '') }}</CtrlSum>
            <PmtTpInf>
                <SvcLvl>
                    <Cd>SEPA</Cd>
                </SvcLvl>
            </PmtTpInf>
            <ReqdExctnDt>{{ today()->format('Y-m-d') }}</ReqdExctnDt>
            <Dbtr>
                <Nm>{{ config('sepa.from') }}</Nm>
            </Dbtr>
            <DbtrAcct>
                <Id>
                    <IBAN>{{ config('sepa.iban') }}</IBAN>
                </Id>
            </DbtrAcct>
            <DbtrAgt>
                <FinInstnId>
                    <BIC>{{ config('sepa.bic') }}</BIC>
                </FinInstnId>
            </DbtrAgt>
            <ChrgBr>SLEV</ChrgBr>
            @foreach($transactions as $transaction)
                <CdtTrfTxInf>
                    <PmtId>
                        <EndToEndId>{{ $messageId }}-{{ $loop->index + 1 }}</EndToEndId>
                    </PmtId>
                    <Amt>
                        <InstdAmt Ccy="EUR">{{ number_format($transaction->amount, 2, '.', '') }}</InstdAmt>
                    </Amt>
                    <CdtrAgt>
                        <FinInstnId>
                            <BIC>{{ $sanitizer->sanitize($transaction->bic) }}</BIC>
                        </FinInstnId>
                    </CdtrAgt>
                    <Cdtr>
                        <Nm>{{ $sanitizer->sanitize($transaction->accountOwner) }}</Nm>
                    </Cdtr>
                    <CdtrAcct>
                        <Id>
                            <IBAN>{{ $sanitizer->sanitize($transaction->iban) }}</IBAN>
                        </Id>
                    </CdtrAcct>
                    <RmtInf>
                        <Ustrd>{{ $sanitizer->sanitize($transaction->subject) }}</Ustrd>
                    </RmtInf>
                </CdtTrfTxInf>
            @endforeach
        </PmtInf>
    </CstmrCdtTrfInitn>
</Document>
