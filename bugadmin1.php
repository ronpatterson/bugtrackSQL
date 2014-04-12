<?php
// bugadmin.php
// Ron Patterson, WildDog Design
// SQLite version

?>
<table id="bt_user_tbl" border="1" cellspacing="0" cellpadding="2">
<thead>
<tr>
<th>UID</th>
<th>Name</th>
<th>Email</th>
<th>Roles</th>
<th>Act</th>
</tr>
</thead>
<tbody>

<?php
$out = "";
foreach ($recs as $rec)
{
	$active = $rec["active"] == "y" ? "Yes" : "No";
    $out .= <<<END
<tr>
<td><a href="#" onclick="return bt.user_show(event,'{$rec["uid"]}');">{$rec["uid"]}</a></td>
<td>{$rec["lname"]}, {$rec["fname"]}</td>
<td>{$rec["email"]}</td>
<td>{$rec["roles"]}</td>
<td>$active</td>
</tr>
END;
}
$out .= "
</tbody>
</table>";
echo $out;
?>
