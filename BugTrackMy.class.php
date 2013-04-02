<?php
// BugTrack.class.php
//
// Ron Patterson, WildDog Design/BPWC
//
// MySQLi version

define("AUSERS","ron,janie");
$sarr=array("o"=>"Open", "h"=>"Hold", "w"=>"Working", "t"=>"Testing", "c"=>"Closed");
$parr=array("1"=>"High","2"=>"Normal","3"=>"Low");
$grparr=array("WDD"=>"WildDog Design");

function q ($val) {
	if (empty($val)) return "NULL";
	//return "'".addslashes(stripslashes($val))."'";
	return "'".mysqli::real_escape_string(stripslashes($val))."'";
}
	
class BugTrack {
	protected $dbh;
	protected $tblsArray = array();
	
	public function __construct () {
		// MySQL database version
		//$dbh = new mysqli('localhost', 'buguser', 'apw4bug', 'bugtrack');
		$this->dbh = new mysqli('wilddo1.fatcowmysql.com', 'buguser', 'apw4bug', 'bugtrack');
		//if ($dbh->connect_errno) {
		if (mysqli_connect_errno()) {
			//die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
			//die("SQL CONNECTION ERROR: ".$e->getMessage());
			header("Location: /dberror.html");
			exit;
		}
	}
	
	public function getTables () {
		$this->tblsArray = array();
		// type|name|tbl_name|rootpage|sql
		$sql = "select table_name from information_schema.tables where table_type='base table' order by table_name";
		$result = $this->dbh->query($sql);
		while ($row = $result->fetch_assoc()) {
			$this->tblsArray[] = $row["table_name"];
		}
		return $this->tblsArray;
	}
	
	public function buildTablesList () {
		if (count($this->tblsArray) == 0) $this->getTables();
		$out = "";
		foreach ($this->tblsArray as $tbl) {
			$sql = "select count(*) from $tbl";
			$result = $this->dbh->query($sql);
			if (!$result) {
				die("SQL ERROR: $sql, ".$this->dbh->error);
			}
			list($sz) = $result->fetch_array();
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
			$sql = "select count(*) from $tbl";
			$result = $this->dbh->query($sql);
			list($sz) = $result->fetch_array();
			if ($this->dbh->errno) {
				die("SQL ERROR: $sql, ".$this->dbh->error);
			}
			$out .= <<<END
	<tr><td align="left">{$tbl}</td><td align="center">{$sz}</td><td><input type="checkbox" name="tables[]" value="{$tbl}"></td></tr>\n
END;
		}
		if ($out != "") return "<table><tr><th>Backup Table</th><th>Rows</th><th></th></tr>\n".$out."</table>";
		return "";
	}

	public function getBug ($id, $type = MYSQLI_ASSOC) {
		// id, descr, product, user_nm, bug_type, status, priority, comments, solution, assigned_to, bug_id, entry_dtm, update_dtm, closed_dtm
		$sql = "select count(*) from bt_bugs where id=".intval($id);
		$result = $this->dbh->query($sql);
		list($found) = $result->fetch_array();
		if ($found == 0) return array(); // empty record!
		$sql = "select *,date_format(entry_dtm,'%d-%b-%Y %l:%i %p') edtm,date_format(update_dtm,'%d-%b-%Y %l:%i %p') udtm,date_format(closed_dtm,'%d-%b-%Y %l:%i %p') cdtm from bt_bugs where id=".intval($id);
		$result = $this->dbh->query($sql);
		if ($this->dbh->errno) die("SQL ERROR: $sql, ".$this->dbh->error);
		return $result->fetch_all($type);
	}

	public function getBugs ($crit = "", $order = "") {
		if (empty($crit)) $crit = "1=1";
		$sql = "select count(*) from bt_bugs where $crit";
		$stmt = $this->dbh->query($sql);
		list($found) = $result->fetch_array();
		if ($found == 0) return array(); // empty record!
		$sql = "select *,date_format(entry_dtm,'%d-%b-%Y %l:%i %p') edtm,date_format(update_dtm,'%d-%b-%Y %l:%i %p') udtm,date_format(closed_dtm,'%d-%b-%Y %l:%i %p') cdtm from bt_bugs where $crit";
		if (!empty($order)) $sql .= " order by ".$order;
		$result = $this->dbh->query($sql);
		if ($this->dbh->errno) die("SQL ERROR: $sql, ".$this->dbh->error);
		return $result->fetch_all($type);
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
		$result = $this->dbh->query($sql);
		if ($result === FALSE) die("SQL ERROR: $sql, ".$this->dbh->error);
		//if ($count == 0) die("ERROR: Record not added! $sql");
		$id = $this->dbh->insert_id;
		$bug_id="$group$id";
		$result = $dbh->query("update bt_bugs set bug_id='$bug_id' where id=".intval($id));
		if (!$result) die("ERROR: Update failed ($sql)");
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
		$result = $this->dbh->query($sql);
		if ($result === FALSE) die("SQL ERROR: $sql, ".$this->dbh->error);
		//if ($count == 0) die("ERROR: Record not updated! $sql");
	}

