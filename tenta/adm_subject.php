<?

session_start();

$sida="admin";
include "Login.php";
include "functions.inc.php";
$delete = TRUE;

if($subDelete){
	foreach($subDelete as $sub){
		$Questions="select * from Question where subject_ID=$sub";
		$QuestionR=mysql_query($Questions);
		if(mysql_fetch_array($QuestionR)){
			$querySub="select * from Subject where ID=$sub";
			$resultSub=mysql_query($querySub);
			$subs=mysql_fetch_array($resultSub);
			$delete = FALSE; 
			$delete_namn = $subs['namn'];
			
		}else{
			$deleteQ="delete from Subject where ID=$sub";
			mysql_query($deleteQ);
			$delete = TRUE;
		}
	}
}
if($NewSubject && ($NewSubject!="")){
	$addQ="insert into Subject (namn) values('$NewSubject')";
	mysql_query($addQ); 
}
?>
<html>
<head>
	<title><? print $HTTP_SESSION_VARS["ProvNAMN"]; ?></title>
</head>
<body>
<center>
<? meny(); ?>
<form method=post action=<? print $PHP_SELF; ?>>

<h2>Lägg till ämne</h2>
<table>
	<tr>
		<td>Nytt ämne</td>
		<td><input type="text" name="NewSubject"> <input type=        "submit" value="Lägg till" NAME="Submit"></td>
	</tr>
</table>
						       
<h2>Tag bort ämnen</h2>
<?php
if(!$delete)
{
	print "Du måste ta bort alla frågor i ".$delete_namn." innan du kan ta bort ämnet.";
}
?>
	
<table>
<?
	$SubjectQ="select * from Subject order by namn";
	$SubjectR=mysql_query($SubjectQ);
	while($SubjectRow=mysql_fetch_array($SubjectR)){
?>
	<tr>
		<td width="200">
			<? print $SubjectRow["namn"]; ?>
		</td>
		<td>
			<input type="checkbox" name="subDelete[]" value="<? print $SubjectRow["ID"]; ?>">Tag bort
		</td>
	</tr>

<?		
	}
?>
	<tr>
		<td colspan="2" align="right">
			<input type="submit" value="Tag bort ämnen" name="Submit">
		</td>
	</tr>
</table>

</form>

<? meny(); ?>
</center>
</body>
</html>
