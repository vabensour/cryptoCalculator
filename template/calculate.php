<?php
// Calculate plus-value
$result = calculate($data);

echo'<h2>Calculate</h2>';
if ($result['success']) {
    ?>
    <table class="table">
        <thead>
            <tr>
                <td>Type</td>
                <td>Pair</td>
                <td>Cost</td>
                <td>Previous cash-in</td>
                <td>New cash-in</td>
                <td style="width: 80px;">Plus-value</td>
                <td style="width: 150px;">Details</td>
            </tr>
        </thead>
        <?php 
            $countSell = 0;

            for($i = 0; $i < count($result['transaction']); $i++) {
                $transaction = $result['transaction'][$i];
                $typeTransaction = $transaction['type'];
                $countColumn = 0;

                if ($typeTransaction == 'sell') {
                    $countSell++;
                }

                echo '<tr>';
                    foreach ($transaction as $key => $value) {
                        echo '<td>';
                        if ($key == 'plusValue') {
                            if ($value != 0) {
                                $value = round($value, 2);
                                echo '<div class="badge badge-pill badge-'. ($value > 0 ? 'success' : 'danger') .'" role="alert">' . ($value > 0 ? '+' . $value : $value) . '</div>';
                            } else {
                                echo '-';
                            }
                        } else if ($key == 'detail') {
                            if ($typeTransaction == 'sell') {
                                echo '<button class="btn btn-primary btn-sm btn-detail" data-detail="' . $countSell . '">Details</button>';
                            } else {
                                echo '-';
                            }
                        } else if ($key != 'type' && $key != 'pair') {
                            $value = round($value, 2);
                            echo $value;
                        } else {
                            echo $value;
                        }
                        echo '</td>';
                        $countColumn++;
                    }

                    // In case of buy, we add an empty column
                    if ($countColumn == 6) {
                        echo '<td></td>';
                    }
                echo '</tr>';

                // If transaction is sell, add line with details of calcul
                if ($typeTransaction == 'sell' && $transaction['detail']) {
                    $oldCashIn = round($transaction['oldCashIn'], 2);
                    $cashOut = round($transaction['detail']['cashOut'], 2);
                    $walletPrice = round($transaction['detail']['walletPrice'], 2);
                    $percent = round($transaction['detail']['walletPercent'], 2);

                    echo '<tr class="detail-calculate" data-detail="' . $countSell . '">';
                        // Display details of calculate percent
                        echo '<td colspan="2">';
                            echo "<b>Cash in</b> : " . $oldCashIn . '<br/>';
                            echo '<b>Cash out</b> : ' . $cashOut . '<br/>';
                            echo '<b>Wallet price</b> : ' . $walletPrice . '<br/>';
                            echo '<b>Percent</b> : ' . $percent . ' (cashOut / walletPrice)<br/>';
                        echo '</td>';

                        // Details of calculate plus value
                        echo '<td colspan="2">';
                            echo '<b>Plus-value</b> :<br/>';
                            echo'cashOut - (cashIn x percent)<br/>';
                            echo $cashOut . ' - (' . $oldCashIn . ' x ' . $percent . ')<br/>';
                        echo '</td>';

                        // Details of calculate New cash in
                        echo '<td colspan="1">';
                            echo '<b>New Cash in</b> :<br/>';
                            echo'cashIn x percent<br/>';
                            echo $oldCashIn . ' x ' . $percent . '<br/>';
                        echo '</td>';
                        
                        echo '<td colspan="2">';
                            echo '* Be carefull, details is displayed with rounded values to simplify calculate details.';
                        echo '</td>';
                    echo '</tr>';
                }
            }
        ?>
    </table>
    <?php
 
    $plusValueTotal = round($result['plusValueTotal'], 2);
    echo 'Plus value to declare : <div class="badge badge-pill badge-'. ($plusValueTotal > 0 ? 'success' : 'danger') .'" role="alert">' . ($plusValueTotal > 0 ? '+' . $plusValueTotal : $plusValueTotal) . '</div><br/>';

    if ($plusValueTotal > 0) {
        echo 'Plus value to pay : <div class="badge badge-pill badge-success" role="alert">' . round($result['plusValueToPay'], 2) . '</div>';
    }
} ?>