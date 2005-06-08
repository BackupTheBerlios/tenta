<?
session_start();
$sida="admin";
include "Login.php";
include "functions.inc.php";

if($deleteProv){
	foreach($deleteProv as $del){
		$provDelQ="delete from Prov where ID=$del";
		mysql_query($provDelQ);
	}
}
if($Spara){
	$i = 0;
	if($provAktiv){
		$provAkt = "where";
		foreach($provAktiv as $aktiv){
			if($i!=0){
				$provAkt = "$provAkt OR";
			}	
			$provAkt = "$provAkt ID=$aktiv";
			$i = $i + 1;
		}
	}
	$aktiv = "update Prov set aktiv=0";
	mysql_query($aktiv) or die("Error in query: $aktiv");
	if($provAkt){
		$aktiv = "update Prov set aktiv=1 $provAkt";
		mysql_query($aktiv) or die ("Error in query: $aktiv");
	}
}		
?>
<HTML>
<HEAD>
	<TITLE>Administration</TITLE>
</HEAD>
<BODY>
<CENTER>
<H1>Administration</H1>
<? meny(); ?>
</CENTER>
<P ALIGN=CENTER><BR><BR>
</P>
<FORM ACTION="<? print $PHP_SELF ?>" METHOD=POST>
	<CENTER>
		<H1>Prov</H1>
		<TABLE WIDTH=600 CELLPADDING=0 CELLSPACING=0>
			<?
				$rakna=0;
				$prov="select * from Prov order by aktiv desc, prov";
				$provQ=mysql_query($prov);
				while($rowQ=mysql_fetch_array($provQ)){
			?>
				<TR bgcolor=<?php if($rakna%2) print(""); else print("#f5f5f5");?>>
				<?php $rakna++; ?>
				<TD WIDTH=200>
					<? 
						print "<INPUT TYPE=CHECKBOX NAME=provAktiv[] Value=";
						print $rowQ["ID"];
						if($rowQ["aktiv"]==1){ print " CHECKED";}
						print "> &nbsp;";
						print "<A HREF=\"UpdateProv.php?id=";
						print $rowQ["ID"];
						print "\">";
						print $rowQ["prov"];
						print "</A>";
					?>
				</TD>
				<TD>
					<A href="ProvCorrect.php?prov=<? print $rowQ["ID"]; ?>&correct=1">R&auml;tta</A>
				</TD>
				<TD align="right">
					<INPUT TYPE=CHECKBOX NAME=deleteProv[] Value="<? print $rowQ["ID"]; ?>">Radera
				</TD>
			<TR>
			<? }	?>
			<TR>
				<TD COLSPAN=3 align="right"><br><br><INPUT TYPE=SUBMIT NAME="Spara" Value="Spara förändringar"></TD>
			</TR>
		</TABLE>
		<BR>
		<?
			$edit=1;
			$delete=1;
			include "adm_question_show.php";
		?> 
	</CENTER>
</FORM>
<CENTER>
<? meny(); ?>
</CENTER>
</BODY>
</HTML>
<?
	mysql_close($link);
?>
