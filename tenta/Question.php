<?
session_start();
$sida="prov";
$bgCOL="#3399FF";
$font="<font face=\"Verdana, Ariel, Sans-Serif\" size=\"2\">";
include "Login.php";

if($Avsluta){
  mysql_close($link);
?>
	<Meta HTTP-EQUIV="refresh" CONTENT="0;URL=Klar.php">
<?
	exit;
}

$questions = $HTTP_SESSION_VARS["ProvQuest"];
$count = $HTTP_SESSION_VARS["QuestionCount"];

if($Next){
	if($Svarat){
		$alternativ = $HTTP_SESSION_VARS["ALT"];
		$correct = 1;
		if(is_array($Svarat)){
			foreach($alternativ as $alt){
				$correctQ = "select * from Svarsalternativ where ID='$alt'";
				$correctR = mysql_query($correctQ) or die("Error in Qyery: $correctQ");
				$correctRow = mysql_fetch_array($correctR);
				$temp = $correctRow["svar"];
				if($correctRow["correct"]==1){
					if(in_array($alt, $Svarat)){
						$correct = 1&&$correct;
						$Svar = "$Svar $temp True & ";
					}else{
						$correct = 0;
						$Svar = "$Svar $temp False & ";
					}
				}else{
					if(in_array($alt, $Svarat)){
						$correct = 0;
						$Svar = "$Svar $temp True & ";
					}else{
						$correct = 1&&$correct;
						$Svar = "$Svar $temp False & ";
					}
				}
				
			}
		}else{
			$correctQ = "select * from Svarsalternativ where ID=$Svarat";
			$correctR = mysql_query($correctQ) or die("Error in Qyery: $correctQ");
			$correctRow = mysql_fetch_array($correctR);
			$Svar = $correctRow["svar"];
			if($correctRow["correct"]==1){
				$correct = 1;
			}else{
				$correct = 0;
			}
		}
	}
	$quest = $questions[$count-1];
	$resultQ = "select * from Question where ID=$quest[0]";
	$result = mysql_query($resultQ) or die("Error in query: $resultQ");
	$row = mysql_fetch_array($result);

	if($row["correct"]==1){
		if($correct){
			$corrected=1;
			$point = $row["points"];
		}else{
			$corrected=1;
		}
	}else{
		$corrected=0;
	}
	if(!$Svar||($Svar=="")){
		$corrected=1;
	}

	$userID = $HTTP_SESSION_VARS["UserID"];
	$provID = $HTTP_SESSION_VARS["ProvID"];
	$questionID=$quest[0];
	$stop = date("Y-m-d H:i:s");
	$SvarQ="insert into Svar values('$userID','$provID','$questionID','$Svar','$corrected','$start','$stop','$point')";
	mysql_query($SvarQ);
}

$svar = NULL;
$quest = $questions[$count];
$HTTP_SESSION_VARS["QuestionCount"] = $count +1;
if((count($questions)>$count)){
	$i = 0;
	if($quest){
		foreach($quest as $q){
			if($i==0){
				$questionQ="select * from Question where ID=$q";
				$questionR=mysql_query($questionQ) or die("Error in query: $questionQ");
				$questionRow=mysql_fetch_array($questionR);
				$i = $i + 1;
			}else{
				if($i!=1){
					$svar = "$svar OR";
				}
				$svar = "$svar ID=$q";
				$i=$i+1;
			}
		}
	}
	$temp=0;
	if($svar){
		$svar = "where $svar";
		$svarQ="select * from Svarsalternativ $svar ORDER BY RAND()";
		$svarR=mysql_query($svarQ) or die("Error in query: $svarQ");
		while($svarRow=mysql_fetch_array($svarR)){
			$temp = $svarRow["correct"] + $temp;
		}
	}
	if($temp<1){
		$type = 0;
	}else if($temp==1){
		$type = 1;
	}else{
		$type = 2;
	}
}else{
  mysql_close($link);
?>
	<Meta HTTP-EQUIV="refresh" CONTENT="0;URL=Klar.php">
<?
	exit;
}

