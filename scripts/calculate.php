<?php

function calculate($data) {
    set_time_limit(0);
    global $brokerPairToApiPair;
    global $previousFileName;
    global $previousCashIn;
    global $typeBroker;

    $response = array(
        'transaction' => array(),
        'success' => true, 
        'plusValueTotal' => 0,
        'plusValueToPay' => 0
    );

    $cashIn = $previousCashIn;
    $walletVolume = array();

    // If there is a file with previous trades (previous years), we take it to calculate actual volume of cryptos.
    if ($previousFileName != '') {
        $previousData = getFormattedData($previousFileName, $typeBroker);
        for ($i = 0; $i < count($previousData); $i++) {
            $transaction = $previousData[$i];
            $walletVolume = updateWalletVolume($walletVolume, $transaction);
        }
    }

    $plusValueTotal = 0;

    for ($i = 0; $i < count($data); $i++) {
        $transaction = $data[$i];
        $pair = $brokerPairToApiPair[$transaction['pair']];
        $isFiatPair = strpos($transaction['pair'], 'EUR') !== false;

        if ($transaction['type'] == 'buy') {
            $oldCashIn = $cashIn;

            if ($isFiatPair) {
                // Save previous cashIn & new cashIn
                $cashIn +=  $transaction['cost'];
            }

            // Update volume bought in walletVolume
            $walletVolume = updateWalletVolume($walletVolume, $transaction);

            // Save resume transaction to display results
            $saveTransaction = array(
                'type' => $transaction['type'],
                'pair' => $pair,
                'cost' => $transaction['cost'],
                'oldCashIn' => $oldCashIn,
                'cashIn' => $cashIn,
                'plusValue' => 0
            );
            array_push($response['transaction'], $saveTransaction);

        } else if ($transaction['type'] == 'sell') {
            $timeTransaction = strtotime($transaction['time']);
            $cashOut = $transaction['cost'];
            $before = $timeTransaction + 150;
            $after = $timeTransaction - 150;
            $oldCashIn = $cashIn;

            // If fiat pair, we must calcultate plus-value
            if ($isFiatPair) {
                // Get price of complete wallet for specific date
                $walletPrice = getWalletPriceAtDate($walletVolume, $before, $after);
                
                if ($walletPrice == 0) {
                    echo 'WalletPrice = 0 : ' . $pair;
                    exit;
                }

                // Make calcul of percentage 
                $walletPercent = $cashOut / $walletPrice;
                $plusValue = $cashOut - ($cashIn * $walletPercent);
                $plusValueTotal +=  $plusValue;

                // Save previous cashIn & new cashIn
                $cashIn = $cashIn * $walletPercent;

                // Save resume transaction to display results
                $saveTransaction = array(
                    'type' => $transaction['type'],
                    'pair' => $pair,
                    'cost' => $transaction['cost'],
                    'oldCashIn' => $oldCashIn,
                    'cashIn' => $cashIn,
                    'plusValue' => $plusValue,
                    'detail' => array(
                        'cashOut' => $cashOut,
                        'walletPrice' => $walletPrice,
                        'walletPercent' => $walletPercent
                    )
                );
            } else {
                // Save resume transaction to display results
                $saveTransaction = array(
                    'type' => $transaction['type'],
                    'pair' => $pair,
                    'cost' => $transaction['cost'],
                    'oldCashIn' => $oldCashIn,
                    'cashIn' => $cashIn,
                    'plusValue' => 0,
                    'detail' => array()
                );
            }

            // Here, we update volume after calculate walletPrice (if fiat pair). We don't must execute it beofre getWalletPriceAtDate to not falsify calcul
            $walletVolume = updateWalletVolume($walletVolume, $transaction);

            array_push($response['transaction'], $saveTransaction);
        }
    }

    $response['plusValueTotal'] = $plusValueTotal;
    $response['plusValueToPay'] = $plusValueTotal * 0.3;

    return $response;
}

function  updateWalletVolume($walletVolume, $transaction) {
	global $brokerPairToApiPair;

	$pair = $brokerPairToApiPair[$transaction['pair']];
	if (!array_key_exists($pair, $walletVolume)) {
		$walletVolume[$pair] = 0; 
	}

    if ($transaction['type'] == 'buy') {
        $walletVolume[$pair] += $transaction['volume']; 
    } else {
        $walletVolume[$pair] -= $transaction['volume']; 
    }
	
    return $walletVolume;
}

function getWalletPriceAtDate($walletVolume, $before, $after) {
    $totalWalletPrice = 0;

    foreach ($walletVolume as $pair => $volume) {
        $unitPairPrice = 0;
        $totalPairPrice = 0;

        // Manage call to api + relaunch with bigger before and after if there are no result
        $unitPairPrice = getValuePairByApi($pair, $before, $after);

        $totalPairPrice = $unitPairPrice * $volume;
        $totalWalletPrice += $totalPairPrice;
    }

    return $totalWalletPrice;
}

function getValuePairByApi($pair, $before, $after) {
    $parameters = array(
        'before' => $before,
        'after' => $after,
        'pair' => $pair
    );

    $response = callApi('calculateAtDate', $parameters);
    
    if (!$response['result']) {
        echo 'No response API, maybe your credits are empty.';
        exit;
    }

    // Parse response to recuperate price of crpto at interval given
    if (count($response['result'][60])) {
        $unitPairPrice = $response['result'][60][0][1];
    } else if (count($response['result'][180])) {
        $unitPairPrice = $response['result'][180][0][1];
    }  else if (count($response['result'][300])) {
        $unitPairPrice = $response['result'][300][0][1];
    } else if (count($response['result'][900])) {
        $unitPairPrice = $response['result'][900][0][1];
    }  else if (count($response['result'][1800])) {
        $unitPairPrice = $response['result'][1800][0][1];
    } else if (count($response['result'][3600])) {
        $unitPairPrice = $response['result'][3600][0][1];
    } else if (count($response['result'][7200])) {
        $unitPairPrice = $response['result'][7200][0][1];
    } else if (count($response['result'][14400])) {
        $unitPairPrice = $response['result'][14400][0][1];
    } else if (count($response['result'][21600])) {
        $unitPairPrice = $response['result'][21600][0][1];
    } else {
        //echo 'ERROR not found ' . $pair . 'between ' . $after . ' and ' . $before.'<br/>';
        
        $before += 500;
        $after += 500;

        //echo 'Relaunch for ' . $pair . 'between ' . $after . ' and ' . $before.'<br/>';

        return getValuePairByApi($pair, $before, $after);
    }

    return $unitPairPrice;
}