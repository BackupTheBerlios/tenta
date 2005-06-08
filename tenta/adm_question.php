<?
session_start();

$sida="admin";
include "Login.php";
include "functions.inc.php";

if($save || $editSvar || ($id && ($NewQuest || $exit))){
	if($data){
		$bin_data=addslashes(fread(fopen($data,"r"),filesize($data)));
		$query="insert into binary_data values ('Bild$id','$bin_data','$data_name','$data_size','$data_type')";
		mysql_query($query);
		$binary_data_ID=mysql_insert_id();
	}
	if($correct){
		$correct=1;
	}else{
		$correct=0; 
	}
	if($save){
		if($svarCor){
			foreach($svarCor as $cor){
				mysql_query($cor);
			}
		}		
		if($removeBild){
			$query="delete from binary_data where ID=$binary_data_ID";
			mysql_query($query);
		}
		if($removeSvar){
			foreach($removeSvar as $val){
				$query="delete from Svarsalternativ where ID=$val";
				mysql_query($query);
			}
		}
		if(!$binary_data_ID){
			$binary_data_ID="NULL";
		}
		if($id){
			$insert = "update Question set question='$Question', correct=$correct,  points=$points, binary_data_ID=$binary_data_ID, subject_ID=$subject_ID, Max_alternativ=$Max_alternativ where ID=$id";
			mysql_query($insert) or die ("error in query $insert"); 
		}else{
			$insert = "insert into Question (Question, correct, points, binary_data_ID, subject_ID, Max_alternativ) values ('$Question', $correct,  $points, $binary_data_ID, $subject_ID, $Max_alternativ)";
			mysql_query($insert) or die ("error in query $insert"); 
			$id=mysql_insert_id();
		}
	}
}else if($addSvar){
	if($newCorrect=="correct"){
		$newCorrect=1;
	}else{
		$newCorrect=0;
	}
	$query="insert into Svarsalternativ (Question_ID, svar, correct) values($id, '$newSvar', $newCorrect)";
	mysql_query($query) or die("Error in query: $query");
}
if($exit){
  mysql_close($link);
?>
	<Meta HTTP-EQUIV="refresh" CONTENT="0;URL=adm_prov.php">;
<?
	exit;
}
if($NewQuest){
	$id=NULL;
}
if($id){
	$fraga = "select * from Question where ID=$id";
	$result = mysql_query($fraga) or die ("Error in Query $fraga");
	if(!$fragaRow=mysql_fetch_array($result)){
	  mysql_close($link);
		echo "Error wrong ID Fragan finns inte";
		exit;
	}
}
?>

<HTML>
<HEAD>
	<TITLE>Question admin</TITLE>
