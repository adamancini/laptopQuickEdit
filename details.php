<!DOCTYPE html>
<html>
<head>
<title>TigerTracks Quick Edit</title>
<meta name="author" content="AJ Mancini IV">
<meta name="date" content="2010-05-19T16:51:07-0400">
<meta name="copyright" content="Clemson University, 2010">
<meta name="ROBOTS" content="NOINDEX, NOFOLLOW">
<link rel="stylesheet" type="text/css" href="css/style.css"/>
<!-- Import JQuery libraries -->

<script type="text/javascript" src="javascript/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="javascript/jquery.form.js"></script>

<!-- db connection setup -->

<?php
include_once "php/db.php";
include_once "php/credentials.php";

$qStatus = "SELECT * FROM BARCODE_Status ORDER BY id ASC";
$qQuickDesc = "SELECT * FROM BARCODE_QuickDescriptions ORDER BY id ASC";
$statusID = array();
$statusText = array();
$quickID = array();
$quickShortText = array();
$quickLongText = array();

try {
	$rStatus = mysql_query($qStatus);
	$rQuickDesc = mysql_query($qQuickDesc);
	$numStatus = mysql_num_rows($rStatus);
	$numQuick = mysql_num_rows($rQuickDesc);
} catch(Exception $e) {
	echo 'Had a problem querying the database.';
}

for($i = 0; $i < $numStatus; $i++) {
	$r = mysql_fetch_assoc($rStatus);
	$statusID[] = $r["id"];
	$statusText[] = $r["status"];
}

for($i = 0; $i < $numQuick; $i++) {
	$r = mysql_fetch_assoc($rQuickDesc);
	$quickID[] = $r["id"];
	$quickShortText[] = $r["short"];
	$quickLongText[] = $r["desc"];
}

?>

<!-- My javascript functions -->
<script type="text/javascript">
$.ajaxSetup({
	cache: false
});

var submitURL = "php/submitDetails.php";
var quickDescriptions = new Array();

$(document).ready(function() {
	$("#history").html("Enter a ticket number...");
	<?php
	for($i = 0; $i < $numStatus; $i++) {
		echo '$("#select-status").append(\'<option value="'.$statusID[$i].'">'.$statusText[$i].'</option>\');' . PHP_EOL;
	}
	for($i = 0; $i < $numQuick; $i++) {
		echo '$("#select-quickdesc").append(\'<option value="'.$quickID[$i].'">'.$quickShortText[$i].'</option>\');' . PHP_EOL;
	}
	?>
	
	// Options hash for jquery.form.  Target: is the field where the result from the ajax call goes.  In this case, the #history div
	// beforeSubmit and success callbacks are disabled.
	var options = {
		target: 		"#history"
		//beforeSubmit:	showRequest,
		//success:		showResponse
	};
 
	// bind form and provide a simple callback function 
	$('#form-ticket-details').ajaxForm(options); 
	
	<?php foreach($quickLongText as $qt) {
		echo 'quickDescriptions.push("'.$qt.'");'.PHP_EOL;
	}?>
});

//pre-submit callback
function showRequest(formData, jqForm, options) {
	var querystring = $.param(formData);
	alert('About to submit: \n\n' + querystring);
	return true;
}

//post-submit callback
function showResponse(responseText, statusText, xhr, $form) {
}

function loadDetails(id) {
	if(id == "" ){
		$("#history").html("Enter a ticket number.");
		return false;
	}
	$("#history").html();
	$("#history").html("Loading...");
	x = new XMLHttpRequest();
	x.onreadystatechange=function() {
		if(x.readyState==4) {
			if(x.status==200) {
				$("#history").html(x.responseText);
			} else {
				alert("Error occurred while pulling descriptions");
			}
		}
	}
	id = encodeURIComponent(id);
	x.open("GET","php/pullDescription.php?ticketnum=" + id, true);
	x.send(null);
}

function submitDetails() {
	$("#history").html("Submitting details...");
	$("#form-ticket-details").submit();
	setTimeout("resetForm(document.getElementById('form-ticket-details'))", 8000);
}

function resetForm(form) {
	form.reset();
	$("#history").html("Enter a ticket number...");
}

function loadQuickDescription(sel) {
	$("#textarea-description").val("");
	$("#textarea-description").val(quickDescriptions[sel.options.selectedIndex]);
	
}
</script>

</head>

<body>
<?php
// echo '<pre>';
// print_r($quickID);
// print_r($quickShortText);
// print_r($quickLongText);
// echo '</pre>';
?>
<div id="header">
<img src="img/banner.png" alt="CCIT Support Center">
</div>

<h1 class="title">TigerTracks Quick Edit</h1>
<form id="form-ticket-details" action="php/submitDetails.php" method="POST" target="history" >
<div id="main">
	<div id="left">
		<div class="section-details-table">
			<table id="ticket-details-table">
				<tr class="table-row-ticketno">
					<th>Ticket #</th>
					<td><input type="text" id="ticketno" name="ticketno" tabindex="1" onChange="loadDetails($('#ticketno').val())"></td>
				</tr>
				<tr class="table-row-userid">
					<th>UserID</th>
					<td><input type="text" id="userid" name="userid" tabindex="2"></td>
				</tr>
				<tr class="table-row-password">
					<th>Password</th>
					<td><input type="password" id="password" name="password" tabindex="3"></td>
				</tr>
			</table>

			<span class="row-status">Status</span>
			<select id="select-status" name="select-status" tabindex="4"></select>
			<input type="hidden" id="status">
		</div>

		<div class="section-descriptions">
			<p class="p-descriptions">Description</p>
			<p>Anything in the Description field may be sent to customers - be professional!</p>
			<span class="row">Quick Descriptions</span>
			<select id="select-quickdesc" tabindex="0" onChange="loadQuickDescription(this)"></select>
			<textarea id="textarea-description" name="textarea-description" rows="12" cols="80"></textarea>
			<input type="button" id="button-submit" name="button-submit" value="Submit" onClick="submitDetails()">
			<input type="button" id="clear" name="clear" value="Clear" onClick='resetForm(document.getElementById("form-ticket-details"))'>
		</div>

	</div>	
		
	<div id="right">
		<div class="section-history">
			<p class="p-history">Ticket History</p>
			<div id="history" class="history"></div>
		</div>
	</div>
</div>
	
</form>
</body>
</html>