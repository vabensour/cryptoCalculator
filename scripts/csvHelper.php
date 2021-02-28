<?php 

function getFormattedData($fileName, $typeBroker) {
    $fileNameWithDirectory = 'uploads/' . $fileName;
    $rawData = getRawArrayFromCsv($fileNameWithDirectory);
    $formattedData = getFormattedDataFromRawData($rawData, $typeBroker);
    
    return $formattedData;
}

function getRawArrayFromCsv($fileName) {
    //Open our CSV file using the fopen function.
    $fh = fopen($fileName, "r");

    //Setup a PHP array to hold our CSV rows.
    $csvData = array();

    //Loop through the rows in our CSV file and add them to
    //the PHP array that we created above.
    while (($row = fgetcsv($fh, 0, ",")) !== FALSE) {
        $csvData[] = $row;
    }

    return $csvData;
}

function getFormattedDataFromRawData($rawData, $typeBroker) {
    require('./broker/' . $typeBroker . '/csvIndex.php' );

    $formattedData = array();

    for ($i = 1; $i < count($rawData); $i++) {
        $newEntry = array(
            'pair' => $rawData[$i][$indexPair], 
            'time' => $rawData[$i][$indexTime],
            'type' => $rawData[$i][$indexType],
            'price' => $rawData[$i][$indexPrice],
            'cost' => $rawData[$i][$indexCost],
            'fee' => $rawData[$i][$indexFee],
            'volume' => $rawData[$i][$indexVolume]
        );
        
        // Verify pair contains actual currency (Euro)
        if (strpos($newEntry['pair'], 'EUR') !== false) {
            array_push($formattedData, $newEntry);
        }
       
    }
    
    return $formattedData;
}

function verifyData($data) {
    global $brokerPairToApiPair;

    $errors =  array();
    $pairToTestApi = array();
    $pairAvailable = array();
    $success = true;

    for($i = 0; $i < count($data); $i++) {
        $transaction = $data[$i];
        $pair = $transaction['pair'];

        // Verify pair exist is config file of broker
        if (!array_key_exists($pair, $brokerPairToApiPair)) {
            $success = false;
            $msgError = $pair . ' key has not been found in file config "brokerPairToApiPair.php" of your broker. Please, add key ' . $transaction['pair'] . ' into "broker/YOUR-BROKER/require/brokerPairToApiPair.php".';
            if (!in_array($msgError, $errors)) {
                array_push($errors, $msgError);
            }
        } else {
            if (!in_array($pair, $pairToTestApi)) {
                array_push($pairToTestApi, $pair);
            }
        }
    }

    // Verify pairs are found into API
    if ($success) {
        for($i = 0; $i < count($pairToTestApi); $i++) {
            $pair = $pairToTestApi[$i];
            $formattedPair = $brokerPairToApiPair[$pair];
            $displayFormattedPair = $formattedPair . ' (' . $pair . ')';
            $parameters = array('pair' => $formattedPair);
            $existingPair = callApi('verifyPairExist', $parameters);

            if (!$existingPair) {
                $success = false;
                $msgError =  $displayFormattedPair . ' has not been found in API, please verify config "brokerPairToApiPair.php".';
                if (!in_array($msgError, $errors)) {
                    array_push($errors, $msgError);
                }
            } else {
                if (!in_array($formattedPair, $pairAvailable)) {
                    array_push($pairAvailable, $formattedPair);
                }
            }
        }
    }
    
    sort($pairAvailable);

    return array(
        'success' => $success, 
        'errors' => $errors,
        'pairAvailable' => $pairAvailable
    );
}

?>