</HEAD>
<BODY>
<CENTER>
<? meny(); ?>
<FORM method=post action="<? print $PHP_SELF; ?>" enctype="multipart/form-data">
	<INPUT TYPE="hidden" name="id" value="<? print $fragaRow["ID"]; ?>">
	<TABLE WIDTH=90% BORDER=1 CELLPADDING=0 CELLSPACING=0>
		<TR COLSPAN=3>
			<?if($id){print "<H1>Fraga ID=$id</H1>";}else{print "<H1>New Question</H1>";}?> 
		</TR>
		<TR>
			<TD WIDTH=33% VALIGN=TOP>
				<P>&Auml;mne:	
					<?
					print  "<select NAME='subject_ID'>";
					$query = "select * from Subject";
					$result = mysql_query($query) or die ("Error in Query $query");
					while($rowSub=mysql_fetch_array($result)){
						$tmp = $rowSub["ID"];
						print "<option value=$tmp";
						if(($tmp==$fragaRow["subject_ID"]) || ($tmp==$subject_ID)){
							print " SELECTED>";
						}else{
							print ">";
						}
						print $rowSub["namn"];
					}
					print "</select>";
					?>
				</P>
			</TD>
			<TD WIDTH=33% VALIGN=TOP>
				<P><INPUT TYPE="CHECKBOX" NAME="correct" value="correct" <? if(($fragaRow["correct"]==1) || !$id){print "CHECKED";} ?>>Auto Correct</P>
			</TD>
			<TD WIDTH=33% VALIGN=TOP>
				<P>Poang<INPUT TYPE=INT name="points" VALUE="<? if($fragaRow){print $fragaRow["points"];}else{print "0";} ?>" SIZE=3>
				Max antl alternativ<INPUT TYPE=INT name="Max_alternativ" Value="<? if($fragaRow){print $fragaRow["Max_alternativ"];}else{print "0";} ?>" SIZE=3></P>
			</TD>
		</TR>
		<TR VALIGN=TOP>
			<TD WIDTH=33%>
				<P>
					Bild:
						<INPUT TYPE="hidden" name=binary_data_ID value="<?print $fragaRow["binary_data_ID"]; ?>">
						<INPUT TYPE="hidden" name="MAX_FILE_SIZE" value="1000000">
				</P>
			</TD>
				<?
					if($fragaRow["binary_data_ID"]){
						$tmp = $fragaRow["binary_data_ID"];
						$query = "select * from binary_data where ID=$tmp";
						$result = mysql_query($query) or die("Error in query $query");;
						if($row=mysql_fetch_array($result)){ ?>
							<? 
								print "<TD WIDTH=33%>";
								print $row["filename"];
								print "</TD><TD WIDTH=33%>";
								print "<INPUT TYPE=CHECKBOX NAME='removeBild' value='remove'>Remove Bild";
								print "</TD>";
							?>
						<?}else{?>
							<TD COLSPAN=2 WIDTH=67%>
							<INPUT TYPE="file" name="data" SIZE="48">
							</TD>
						<?
						}
					}else{
					?>
						<TD COLSPAN=2 WIDTH=67%>
						<INPUT TYPE="file" name="data" SIZE="48">
						</TD>
					<?
					}
					?>
		</TR>
		<TR VALIGN=TOP>
			<TD WIDTH=33%>
				<P>Fr&aring;ga:</P>
			</TD>
			<TD WIDTH=33%>
				<P><TEXTAREA NAME="Question" ROWS=10 COLS=48  WRAP=virtual><? print $fragaRow["question"]; ?></TEXTAREA></P>
			</TD>
			<TD WIDTH=33%>
			<BR>
			</TD>
		</TR>
				<TR VALIGN=TOP>
			<TD WIDTH=33%>
				<P><BR>
				</P>
			</TD>
			<TD WIDTH=33%>
				<P><BR>
				</P>
			</TD>
			<TD WIDTH=33%>
				<P><INPUT TYPE=SUBMIT NAME="save" VALUE="Save"><INPUT TYPE=SUBMIT NAME="NewQuest" VALUE="new Question"><INPUT TYPE=SUBMIT NAME="exit" VALUE="Exit"></P>
			</TD>
		</TR>
		<TR WIDTH=100%>
			<TD COLSPAN=3><CENTER><H1>Svarsalternativ</H1></CENTER></TD>
		</TR>
		<?
		
		$temp = $fragaRow["ID"];
		$svar = "select * from Svarsalternativ where Question_ID='$temp'";
		$svarReply = mysql_query($svar) or die ("Error in Query $svar");
		while($svarRow=mysql_fetch_array($svarReply)){
			print "<TR VALIGN=TOP>";
			print "<TD WIDTH=33%>";
			$svarID = $svarRow["ID"];
			print "<SELECT NAME=svarCor[]>";
			if($svarRow["correct"]==1){
				print "<OPTION VALUE='update Svarsalternativ set correct=1 where ID=$svarID' SELECTED>True";
				print "<OPTION VALUE='update Svarsalternativ set correct=0 where ID=$svarID'>Fales";
			}else{
				print "<OPTION VALUE='update Svarsalternativ set correct=1 where ID=$svarID'>True";
				print "<OPTION VALUE='update Svarsalternativ set correct=0 where ID=$svarID' SELECTED>Fales";
			}
			print "</SELECT>";
			print "</TD>";
			print "<TD WIDTH=33%>";
			print "<P>";
			print $svarRow["svar"];
			print "</P>";
			print "</TD>";
			print "<TD WIDTH=33%>";
			$tmp = $svarRow["ID"];
			print "<P><INPUT TYPE=CHECKBOX NAME=removeSvar[] value='$tmp'>Delete";
			print "</P>";
			print "</TD>";
			print "</TR>";
		}
		?>
		<TR>
			<TD COLSPAN=2 WIDTH=67%>
				<BR>
			</TD>
			<TD WIDTH=33%>
				<INPUT TYPE=SUBMIT NAME="save" VALUE="Save">
			</TD>
		</TR>
		<TR VALIGN=TOP>
			<TD WIDTH=33%>
				<INPUT TYPE=CHECKBOX name="newCorrect" value="correct">Correct
			</TD>
			<TD WIDTH=33%>
				<P><TEXTAREA COLS=48 ROWS=5 NAME="newSvar"></TEXTAREA></P>
			<TD WIDTH=33%>
				<P><INPUT TYPE=SUBMIT NAME="addSvar" VALUE="Add Svar"></P>
			</TD>
		</TR>
	</TABLE>
</FORM>
<? meny() ?>
</CENTER>
</BODY>
</HTML>

<? mysql_close($link); ?>
