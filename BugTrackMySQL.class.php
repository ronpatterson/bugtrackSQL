<?php
// BugTrack.class.php
//
// Ron Patterson, WildDog Design/BPWC
//
// PDO version

define("AUSERS","ron,janie");
$sarr=array("o"=>"Open", "h"=>"Hold", "w"=>"Working", "t"=>"Testing", "c"=>"Closed");
$parr=array("1"=>"High","2"=>"Normal","3"=>"Low");
$grparr=array("WDD"=>"WildDog Design");

function q ($val) {
	if (empty($val)) return "NULL";
	return "'".addslashes(stripslashes($val))."'";
}
	
class BugTrack {
	protected $dbh;
	protected $tblsArray = array();
	
	public function __construct () {
		// MySQL database version
		$dsn = "mysql:dbname=bugtrack;host=wilddo1.fatcowmysql.com";
		try {
			$this->dbh = new PDO($dsn,"buguser","apw4bug");
		}
		catch (PDOException $e) {
			//die("SQL CONNECTION ERROR: ".$e->getMessage());
			header("Location: /dberror.html");
			exit;
		}
	}
	
	public function getTables () {
		$this->tblsArray = array();
		// type|name|tbl_name|rootpage|sql
		$sql = "select table_name from information_schema.tables where table_type='base table' order by table_name";
		try {
			$stmt = $this->dbh->query($sql);
			while ($row = $stmt->fetch()) {
				$this->tblsArray[] = $row["table_name"];
			}
		}
		catch (PDOException $e) {
			die("SQL ERROR: $sql, ".$e->getMessage());
		}
		return $this->tblsArray;
	}
	
	public function buildTablesList () {
		if (count($this->tblsArray) == 0) $this->getTables();
		$out = "";
		foreach ($this->tblsArray as $tbl) {
			try {
				$sql = "select count(*) from $tbl";
				$stmt = $this->dbh->query($sql);
				$sz = $stmt->fetchColumn();
			}
			catch (PDOException $e) {
				die("SQL ERROR: $sql, ".$e->getMessage());
			}
			$out .= <<<END
	<li>{$tbl} ({$sz}) <input type="checkbox" name="tables[]" value="{$tbl}"></li>\n
END;
		}
		if ($out != "") return "<ul>\n".$out."</ul>";
		return "";
	}

	public function buildTablesTable () {
		if (count($this->tblsArray) == 0) $this->getTables();
		$out = "";
		foreach ($this->tblsArray as $tbl) {
			try {
				$sql = "select count(*) from $tbl";
				$stmt = $this->dbh->query($sql);
				$sz = $stmt->fetchColumn();
			}
			catch (PDOException $e) {
				die("SQL ERROR: $sql, ".$e->getMessage());
			}
			$out .= <<<END
	<tr><td align="left">{$tbl}</td><td align="center">{$sz}</td><td><input type="checkbox" name="tables[]" value="{$tbl}"></td></tr>\n
END;
		}
		if ($out != "") return "<table><tr><th>Backup Table</th><th>Rows</th><th></th></tr>\n".$out."</table>";
		return "";
	}

	public function getBug ($id, $type = PDO::FETCH_ASSOC) {
		// id, descr, product, user_nm, bug_type, status, priority, comments, solution, assigned_to, bug_id, entry_dtm, update_dtm, closed_dtm
		$sql = "select count(*) from bt_bugs where id=".intval($id);
		$stmt = $this->dbh->query($sql);
		$found = $stmt->fetchColumn();
		if ($found == 0) return array(); // empty record!
		$sql = "select *,date_format(entry_dtm,'%d-%b-%Y %l:%i %p') edtm,date_format(update_dtm,'%d-%b-%Y %l:%i %p') udtm,date_format(closed_dtm,'%d-%b-%Y %l:%i %p') cdtm from bt_bugs where id=".intval($id);
		$stmt = $this->dbh->query($sql);
		if (!$stmt) die("SQL ERROR: $sql, ".print_r($this->dbh->errorInfo(),true));
		return $stmt->fetch($type);
	}

