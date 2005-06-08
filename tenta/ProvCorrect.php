<?
	session_start();
	$sida="admin";
	include "Login.php";
	include "functions.inc.php";

?>

<HTML>
<HEAD>
	<TITLE>Correct</TITLE>
</HEAD>
<BODY>
<CENTER>

<?	
	meny();
	if(!$prov){
		print "syntaxerror: ProvCorrect.php?prov=X";
		exit;
	}

	$query = "select * from Prov where ID=$prov";
	$result = mysql_query("$query");
	$rowProv=mysql_fetch_array($result);
	
	if($submit){
		$how = explode("&", $ho);
		$where="update Svar SET corrected=1, points=$svar where user_ID=$how[0] AND question_ID=$how[1] AND prov_ID=$prov";
		$whereR=mysql_query($where);
	}
	
	if($correct){
		print "<BR><BR>";
		$provQ="select * from Svar where Prov_ID=$prov AND corrected!=1";
		$provR=mysql_query($provQ);
		if($row=mysql_fetch_array($provR)){
			print "<FORM METHOD=POST ACTION=$PHP_SELF?prov=$prov>";
			$questionQ="select * from Question where ID=".$row["question_ID"];
			$questionR=mysql_query($questionQ);
			if($quest=mysql_fetch_array($questionR)){
				print "Question:<BR>";
				print $quest["question"];
				print "<BR><BR>Max points:".$quest["points"];
				print "<BR>";
			}
			$svars=explode("&",$row["svar"]);
			foreach($svars as $svar){
				print $svar;
				print "<BR>";
			}
			print "<BR>";
			
			print "<INPUT TYPE=HIDDEN NAME=ho VALUE='".$row["user_ID"]."&".$row["question_ID"]."'>";
			print "Poang:";
			print "<INPUT TYPE=INT NAME=svar SIZE=4>";
			print "<BR>";
			print "<INPUT TYPE=SUBMIT VALUE=Spara NAME=submit>";
			print "</FORM>";
			print "<BR><BR>";
		}else{
			print "No more Questions to correct<BR><BR>";
		}
	}

	if($grupp){
		$users=array();
		$gruppQ="select * from User where grupp='$grupp'";
		$gruppR=mysql_query($gruppQ);
		while($row=mysql_fetch_array($gruppR)){
			array_push($users, $row["ID"]);
		}
		foreach($users as $user){
			print "<BR><BR>";
			$userQ="select * from User where ID=$user";
			$userR=mysql_query($userQ);
			if($userRow=mysql_fetch_array($userR)){
				print "Namn: ";
				print $userRow["namn"];
				print "<BR><BR>";
			}
			$poang = 0;
			$userQ="select * from Svar where User_ID=$user AND Prov_ID=$prov";
			$userR=mysql_query($userQ);
			while(($row=mysql_fetch_array($userR))){
				$questionQ="select * from Question where ID=".$row["question_ID"];
				$questionR=mysql_query($questionQ);
				if(($quest=mysql_fetch_array($questionR))&&($show==1)){
					print $quest["question"];
					print "<BR>";
				}
				$poang=$poang+$row["points"];
				if($show==1){
					if($row["points"]>0){
						print "<font color='green'>";
					}else{
						print "<font color='red'>";
					}
					$svars=explode("&",$row["svar"]);
					foreach($svars as $svar){
						print $svar;
						print "<br>";
					}
					print "<font color='black'>";
					print "Points: ".$row["points"];
					print "<BR><BR>";
				}
			}
			print "<img src=\"rateme.php?result=$poang&max=".$rowProv["Max"]."&g=".$rowProv["G"]."&vg=".$rowProv["VG"]."\"><br>";
		}
	}else{
		$selectUserQ="select * from Svar where Prov_ID=$prov";
		$selectUserR=mysql_query($selectUserQ);
		$grupps = array();
		print "Show result for grupp<BR>";
		print "<FORM METHOD=POST ACTION=$PHP_SELF?prov=$prov>";
		print "<SELECT NAME=grupp>";
		while($user=mysql_fetch_array($selectUserR)){
			$gruppQ="select * from User where ID=".$user["user_ID"];
			$gruppR=mysql_query($gruppQ);
			if($row=mysql_fetch_array($gruppR)){
				if(!in_array($row["grupp"],$grupps)){
					array_push($grupps,$row["grupp"]);
					print "<OPTION VALUE=".$row["grupp"].">".$row["grupp"];
				}
			}
		}
		print "</SELECT>";
		print "<INPUT TYPE=CHECKBOX NAME=show VALUE=1>Show questions and answers";
		print "<BR>";
		print "<INPUT TYPE=SUBMIT NAME=SUBMIT VALUE=Submit>";
		print "</FORM>";
	}
	
	if($Mail){
		
		$message = "Grupp nummer: $grupp";

		$provQ="select * from Prov where ID=$prov";
		$provR=mysql_query($provQ);
		$provSQL=mysql_fetch_array($provR);
	
		$message = "$message\nProv: ".$provSQL["prov"]."\n\n";

		$mailQ="select DISTINCT(User_ID) as User from Svar where Prov_ID=$prov";
		$mailR=mysql_query($mailQ);
		while($mailUser=mysql_fetch_array($mailR)){
			$userName="select * from User where ID=".$mailUser["User"]." AND grupp='$grupp'";
			$userResult=mysql_query($userName);
			if($userIs=mysql_fetch_array($userResult)){
				$points="select SUM(points) from Svar where User_ID=".$mailUser["User"]." AND Prov_ID=$prov";
				$points=mysql_query($points);
				if($pointsUser=mysql_fetch_array($points)){
					$point=$pointsUser["SUM(points)"];
					$pnt = ($point / $provSQL["Max"]) * 100;
					if($pnt < $provSQL["G"]) {
						$betyg="IG";
					} elseif ($pnt < $provSQL["VG"]) {
						$betyg="G";
					} else {
						$betyg="VG";
					}
				}
				$message = "$message \n".$userIs["namn"]."\t\t\t $betyg";
			} 
		}
		$message = "$message\n\n$comments";
		$from = "From:$from";
		mail($to,$subject,$message,$from);
	}

	if($grupp){
?>
	<BR><BR><BR>
	<H2>Mail result</H2>
	<FORM METHOD=POST ACTION=<? print "$PHP_SELF?prov=$prov&grupp=$grupp"; ?>>
		<TABLE>
		<TR>
			<TD>to:</TD><TD><INPUT TYPE=TEXT NAME=to VALUE="veme@multinet.se" SIZE=40></TD>
		</TR>
		<TR>
			<TD>from:</TD><TD><INPUT TYPE=TEXT NAME=from VALUE="personal@mbs.nu" SIZE=40></TD>
		</TR>
		<TR>
			<TD>subject:</TD><TD><INPUT TYPE=TEXT NAME=subject VALUE="Tenta resultat grupp <? print $grupp; ?>" SIZE=40></TD>
		</TR>
		<TR>
			<TD VALIGN=TOP>comments:</TD><TD><TEXTAREA NAME=comments ROWS=10 COLS=40></TEXTAREA></TD>
		</TR>
		<TR>
			<TD COLSPAN=2 ALIGN=RIGHT><INPUT TYPE=SUBMIT NAME=Mail VALUE=Mail></TD>
		</TR>
		</TABLE>
	</FORM>
<?
	}	
	mysql_close($link);
?>
</CENTER>
</BODY>
</HTML>






<?
/*
Grypp
namn	G/VG/MVG
namn	G/VG/MVG

Coments....
*/
?>
