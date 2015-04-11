<?php
// BugTrack.class.php
//
// Ron Patterson, WildDog Design/BPWC
//
// SQLite version

define("AUSERS","ron,janie");
$sarr=array("o"=>"Open", "h"=>"Hold", "w"=>"Working", "y"=>"Awaiting Customer", "t"=>"Testing", "c"=>"Closed");
$parr=array("1"=>"High","2"=>"Normal","3"=>"Low");
$rarr=array("admin","ro","user");
$grparr=array("DOC"=>"Dept of Corrections","WDD"=>"WildDog Design");

function q ($val) {
	if (empty($val)) return "NULL";
	return "'".str_replace("'","''",$val)."'";
}

class BugTrack {
	protected $dbh;
	protected $adir = "/usr/local/data/";
	protected $tblsArray = array();

	public function __construct ( $dbpath )
	{
		// SQLite database version
		try
		{
			$this->dbh = new SQLite3($dbpath);
		}
		catch (Exception $e)
		{
			//die("SQL CONNECTION ERROR: ".$e->getMessage());
			header("Location: /dberror.html");
			exit;
		}
	}

	public function __destruct ()
	{
		$this->dbh->close();
		$this->dbh = null;
	}

	public function getTables ()
	{
		$this->tblsArray = array();
		// type|name|tbl_name|rootpage|sql
		$sql = "select table_name from information_schema.tables where table_type='base table' order by table_name";
		try
		{
			$stmt = $this->dbh->query($sql);
			while ($row = $stmt->fetchArray())
			{
				$this->tblsArray[] = $row["table_name"];
			}
		}
		catch (Exception $e)
		{
			die("SQL ERROR: $sql, ".$e->getMessage());
		}
		return $this->tblsArray;
	}

	public function buildTablesList ()
	{
		if (count($this->tblsArray) == 0) $this->getTables();
		$out = "";
		foreach ($this->tblsArray as $tbl)
		{
			try
			{
				$sql = "select count(*) from $tbl";
				$sz = $this->dbh->querySingle($sql);
			}
			catch (Exception $e)
			{
				die("SQL ERROR: $sql, ".$e->getMessage());
			}
			$out .= <<<END
	<li>{$tbl} ({$sz}) <input type="checkbox" name="tables[]" value="{$tbl}"></li>\n
END;
		}
		if ($out != "") return "<ul>\n".$out."</ul>";
		return "";
	}

	public function buildTablesTable ()
	{
		if (count($this->tblsArray) == 0) $this->getTables();
		$out = "";
		foreach ($this->tblsArray as $tbl)
		{
			try
			{
				$sql = "select count(*) from $tbl";
				$sz = $this->dbh->querySingle($sql);
			}
			catch (Exception $e)
			{
				die("SQL ERROR: $sql, ".$e->getMessage());
			}
			$out .= <<<END
	<tr><td align="left">{$tbl}</td><td align="center">{$sz}</td><td><input type="checkbox" name="tables[]" value="{$tbl}"></td></tr>\n
END;
		}
		if ($out != "") return "<table><tr><th>Backup Table</th><th>Rows</th><th></th></tr>\n".$out."</table>";
		return "";
	}

	public function getBug ($id, $type = SQLITE3_ASSOC)
	{
		global $sarr, $parr;
		// id, descr, product, user_nm, bug_type, status, priority, comments, solution, assigned_to, bug_id, entry_dtm, update_dtm, closed_dtm
		$crit = is_numeric($id) ? " id=?" : " bug_id=?";
		$sql = "
select b.*,t.descr t_descr,trim(fname)||' '||trim(lname) aname,email from bt_bugs b
	inner join bt_type t on (t.cd = b.bug_type)
	left join bt_users u on (u.uid = b.assigned_to)
where $crit
";
		$stmt = $this->dbh->prepare($sql);
		if (!$stmt) die("SQL ERROR: $sql, ".print_r($this->dbh->lastErrorMsg(),true));
		$stmt->bindValue(1,$id);
		$result = $stmt->execute();
		if ($result === FALSE) die("SQL ERROR: $sql, ".print_r($this->dbh->lastErrorMsg(),true));
		$row = $result->fetchArray($type);
		$result->finalize();
		if (is_array($row) and count($row) > 0)
		{
			$row["status_descr"] = $sarr[$row["status"]];
			$row["priority_descr"] = $parr[$row["priority"]];
			$row["edtm"] = $row["entry_dtm"] != "" ? date("m/d/Y g:i a",strtotime($row["entry_dtm"])) : "";
			$row["udtm"] = $row["update_dtm"] != "" ? date("m/d/Y g:i a",strtotime($row["update_dtm"])) : "";
			$row["cdtm"] = $row["closed_dtm"] != "" ? date("m/d/Y g:i a",strtotime($row["closed_dtm"])) : "";
		}
		return (object)$row;
	}

