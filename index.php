<?php

// Entry points
$fileName = isset($_GET["fileName"]) ? $_GET["fileName"] : '';
$typeBroker =  isset($_GET["typeBroker"]) ? $_GET["typeBroker"] : 'kraken';
$step = isset($_GET["step"]) && $_GET["step"] > 0 ? $_GET['step'] : 0;
$previousFileName = isset($_GET["previousFileName"]) ? $_GET["previousFileName"] : '';
$previousCashIn = isset($_GET["previousCashIn"]) && $_GET["previousCashIn"] > 0 ? $_GET["previousCashIn"] : 0;

// Require / Data
require('scripts/callApi.php'); 
$remainingCredit = callApi('getRemainingCreditApi', array());
if(!$remainingCredit) {
	$remainingCredit = 0;
}
$stateCredit = $remainingCredit > 5 ? 'success' : ($remainingCredit > 1 ? 'warning' : 'danger');

if ($step > 0) {
	require('broker/' . $typeBroker . '/' . 'require.php');
	require('scripts/csvHelper.php'); 
	require('scripts/calculate.php'); 
	
	// Get formatted data from CSV
	$data = getFormattedData($fileName, $typeBroker);
}
?>

<!doctype html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Crypto Plus-value</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<link rel="stylesheet" href="styles/styles.css">
	<link rel="stylesheet" type="text/css" href="styles/print.css" media="print">
</head>
<body>
<div class="container">
		<div class="row">
			<div class="col-12">
				<div class="row header align-items-center">
					<div class="col-6">
						<a href="index.php"><h1>Crypto calculator</h1></a>
					</div>
					<div class="col-6 text-right">
						<button type="button" class="btn btn-<?php echo $stateCredit; ?>">
							API remaing credit <span class="badge badge-light remaing-credit"><?php echo $remainingCredit; ?></span>
						</button>
					</div>
				</div>
				<?php 
				if ($step > 0) {
					if ($step == 1) {
						include('template/verification.php');
					} else {
						include('template/calculate.php');
					}
				} else {
					include('template/begin.php');
				} ?>
				
			</div>
		</div>
	</div>

	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	<script src="styles/js/app.js"></script>
</body>
</html>