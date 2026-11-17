@php
    /** @var \DREID\LaravelSepaXml\DataTransferObjects\TransactionDto[] $transactions */
    /** @var \DREID\LaravelSepaXml\DataTransferObjects\ConfigurationDto $configuration */
@endphp

<?xml version="1.0" encoding="UTF-8"?>
<Document xmlns="urn:iso:std:iso:20022:tech:xsd:pain.001.001.09" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="urn:iso:std:iso:20022:tech:xsd:pain.001.001.09 pain.001.001.09.xsd">
    <CstmrCdtTrfInitn>
        <GrpHdr>
            <MsgId>{{ $messageId }}</MsgId>
            <CreDtTm>{{ now()->toISOString() }}</CreDtTm>
            <NbOfTxs>{{ count($transactions) }}</NbOfTxs>
            <CtrlSum>{{ number_format($transactionSum, 2, '.', '') }}</CtrlSum>
            <InitgPty>
                <Nm>{{ $configuration->from }}</Nm>
            </InitgPty>
        </GrpHdr>
        <PmtInf>
            <PmtInfId>{{ $messageId }}</PmtInfId>
            <PmtMtd>TRF</PmtMtd>
            <BtchBookg>{{ $configuration->batchBooking ? 'true' : 'false' }}</BtchBookg>
            <NbOfTxs>{{ count($transactions) }}</NbOfTxs>
            <CtrlSum>{{ number_format($transactionSum, 2, '.', '') }}</CtrlSum>
            <PmtTpInf>
                <SvcLvl>
                    <Cd>SEPA</Cd>
                </SvcLvl>
            </PmtTpInf>
            <ReqdExctnDt>{{ today()->format('Y-m-d') }}</ReqdExctnDt>
            <Dbtr>
                <Nm>{{ $configuration->from }}</Nm>
            </Dbtr>
            <DbtrAcct>
                <Id>
                    <IBAN>{{ str_replace(' ', '', $configuration->iban) }}</IBAN>
                </Id>
            </DbtrAcct>
            <DbtrAgt>
                <FinInstnId>
                    <BICFI>{{ $configuration->bic }}</BICFI>
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
                            <BICFI>{{ $transaction->bic }}</BICFI>
                        </FinInstnId>
                    </CdtrAgt>
                    <Cdtr>
                        <Nm>{{ $transaction->accountOwner }}</Nm>
                    </Cdtr>
                    <CdtrAcct>
                        <Id>
                            <IBAN>{{ $transaction->iban }}</IBAN>
                        </Id>
                    </CdtrAcct>
                    <RmtInf>
                        <Ustrd>{{ $transaction->subject }}</Ustrd>
                    </RmtInf>
                </CdtTrfTxInf>
            @endforeach
        </PmtInf>
    </CstmrCdtTrfInitn>
</Document>
