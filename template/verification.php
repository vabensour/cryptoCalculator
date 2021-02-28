<h2>Verification of "<?php echo $fileName ?>"</h2>
<?php
// Verify data, currencies and indicate if there are some errors to fix
$verifyResponse = verifyData($data);
$pairAvailable = $verifyResponse['pairAvailable'];

// Display pairs validated if exists
if (count($pairAvailable) > 0) {
    echo '<div class="alert alert-success custom-alert" role="alert">
        <p>' . count($pairAvailable) . '  pair' . (count($pairAvailable) > 1 ? 's' : '') . ' have been validated : </p>';
        for ($i = 0; $i < count($pairAvailable); $i++) {
            echo '<span class="badge badge-pill badge-primary text-uppercase">' . $pairAvailable[$i] . '</span>';
        }
    echo '</div>';
}

// If al is ok, display a successfull message and button to proceed calculate
if ($verifyResponse['success']) {
    ?>
    <div class="alert alert-success custom-alert" role="alert">
        <h4 class="alert-heading">Oh yeah ! :)</h4>
        <p>Your file has been verified, no errors have been found.</p>
        <hr>
        <p class="mb-0">You can now go to next step and calculate your plus-value ! :)</p>
    </div>

    <button type="button" 
            class="btn btn-success goStep btn-lg" 
            data-step="2" 
            data-file-name="<?php echo $fileName; ?>" 
            data-type-broker="<?php echo $typeBroker; ?>"
            data-previous-file-name="<?php echo $previousFileName; ?>"
            data-previous-cash-in="<?php echo $previousCashIn; ?>">
        Calculate
    </button>
    <?php
} else {
    // If verification not ok, advice user to fix it
    echo'<div class="alert alert-danger custom-alert" role="alert">
        <h4 class="alert-heading">Oops ! :(</h4>
        <p>Your file has been verified, some errors have been found. Please fix them before calculate. You can refresh this page to make verification again after your edits.</p>
        <hr>
        <ul>';

        // List errors
        for ($i = 0; $i < count($verifyResponse['errors']); $i++) {
            echo '<li>' . $verifyResponse['errors'][$i] . '</li>';
        }

        echo'</ul>
    </div>';
}
