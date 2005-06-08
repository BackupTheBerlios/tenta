<?
	include "Login.php?sida=admin";
	
	if($submit){
		$i = 0;
		foreach(HTTP_POST_VARS["Questions"] as $fraga){
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
		$max=0;
		$query = "select * from Question where $poang";
		$result = mysql_query($query);
		while($row=mysql_fetch_array($result)){
			$max = $max + $row["points"];
		}
		$query = "update Prov SET Max=$max where ID=$id";
		mysql_query($query);
		header("Location: adm_prov.php");
		exit;
	}else{
		print "no question to add";
	}
?>
<html>
<head>
<title>Untitled</title>
</head>
<body>
<TABLE>
  <TR>
    <TD>
      <form action="<? print PHP_SELF; ?>" method="post">
<?
	foreach(HTTP_POST_VARS["Questions"] as $question){
	  $questionQ="select * from Question where ID=$question");
		$questionR=mysql_query($questionQ);
		$questionRow=mysql_fetch_array($questionR);
?>
		<TABLE>
			  <TR>
				  <TD>
					  <? print $questionRow["Question"]; ?>
					</TD>
				</TR>
<?
		$svarQ="select * from Svarsalternativ where Question_ID=$question");
		$svarR=mysql_query($svarQ); 
		while($svarRow=mysql_fetch_array){
?>
      <TR>
			  <TD>
				  <? print $svarRow["svar"]; ?>
				</TD>
				<TD>
					<input type="checkbox" name=exSvar[<? print $question ?>][] value=<? print $svarRow["ID"]; ?>>Ta inte med
				</TD>
			</TR>
			<? } ?>
			</TABLE>
			<? } ?>
			<input type="submit" name="submit" value="Spara">
      </from>
		</TD>
	</TR>
</TABLE>
</body>
</html>
