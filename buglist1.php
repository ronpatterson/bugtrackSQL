<?php
//require("../session.php");
// buglist1.php
// Ron Patterson, WildDog Design
// PDO version
// return a standard <select> for a lookup table
// connect to the database 
require("bugcommon.php");
$max=50;
$start=isset($_GET["start"]) ? $_GET["start"] : "";
if ($start == "") $start=0;
$ttl="BugTrack Bugs List";
$dbh = $db->getHandle();
?>
<p>Click on the ID to see details. Click column title to sort.</p>
<div style="width: 650px;">
<table id="bt_tbl" class="display" border="1" cellspacing="0" cellpadding="3" width="100%">
<thead>
<tr>
<th>ID</th><th>Description</th><th>Date entered</th><th>Status</th>
</tr>
</thead>
<tbody>
<?php
// execute query 
$sql = "select count(*) from bt_bugs";
$count = $dbh->querySingle($sql);
//echo $count;
$cnt2=0; $out="";
if ($count > 0) {
	$sql = "select * from bt_bugs order by bug_id desc -- limit $start,$max";
	$stmt = $dbh->query($sql);
	// loop thru all rows
    while ($arr = $stmt->fetchArray(SQLITE3_NUM)) {
		list($id,$descr,$prod,$entry_id,$bug_type,$status,$priority,$comments,$solution,$assigned_to,$bug_id,$entry_dtm,$update_dtm,$closed_dtm) = $arr;
		//extract($arr);
		$entry_dt = date("m/d/Y g:i a",strtotime($entry_dtm));
		//$class = $count%2==0 ? "even" : "odd";
		$out .= <<<END
<tr valign="top">
<td><a href="#" onclick="return bt_bugshow(null,$id);"><b>$bug_id</b></a></td>
<td>$descr</td>
<td>$entry_dt</td>
<td>{$sarr[$status]}</td>
</tr>
END;
		$cnt2++;
	}
	echo $out;
}
$start+=$cnt2;
?>
</tbody>
</table>
</div>
<!-- <b><?php echo $cnt2; ?> Bugs shown.</b> -->
<!-- <a href=viewphp1.php><b>View PHP code modules</b></a> -->
<br>
</center>
