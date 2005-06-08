<?
session_start();

$sida="admin";

include "Login.php";
include "functions.inc.php";

if($Subject){
	$include = " where";
	$i = 0;
	foreach($Subject as $select){
		if($i!=0){
			$include = "$include OR";
		}
		$include = "$include ID=$select";
		$i=$i+1;
	}
}else{
	$include = "";
}
if($create){
	$password = crypt($password,$Prov);
	$provQ = "insert into Prov (prov, ansvarig, email, G, VG, pw, aktiv) values ('$Prov', '$ansvarig', '$email', $g, $vg, '$password', 1)";
	$provRes = mysql_query($provQ) or die("Error in query $provQ");
	$id = mysql_insert_id();
	if($time){
		$provQ = "update Prov SET time=$time where ID=$id";
		mysql_query($provQ) or die("Error in query: $provQ");
	}
	if($selectedQuestion){
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
		print "Error then creating prov: No questions to add";
		//<Meta HTTP-EQUIV="refresh" CONTENT="0;URL=UpdateProv.php?id=$id">;
		exit;
	}
	
	$HTTP_POST_VARS["Questions"]=$selectedQuestion;
	$Prov="";
	$password="";
}
?>

<HTML>
<HEAD>
	<TITLE>Skapa prov</TITLE>
</HEAD>
<BODY>
<CENTER>
<? meny(); ?>
</CENTER>
<FORM ACTION="<? print $PHP_SELF; ?>" method=post>
	<CENTER>
		<TABLE WIDTH=60% BORDER=1 CELLPADDING=0 CELLSPACING=0>
			<TR>
				<TD WIDTH=100%>
						<P>Prov: <INPUT TYPE=TEXT NAME="Prov" VALUE="<? print $Prov; ?>" SIZE=18>
						Losenord: <INPUT TYPE=TEXT NAME="password" VALUE="<? print $password; ?>" SIZE=18></P>
				</TD>
			</TR>
						<TR>
				<TD WIDTH=100%>
					<P>Ansvarig: <INPUT TYPE=TEXT NAME="ansvarig" VALUE="<? print $ansvarig; ?>"> 
					email: <INPUT TYPE=TEXT NAME="email" VALUE="<? print $email; ?>"><BR>
					G Grans (%): <INPUT TYPE=INT NAME="g" VALUE=<?if($g){print $g;}else{ print "50";}?> SIZE=2>
					VG Grans (%): <INPUT TYPE=INT NAME="vg" VALUE=<?if($vg){print $vg;}else{ print "80";}?> SIZE=2>
					Max tid (min): <INPUT TYPE=INT NAME="time" VALUE="<? print $time ?>" SIZE=4>
				<TD>
			</TR>
		</TABLE>
<? 
	$select=1;
	include "adm_question_show.php"; 
?>
		<TABLE>
			<TR VALIGN=TOP>
				<TD COLSPAN=2>
					<BR>
				</TD>
				<TD>
					<P><INPUT TYPE=SUBMIT NAME="create" VALUE="Create"></P>
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














