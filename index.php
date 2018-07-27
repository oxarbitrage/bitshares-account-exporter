<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

//$es_wrapper_url = "http://185.208.208.184:5000";
$es_wrapper_url = "http://95.216.32.252:5000"; // clockwork backup


if(isset($_POST['download'])) {
	
	$account_id = addslashes($_POST['account_id']);
	$start_date = addslashes($_POST['start_date']);
	$end_date = addslashes($_POST['end_date']);
	$operation_type = addslashes($_POST['operation_type']);
	$output = addslashes($_POST['output']);
	
	$f_start_date = str_replace(" ", "T", $start_date);
	$f_end_date = str_replace(" ", "T", $end_date);
	
	$filename = "$account_id-from$f_start_date-to$f_end_date.csv";
	header('Content-Type: application/csv');
	header('Content-Disposition: attachment; filename="'.$filename.'";');
	
	if($operation_type == "ALL") {
		$es_query = $es_wrapper_url . "/get_account_history?account_id=$account_id&from=0&size=10000&sort_by=-block_data.block_time&from_date=$f_start_date&to_date=$f_end_date";
	}
	else {
		$es_query = $es_wrapper_url . "/get_account_history?account_id=$account_id&from=0&size=10000&sort_by=-block_data.block_time&from_date=$f_start_date&to_date=$f_end_date&operation_type=$operation_type";
	}
	//echo $es_query;
	$response = file_get_contents($es_query);

	$json = json_decode($response);
	
	$f = fopen("tmp/$filename", "w");
	
	$header = array(
				"Account History - Account", "Account History - ID", "Account History - Next", "Account History - Operation ID", "Account History - Sequence",
				"Additional Data - Fee Data - Amount", "Additional Data - Fee Data -Asset", "Additional Data - Transfer Data - Amount", "Additional Data - Transfer Data - Asset",
				"Block Data - Block Number", "Block Data - Block Time", "Block Data - Transaction ID",
				"Operation History - Raw Operation", "Operation History - Operation in Transaction", "Operation History - Operation Result", "Operation History - Transaction in Block", "Operation Data - Virtual Operation",
				"Operation Type"
	);
	fputcsv($f, $header);
	
	foreach($json as $j){
		
		$line = array(
					$j->account_history->account, $j->account_history->id, $j->account_history->next, $j->account_history->operation_id, $j->account_history->sequence, 
					$j->additional_data->fee_data->amount, $j->additional_data->fee_data->asset, $j->additional_data->transfer_data->amount, $j->additional_data->transfer_data->asset,
					$j->block_data->block_num, $j->block_data->block_time, $j->block_data->trx_id, 
					$j->operation_history->op, $j->operation_history->op_in_trx, $j->operation_history->operation_result, $j->operation_history->trx_in_block, $j->operation_history->virtual_op,
					$j->operation_type
					);
		
		fputcsv($f, $line);
	}
	fseek($f, 0);
	readfile("tmp/$filename");
	
	unlink("tmp/$filename");
	exit;
	
	//echo "<pre>";
	//print_r($response);
	//echo "</pre>";
	
	
	
}
?>
<!doctype html>
<html lang="en" class="no-js">
<head>
  <meta charset="UTF-8" />
  <title>Bitshares Account History Exporter</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="bootstrap-4.0.0-beta.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="particleground-master/demo/css/style.css" />
  <link rel="stylesheet" href="bootstrap-datetimepicker-master/css/bootstrap-datetimepicker.min.css" />
  <link rel="stylesheet" href="font-awesome-4.7.0/css/font-awesome.min.css" />
  <link href='http://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
  <script type='text/javascript' src='particleground-master/demo/js/jquery-1.11.1.min.js'></script>
  <script src="bootstrap-4.0.0-beta.2/dist/js/bootstrap.min.js"></script>
  <script type='text/javascript' src='particleground-master/jquery.particleground.js'></script>
  <script type='text/javascript' src='particleground-master/demo/js/demo.js'></script>
  <script type='text/javascript' src='bootstrap-datetimepicker-master/js/bootstrap-datetimepicker.min.js'></script>
</head>

<body>

