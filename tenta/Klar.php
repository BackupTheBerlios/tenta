<?
session_start();
$sida="prov";
$font="<font face=\"Verdana, Arial, Sans-Serif\" size=\"2\">";
include "Login.php";
	
	$query = "select * from Prov where ID=".$HTTP_SESSION_VARS["ProvID"];
	$result = mysql_query("$query");
	$rowProv=mysql_fetch_array($result);

	$pointQ="select SUM(points) from Svar where User_ID=".$HTTP_SESSION_VARS["UserID"]." AND Prov_ID=".$HTTP_SESSION_VARS["ProvID"];
	$pointR=mysql_query($pointQ) or die("Error in query: $pointQ");
	$rowUser=mysql_fetch_array($pointR);

	$restQ="select * from Svar where corrected=0 AND User_ID=".$HTTP_SESSION_VARS["UserID"]." AND Prov_ID=".$HTTP_SESSION_VARS["ProvID"];
	$restR=mysql_query($restQ);
	while($rest=mysql_fetch_array($restR)){
		if($more){
			$more="$more OR ";
		}
		$more = "ID=$more".$rest["question_ID"];
	}

	print "<br><br><center><h2>Ditt resultat:</h2></center>";

	print "<center><br><br>";
	print "<img src=\"rateme.php?result=".$rowUser["SUM(points)"]."&max=".$rowProv["Max"]."&g=".$rowProv["G"]."&vg=".$rowProv["VG"]."\"><br><br>";

	if($more){
		$pointMaxQ="select SUM(points) from Question where $more";
		$pointMaxR=mysql_query($pointMaxQ) or die("Error in query: $pointMaxQ");
		$rowMax=mysql_fetch_array($pointMaxR);
		print "Du kan max fa<BR>";
		$max = $rowUser["SUM(points)"]+$rowMax["SUM(points)"];
		print "<img src=\"rateme.php?result=".$max."&max=".$rowProv["Max"]."&g=".$rowProv["G"]."&vg=".$rowProv["VG"]."\"><br><br>";
	}

        print $font."Dina po\xe4ng: " . $rowUser["SUM(points)"] . "&nbsp;&nbsp;";
        print "Max po\xe4ng: " . $rowProv["Max"] . "</font><br>";

	print "<p>".$font."Grön färg = Betyget Väl Godkänd (VG)<br>";
	print "Gul färg = Betyget Godkänd (G)<br>";
	print "Röd färg = Betyget Icke Godkänd (IG)</p>";


?>

<table width=600 border=0 cellspacing=0 cellpadding=1>
<tr bgcolor=#000000><td>

<table width=100% border=0 cellspacing=0>
<tr bgcolor=#ffffff><td><b><?php print($font);?>Du svarade fel på följande fråga/frågor:</b><br><br></td></tr>

<?php
	$felsvarQ="select * from Svar where User_ID=".$HTTP_SESSION_VARS["UserID"]." AND Prov_ID=".$HTTP_SESSION_VARS["ProvID"]." AND points=0";
	$felsvarR=mysql_query($felsvarQ);
	while($felsvar=mysql_fetch_array($felsvarR)){
		$fraganQ="select * from Question where ID=".$felsvar["question_ID"]." AND correct=1";
		$fraganR=mysql_query($fraganQ) or die("Error in query $fraganQ");
		if($fragan=mysql_fetch_array($fraganR)){
			print "<tr bgcolor=#ffffff><td><li>" .$font. $fragan["question"] . "</td></tr>";
		}
	}
?>
	</table>
	</td></tr>
	</table>
	<br><br>
	<a href="logout.php">Klar! Logga ut mig!</a>
	</center>
</body>
</html>

<?php
	mysql_close($link);
?>





