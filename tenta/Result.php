<?
	include "Login.php?sida=admin";
	
?>

<html>
<head>
<title>Result</title>
</head>
<body>
<?  
	if($Left){
		
  }
?>
<?
	if(!$grupp){
?>
<form action="<? print PHP_SELF; ?>" method="post">
<div align="CENTER">
<H1>Select grupp</H1>
<?

	$grupp=
	print "<input type='radio' name='grupp'>$grupp";
?>
<input type="Submit" value="Submit">
</div>
</form>
<?
	}else{
		print "<img src=\"rateme.php?result=".$row["SUM(points)"]."&max=".$row["Max"]."&g=".$row["G"]."&vg=".$row["VG"]."\"><br>";
		if($left){
?>
		  <button type="submit" name="Left" value="<? print "$user_id" ?>">Correct</button>
<?
		}
	}
?>
</body>
</html>

<? mysql_close($link); ?>
