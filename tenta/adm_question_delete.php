<? 

session_start();
$sida="admin";

include "Login.php";
?>
<HTML>
<HEAD>
	<TITLE>Delete Question</TITLE>
</HEAD>
<BODY>
<?
if($id){
	$inTestQ="select * from Test where question_ID=$id";
	$inTestR=mysql_query($inTestQ);
	if($inTest=mysql_fetch_array($inTestR)){
		$isProvQ="select * from Prov where ID=".$inTest["prov_ID"];
		$isProvR=mysql_query($isProvQ);
		if($isProv=mysql_fetch_array($isProvR)){
			print "Is in use in prov ".$isProv["prov"];
		}else{
			$deleteTestQ="delete from Test where question_ID=$id";
			mysql_query($deleteTestQ);
			$deleteSvarsAltQ="delete from Svarsalternativ where question_ID=$id";
			mysql_query($deleteSvarAltQ);
			$deleteSvarQ="delete from Svar where question_ID=$id";
			mysql_query($deleteSvarQ);
			$deleteQuestionQ="delete from Question where ID=$id";
			mysql_query($deleteQuestionQ);
			print "Question deteted";
		}
	}else{
		$deleteSvarsAltQ="delete from Svarsalternativ where question_ID=$id";
		mysql_query($deleteSvarAltQ);
		$deleteSvarQ="delete from Svar where question_ID=$id";
		mysql_query($deleteSvarQ);
		$deleteQuestionQ="delete from Question where ID=$id";
		mysql_query($deleteQuestionQ);
		print "Question deteted";
	}
}
print " <BR> ";
print "<a href=adm_prov.php>AdminProv</a>";
mysql_close($link);
?>

</BODY>
</HTML>




