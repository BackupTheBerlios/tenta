<?php
/* Changelog:
	2002-09-18 Jonas Björk, jonas@mbs.nu
		* Skapade sidan och implementerade den i TestIT!
*/
session_start();
$sida="admin";
include "Login.php";
include "functions.inc.php";

/* Lite funktioner som används av sidan */

function searchform(){
	global $PHP_SELF;
	print "<center><form method=post action=" .$PHP_SELF. " name=srch>
	Sök efter: <input type=\"text\" name=\"namn\">
	<input type=\"submit\" name=\"s\">
	</form></center>";
} // searchform()

function search() {
	global $namn;
	$query = "select * from User where namn LIKE '%$namn%' order by grupp, namn";
	$result = mysql_query($query) or die ("Error in query");

	print "<center><table width=400>\n";
	print "<tr><td>Grupp</td><td>Namn</td></tr>\n";
	while($row = mysql_fetch_array($result)){
		print "<tr><td>".$row["grupp"]."</td><td><a href=\"".$PHP_SELF."?mi=yes&ID=".$row["ID"]."\">".$row["namn"]."</a></td></tr>";
	}
	print "</table></center>\n";
} // search()

function showresult() {
	global $ID,$font;
	$query = "select namn from User where ID=".$ID;
	$result = mysql_query($query) or die ("Fel 40!");
	while($row=mysql_fetch_array($result)){
		print"<center><h2>".$row[namn]."</h2></center>";
	}
	$query = "select DISTINCT(prov_ID) from Svar where user_ID=".$ID." ORDER BY prov_ID";
	$result = mysql_query($query) or die ("Error in Query: $query");
	$svarat=NULL;
	while($row=mysql_fetch_array($result)){
		if($svarat){
			$svarat="$svarat OR";
		}
		$svarat="$svarat ID=".$row["prov_ID"];
	}
	$query = "select * from Prov where $svarat ORDER BY prov";
	$resultProv=mysql_query($query);
	print '<CENTER>
		<TABLE BORDER=0>
		<TR>
			<TD><font face="'.$font.'" size="'.$fontSize.'"><b>Prov</b></font></TD>
			<TD><font face="'.$font.'" size="'.$fontSize.'"><b>Resultat</b></font></td>
		</TR>';
	while($row=mysql_fetch_array($resultProv)){
		print '<TR><TD VALIGN=bottom>';
		$tmp =  $row["ID"];
		print '<font face="'.$font.'" size="'.($fontSize-1).'">';
		print $row["prov"];
		print "</font>";
		print "</TD>";
		$svarQ="select SUM(points) from Svar where User_ID=".$ID." AND Prov_ID=$tmp";
		$svarR=mysql_query($svarQ) or die("error in query: $svarQ");
		print "<TD VALIGN=top>";

		if($svar=mysql_fetch_array($svarR)){
			$query = "select * from Prov where ID=$tmp";
			$result = mysql_query("$query") or die("Error in query: $query");
			$rowProv=mysql_fetch_array($result);
			print "<img  src=\"rateme.php?type=small&result=".$svar["SUM(points)"]."&max=".$rowProv["Max"]."&g=".$rowProv["G"]."&vg=".$rowProv["VG"]."\">";
		}
		print "</TD>";
		print "</TR>";
	}
	print "</TABLE>";
} // showresult()
?>

<?php /* Skriva ut HTML-dokumentet */ ?>
<head>
	<title>Sök användare</title>
</head>
<body bgcolor="#ffffff" text="#000000">
<?php
	meny();
	searchform(); // skriv ut sökfältet
	if($namn) { search(); }
	if($mi) { showresult(); }
	print "<br><br>";
	meny();
?>
</body></html>
<?php mysql_close($link); ?>
