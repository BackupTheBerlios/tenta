<?php
session_start();
$sida="admin";
include "Login.php";
include "functions.inc.php";
/*
	Changelog
	2002-09-18 Jonas Björk, jonas@mbs.nu
		* Fixade sortering av tabellerna med fallande och stigande sortering.
		* Rensade ur koden, snyggade upp lite.
*/

function visaProv() {
	/* Visar alla prov som finns i databasen */

	global $PHP_SELF,$prov;
	$extra = "";
	$sql = "select ID,prov from Prov order by prov";
	$data = mysql_query($sql);
	print "<form name=frmProv action=".$PHP_SELF." method=post>\n";
	print "<input type=\"hidden\" name=\"vad\" value=\"prov\">";
	print "<select name=\"prov\">\n";
	while($row=mysql_fetch_array($data)) {
		if($row[ID]==$prov){ $extra = " selected"; }
		print "<option value=\"".$row[ID]."\"".$extra.">".$row[prov]."</option>\n";
		$extra = "";
	}
	print "</select>\n";
	print "<input type=\"submit\" name=\"btnProv\" value=\"Visa prov\">\n";
	print "</form>\n";
} // visaProv

function visaSubj() {
	/* Visar alla ämnen som finns i databasen */

	global $PHP_SELF,$subj;
	$extra = "";
	$sql = "select ID,namn from Subject order by namn";
	$data = mysql_query($sql);
	print "<form name=frmSubj action=".$PHP_SELF." method=post>\n";
	print "<input type=\"hidden\" name=\"vad\" value=\"subj\">";
	print "<select name=\"subj\">\n";

	while($row=mysql_fetch_array($data)) {
		if($row[ID]==$subj){ $extra = " selected"; }
		print "<option value=\"".$row[ID]."\"".$extra.">".$row[namn]."</option>\n";
		$extra = "";
	}
	print "</select>\n";
	print "<input type=\"submit\" name=\"btnSubj\" value=\"Visa ämne\">\n";
	print "</form>\n";
} // visaSubj

function visaTabell($sql) {
	/* Ritar ut tabellen */
	global $PHP_SELF,$prov,$subj,$otype;

	$extra="";
	if(!isset($otype)) { $otype="desc"; }
		else if($otype=="desc") { $otype="asc"; }
		else if($otype=="asc") { $otype="desc"; }
	$string="&prov=".$prov."&subj=".$subj."&otype=".$otype;

	print "<center>\n";
	print "<table border=1>\n";
	print "<tr><td><a href=\"".$PHPSELF."?order=question_ID".$string."\">ID</a></td><td><a href=\"".$PHPSELF."?order=question".$string."\">Fråga</a></td><td><a href=\"".$PHPSELF."?order=antal".$string."\">Antal svar</a></td><td><a href=\"".$PHPSELF."?order=tidMin".$string."\">tidMin</a></td><td><a href=\"".$PHPSELF."?order=tidMax".$string."\">tidMax</a></td><td><a href=\"".$PHPSELF."?order=tidMedel".$string."\">tidMedel</a></td><td><a href=\"".$PHPSELF."?order=pntMax".$string."\">Poäng</td><td><a href=\"".$PHPSELF."?order=ratt".$string."\">Frekvens rätt i %</a></td></tr>\n";

	$data = mysql_query($sql);
	while($row=mysql_fetch_array($data)) {
		if($row["question"]!=NULL) {
			if($row['ratt']>80) { $extra=" bgcolor=#ff0000"; }
			if($row['ratt']<20) { $extra=" bgcolor=#00ff00"; }
			print "<tr".$extra."><td>".$row['fraga'] . "</td><td>".$row['question']."</td><td>".$row['antal']."</td><td>".$row['tidMin']."</td><td>".$row['tidMax']."</td><td>".$row['tidMedel']."</td><td>".$row['pntMax']."</td><td>".$row['ratt']."%</td></tr>\n";
		}
		$extra = "";
	}
	print "</table>\n";
	print "</center>\n";
} // visaTabell

?>
<?php /* Skapa HTML-dokumentet och visa en rubrik med meny på sidan */ ?>
<html>
<head>
	<title>Statistik</title>
</head>
<body>
<center>
	<h1>Statistik</h1>
	<?php meny(); ?>
	<table>
		<tr><td><?php visaProv(); ?></td><td><?php visaSubj(); ?></td><td><form name=frmAlla action=<?=$PHP_SELF; ?> method=post><input type=hidden name=vad value=alla><input type=submit name=btnAlla value="Visa Alla"></form></td></tr>
	</table>
</center>
<?php
if($vad=="alla" OR (!isset($vad) AND !isset($order))) {
	$sql = "select question_ID as fraga, rpad(question,50,'') as question, count(question_id) as antal, min(klar-start) as tidMin, max(klar-start) as tidMax, round(avg(klar-start),0) as tidMedel, min(Svar.points) as pntMin, max(Svar.points) as pntMax, round((avg(Svar.points)/max(Svar.points))*100,1) as ratt from Svar inner join Question on Svar.question_ID=Question.ID GROUP BY question_ID";
	visaTabell($sql);
}
if($vad=="prov") {
	$sql = "select question_ID as fraga, rpad(question,50,'') as question, count(question_id) as antal, min(klar-start) as tidMin, max(klar-start) as tidMax, round(avg(klar-start),0) as tidMedel, min(Svar.points) as pntMin, max(Svar.points) as pntMax, round((avg(Svar.points)/max(Svar.points))*100,1) as ratt from Svar inner join Question on Svar.question_ID=Question.ID AND Svar.prov_ID='".$prov."' GROUP BY question_ID";
	visaTabell($sql);
}
if($vad=="subj") {
	$sql = "select question_ID as fraga, rpad(question,50,'') as question, count(question_id) as antal, min(klar-start) as tidMin, max(klar-start) as tidMax, round(avg(klar-start),0) as tidMedel, min(Svar.points) as pntMin, max(Svar.points) as pntMax, round((avg(Svar.points)/max(Svar.points))*100,1) as ratt from Svar inner join Question on Svar.question_ID=Question.ID AND Question.subject_ID='".$subj."' GROUP BY question_ID";
	visaTabell($sql);
}
if(isset($order)) {
	$extra="";
	if($prov!="") { $extra=" AND Svar.prov_ID='".$prov."'"; }
	if($subj!="") { $extra=" AND Question.subject_ID='".$subj."'"; }

	$sql = "select question_ID as fraga, rpad(question,50,'') as question, count(question_id) as antal, min(klar-start) as tidMin, max(klar-start) as tidMax, round(avg(klar-start),0) as tidMedel, min(Svar.points) as pntMin, max(Svar.points) as pntMax, round((avg(Svar.points)/max(Svar.points))*100,1) as ratt from Svar inner join Question on Svar.question_ID=Question.ID".$extra." GROUP BY question_ID ORDER BY ".$order." ".$otype;
	visaTabell($sql);
}
?>

<?php /* Skriva ut botten på sidan */ ?>

<?php meny(); ?>
</form>
</body>
</html>
<?phpmysql_close($link); ?>
