<?
/*
	TODO:
		Add check for Max point in test == max in "Prov"
*/

session_start();
$sida="prov";
include "Login.php";

if($logout){
?>
	<meta HTTP-EQUIV="refresh" CONTENT="0;URL=logout.php">;
<?
	exit;
}	

if($NextQuestion){
?>
	<meta HTTP-EQUIV="refresh" CONTENT="0;URL=Question.php">;
<?
	exit;
}

if(isset($HTTP_SESSION_VARS["ProvID"])){
	$Prov = $HTTP_SESSION_VARS["ProvID"];
	$provQ="select * from Prov where ID=$Prov";
	$provR=mysql_query($provQ);
	if($provRow = mysql_fetch_array($provR)){
		$HTTP_SESSION_VARS["ProvID"]=$Prov;
		$HTTP_SESSION_VARS["QuestionCount"]=0;
		$HTTP_SESSION_VARS["ProvNAMN"]=$provRow["prov"];
		$HTTP_SESSION_VARS["ProvANS"]=$provRow["ansvarig"];
		$HTTP_SESSION_VARS["ProvEMAIL"]=$provRow["email"];
		if($provRow["time"]){
			$HTTP_SESSION_VARS["ProvTIME"]=$provRow["time"];
		}
	}else{
		print "Error";
	}
	$gjordaQ="select * from Svar where Prov_ID=$Prov AND user_ID=".$HTTP_SESSION_VARS["UserID"];
	$gjordaR=mysql_query($gjordaQ);
	$i=0;
	while($row=mysql_fetch_array($gjordaR)){
		if($i!=0){
			$gjorda = "$gjorda AND";
		}
		$i++;
		$gjorda = "$gjorda Question_ID!=".$row["question_ID"];
	}
	if($gjorda){
		$questionQ="select * from Test where $gjorda AND Prov_ID=$Prov order by rand()";
	}else{
		$questionQ="select * from Test where Prov_ID=$Prov order by rand()";
	}
	$questionR=mysql_query($questionQ) or die("Error in query: $questionQ");
	$i = 0;
	while($questRow=mysql_fetch_array($questionR)){
		$j = 0;
		$provQuest[$i][$j]=$questRow["question_ID"];
		$id = $provQuest[$i][0];
		$exclude = $questRow["exclude_svarsalternativ"];
		if($exclude){
			$exclude = "where Question_ID=$id AND $exclude";
		}else{
			$exclude = "where Question_ID=$id";
		}
		$visaAntalSvar="select Max_alternativ from Question where ID=$id";
		$visaAntalSvar=mysql_query($visaAntalSvar);
		$visaAntalSvar=mysql_fetch_array($visaAntalSvar);
		$Max_alternativ=$visaAntalSvar["Max_alternativ"];
		$finnsCorrect=0;
		while($finnsCorrect==0){
			$j=0;
			if($Max_alternativ){
				$svarQ="select * from Svarsalternativ $exclude order by rand() LIMIT $Max_alternativ";
			}else{
				$svarQ="select * from Svarsalternativ $exclude order by rand()";
			}
			$svarR=mysql_query($svarQ) or die("Error in query: $svarQ");
			while($svarRow=mysql_fetch_array($svarR)){
				$j=$j+1;
				$finnsCorrect=$finnsCorrect + $svarRow["correct"];
				$provQuest[$i][$j]=$svarRow["ID"];
			}
			if($finnsCorrect==0){
				$anyCorrect="select * from Svarsalternativ $exclude";
				$anyCorrect=mysql_query($anyCorrect);
				if(!mysql_fetch_array($anyCorrect)){
					$finnsCorrect=1;
				}else{
					$anyCorrect="select * from Svarsalternativ $exclude AND correct>0";
					$anyCorrect=mysql_query($anyCorrect);
					if(!mysql_fetch_array($anyCorrect)){
						print "Question $id is incorect";
						exit;
					}
				}
			}
		}
		$i = $i+1;
	}
	$HTTP_SESSION_VARS["ProvQuest"]=$provQuest;
}
if($provQuest){
?>

<HTML>
<HEAD>
	<TITLE><? print $HTTP_SESSION_VARS["ProvNAMN"]; ?></TITLE>
</HEAD>
<BODY>
<P ALIGN=CENTER>V&auml;lkommen till provet <b><? print $HTTP_SESSION_VARS["ProvNAMN"]; ?></b></p>
<?
	if(isset($HTTP_SESSION_VARS["ProvTIME"])){
?>
		<P ALIGN=CENTER>Du har <b><? print $HTTP_SESSION_VARS["ProvTIME"]; ?> minuter</b> p� dig att g�ra provet.<br>N�r tiden g�tt ut kommer du automatiskt att loggas ur provet<br>och de fr�gor du inte besvarat kommer att ge dig noll po�ng.<br>Tiden b�rjar r�knas fr�n det att du klickat p� b�rja.</P>
<?
	}
?>
<p align=center>Om provet bryts p� grund av att datorn eller webbl�saren l�ser sig eller tappar f�rbindelsen med<br>TestIT!-servern kan du �teruppta provet igen. De fr�gor du redan besvarat kommer att vara besvarade<br>och du kan inte svara p� dem igen.</p>
<p align=center>Om fr�gan inneh�ller en bild kan du klicka p� bilden f�r att f� se den f�rstorad. Bilden kommer att �ppnas<br>i ett annat webbl�sarf�nster och du st�nger ned detta f�nster n�r du inte vill se bilden mer.</p>
<P ALIGN=CENTER>Uppst�r problem under tiden du g�r provet?<br>Tag kontakt med v�rt supportcenter p� telefon 042 - 240470, uppge vilket prov du h�ller p� att g�ra.</P>
<FORM ACTION=<? print $PHP_SELF; ?>>
	<DIV ALIGN=CENTER>
		<INPUT TYPE=hidden name=prov value= >
		<P>
			 <INPUT TYPE=SUBMIT NAME="logout" VALUE="Avbryt">
			 <INPUT TYPE=SUBMIT NAME="NextQuestion" VALUE="B&ouml;rja">
		</P>
	</DIV>
</FORM>
</BODY>
</HTML>

<? 
}else{
?>
	<Meta HTTP-EQUIV="refresh" CONTENT="0;URL=Klar.php">
<?
}
mysql_close($link); 
?>
