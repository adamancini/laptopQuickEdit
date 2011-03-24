<?php
$username = "iwuml74";
$pw = "m8fuo7";
$database = "DCIT2804_FOOTPRINTSADMIN";
$server = "bark.clemson.edu";

mysql_connect($server,$username,$pw);
@mysql_select_db($database) or die("Unable to select database.\n");
?>
