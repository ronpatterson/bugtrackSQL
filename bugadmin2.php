<?php
// bugadmin2.php
// Ron Patterson, WildDog Design
// SQLite version
require("bugcommon.php");

if (empty($rec))
{
	$rec = array("uid"=>"","lname"=>"","fname"=>"","email"=>"","active"=>"y","roles"=>"user","pw"=>"","bt_group"=>"");
}

$dbh = $db->getHandle();
$uidx = $rec["uid"] == "" ? "<input type=\"text\" name=\"uid2\" value=\"\">" : $rec["uid"];
$active_y = $active_n = "";
$active_y = $rec["active"] == "y" ? " checked" : "";
$active_n = $rec["active"] != "y" ? " checked" : "";
$roles = retradioarray("roles[]",$rarr,explode(" ", $rec["roles"]));
$sql = "select cd,descr from bt_groups order by descr";
$stmt = $dbh->query($sql);
if (!$stmt) die("SQL ERROR: $sql, ".print_r($dbh->lastErrorMsg(),true));
$results = array();
while ($row = $stmt->fetchArray())
{
	$results[$row["cd"]] = $row["descr"];
}
$groups = retselectarray("bt_group",$results,$rec["bt_group"]);
?>
<fieldset>
<legend>User Add/Edit</legend>
<form name="bt_user_form" id="bt_user_form_id">
<table id="bt_user_tbl2" border="0" cellspacing="0" cellpadding="2">
<tr><th align="right">UID</th><td><?php echo $uidx ?></td></tr>
<tr><th align="right">Last Name</th><td><input type="text" name="lname" value="<?php echo $rec["lname"] ?>"></td></tr>
<tr><th align="right">First Name</th><td><input type="text" name="fname" value="<?php echo $rec["fname"] ?>"></td></tr>
<tr><th align="right">Email</th><td><input type="text" name="email" size="40" value="<?php echo $rec["email"] ?>"></td></tr>
<tr><th align="right">Password</th><td><input type="password" name="pw" value="<?php echo $rec["pw"] ?>"><input type="hidden" name="pw2" value="<?php echo $rec["pw"] ?>"></td></tr>
<tr><th align="right">Active</th><td><label class="yesno"><input type="radio" name="active" value="y"<?php echo $active_y ?>>Yes</label> <label class="yesno"><input type="radio" name="active" value="n"<?php echo $active_n ?>>No</label></td></tr>
<tr><th align="right">Roles</th><td><?php echo $roles ?></td></tr>
<tr><th align="right">Group</th><td><?php echo $groups ?></td></tr>
</table>
<input type="submit" value="Save">
<input type="hidden" name="uid" value="<?php echo $rec["uid"] ?>">
</form>
</fieldset>