if($HTTP_SESSION_VARS["ProvTIME"]!=NULL){
	$Spent=0;
	$TimeSpentQ ="select (TIME_TO_SEC(klar)-TIME_TO_SEC(start)) as SEC from Svar where Prov_ID=".$HTTP_SESSION_VARS["ProvID"]." AND user_ID=".$HTTP_SESSION_VARS["UserID"];
	$TimeSpent=mysql_query($TimeSpentQ) or die("Error in Query: $TimeSpentQ");
	while($row=mysql_fetch_array($TimeSpent)){
		$Spent = $Spent + $row["SEC"];
	}

	if($Spent>=($HTTP_SESSION_VARS["ProvTIME"]*60)){
		print $HTTP_SESSION_VARS["ProvTIME"]*60;
		print "\n$Spent\n";
		print "Tiden ar Slut<BR>";
		print "<A HREF=Klar.php>Fortsätt</A>";
		mysql_close($link);
		exit;
	}
}

?>

<HTML>
<HEAD>
	<TITLE>Fr&aring;ga <? print $count+1; ?></TITLE>
</HEAD>
<BODY bgcolor="#ffffff" text="#000000">
<br><br>
<FORM method=post>
	<CENTER>
	<table width=600 border=0 cellspacing=0 cellpadding=1>
	<tr bgcolor="#000000">
	<td>

		<TABLE WIDTH=100% BORDER=0 cellspacing=0 cellpadding=2>
			<TR bgcolor=<?php print($bgCOL);?>>
				<TD COLSPAN=3 WIDTH=100% align=right>
					<B>Fr&aring;ga <? print $count+1; ?></B>
					<INPUT TYPE=HIDDEN NAME=start VALUE="<? print date("Y-m-d H:i:s"); ?>">
				</TD>
			</TR>
			<TR bgcolor="#ffffff">
				<TD COLSPAN=3 WIDTH=100% VALIGN=TOP>
					<? 
					if($questionRow["binary_data_ID"]){
						$bildID=$questionRow["binary_data_ID"];
					?>
						<A HREF="getdata.php?id=<? print $bildID; ?>" target=blank><img src="getdata.php?id=<? print $bildID ?>" Height="100" WIDTH="100" alt="Bild<? print $bildID ?>"></A> 
					<?
					}
					?>
					<br>
					<p><? print $font . $questionRow["question"]; ?></p>
					<p>
<?
	switch($type){
		case 0:		
			print "<TEXTAREA ROWS=10 COLS=60 NAME=Svar WRAP=virtual></TEXTAREA>";
			break;
		case 1:
			$alternativ=array();
			$svarR=mysql_query($svarQ);
			while($svarRow=mysql_fetch_array($svarR)){
				$id = $svarRow["ID"];
				$svaralt = $svarRow["svar"];
				array_push($alternativ,$id);
				print "<INPUT TYPE=RADIO NAME=Svarat VALUE=$id>$svaralt<BR>";
			}
			$HTTP_SESSION_VARS["ALT"]=$alternativ;
			break;
		case 2:
			$alternativ=array();
			$svarR=mysql_query($svarQ);
			while($svarRow=mysql_fetch_array($svarR)){
				$id = $svarRow["ID"];
				$svaralt = $svarRow["svar"];
				array_push($alternativ,$id);
				print "<INPUT TYPE=CHECKBOX NAME=Svarat[] VALUE=$id>$svaralt<BR>";
			}
			$HTTP_SESSION_VARS["ALT"]=$alternativ;
			break;
		}
?>
				<br><br>
					</P>
				</TD>
			</TR>
			<tr bgcolor="<?php print($bgCOL);?>">
			<td align="left" valign="top">
				 <INPUT TYPE=SUBMIT NAME=Avsluta VALUE=Avsluta>
			</td>
			<td>&nbsp;</td>
			<td align="right" valign="top">
				 <INPUT TYPE=SUBMIT NAME=Next VALUE=Nästa>
			</td>
			</tr>
		</TABLE>

		</td>
		</tr>
		</table>
	</CENTER>
</FORM>
</BODY>
</HTML>

<? mysql_close($link); ?>