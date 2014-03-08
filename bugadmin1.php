<?php
// bugadmin.php
// Ron Patterson, WildDog Design
// SQLite version

?>
<table id="bt_user_tbl" border="1" cellspacing="0" cellpadding="2">
<tr>
<th>UID</th>
<th>Name</th>
<th>Email</th>
<th>Roles</th>
</tr>

<?php
$out = "";
foreach ($recs as $rec)
{
    $out .= <<<END
<tr>
<td><a href="#" onclick="return bt.user_show('{$rec["uid"]}');">{$rec["uid"]}</a></td>
<td>{$rec["lname"]}, {$rec["fname"]}</td>
<td>{$rec["email"]}</td>
<td>{$rec["roles"]}</td>
</tr>
END;
}
$out .= "</table>";
echo $out;
?>