	public function getBugs ($crit = "", $order = "") {
		if (empty($crit)) $crit = "1=1";
		$sql = "select count(*) from bt_bugs where $crit";
		$stmt = $this->dbh->query($sql);
		$found = $stmt->fetchColumn();
		if ($found == 0) return array(); // empty record!
		$sql = "select *,date_format(entry_dtm,'%d-%b-%Y %l:%i %p') edtm,date_format(update_dtm,'%d-%b-%Y %l:%i %p') udtm,date_format(closed_dtm,'%d-%b-%Y %l:%i %p') cdtm from bt_bugs where $crit";
		if (!empty($order)) $sql .= " order by ".$order;
		$stmt = $this->dbh->query($sql);
		if (!$stmt) die("SQL ERROR: $sql, ".print_r($this->dbh->errorInfo(),true));
		return $stmt->fetchAll(); // all records array
	}

	// rec = record array
	public function addBug ($rec) {
		extract($rec);
		$sql = "insert into bt_bugs (descr, product, user_nm, bug_type, status, priority, comments, solution, assigned_to, entry_dtm) values (";
		$sql .= quotem($descr);
		$sql .= ",".q($product);
		$sql .= ",".q($user_nm);
		$sql .= ",".q($bug_type);
		$sql .= ",".q($status);
		$sql .= ",".q($priority);
		$sql .= ",".q($comments);
		$sql .= ",".q($solution);
		$sql .= ",".q($assigned_to);
		$sql .= ",now())";
		#echo $sql;
		$count = $this->dbh->exec($sql);
		if ($count === FALSE) die("SQL ERROR: $sql, ".print_r($this->dbh->errorInfo(),true));
		if ($count == 0) die("ERROR: Record not added! $sql");
		$id = $this->dbh->lastInsertId();
		$bug_id="$group$id";
		$count = $dbh->exec("update bt_bugs set bug_id='$bug_id' where id=".intval($id));
		if (!$count) die("ERROR: Update failed ($sql)");
		return $id;
	}

	// idx = record index
	// rec = record array
	// closed = boolean
	public function updateBug ($idx, $rec, $closed) {
		extract($rec);
		if ($closed)
			$closed2 = ",closed_dtm=now()"; else $closed2 = "";
		$sql = "update bt_bugs set";
		$sql .= " descr=".q($descr);
		$sql .= ",product=".q($product);
		$sql .= ",bug_type=".q($bug_type);
		$sql .= ",status=".q($status);
		$sql .= ",priority=".q($priority);
		$sql .= ",comments=".q($comments);
		$sql .= ",solution=".q($solution);
		$sql .= ",assigned_to=".q($assigned_to);
		$sql .= $closed2;
		$sql .= ",update_dtm=now()";
		$sql .= " where id=".intval($idx);
		$count = $this->dbh->exec($sql);
		if ($count === FALSE) die("SQL ERROR: $sql, ".print_r($this->dbh->errorInfo(),true));
		if ($count == 0) die("ERROR: Record not updated! $sql");
	}

	public function deleteBug ($id) {
		$sql = "delete from bt_worklog where bug_id=".intval($id);
		$count = $this->dbh->exec($sql);
		if (!$count) die("SQL ERROR: $sql, ".print_r($this->dbh->errorInfo(),true));
		$sql = "delete from bt_attachments where bug_id=".intval($id);
		$count = $this->dbh->exec($sql);
		if (!$count) die("SQL ERROR: $sql, ".print_r($this->dbh->errorInfo(),true));
		$sql = "delete from bt_bugs where id=".intval($id);
		$count = $this->dbh->exec($sql);
		if (!$count) die("SQL ERROR: $sql, ".print_r($this->dbh->errorInfo(),true));
	}