	public function getBugs ($type = "", $crit = "")
	{
		global $sarr;
		$sql = "
select id,bug_id,b.descr,entry_dtm,t.descr,status from bt_bugs b
	inner join bt_type t on (t.cd=b.bug_type)
where 1=1 $crit
";
		//if (!empty($order)) $sql .= " order by ".$order;
		$stmt = $this->dbh->query($sql);
		if (!$stmt) die("SQL ERROR: $sql, ".print_r($this->dbh->lastErrorMsg(),true));
		$results = array();
		while ($row = $stmt->fetchArray(SQLITE3_ASSOC))
		{
			$row["edtm"] = $row["entry_dtm"] != "" ? date("m/d/Y g:i a",strtotime($row["entry_dtm"])) : "";
			$row["status"] = $sarr[$row["status"]];
			$results[] = (object)$row;
		}
		$results = array("data"=>$results);
		return $results;
	}

	// rec = record array
	public function addUpdateBug ( $rec, $closed = false)
	{
		extract($rec);
		//error_log("rec=".print_r($rec,1));
		$user_nm = (isset($_SESSION["user_nm"])) ? $_SESSION["user_nm"] : "admin";
		if ($id == "") // add
		{
			$sql = "insert into bt_bugs (descr, product, user_nm, bug_type, status, priority, comments, solution, entry_dtm) values (?,?,?,?,?,?,?,?,datetime('now','localtime'))";
			$stmt = $this->dbh->prepare($sql);
			$params = array($descr,$product,$user_nm,$bug_type,$status,$priority,$comments,$solution);
			#echo $sql;
			for ($i=0; $i<count($params); ++$i) $stmt->bindValue($i+1,$params[$i]);
			$result = $stmt->execute();
			if ($result === FALSE) die("SQL ERROR: $sql, ".print_r($this->dbh->lastErrorMsg(),true));
			$count = $this->dbh->changes();
			if ($count == 0) die("ERROR: Record not added! $sql");
			$id = $this->dbh->lastInsertRowID();
			$bug_id="$bt_group".sprintf("%04d",$id);
			$sql = "update bt_bugs set bug_id=? where id=?";
			$stmt = $this->dbh->prepare($sql);
			$params = array($bug_id,$id);
			for ($i=0; $i<count($params); ++$i) $stmt->bindValue($i+1,$params[$i]);
			$result = $stmt->execute();
			if ($result === FALSE) die("SQL ERROR: $sql, ".print_r($this->dbh->lastErrorMsg(),true));
			$count = $this->dbh->changes();
			if ($count == 0) die("ERROR: Update failed ($sql)");
			return $id;
		}
		else // update
		{
			if ($closed)
				$closed2 = ",closed_dtm=datetime('now','localtime')"; else $closed2 = "";
			$sql = "update bt_bugs set descr=?,product=?,bug_type=?,status=?,priority=?,comments=?,solution=?";
			$sql .= $closed2;
			$sql .= ",update_dtm=datetime('now','localtime')";
			$sql .= " where id=?";
			$stmt = $this->dbh->prepare($sql);
			$params = array($descr,$product,$bug_type,$status,$priority,$comments,$solution,$id);
			for ($i=0; $i<count($params); ++$i) $stmt->bindValue($i+1,$params[$i]);
			$result = $stmt->execute();
			if ($result === FALSE) die("SQL ERROR: $sql, ".print_r($this->dbh->lastErrorMsg(),true));
			$count = $this->dbh->changes();
			if ($count == 0) die("ERROR: Record not updated! $sql");
		}
		return "SUCCESS ".$id;
	}

	public function deleteBug ($id)
	{
		$sql = "delete from bt_worklog where bug_id=".intval($id);
		$count = $this->dbh->exec($sql);
		if (!$count) die("SQL ERROR: $sql, ".print_r($this->dbh->lastErrorMsg(),true));
		$sql = "delete from bt_attachments where bug_id=".intval($id);
		$count = $this->dbh->exec($sql);
		if (!$count) die("SQL ERROR: $sql, ".print_r($this->dbh->lastErrorMsg(),true));
		$sql = "delete from bt_bugs where id=".intval($id);
		$count = $this->dbh->exec($sql);
		if (!$count) die("SQL ERROR: $sql, ".print_r($this->dbh->lastErrorMsg(),true));
		return "SUCCESS";
	}

