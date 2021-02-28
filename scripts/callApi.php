<?php 

// Here indicate public and private keys of API (If you wish unlimited calls)
// Create a Cryptowatch account to get started : https://cryptowat.ch/account/create
// Generate an API key : https://cryptowat.ch/account/api-access

$public = ''; 
$secret = '';
$context = null;
$completeUrl = "";

if ($public != '' && $secret != '') {
    
    $opts = [
        "https" => [
            "method" => "GET",
            "header" => "X-CW-API-Key: " . $secret
        ]
    ];

    $context = stream_context_create($opts);
    $completeUrl = 'apikey=' . $public;
}

function callApi($endpoint, $parameters) {
    $callFunction = $endpoint;
    return $callFunction($parameters);
}

// Call classic to get remaining credit
function getRemainingCreditApi($parameters) {
    global $context;
    global $completeUrl;

    $url = 'https://api.cryptowat.ch/pairs/btceur?' . $completeUrl;
    $result =  @file_get_contents($url, false, $context);
    $resultParsed = json_decode($result, true);

    $remainingCredit = $resultParsed['allowance']['remaining'];
    
    return $remainingCredit;
}

function verifyPairExist($parameters) {
    global $context;
    global $completeUrl;

    // Verify if url with pair return a response, if not pair does not exist in API
    $url = 'https://api.cryptowat.ch/pairs/' . $parameters['pair'] . '?' . $completeUrl;
    $result =  @file_get_contents($url, false, $context);
     
	return !!$result;
}

function calculateAtDate($parameters) {
    global $context;
    global $completeUrl;

    // Verify if url with pair return a response, if not pair does not exist in API
    $url = 'https://api.cryptowat.ch/markets/kraken/'. $parameters['pair'] .'/ohlc?before='. $parameters['before'] .'&after='. $parameters['after'] . '&' . $completeUrl;
    $result =  @file_get_contents($url, false, $context);

	return json_decode($result, true);
}

// https://api.cryptowat.ch/markets/kraken/btceur/ohlc?before=1613229001&after=1613229000