	// rec = record array
	public function addWorkLog ($rec) {
		// id, bug_id, user_nm, comments, entry_dtm
		extract($rec);
		$sql = "insert into bt_worklog (bug_id, user_nm, comments, entry_dtm) values (";
		$sql .= $id;
		$sql .= ",".q($usernm);
		$sql .= ",".q($comments);
		$sql .= ",now())";
		#echo $sql;
		$count = $this->dbh->exec($sql);
		if ($count === FALSE) die("SQL ERROR: $sql, ".print_r($this->dbh->errorInfo(),true));
		if ($count == 0) die("ERROR: Record not added! $sql");
		return $this->dbh->lastInsertId();
	}

	// idx = record index
	// rec = record array
	public function updateWorkLog ($idx, $rec) {
		extract($rec);
		$sql = "update bt_worklog set";
		$sql .= " usernm=".q($usernm);
		$sql .= ",comments=".q($comments);
		$sql .= " where id=".intval($idx);
		$count = $this->dbh->exec($sql);
		if ($count === FALSE) die("SQL ERROR: $sql, ".print_r($this->dbh->errorInfo(),true));
		if ($count == 0) die("ERROR: Record not updated! $sql");
	}

	public function getBugTypeDescr ($bug_type) {
		$sql = "select descr from bt_type where cd=".q($bug_type);
		$stmt = $this->dbh->query($sql);
		$descr = $stmt->fetchColumn();
		return $descr;
	}

	public function getWorkLogEntries ($id) {
		$sql = "select count(*) from bt_worklog where bug_id=".intval($id);
		$stmt = $this->dbh->query($sql);
		$found = $stmt->fetchColumn();
		if ($found == 0) return array(); // empty record!
		$sql = "select *,date_format(entry_dtm,'%d-%b-%Y %l:%i %p') edtm from bt_worklog where bug_id=".intval($id)." order by entry_dtm desc";
		$stmt = $this->dbh->query($sql);
		if (!$stmt) die("SQL ERROR: $sql, ".print_r($this->dbh->errorInfo(),true));
		return $stmt->fetchAll(); // all records array
	}

	public function getBugAttachment ($id, $type = PDO::FETCH_ASSOC) {
		$sql = "select count(*) from bt_attachments where id=".intval($id);
		$stmt = $this->dbh->query($sql);
		$found = $stmt->fetchColumn();
		if ($found == 0) return array(); // empty record!
		$sql = "select * from bt_attachments where id=".intval($id);
		$stmt = $this->dbh->query($sql);
		if (!$stmt) die("SQL ERROR: $sql, ".print_r($this->dbh->errorInfo(),true));
		return $stmt->fetch($type);
	}

	public function getBugAttachments ($id) {
		$sql = "select count(*) from bt_attachments where bug_id=".intval($id);
		$stmt = $this->dbh->query($sql);
		$found = $stmt->fetchColumn();
		if ($found == 0) return array(); // empty record!
		$sql = "select id,file_name,file_size from bt_attachments where bug_id=".intval($id);
		$stmt = $this->dbh->query($sql);
		if (!$stmt) die("SQL ERROR: $sql, ".print_r($this->dbh->errorInfo(),true));
		return $stmt->fetchAll(); // all records array
	}

	// rec = record array
	public function addAttachment ($id, $filename, $size, $raw_file) {
		// id, bug_id, file_name, file_size, attachment, entry_dtm
		extract($rec);
		$sql  = "insert into bt_attachments (bug_id, file_name, file_size, attachment, entry_dtm) values ";
		$sql .= "(".intval($id).",'".$filename."', ";
		$sql .= "'".$size." Bytes',";
		$sql .= "'".$raw_file."',now())";
		#echo $sql;
		$count = $this->dbh->exec($sql);
		if ($count === FALSE) die("SQL ERROR: $sql, ".print_r($this->dbh->errorInfo(),true));
		if ($count == 0) die("ERROR: Record not added! $sql");
		return $this->dbh->lastInsertId();
	}

	public function deleteAttachment ($id) {
		$sql = "delete from bt_attachments where id=".intval($id);
		$count = $this->dbh->exec($sql);
		if (!$count) die("SQL ERROR: $sql, ".print_r($this->dbh->errorInfo(),true));
	}

	public function getHandle () {
		return $this->dbh;
	}
} // end class BugTrack
?>