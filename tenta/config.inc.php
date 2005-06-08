<?php
/*
	Denna fil innehåller konfigurationen för TestIT!
	ChangeLog:
		2002-09-18 Jonas Björk, jonas@mbs.nu
			* Skapade filen och började implementera den i TestIT!
*/

// IMAP
$imapHost="{localhost:143}INBOX";

// mySQL
$mysqlHost="localhost";
$mysqlUser="tenta";
$mysqlPassword="";
$mysqlDB="tenta";
$link = mysql_connect($mysqlHost,$mysqlUser,$mysqlPassword) or die ("Could not connect");
mysql_select_db($mysqlDB);

$font="<font face=\"Verdana, Arial, Sans-Serif\" size=\"2\">";
$fontSize="1";

?>
