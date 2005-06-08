<?
session_start();
$sida="admin";
include "Login.php";
include "functions.inc.php";

if($Add){
	if($id){
		if($password){
			$password = crypt($password,$username);
			$insert = "update User SET username='$username', namn='$name', grupp='$grupp', pw='$password' where ID=$id";
		}else{
			$insert = "update User SET username='$username', namn='$name', grupp='$grupp' where ID=$id";
		}
	}else{
		if($password){
			$password = crypt($password,$username);
			$insert = "insert into User (username, namn, grupp, pw)  values ('$username', '$name', '$grupp', '$password')";
		}else{
			$insert = "insert into User (username, namn, grupp) values ('$username','$name','$grupp')";
		}
	}
	mysql_query($insert) or die("Error in query: $insert");
}

if($delete){
	foreach($delete as $DelUser){
		if($DelUser==$HTTP_SESSION_VARS["UserID"]){
			print "Can't delete your user";
		}else{
			$del = "delete from User where ID=$DelUser";
			mysql_query($del);
		}
	}
}

if($id){
	$selectQ="select * from User where ID=$id";
	$selectR=mysql_query($selectQ);
	if($select=mysql_fetch_array($selectR)){
		extract($select,EXTR_PREFIX_ALL,"old");
	}
}

?>
<HTML>
<HEAD>
<TITLE>User Admin</TITLE>
</HEAD>
<BODY>
<CENTER>
<? meny(); ?>
<FORM method="post" ACTION="<? print $PHP_SELF; if($id){ print "?id=$id";} ?>" >
<?
$quest = "select * from User order by grupp";
$result = mysql_query($quest);
$i = 0;
$grupps = array();
while($row = mysql_fetch_array($result)){
	if(!in_array($row["grupp"],$grupps)){
		$grupps[$i] = $row["grupp"];
		if($gruppShow && in_array($grupps[$i],$gruppShow)){
			$check = "CHECKED";
		}else{
			$check = "";
		}
		print "<INPUT TYPE=CHECKBOX NAME=gruppShow[] VALUE=$grupps[$i] $check>$grupps[$i]&nbsp;";
		$i = $i+1;
	}
}
print "<INPUT TYPE=SUBMIT NAME=Update VALUE=Uppdatera>";
if($gruppShow){
	$i = 0;
	$show = "where";
	foreach($gruppShow as $showGrupp){
		if($i != 0){
			$show = "$show OR";
		}
		$show = "$show grupp='$showGrupp'";
		$i = $i+1;
	}
}
?>
<TABLE WIDTH=60% BORDER=1 CELLPADDING=0 CELLSPACING=0>
<TR>
	<TD><B>Grupp</B></TD>
	<TD><B>Namn</B></TD>
	<TD><B>Användarnamn</B></TD>
</TR>
<?
if($show){
	$quest = "select * from User $show order by grupp, namn";
	$result = mysql_query($quest) or die("Error in query: $quest");
	while($row = mysql_fetch_array($result)){
?>
<TR>
	<TD>
		<? print $row["grupp"];?>
	</TD>
	<TD>
		<a href=UserAdmin.php?id=<? print $row["ID"]; ?>><? print $row["namn"];?></A>
	</TD>
	<TD>
		<a href=UserAdmin.php?id=<? print $row["ID"]; ?>><? print $row["username"];?></A>
	</TD>
	<TD>
		<?
			if($row["ID"]==$HTTP_SESSION_VARS["UserID"]){
				print "<BR>";
			}else{
		?>
		<INPUT TYPE=CHECKBOX NAME=delete[] VALUE=<? print $row["ID"]; ?>>Tag bort
		<?
			}
		?>
	</TD>
</TR>

<?
	}
}
?>
</TABLE>
<INPUT TYPE=SUBMIT VALUE="Spara förändringar">
<BR>
<TABLE WIDTH=60% BORDER=1 CELLPADDING=0 CELLSPACING=0>
<TR>
	<TD COLSPAN=3><H2><? if($id){print "Ändra användare";}else{print "Skapa ny användare";}?></H2></TD>
<TR>
	<TD>Grupp</TD><TD WIDTH=70%><INPUT TYPE=TEXT NAME="grupp" SIZE="48" VALUE="<?if($id){print $old_grupp;}else{print $grupp;} ?>"></TD>
	<TD><INPUT TYPE=CHECKBOX NAME=Admin VALUE=1 <? if($old_admin==1){ print "CHECKED";} ?>>User Admin</TD> 
</TR>
<TR>
	<TD>Namn</TD><TD COLSPAN=2><INPUT TYPE=TEXT NAME="name" SIZE="48" VALUE="<? print $old_namn; ?>"></TD>
</TR>
<TR>
	<TD>Username</TD><TD COLSPAN=2><INPUT TYPE=TEXT NAME="username" SIZE="48" VALUE="<? print $old_username; ?>"></TD>
</TR>
<TR>
	<TD>Password</TD><TD COLSPAN=2><INPUT TYPE=TEXT NAME="password" SIZE="48"></TD>
</TR>
<TR>
	<TD COLSPAN=3 align="right"><INPUT TYPE=SUBMIT NAME="Add" VALUE="<? if($id){print "Ändra";}else{print "Lägg till";} ?>"></TD>
</TR>
</TABLE>
</FORM>
<? meny(); ?>
</CENTER>
</BODY>
</HTML>

<? mysql_close($link); ?>
