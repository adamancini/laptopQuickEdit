<?php
include_once "db.php";
include_once "credentials.php";
//echo '<h2>Web Service Test - get issue #1 details</h2>';
try {
	$client = new SoapClient(NULL,
	array(
		"location"	=>	"https://ithelp.clemson.edu/MRcgi/MRWebServices.pl",
		"uri"		=>	"MRWebServices",
		"style"		=>	SOAP_RPC,
		"use"		=>	SOAP_ENCODED));

	$iss_details = $client->MRWebServices__getIssueDetails($username,$pw,'','4',$_GET["ticketnum"]);

	print "<h2>Issue details:</h2>";
	$descriptions = $iss_details->allDescriptions;
	$vendor = $iss_details->Vendor;
	$serialno = $iss_details->Serial__bNumber;
	echo '<h3>You are working on a ' . $vendor . ' with serial number ' . $serialno . '.</h3><br>';

	if(is_array($descriptions)){
		foreach($descriptions as $desc) {
			echo '<h4>' . $desc->stamp . '</h4>';
			echo $desc->data;
			echo '<br>';
		}
	}
	else {
		echo "Invalid ticket number.";
	}
	//print"</pre><br>\n";
	} catch (SoapFault $exception) {
	echo "Found an error! Emailing administrators.  Please try again.";
	mail("mancini.iv@gmail.com","SOAP Exception, Details page",$exception);
	//echo $exception;
}
?>

