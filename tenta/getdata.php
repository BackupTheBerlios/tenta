<?php
// Syntax:getdata.php?id=<id>

include("config.inc.php");

if($id) {
	mysql_connect($mysqlHost, $mysqlUser, $mysqlPassword);
	mysql_select_db($mysqlDB);
	$query = "select bin_data,filetype from binary_data where id=$id";
	$result = mysql_query($query) or die ("Invalid query");
	$data = mysql_result($result,0,"bin_data");
	$type = mysql_result($result,0,"filetype");
	Header( "Content-type: $type");
	echo $data;
};
?>



