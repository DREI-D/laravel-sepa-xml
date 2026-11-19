<?php

return [
    'from'                       => 'FROM EXAMPLE',
    'iban'                       => 'IBAN EXAMPLE',
    'bic'                        => 'BIC EXAMPLE',
    'prefix'                     => 'PREFIX-EXAMPLE-',

    /*
     * group transactions as a single transfer
     */
    'batch_booking'              => false,

    /*
     * some banks set a limit of transactions per file, in our case 999
     * null = unlimited transactions per file
     */
    'max_number_of_transactions' => null,

    /*
     * some banks set a limit of the value per file, in our case 999.999 €
     * null = unlimited value per file
     */
    'max_value_of_transactions'  => null,
];
