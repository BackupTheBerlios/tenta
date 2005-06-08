<?
session_start();

$sida="admin";

include "Login.php";
include "functions.inc.php";

?>

<HTML>
<HEAD>
	<TITLE>Skapa prov</TITLE>
</HEAD>
<BODY>

<?
if(!$id){
  mysql_close($link);
	print "Error: wrong id go to ";
	print "<A href=adm_prov.php>AdminProv</a>";
	exit;
}

if($new){
	$create=1;
	$copy=1;
}

if($create){
	if($copy){
		$password = crypt($password,$Prov);
		$provQ = "insert into Prov (prov, ansvarig, email, G, VG, pw, aktiv) values ('$Prov', '$ansvarig', '$email', $g, $vg, '$password', 1)";
		$provRes = mysql_query($provQ) or die("Error in query $provQ");
		$id = mysql_insert_id();
	}else{
		if($password){
			$password = crypt($password,$Prov);
			$provQ = "update Prov set prov='$Prov', ansvarig='$ansvarig', email='$email', G=$g, VG=$vg, pw='$password', aktiv=1 where ID=$id";
		}else{
			$provQ = "update Prov set prov='$Prov', ansvarig='$ansvarig', email='$email', G=$g, VG=$vg, aktiv=1 where ID=$id";
		}
		$provRes = mysql_query($provQ) or die("Error in query $provQ");
	}
	if($time){
		$provQ = "update Prov SET time=$time where ID=$id";
		mysql_query($provQ) or die("Error in query: $provQ");
	}
	if($selectedQuestion){
		if(!$copy){
			$deleteTest = "delete from Test where Prov_ID=$id";
			mysql_query($deleteTest);
		}
		foreach($selectedQuestion as $fraga){
			if($poang){
				$poang = "$poang OR";
			}
			$poang ="$poang ID=$fraga";
			$exclude="";
			if($exSvar[$fraga]){
				if($exSvar[$fraga]){
					foreach($exSvar[$fraga] as $svar){
						if($i != 0){
							$exclude = "$exclude OR";
						}
						$exclude = "$exclude ID!=$svar";
						$i=$i+1;
					}
				}
			}
			if($exclude){
				$test = "insert into Test (Prov_ID, Question_ID, exclude_svarsalternativ) values ($id, $fraga, '$exclude')";
			}else{
				$test = "insert into Test (Prov_ID, Question_ID) values ($id, $fraga)";
			}
			mysql_query($test) or die("Error in query: $test");
		}
	
		$query = "select SUM(points) from Question where $poang";
		$result = mysql_query($query);
		if($row=mysql_fetch_array($result)){
			$max = $row["SUM(points)"];
			$query = "update Prov SET Max=$max where ID=$id";
			mysql_query($query);
		}
		
	}else{
		print "Error then creating prov: No Questions to add";
	}
}

$provQ="select * from Prov where ID=$id";
$provR = mysql_query($provQ) or die("Error in Query: $provQ");
$prov = mysql_fetch_array($provR);
extract($prov,EXTR_PREFIX_ALL,"old");

print "<CENTER>";
meny();
print "</CENTER><BR><BR>";
?>

<FORM ACTION="<? print $PHP_SELF; print "?id=$id"; ?>" method=post>
	<P ALIGN=CENTER><BR><BR>
	</P>
	<CENTER>
		<TABLE WIDTH=60% BORDER=1 CELLPADDING=0 CELLSPACING=0>
			<TR>
				<TD WIDTH=100%>
						<P>Prov: <INPUT TYPE=TEXT NAME="Prov" VALUE="<? print $old_prov; ?>" SIZE=18>
						Losenord: <INPUT TYPE=TEXT NAME="password" SIZE=18></P>
				</TD>
			</TR>
			<TR>
				<TD WIDTH=100%>
					Ansvarig: <INPUT TYPE=TEXT NAME="ansvarig" VALUE="<? print $old_ansvarig; ?>"> 
					email: <INPUT TYPE=TEXT NAME="email" VALUE="<? print $old_email; ?>"><BR>
					G Grans (%): <INPUT TYPE=INT NAME="g" value=<? print $old_G; ?> SIZE=2>
					VG GRANS (%): <INPUT TYPE=INT NAME="vg" value=<? print $old_VG; ?> SIZE=2>
					Max tid (min): <INPUT TYPE=INT NAME="time" <? if($old_time){print "VALUE=$old_time";} ?> SIZE=4>
				<TD>
			</TR>
			<TR>
				<TD>
					<INPUT TYPE=SUBMIT VALUE=Save NAME=create><INPUT TYPE=SUBMIT VALUE=SaveAsNew NAME=new>
				</TD>
			</TR>
			<TR>
				<TD>
<?
if(!$Update){
	$subjectQ = "select DISTINCT(Subject.ID) from Subject,Question,Test where Test.question_ID=Question.ID AND Subject.ID=Question.subject_ID AND Test.prov_ID=$id";
	$subjectR = mysql_query($subjectQ) or die("Error in query: $subjectQ");
	$ShowSub=array();
	while($subject=mysql_fetch_array($subjectR)){
		array_push($ShowSub,$subject["ID"]);
	}
	$questionsQ="select * from Test where prov_ID=$id";
	$questionsR=mysql_query($questionsQ);
	$selectedQuestion=array();
	while($question=mysql_fetch_array($questionsR)){
		array_push($selectedQuestion,$question["question_ID"]);
	}
}
	$select=1;
	include "ShowQuestions.php"; 
?>
				</TD>
			</TD>
			<TR>
				<TD>
					<INPUT TYPE=SUBMIT VALUE=Save NAME=create><INPUT TYPE=SUBMIT VALUE=SaveAsNew NAME=new>
				</TD>
			</TR>
		</TABLE>
	<? meny(); ?>
	</CENTER>
</FORM>
</BODY>
</HTML>
<?
	mysql_close($link); 
?>