	public function get_worklog_entries ($id)
	{
		$sql = "
select * from bt_worklog w
where bug_id = ?
order by entry_dtm desc
";
		$stmt = $this->dbh->prepare($sql);
		$params = array($id);
		for ($i=0; $i<count($params); ++$i) $stmt->bindValue($i+1,$params[$i]);
		$result = $stmt->execute();
		if ($result === FALSE) die("SQL ERROR: $sql, ".print_r($this->dbh->lastErrorMsg(),true));
		$results = array();
		while ($row = $result->fetchArray(SQLITE3_ASSOC))
		{
			$row["entry_dtm"] = $row["entry_dtm"] != "" ? date("m/d/Y g:i a",strtotime($row["entry_dtm"])) : "";
			$results[] = (object)$row;
		}
		return $results;
	}

	// rec = record array
	public function addWorkLog ($rec)
	{
		// id, bug_id, user_nm, comments, entry_dtm
		extract($rec);
		$sql = "insert into bt_worklog (bug_id, user_nm, comments, wl_public, entry_dtm) values (?,?,?,?,datetime('now','localtime'))";
		$stmt = $this->dbh->prepare($sql);
		$params = array($id,$_SESSION["user_id"],$wl_comments,$wl_public);
		for ($i=0; $i<count($params); ++$i) $stmt->bindValue($i+1,$params[$i]);
		$result = $stmt->execute();
		if ($result === FALSE) die("SQL ERROR: $sql, ".print_r($this->dbh->lastErrorMsg(),true));
		$count = $this->dbh->changes();
		if ($count == 0) die("ERROR: Record not added! $sql");
		return "SUCCESS ".$this->dbh->lastInsertRowID();
	}

	// idx = record index
	// rec = record array
	public function updateWorkLog ($idx, $rec)
	{
		extract($rec);
		$sql = "update bt_worklog set user_nm=?,comments=?,wl_public=? where id=?";
		$stmt = $this->dbh->prepare($sql);
		$params = array($_SESSION["user_id"],$comments,$wl_public,$idx);
		for ($i=0; $i<count($params); ++$i) $stmt->bindValue($i+1,$params[$i]);
		$result = $stmt->execute();
		if ($result === FALSE) die("SQL ERROR: $sql, ".print_r($this->dbh->lastErrorMsg(),true));
		$count = $this->dbh->changes();
		if ($count == 0) die("ERROR: Record not updated! $sql");
	}

	public function getBugTypeDescr ($bug_type)
	{
		$sql = "select descr from bt_type where cd=".q($bug_type);
		$descr = $this->dbh->querySingle($sql);
		return $descr;
	}

	public function getWorkLogEntries ($id)
	{
		$sql = "select count(*) from bt_worklog where bug_id=".intval($id);
		$found = $this->dbh->querySingle($sql);
		if ($found == 0) return array(); // empty record!
		//$sql = "select *,date_format(entry_dtm,'%d-%b-%Y %l:%i %p') edtm from bt_worklog where bug_id=".intval($id)." order by entry_dtm desc";
		$sql = "select * from bt_worklog where bug_id=".intval($id)." order by entry_dtm desc";
		$stmt = $this->dbh->query($sql);
		if (!$stmt) die("SQL ERROR: $sql, ".print_r($this->dbh->lastErrorMsg(),true));
		$results = array();
		while ($row = $stmt->fetchArray(SQLITE3_ASSOC))
		{
			$row["edtm"] = date("m/d/Y G:i",strtotime($row["entry_dtm"]));
			$results[] = $row;
		}
		return $results;
	}

	public function getBugAttachment ($id, $type = SQLITE3_ASSOC)
	{
		$sql = "select * from bt_attachments where id=".intval($id);
		$stmt = $this->dbh->query($sql);
		if (!$stmt) die("SQL ERROR: $sql, ".print_r($this->dbh->lastErrorMsg(),true));
		$results = array();
		while ($row = $stmt->fetchArray($type))
		{
			$results[] = (object)$row;
		}
		return $results;
	}

