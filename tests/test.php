<?php

require_once '../vendor/autoload.php';

use Remfath\EasySoap\Client;

$client = new Client('http://currencyconverter.kowabunga.net/converter.asmx?WSDL');

try {
    $result = $client->call('GetConversionAmount', [
            'CurrencyFrom' => 'USD',
            'CurrencyTo'   => 'CNY',
            'RateDate'     => '2018-01-05',
            'Amount'       => 1000,
        ]
    );
    var_dump($result);
} catch(Exception $e) {
    echo $e->getMessage();
}

try {
    $result = $client->call('NotExistsMethod', [
            'CurrencyFrom' => 'USD',
            'CurrencyTo'   => 'CNY',
            'RateDate'     => '2018-01-05',
            'Amount'       => 1000,
        ]
    );
    var_dump($result);
} catch(Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}

try {
    $result = $client->mustCall('GetConversionAmount', [
            'CurrencyFrom' => 'USD',
            'RateDate'     => '2018-01-05',
            'Amount'       => 1000,
        ]
    );
    var_dump($result);
} catch(Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}