	public function deleteBug ($id) {
		$sql = "delete from bt_worklog where bug_id=".intval($id);
		$result = $this->dbh->query($sql);
		if (!$result) die("SQL ERROR: $sql, ".$this->dbh->error);
		$sql = "delete from bt_attachments where bug_id=".intval($id);
		$count = $this->dbh->query($sql);
		if (!$result) die("SQL ERROR: $sql, ".$this->dbh->error);
		$sql = "delete from bt_bugs where id=".intval($id);
		$result = $this->dbh->query($sql);
		if (!$result) die("SQL ERROR: $sql, ".$this->dbh->error);
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
		$result = $this->dbh->query($sql);
		if ($result === FALSE) die("SQL ERROR: $sql, ".$this->dbh->error);
		//if ($result == 0) die("ERROR: Record not added! $sql");
		return $this->dbh->insert_id;
	}

	// idx = record index
	// rec = record array
	public function updateWorkLog ($idx, $rec) {
		extract($rec);
		$sql = "update bt_worklog set";
		$sql .= " usernm=".q($usernm);
		$sql .= ",comments=".q($comments);
		$sql .= " where id=".intval($idx);
		$query = $this->dbh->query($sql);
		if ($query === FALSE) die("SQL ERROR: $sql, ".$this->dbh->error);
		if ($query == 0) die("ERROR: Record not updated! $sql");
	}

	public function getBugTypeDescr ($bug_type) {
		$sql = "select descr from bt_type where cd=".q($bug_type);
		$result = $this->dbh->query($sql);
		list($descr) = $result->fetch_array();
		return $descr;
	}

	public function getWorkLogEntries ($id) {
		$sql = "select count(*) from bt_worklog where bug_id=".intval($id);
		$result = $this->dbh->query($sql);
		list($found) = $result->fetch_array();
		if ($found == 0) return array(); // empty record!
		$sql = "select *,date_format(entry_dtm,'%d-%b-%Y %l:%i %p') edtm from bt_worklog where bug_id=".intval($id)." order by entry_dtm desc";
		$result = $this->dbh->query($sql);
		if (!$result) die("SQL ERROR: $sql, ".$this->dbh->error);
		return $result->fetch_all(); // all records array
	}

	public function getBugAttachment ($id, $type = MYSQLI_ASSOC) {
		$sql = "select count(*) from bt_attachments where id=".intval($id);
		$result = $this->dbh->query($sql);
		list($found) = $result->fetch_array();
		if ($found == 0) return array(); // empty record!
		$sql = "select * from bt_attachments where id=".intval($id);
		$result = $this->dbh->query($sql);
		if (!$result) die("SQL ERROR: $sql, ".$this->dbh->error);
		return $result->fetch_all($type);
	}

	public function getBugAttachments ($id) {
		$sql = "select count(*) from bt_attachments where bug_id=".intval($id);
		$result = $this->dbh->query($sql);
		list($found) = $result->fetch_array();
		if ($found == 0) return array(); // empty record!
		$sql = "select id,file_name,file_size from bt_attachments where bug_id=".intval($id);
		$result = $this->dbh->query($sql);
		if (!$result) die("SQL ERROR: $sql, ".$this->dbh->error);
		return $result->fetch_all(); // all records array
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
		$result = $this->dbh->query($sql);
		if ($result === FALSE) die("SQL ERROR: $sql, ".$this->dbh->error);
		//if ($result == 0) die("ERROR: Record not added! $sql");
		return $this->dbh->insert_id();
	}

	public function deleteAttachment ($id) {
		$sql = "delete from bt_attachments where id=".intval($id);
		$result = $this->dbh->query($sql);
		if (!$result) die("SQL ERROR: $sql, ".$this->dbh->error);
	}

	public function getHandle () {
		return $this->dbh;
	}
} // end class BugTrack
?>