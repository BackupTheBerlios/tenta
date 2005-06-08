<?
	/*
		edit==1 om man ska kunna editera frågorna
		select==1 om man ska kunna välja frågor valda frågor finns i $selectedQuestion[]
		delete==1 om man ska kunna tabort en fraga
	*/
?>

<H1>Frågedatabas</H1>
<?
	$subj = "select * from Subject";
  	$subjResult = mysql_query($subj) or die("Error in question $subj");
	while($rowSub=mysql_fetch_array($subjResult)){
?>

<TABLE WIDTH=80% BORDER=1 CELLPADDING=0 CELLSPACING=0>
  	<TR>
    		<TD COLSPAN=2 WIDTH=50%>
<? 
		print $rowSub["namn"];
?>
		</TD>
		<TD COLSPAN=2 WIDTH=50%>
			<DIV ALIGN=CENTER>
			<INPUT TYPE=CHECKBOX NAME=ShowSub[] VALUE="<? print $rowSub["ID"]; ?>" <? if($ShowSub && in_array($rowSub["ID"], $ShowSub)){ print " CHECKED";}?>>Visa
			<INPUT TYPE=SUBMIT NAME="Update" VALUE="Update">
			</DIV>
		</TD>
	</TR>
<?
		$temp = $rowSub["ID"];
		if($ShowSub && in_array($temp, $ShowSub)){
	  		$quest = "select * from Question where subject_ID=$temp";
			$questResult = mysql_query($quest);
			while($rowQuest=mysql_fetch_array($questResult)){
?>
	<TR>
		<TD WIDTH=15%>
			Question:
		</TD>
		<TD COLSPAN=2 WIDTH=52%>
<?
				print $rowQuest["question"];
?>
		</TD>
		<TD WIDTH=33%>
<?
				if($edit){
?>
				<a href="adm_question.php?id=<? print $rowQuest["ID"]; ?>" >Edit</A>
<?
				}
				if($select){
?>
					<INPUT TYPE=CHECKBOX NAME=selectedQuestion[] VALUE="<? print $rowQuest["ID"]; ?>" <? if($selectedQuestion && in_array($rowQuest["ID"], $selectedQuestion)){print "CHECKED";} ?>>Select
<?
				}
				if($delete){
?>
					<a href="adm_question_delete.php?id=<? print $rowQuest["ID"]; ?>">Tag bort</A>
<?
				}	
?>
			<INPUT TYPE=CHECKBOX NAME=visaSvar[] VALUE="<? print $rowQuest["ID"]; ?>" <? if($visaSvar && in_array($rowQuest["ID"], $visaSvar)){print "CHECKED";} ?>>VisaSvar 
			<INPUT TYPE=SUBMIT NAME="Update" VALUE="Update">
		</TD>
  </TR>
<?
				$temp = $rowQuest["ID"];
				if($visaSvar && in_array($temp, $visaSvar)){
					$svar = "select * from Svarsalternativ where question_ID=$temp";
					$svarResult = mysql_query($svar) or Die ("Error in query $svar");
					while($svarRow=mysql_fetch_array($svarResult)){
?>
  <TR>
	  <TD WIDTH=15%>
		  Svar:
		</TD>
		<TD COLSPAN=2 WIDTH=52%>
<?
						print $svarRow["svar"];
?>
		</TD>
		<TD WIDTH=33%>
		  <BR>
		</TD>
  </TR>
<?
					}
				}
			}
		}
?>
</TABLE>
<?
	}
?>
