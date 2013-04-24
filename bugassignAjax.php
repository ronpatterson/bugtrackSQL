<?php
require("btsession.php");
// bugassign1.php - Directory staff search results
// Ron Patterson, WildDog Design
// SQLite version
require("bugcommon.php");
extract($_POST);
#print_r($_POST); exit;

if ($fname == " " && $lname == "") die("No search data entered!!");

$bid=$id;
$lname = slashem($lname);
$fname = slashem($fname);

// connect to the database 
require_once("dbdef.php");
require("BugTrack.class.php");
$db = new BugTrack($dbpath);
$dbh = $db->getHandle();
//reset($UsersArr);
$list = ""; $found = 0;
$sql = "select * from bt_users where active='y' order by lname,fname";
$stmt = $dbh->query($sql);
if (!$stmt) die("SQL ERROR: $sql, ".print_r($this->dbh->lastErrorMsg(),true));
//foreach ($UsersArr as $k=>$v) {
while ($arr = $stmt->fetchArray(SQLITE3_NUM))
{
	//list($uid,$lnm,$fnm,$email) = preg_split("/,/",$v);
	list($uid,$lnm,$fnm,$email,$act) = $arr;
	if ((trim($lname) != "" and preg_match("/".$lname."/i",$lnm)) or (trim($fname) != "" and preg_match("/".$fname."/i",$fnm))) {
		$list .= <<<END
  <tr>
    <td valign="TOP" height="16"><a href="#" onclick="return opener.do_assign($bid,'$uid');">$lnm, $fnm</a></td> 
    <td valign="TOP" height="16">$uid</td> 
    <td valign="TOP" height="16">$email</td> 
  </tr>
END;
	++$found;
	}
}
?>
<h5>Your search found <?php echo $found; ?> listing(s). Click
on the Last Name link to assign to BugTrack record.</h5>
<?php
if ($found > 0):
?>
<table width="520" border="1" cellspacing="0" cellpadding="3" class="worklog">
  <tr>
    <td width="40%" valign="TOP"><b>Name</b></td> 
    <td width="20%" valign="TOP"><b>Username</b></td> 
    <td width="40%" valign="TOP"><b>Email</b></td> 
  </tr>
  <?php echo $list ?>
</table>
<div id="results2"></div>
<?php

endif;
?>
