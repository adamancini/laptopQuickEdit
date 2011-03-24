<?php
include_once 'db.php';
$ticket = $_POST["ticketno"];
$userid = $_POST["userid"];
$pass = $_POST["password"];
$details = $_POST["textarea-description"];
$status = $_POST["select-status"];
//$tech = $_POST["tech"];
//echo '<pre>';
//print_r($_POST);

$q = "SELECT status FROM BARCODE_Status WHERE id = " . $status;
try { 
	$result = mysql_query($q);
	$out = mysql_fetch_assoc($result);
} catch (Exception $e) {
	echo 'Problem accessing database.';
}

try {
	$client = new SoapClient(NULL,
	array(	"location"=>"https://ithelp.clemson.edu/MRcgi/MRWebServices.pl",
		"uri"=>"MRWebServices",
		"style"=>SOAP_RPC,
		"use" => SOAP_ENCODED));
		
	if ($status == 0) {	// no change in status
		$issue_number = $client->MRWebServices__editIssue($userid,$pass,'',array(
			projectID =>4,
			mrID => $ticket,
			description => $details
			));
	} else {		//change in status
		$issue_number = $client->MRWebServices__editIssue($userid,$pass,'',array(
			projectID =>4,
			mrID => $ticket,
			description => $details,
			//projfields => array(Tech__bNotes => $tech),
			status => $out["status"]
			));
	}
	print "<BR><b> Updated Ticket<hr>\n";
} catch (SoapFault $exception) {
	print "ERROR! - Got a SOAP exception:<br>";

	echo $exception;
	//echo "</pre>";
}
?>

