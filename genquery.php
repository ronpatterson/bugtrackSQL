<?php
ini_set("display_errors", "on");
date_default_timezone_set("America/Denver");
$ttl="Generic Query";
$method=$_SERVER["REQUEST_METHOD"];
$sql = $_REQUEST['sql'];
$types = array(SQLITE3_INTEGER=>"integer", SQLITE3_FLOAT=>"float", SQLITE3_TEXT=>"text", SQLITE3_BLOB=>"blob", SQLITE3_NULL=>"null");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
  <title>BugTrack List</title>
  <meta name="author" content="Ron Patterson, ASD20">
  <link type="text/css" rel="stylesheet" href="bugtrack.css" title="bt styles">
</head>
<body background="" bgcolor="white">
<center>
<table>
<tr><td><img src="BugTrack.gif" alt="BugTrack"></td><td width="30">&nbsp;</td>
<td valign="middle"><font size="+1"><b><?php echo $ttl; ?></b></font></td></tr>
</table><br>
<?php
  if (!isset($sql)) {
    echo "<form method=post>
Enter query: (<a href='bugtrack_sqlite.sql.html' target=_blank>Table/field definitions</a>)<br>
<textarea name='sql' rows='5' cols='50'></textarea><p>
Max. rows returned <input type='text' name='rows' size='5' value='100'><p>
<input type='submit'> <input type='reset'>
</form>\n";
  } else {
	$rows = $_REQUEST['rows'];
	$cnt=0;
	$sql = ereg_replace("\\\'", "'", $sql);
	$sql = ltrim($sql) . " limit $rows";
	if (!eregi("^select", $sql)) {
		echo "Must use only a select statement.</body></html>";
		exit;
	}
	
	echo "Query: $sql<p><table border=1 cellpadding=2 cellspacing=0>\n";
	require_once("BugTrack.class.php");
	$db = new BugTrack("/usr/local/db/test.db");
	$dbh = $db->getHandle();

	// execute query 
	$stmt = $dbh->query($sql);
	if (!$stmt) {
		echo "SQL error: " . mysql_error() . "</body></html>";
		exit;
	}

	// open a temp. file for possible download
	$tmpnm = tempnam("../temp", "tmp");
	$tmpnm2 = $tmpnm . ".txt";
	//unlink($tmpnm);
	$fd = fopen($tmpnm2,"w");
	$rec = "";
	$sep = "";
	$row = $stmt->fetchArray(SQLITE3_ASSOC);

	// print header row 
	print("<tr>\n");
	for ($field = 0; $field < $stmt->numColumns(); $field++) {
		print("<th>");
		print($stmt->columnName($field));
		print(" ");
		print($types[$stmt->columnType($field)]);
		print("</th>\n");
		$rec .= "$sep" . $stmt->columnName($field);
		$sep = "|";
	}
	print("</tr>\n");
	fputs($fd, "$rec\n");

	$stmt->reset();
	while ($row = $stmt->fetchArray(SQLITE3_NUM))
	{
		$rec = "";
		$sep = "";
		print("<tr>\n");
		for($field = 0; $field < $stmt->numColumns(); $field++) {
			print("<td>");
			print($row[$field]);
			print("</td>\n");
			$rec .= "$sep" . $row[$field];
			$sep = "|";
		}
		print("</tr>\n");
		fputs($fd, "$rec\n");
		$cnt++;
	}
    echo "</table><p>$cnt rows returned<p>\n";
	fclose($fd);
	echo "<a href=../temp/" . basename($tmpnm2) . ">Download</a> pipe-delimited result data file\n";

	// free the result and close the connection 
	$db = null;
  }
?>
<p>
<a href="buglist.php"><b>Show bug list</b></a><p>
<address>Copyright &copy;<?php echo date("Y") ?>, WildDog Design</address>
<br>
</center>
</body>
</html>
