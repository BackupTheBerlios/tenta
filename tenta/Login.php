<?
/* ChangeLog:
	2002-09-18 Jonas Björk, jonas@mbs.nu
		* Fixade lite i koden, rensade upp.
*/
error_reporting(0);
// Anslut till databasen
include("config.inc.php");

if($HTTP_SESSION_VARS["IP"]==$REMOTE_ADDR){
	if($sida=="admin" && $HTTP_SESSION_VARS["admin"]!=1){
	}else{
		$Login=1;
	}
}else{
	if($Login){
		if($Password && $Username){
			$query = "select * from User where username='$Username'";
			$result = mysql_query($query) or die ("Error in query");
			if($userRow=mysql_fetch_array($result)){
				if($sida=="admin"){
					if(($userRow["pw"]==crypt($Password,$Username))&&($userRow["admin"]==1)){
						$HTTP_SESSION_VARS["IP"]=$REMOTE_ADDR;
						$HTTP_SESSION_VARS["UserID"]=$userRow["ID"];
						$HTTP_SESSION_VARS["admin"]=1;
						$user=1;
						$Login=1;
					}
				}
				if($sida=="prov"){
					$query = "select * from Prov where ID=$Prov";
					$result = mysql_query($query) or die ("Error in query");
					if($row=mysql_fetch_array($result)){
						if($row["pw"]==crypt($ProvPassword,$row["prov"])&&($userRow["pw"]==crypt($Password,$Username))){
							$HTTP_SESSION_VARS["IP"]=$REMOTE_ADDR;
							$HTTP_SESSION_VARS["UserID"]=$userRow["ID"];
							$HTTP_SESSION_VARS["ProvID"]=$Prov;
							$HTTP_SESSION_VARS["admin"]=0;
							$user=1;
							$Login=1;
						}else if($row["pw"]==crypt($ProvPassword,$row["prov"])&&($imap=imap_open($imapHost,$Username,$Password))){
							$HTTP_SESSION_VARS["IP"]=$REMOTE_ADDR;
							$HTTP_SESSION_VARS["UserID"]=$userRow["ID"];
							$HTTP_SESSION_VARS["ProvID"]=$Prov;
							$HTTP_SESSION_VARS["admin"]=0;
							$user=1;
							$Login=1;
							imap_close($imap);
						}
					}
				}
				if($sida=="MinaProv"){
					if($userRow["pw"]==crypt($Password,$Username)){
						$HTTP_SESSION_VARS["IP"]=$REMOTE_ADDR;
						$HTTP_SESSION_VARS["UserID"]=$userRow["ID"];
						$HTTP_SESSION_VARS["admin"]=0;
						$user=1;
						$Login=1;
					}else if($imap=imap_open($imapHost,$Username,$Password)){
						$HTTP_SESSION_VARS["IP"]=$REMOTE_ADDR;
						$HTTP_SESSION_VARS["UserID"]=$userRow["ID"];
						$HTTP_SESSION_VARS["admin"]=0;
						$user=1;
						$Login=1;
						imap_close($imap);
					}
				}
			}
		}else{
			$error = "Fyll i alla f&auml;lt";
		}
		if(!$user){
			$error = "<font color=\"#ff0000\"><b>Felaktigt anv&auml;ndarnamn eller l&ouml;senord!</b></font>";
		}
	}
}

if(!$Login || $error){
?>

<body bgcolor="#ffffff" text="#000000">
<?
	if($sida=="admin"){
		include("ikon.inc.php");
	}elseif($sida=="prov"){
		include("ikon.inc.php");
	}elseif($sida=="MinaProv"){
		include("ikon.inc.php");
	}else{
		include("ikon.inc.php");
	}
?>

<FORM method="post" NAME="Login" ACTION="<? print $PHP_SELF; ?>" >
	<CENTER>
<? 
	if($error){
		print $error;
		print "\n";
	}
?>

<br>
		<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0>
<?
	if($sida=="prov"){
?>
			<TR VALIGN=TOP>
				<TD>
					<P>V&auml;lj prov:</P>
				</TD>
				<TD>
					<SELECT NAME=Prov>
<?
		$query = "select * from Prov where aktiv=1 ORDER BY prov";
		$result = mysql_query($query) or die ("Error in Query");
		while($row=mysql_fetch_array($result)){
			$tmp =  $row["ID"];
			print "<OPTION VALUE=$tmp>";
			print $row["prov"];
		}
?>
					</SELECT>
				</TD>
			</TR>
<?
	}
?>
			<TR VALIGN=TOP>
				<TD>
					<P>Anv&auml;ndarnamn:</P>
				</TD>
				<TD>
					<P><INPUT TYPE=TEXT NAME="Username" SIZE=24></P>
				</TD>
			</TR>
			<TR VALIGN=TOP>
				<TD WIDTH=248>
					<P>L&ouml;senord:</P>
				</TD>
				<TD WIDTH=248>
					<P><INPUT TYPE=PASSWORD NAME="Password" SIZE=24></P>
				</TD>
			</TR>
<? if($sida=="prov"){ ?>
			<TR VALIGN=TOP>
				<TD WIDTH=248>
					<P>Provets l&ouml;senord:</P>
				</TD>
				<TD WIDTH=248>
					<P><INPUT TYPE=PASSWORD NAME="ProvPassword" SIZE=24></P>
				</TD>
			</TR>
<? } ?>
			<TR VALIGN=TOP>
				<TD WIDTH=248>
					<P><BR>
					</P>
				</TD>
				<TD WIDTH=248 align=center>
					<br>
					<P><INPUT TYPE="submit" NAME="Login" value="Logga in!"></P>
				</TD>
			</TR>
		</TABLE>
	</CENTER>
</FORM>
<? 
	exit;
}
error_reporting(E_ALL ^ E_NOTICE);
?>