	public function getBugAttachments ($id)
	{
		$sql = "select id,file_name,file_size from bt_attachments where bug_id=".intval($id);
		$stmt = $this->dbh->query($sql);
		if (!$stmt) die("SQL ERROR: $sql, ".print_r($this->dbh->lastErrorMsg(),true));
		$results = array();
		while ($row = $stmt->fetchArray(SQLITE3_ASSOC))
		{
			$results[] = (object)$row;
		}
		return $results;
	}

	// rec = record array
	public function addAttachment ($id, $filename, $size, $raw_file)
	{
		// id, bug_id, file_name, file_size, file_hash, entry_dtm
		//extract($rec);
		//$hash = md5($id.$filename.date("YmdHis"));
		$hash = md5($raw_file);
		$sql  = "insert into bt_attachments (bug_id, file_name, file_size, file_hash, entry_dtm) values (?,?,?,?,datetime('now','localtime'))";
		$stmt = $this->dbh->prepare($sql);
		$params = array(intval($id),$filename,$size." Bytes",$hash);
		#echo $sql;
		for ($i=0; $i<count($params); ++$i) $stmt->bindValue($i+1,$params[$i]);
		$result = $stmt->execute();
		if ($result === FALSE) die("SQL ERROR: $sql, ".print_r($this->dbh->lastErrorMsg(),true));
// 		$count = $this->dbh->changes();
// 		if ($count == 0) die("ERROR: Record not added! $sql");
		$id = $this->dbh->lastInsertRowID();
		$pdir = substr($hash,0,3);
		if (!file_exists($this->adir.$pdir))
		{
			mkdir($this->adir.$pdir);
		}
		$fp = fopen($this->adir.$pdir."/".$hash,"wb");
		fwrite($fp,$raw_file);
		fclose($fp);

		return $id;
	}

	public function deleteAttachment ($id)
	{
		$sql = "select file_hash from bt_attachments where id=".intval($id);
		$hash = $this->dbh->querySingle($sql);
		$sql = "select count(*) from bt_attachments where file_hash=(select file_hash from bt_attachments where id=".intval($id).")";
		$count = $this->dbh->querySingle($sql);
		$sql = "delete from bt_attachments where id=".intval($id);
		$count2 = $this->dbh->exec($sql);
		if (!$count2) die("SQL ERROR: $sql, ".print_r($this->dbh->lastErrorMsg(),true));
		if ($count == 1)
		{
			$pdir = substr($hash,0,3);
			unlink($this->adir.$pdir."/".$hash);
		}
	}

	public function getUserEntries ()
	{
		$sql = "select uid,lname||', '||fname name,email,roles,case active when 'y' then 'Yes' else 'No' end active from bt_users order by lname,fname";
		$stmt = $this->dbh->query($sql);
		if (!$stmt) die("SQL ERROR: $sql, ".print_r($this->dbh->lastErrorMsg(),true));
		$results = array();
		while ($row = $stmt->fetchArray(SQLITE3_ASSOC))
		{
			$results[] = (object)$row;
		}
		$results = array("data"=>$results);
		return $results;
	}

	public function getUsersSearch ( $args )
	{
		$crit = "1=1"; $params = array();
		if (trim($args["lname"]) != "")
		{
			$crit .= " and lname like ?";
			$params[] = $args["lname"]."%";
		}
		if (trim($args["fname"]) != "")
		{
			$crit .= " and fname like ?";
			$params[] = $args["fname"]."%";
		}
		$sql = "
select uid,lname||', '||fname name,email,roles,case active when 'y' then 'Yes' else 'No' end active from bt_users
where $crit
order by lname,fname";
		$stmt = $this->dbh->prepare($sql);
		if (!$stmt) die("SQL ERROR: $sql, ".print_r($this->dbh->lastErrorMsg(),true));
		for ($i=0; $i<count($params); ++$i) $stmt->bindValue($i+1,$params[$i]);
		$result = $stmt->execute();
		if ($result === FALSE) die("SQL ERROR: $sql, ".print_r($this->dbh->lastErrorMsg(),true));
		$results = array();
		while ($row = $result->fetchArray(SQLITE3_ASSOC))
		{
			$results[] = (object)$row;
		}
		$results = array("data"=>$results);
		return $results;
	}
	
	public function assign_user ( $args )
	{
		$params = array();
		$params[] = $args["uid"];
		$params[] = $args["id"];
		$sql = "update bt_bugs set assigned_to = ? where bug_id = ?";
		$stmt = $this->dbh->prepare($sql);
		if (!$stmt) die("SQL ERROR: $sql, ".print_r($this->dbh->lastErrorMsg(),true));
		for ($i=0; $i<count($params); ++$i) $stmt->bindValue($i+1,$params[$i]);
		$result = $stmt->execute();
		if ($result === FALSE) die("SQL ERROR: $sql, ".print_r($this->dbh->lastErrorMsg(),true));
		return "SUCCESS";
	}

