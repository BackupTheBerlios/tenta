<?
session_start();
$sida="MinaProv";
$font="<font face=\"Verdana, Arial, Sans-Serif\" size=\"2\">";
include "Login.php";
?>

<head>
	<title>mina prov</title>
</head>
<body bgcolor="#ffffff" text="#000000">

<?php
	include("ikon.inc.php");
?>

<?php
if($Prov){
	$query = "select * from Prov where ID=$Prov";
	$result = mysql_query("$query");
	$rowProv=mysql_fetch_array($result);

	$pointQ="select SUM(points) from Svar where User_ID=".$HTTP_SESSION_VARS["UserID"]." AND Prov_ID=$Prov";
	$pointR=mysql_query($pointQ) or die("Error in query: $pointQ");
	$rowUser=mysql_fetch_array($pointR);

	$restQ="select * from Svar where corrected=0 AND User_ID=".$HTTP_SESSION_VARS["UserID"]." AND Prov_ID=$Prov";
	$restR=mysql_query($restQ);
	while($rest=mysql_fetch_array($restR)){
		if($more){
			$more="$more OR ";
		}
		$more = "ID=$more".$rest["question_ID"];
	}
	$medel="select SUM(points),COUNT(DISTINCT(User_ID)) from Svar where Prov_ID=$Prov";
	$medel=mysql_query($medel);
	if($medelRow=mysql_fetch_array($medel)){
		$medel=$medelRow["SUM(points)"]/$medelRow["COUNT(DISTINCT(User_ID))"];
	}

	print "<center><h1>".$rowProv["prov"]."</h1></center>\n";

	print "<center>\n";
	print "<table border=0>";
	print "<tr>\n<td>".$font."Ditt resultat: </td>\n";
	print "<td><img src=\"rateme.php?result=" . $rowUser["SUM(points)"] . "&max=" . $rowProv["Max"] . "&g=" . $rowProv["G"] . "&vg=" . $rowProv["VG"] . "&type=small\"></td>\n</tr>\n";

	print "<tr>\n<td>".$font."Medel: </td>\n";
	print "<td><img src=\"rateme.php?result=" . $medel . "&max=" . $rowProv["Max"] . "&g=" . $rowProv["G"] . "&vg=" . $rowProv["VG"] . "&type=small\"></td></tr>\n";
	print "</table>\n";
	print "</center>\n";

	if($more){
		$pointMaxQ="select SUM(points) from Question where $more";
		$pointMaxR=mysql_query($pointMaxQ) or die("Error in query: $pointMaxQ");
		$rowMax=mysql_fetch_array($pointMaxR);
		print "Du kan max fa<BR>";
		$max = $rowUser["SUM(points)"]+$rowMax["SUM(points)"];
		print "<img src=\"rateme.php?result=".$max."&max=".$rowProv["Max"]."&g=".$rowProv["G"]."&vg=".$rowProv["VG"]."\"><br><br>";
	} 
	
		print "<p align=\"center\">".$font."Dina po\xe4ng: " . $rowUser["SUM(points)"] . "&nbsp;&nbsp;";
        print "Max po\xe4ng: " . $rowProv["Max"] . "</p>";

	print "<p align=\"center\">".$font."Grön färg = Betyget Väl Godkänd (VG)<br>";
	print "Gul färg = Betyget Godkänd (G)<br>";
	print "Röd färg = Betyget Icke Godkänd (IG)</p>";

	print "<br><br>";

// skriva ut alla frågor med provtagarens svar
print "<center>";

	$svarQ="select * from Svar where User_ID=".$HTTP_SESSION_VARS["UserID"]." AND Prov_ID=$Prov";
	$svarR=mysql_query($svarQ);
	while($svar=mysql_fetch_array($svarR)){
		$fraganQ="select * from Question where ID=".$svar["question_ID"];
		$fraganR=mysql_query($fraganQ) or die("Error in query $fraganQ");
		$fragan=mysql_fetch_array($fraganR);

		// i $svar["points"] har vi hur många poäng frågan gett

print "<table border=0 width=600>";

		print "<tr><td>" . $font . $fragan["question"] . "</td></tr>";
		//print "<br><br>";
		print "<tr><td>" . $font;
		if($svar["points"]>0){
			print "<font color='green'>";
		}else{
			print "<font color='red'>";
		}
		$svars=explode("&",$svar["svar"]);
		foreach($svars as $svarat){
			print $svarat;
			print "<br>";
		}
//		print "<font color='black'>";
//		print "Points:".$svar["points"];
		print "</td></tr></table><br>";
	}
}

$query = "select DISTINCT(prov_ID) from Svar where user_ID=".$HTTP_SESSION_VARS["UserID"]." ORDER BY prov_ID";
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

?>
<CENTER>
<TABLE BORDER=0>
<TR>
	<TD><font face=\"verdana,arial,sans-serif\" size=\"2\"><b>Prov</b></font></TD>
	<TD><font face=\"verdana,arial,sans-serif\" size=\"2\"><b>Ditt resultat</b></font></td>
</TR>
<?
while($row=mysql_fetch_array($resultProv)){
	print "<TR><TD VALIGN=bottom>";
	$tmp =  $row["ID"];
	print "<font face=\"verdana,arial,sans-serif\" size=\"2\"><A href=$PHP_SELF?Prov=$tmp>";
	print $row["prov"];
	print "</font></A>";
	print "</TD>";
	$svarQ="select SUM(points) from Svar where User_ID=".$HTTP_SESSION_VARS["UserID"]." AND Prov_ID=$tmp";
	$svarR=mysql_query($svarQ) or die("error in query: $svarQ");
	print "<TD VALIGN=top>";

	if($svar=mysql_fetch_array($svarR)){
		$query = "select * from Prov where ID=$tmp";
		$result = mysql_query("$query") or die("Error in query: $query");
		$rowProv=mysql_fetch_array($result);
		print "<img  src=\"rateme.php?result=".$svar["SUM(points)"]."&max=".$rowProv["Max"]."&g=".$rowProv["G"]."&vg=".$rowProv["VG"]."&type=small\">";
	}
	print "</TD>";
	print "</TR>";
}
?>

</TABLE>
<?

print "<br><br><A HREF=logout.php>Klar! Logga ut mig!</A>";
print "</CENTER>";
mysql_close($link);
?>


