<div id="particles">
	<div id="intro">

		<h1>Bitshares Account History Exporter</h1>
		
		<div class="row">
			<div class="col-md-2">
			</div>
			<div class="col-md-8">
			
				<div class="well m-6" align="left">
				
					<form action="index.php" method="post">
						<input type="hidden" name="download" value="1">
						<div class="row">
							<div class="col-md-1">
							</div>
							<div class="col-md-10">
								<br>
								<div class="form-group row">
									<label class="col-sm-3 col-form-label">Account ID</label>
									<div class="col-sm-9">
										<div class="input-group">
											<input type="text" class="form-control" placeholder="1.2.356589" name="account_id" required>
											<div class="input-group-addon">
												<span class="fa fa-user-o"></span>
											</div>
										</div>
									</div>
								</div>
									
								<div class="form-group row">
									<label class="col-sm-3 col-form-label">Start Date</label>
									<div class="col-sm-9">
										<div class="input-group date">
											<input type="text" class="form-control datepicker" placeholder="2016-09-01 00:00:00" name="start_date" required>
											<div class="input-group-addon">
												<span class="fa fa-calendar"></span>
											</div>
										</div>
									</div>
								</div>
									
								<div class="form-group row">
									<label class="col-sm-3 col-form-label">End Date</label>
									<div class="col-sm-9">
										<div class="input-group date">
											<input type="text" class="form-control datepicker" placeholder="2016-12-01 00:00:00" name="end_date" required>
											<div class="input-group-addon">
												<span class="fa fa-calendar"></span>
											</div>
										</div>
									</div>
								</div>
									
								<div class="form-group row">
									<label class="col-sm-3 col-form-label">Operation type</label>
									<div class="col-sm-9">
										<select class="form-control" name="operation_type" required>
											<option value="ALL">ALL</option>
											<option value="0">TRANSFER</option>
											<option value="1">LIMIT ORDER CREATE</option>
											<option value="2">LIMIT ORDER CANCEL</option>
											<option value="3">CALL ORDER UPDATE</option>
											<option value="4">FILL ORDER</option>
											<option value="5">ACCOUNT CREATE</option>
											<option value="6">ACCOUNT UPDATE</option>
											<option value="7">ACCOUNT WHIELIST</option>
											<option value="8">ACCOUNT UPGRADE</option>
											<option value="9">ACCOUNT TRANSFER</option>
											<option value="10">ASSET CREATE</option>
											<option value="11">ASSET UPDATE</option>
											<option value="12">ASSET UPDATE BITASSET</option>
											<option value="13">ASSET UPDATE FEED PRODUCERS</option>
											<option value="14">ASSET ISSUE</option>
											<option value="15">ASSET RESERVE</option>
											<option value="16">ASSET FUND FEE POOL</option>
											<option value="17">ASSET SETTLE</option>
											<option value="18">ASSET GLOBAL SETTLE</option>
											<option value="19">ASSET PUBLISH FEED</option>
											<option value="20">WITNESS UPDATE</option>
											<option value="21">PROPOSAL CREATE</option>
											<option value="22">PROPOSAL UPDATE</option>
											<option value="23">PROPOSAL DELETE</option>
											<option value="24">WITHDRAW PERMISSION CREATE</option>
											<option value="25">WITHDRAW PERMISSION</option>
											<option value="26">WITHDRAW PERMISSION CLAIM</option>
											<option value="27">WITHDRAW PERMISSION DELETE</option>
											<option value="28">COMITEE MEMBER CREATE</option>
											<option value="29">COMITEE MEMBER UPDATE</option>
											<option value="30">COMITEE MEMBER UPDATE GLOBAL PARAMETERS</option>
											<option value="31">VESTING BALANCE CREATE</option>
											<option value="32">VESTING BALANCE WITHDRAW</option>
											<option value="33">WORKER CREATE</option>
											<option value="34">CUSTOM</option>
											<option value="35">ASSERT</option>
											<option value="36">BALANCE CLAIM</option>
											<option value="37">OVERRIDE TRANSFER</option>
											<option value="38">TRANSFER TO BLIND</option>
											<option value="39">BLIND TRANSFER</option>
											<option value="41">ASSET SETTLE CANCEL</option>
											<option value="43">FBA DISTRIBUTE</option>
										</select>
									</div>
								</div>
			  
								<fieldset class="form-group">
									<div class="row">
										<legend class="col-form-legend col-sm-3">Output</legend>
										<div class="col-sm-9">
											<div class="form-check">
												<label class="form-check-label">
													<input class="form-check-input" type="radio" name="output" value="excel" disabled>
													Excel
												</label>
												</div>
											<div class="form-check">
												<label class="form-check-label">
													<input class="form-check-input" type="radio" name="output" value="csv" checked>
													CSV
												</label>
											</div>
										</div>
									</div>
								</fieldset>
								  
								<div class="form-group row">
									<div class="col-sm-10">
										<button type="submit" class="btn">Download</button>
									</div>
								</div>
			  
							</div>
							<div class="col-md-1">
							</div>
						</div>
					</form>
				</div>
			</div>
			<div class="col-md-2">
			</div>
		</div>
	</div>
</div>

<a href="https://github.com/oxarbitrage/bitshares-account-exporter"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://camo.githubusercontent.com/38ef81f8aca64bb9a64448d0d70f1308ef5341ab/68747470733a2f2f73332e616d617a6f6e6177732e636f6d2f6769746875622f726962626f6e732f666f726b6d655f72696768745f6461726b626c75655f3132313632312e706e67" alt="Fork me on GitHub" data-canonical-src="https://s3.amazonaws.com/github/ribbons/forkme_right_darkblue_121621.png"></a>

        <script type="text/javascript">
$('.datepicker').datetimepicker({format: 'yyyy-mm-dd hh:ii:ss'});

        </script>

</body>
</html>