	public function getUserRec ($uid)
	{
		$sql = "select * from bt_users where uid=?";
		$stmt = $this->dbh->prepare($sql);
		if (!$stmt) die("SQL ERROR: $sql, ".print_r($this->dbh->lastErrorMsg(),true));
		$stmt->bindValue(1,$uid);
		$result = $stmt->execute();
		if (!$result) die("SQL ERROR: $sql, ".print_r($this->dbh->lastErrorMsg(),true));
		$results = array();
		while ($row = $result->fetchArray(SQLITE3_ASSOC))
		{
			$results[] = (object)$row;
		}
		return $results;
	}

	// rec = record array
	public function addUser ($rec)
	{
		// uid, lname, fname, email, active, roles
		extract($rec);
		$pw5 = md5($pw);
		//$roles = join(" ",$roles);
		$sql = "insert into bt_users (uid, lname, fname, email, active, roles, pw, bt_group) values (?,?,?,?,?,?,?,?)";
		$stmt = $this->dbh->prepare($sql);
		$params = array($uid1, $lname, $fname, $email, $active, $roles, $pw5, $bt_group);
		for ($i=0; $i<count($params); ++$i) $stmt->bindValue($i+1,$params[$i]);
		$result = $stmt->execute();
		if ($result === FALSE) die("SQL ERROR: $sql, ".print_r($this->dbh->lastErrorMsg(),true));
		$count = $this->dbh->changes();
		if ($count == 0) die("ERROR: Record not added! $sql");
		//return $this->dbh->lastInsertRowID();
		$ename = $_SESSION["user_nm"];
		$msg = "Hello,

BugTrack user $uid1 was added by $ename.

Name: $lname, $fname
";
		$to = $email;
		//if ($to == "") $to = $email;
		$admin_emails = $this->get_admin_emails();
		$headers = "From: BugTrack <no_reply@none.com>\r\nCC: $admin_emails,$email";
		//mail($to,"BugTrack $uid2 user entry",stripcslashes($msg),$headers);
		return "SUCCESS";
	}

	// uid = record key
	// rec = record array
	public function updateUser ($rec)
	{
		extract($rec);
		if ($pw == $pw2) $pw5 = $pw;
		else $pw5 = md5($pw);
		//$roles = join(" ",$roles);
		$sql = "update bt_users set lname=?, fname=?, email=?, active=?, roles=?, pw=?, bt_group=? where uid=?";
		$stmt = $this->dbh->prepare($sql);
		$params = array($lname, $fname, $email, $active, $roles, $pw5, $bt_group, $uid);
		for ($i=0; $i<count($params); ++$i) $stmt->bindValue($i+1,$params[$i]);
		$result = $stmt->execute();
		if ($result === FALSE) die("SQL ERROR: $sql, ".print_r($this->dbh->lastErrorMsg(),true));
		$count = $this->dbh->changes();
		if ($count == 0) die("ERROR: Record not updated! $sql");
		return "SUCCESS";
	}

	public function get_admin_emails ()
	{
		$results = array();
		$sql = "select email from bt_users where roles='admin'";
		try
		{
			$stmt = $this->dbh->query($sql);
			while ($row = $stmt->fetchArray(SQLITE3_ASSOC))
			{
				$results[] = $row["email"];
			}
		}
		catch (Exception $e)
		{
			die("SQL ERROR: $sql, ".$e->getMessage());
		}
		return join(",",$results);
	}
	
	public function do_bug_email ( $args )
	{
		global $sarr, $parr;
		$rec = json_decode($this->getBug($args["id"]));
		if (empty($rec)) die("ERROR: Bug not found ({$args["id"]})");
		$bt = $this->getBugTypeDescr($rec->bug_type);
		if ($rec->user_nm != "") {
			$arr = $this->get_user($rec->user_nm);
			$ename = $arr->name;
			$email = $arr->email;
		} else $ename="";
		if ($rec->assigned_to != "") {
			$arr = $this->get_user($rec->assigned_to);
			$aname = $arr->name;
			$aemail = $arr->email;
		} else $aname="";
		$msg = "{$args["msg2"]}

Details of Bug ID {$rec->bug_id}.

Description: {$rec->descr}
Product or Application: {$rec->product}
Bug Type: $bt
Status: {$sarr[$rec->status]}
Priority: {$parr[$rec->priority]}
Comments: {$rec->comments}
Solution: {$rec->solution}
Entry By: $ename
Assigned To: $aname
Entry Date/Time: {$rec->edtm}
Update Date/Time: {$rec->udtm}
Closed Date/Time: {$rec->cdtm}

";
		$rows = $this->getWorkLogEntries($args["id"]);
		$msg .= count($rows)." Worklog entries found

";
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				//list($wid,$bid,$usernm,$comments,$entry_dtm,$edtm)=$row;
				$o = (object)$row;
				if ($o->user_nm != "") {
					$arr = $this->get_user($o->user_nm);
					$ename = $arr->name;
				} else $ename="";
				$comments = stripcslashes($o->comments);
				$msg .= "Date/Time: {$o->edtm}, By: $ename
Comments: $comments
";
			}
		}
		$sendto = $args["sendto"];
		$cc = $args["cc"];
		$subject = $args["subject"];
		if (!preg_match("/@/",$sendto)) $sendto.="@wilddogdesign.com";
		if ($cc != "" and !preg_match("/@/",$cc)) $cc.="@wilddogdesign.com";
		if ($cc != "") $ccx="CC: $cc"; else $ccx="";
		if (1) {
		$msg = nl2br($msg);
		return <<<END
To: $sendto<br>
Subject: $subject<br>
Headers: $ccx<br>
Content:<br>
$msg
END;
		}
		//mail($sendto,$subject,stripcslashes($msg),$ccx);
	}

	public function check_session ()
	{
		return (isset($_SESSION["user_id"]) and $_SESSION["user_id"] != "") ? 1 : 0;
	}

	public function login_session ( $uid, $pw )
	{
		$results = array();
		$sql = "select * from bt_users where uid=? and pw=? and active='y'";
		$stmt = $this->dbh->prepare($sql);
		$params = array($uid,md5($pw));
		#echo $sql;
		#print_r($params);
		for ($i=0; $i<count($params); ++$i) $stmt->bindValue($i+1,$params[$i]);
		$result = $stmt->execute();
		if ($result === FALSE) die("SQL ERROR: $sql, ".print_r($this->dbh->lastErrorMsg(),true));
		while ($row = $result->fetchArray(SQLITE3_ASSOC))
		{
			$results[] = $row;
		}
		if (empty($results)) die("FAIL");
		$row = $results[0];
		$_SESSION["user_id"] = $uid;
		$_SESSION["user_nm"] = $row["fname"]." ".$row["lname"];
		$_SESSION["email"] = $row["email"];
		$_SESSION["roles"] = $row["roles"];
		$_SESSION["group"] = $row["bt_group"];
		echo $row;
	}

	public function getBTlookups ()
	{
		global $sarr,$parr;
		$sql = "select cd,descr from bt_groups order by descr";
		$stmt = $this->dbh->query($sql);
		if (!$stmt) die("SQL ERROR: $sql, ".print_r($this->dbh->lastErrorMsg(),true));
		$results = array();
		while ($row = $stmt->fetchArray(SQLITE3_ASSOC))
		{
			$results[] = (object)$row;
		}
		$results = array("bt_groups"=>$results);

		$sql = "select cd,descr from bt_type order by descr";
		$stmt = $this->dbh->query($sql);
		if (!$stmt) die("SQL ERROR: $sql, ".print_r($this->dbh->lastErrorMsg(),true));
		$results2 = array();
		while ($row = $stmt->fetchArray(SQLITE3_ASSOC))
		{
			$results2[] = (object)$row;
		}
		$results["bt_types"] = $results2;
		$results["bt_status"] = $sarr;
		$results["bt_priority"] = $parr;
		$results["roles"] = $_SESSION["roles"];
		return $results;
	}

	// determine user info
	function get_user ($uname) {
	//	global $UsersArr;
		$sql = "select lname,fname,email,lname||', '||fname name from bt_users where uid='$uname'";
		$stmt = $this->dbh->query($sql);
		$arr = $stmt->fetchArray(SQLITE3_ASSOC);
	//	return explode(",", $UsersArr[$uname]);
		return (!empty($arr)) ? (object)$arr : array();
	}

	public function getHandle ()
	{
		return $this->dbh;
	}

	public function getAdir ()
	{
		return $this->adir;
	}

} // end class BugTrack
?>
