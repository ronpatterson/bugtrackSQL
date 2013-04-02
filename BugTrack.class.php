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
	return "'".str_replace("'","''",$val)."'";
}
	
class BugTrack {
	protected $dbh;
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
		// id, descr, product, user_nm, bug_type, status, priority, comments, solution, assigned_to, bug_id, entry_dtm, update_dtm, closed_dtm
		$sql = "select count(*) from bt_bugs where id=".intval($id);
		$found = $this->dbh->querySingle($sql);
		if ($found == 0) return array(); // empty record!
		//$sql = "select *,date_format(entry_dtm,'%d-%b-%Y %l:%i %p') edtm,date_format(update_dtm,'%d-%b-%Y %l:%i %p') udtm,date_format(closed_dtm,'%d-%b-%Y %l:%i %p') cdtm from bt_bugs where id=".intval($id);
		$sql = "select * from bt_bugs where id=".intval($id);
		$stmt = $this->dbh->query($sql);
		if (!$stmt) die("SQL ERROR: $sql, ".print_r($this->dbh->lastErrorMsg(),true));
		$results = array();
		$row = $stmt->fetchArray($type);
		$stmt->finalize();
		return $row;
	}

	public function getBugs ($crit = "", $order = "")
	{
		if (empty($crit)) $crit = "1=1";
		$sql = "select count(*) from bt_bugs where $crit";
		$found = $this->dbh->querySingle($sql);
		if ($found == 0) return array(); // empty record!
		//$sql = "select *,date_format(entry_dtm,'%d-%b-%Y %l:%i %p') edtm,date_format(update_dtm,'%d-%b-%Y %l:%i %p') udtm,date_format(closed_dtm,'%d-%b-%Y %l:%i %p') cdtm from bt_bugs where $crit";
		$sql = "select * from bt_bugs where $crit";
		if (!empty($order)) $sql .= " order by ".$order;
		$stmt = $this->dbh->query($sql);
		if (!$stmt) die("SQL ERROR: $sql, ".print_r($this->dbh->lastErrorMsg(),true));
		$results = array();
		while ($row = $stmt->fetchArray())
		{
			$results[] = $row;
		}
		return $results;
	}

	// rec = record array
	public function addBug ($rec)
	{
		extract($rec);
		$sql = "insert into bt_bugs (descr, product, user_nm, bug_type, status, priority, comments, solution, assigned_to, entry_dtm) values (?,?,?,?,?,?,?,?,?,datetime('now'))";
		$stmt = $this->dbh->prepare($sql);
		$params = array($descr,$product,$user_nm,$bug_type,$status,$priority,$comments,$solution,$assigned_to);
		#echo $sql;
		for ($i=0; $i<count($params); ++$i) $stmt->bindValue($i+1,$params[$i]);
		$result = $stmt->execute();
		if ($result === FALSE) die("SQL ERROR: $sql, ".print_r($this->dbh->lastErrorMsg(),true));
		$count = $this->dbh->changes();
		if ($count == 0) die("ERROR: Record not added! $sql");
		$id = $this->dbh->lastInsertRowID();
		$bug_id="$group$id";
		$sql = "update bt_bugs set bug_id=? where id=?";
		$params = array($bug_id,$id);
		for ($i=0; $i<count($params); ++$i) $stmt->bindValue($i+1,$params[$i]);
		$result = $stmt->execute();
		if ($result === FALSE) die("SQL ERROR: $sql, ".print_r($this->dbh->lastErrorMsg(),true));
		$count = $this->dbh->changes();
		if ($count == 0) die("ERROR: Update failed ($sql)");
		return $id;
	}

	// idx = record index
	// rec = record array
	// closed = boolean
	public function updateBug ($idx, $rec, $closed)
	{
		extract($rec);
		if ($closed)
			$closed2 = ",closed_dtm=datetime('now')"; else $closed2 = "";
		$sql = "update bt_bugs set descr=?,product=?,bug_type=?,status=?,priority=?,comments=?,solution=?,assigned_to=?";
		$sql .= $closed2;
		$sql .= ",update_dtm=datetime('now')";
		$sql .= " where id=?";
		$stmt = $this->dbh->prepare($sql);
		$params = array($descr,$product,$bug_type,$status,$priority,$comments,$solution,$assigned_to,$idx);
		for ($i=0; $i<count($params); ++$i) $stmt->bindValue($i+1,$params[$i]);
		$result = $stmt->execute();
		if ($result === FALSE) die("SQL ERROR: $sql, ".print_r($this->dbh->lastErrorMsg(),true));
		$count = $this->dbh->changes();
		if ($count == 0) die("ERROR: Record not updated! $sql");
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
	}

	// rec = record array
	public function addWorkLog ($rec)
	{
		// id, bug_id, user_nm, comments, entry_dtm
		extract($rec);
		$sql = "insert into bt_worklog (bug_id, user_nm, comments, entry_dtm) values (?,?,?,datetime('now'))";
		$stmt = $this->dbh->prepare($sql);
		$params = array($id,$usernm,$comments);
		for ($i=0; $i<count($params); ++$i) $stmt->bindValue($i+1,$params[$i]);
		$result = $stmt->execute();
		if ($result === FALSE) die("SQL ERROR: $sql, ".print_r($this->dbh->lastErrorMsg(),true));
		$count = $this->dbh->changes();
		if ($count == 0) die("ERROR: Record not added! $sql");
		return $this->dbh->lastInsertRowID();
	}

	// idx = record index
	// rec = record array
	public function updateWorkLog ($idx, $rec)
	{
		extract($rec);
		$sql = "update bt_worklog set user_nm=?,comments=? where id=?";
		$stmt = $this->dbh->prepare($sql);
		$params = array($usernm,$comments,$idx);
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
		while ($row = $stmt->fetchArray())
		{
			$results[] = $row;
		}
		return $results;
	}

	public function getBugAttachment ($id, $type = SQLITE3_ASSOC)
	{
		$sql = "select count(*) from bt_attachments where id=".intval($id);
		$found = $this->dbh->querySingle($sql);
		if ($found == 0) return array(); // empty record!
		$sql = "select * from bt_attachments where id=".intval($id);
		$stmt = $this->dbh->query($sql);
		if (!$stmt) die("SQL ERROR: $sql, ".print_r($this->dbh->lastErrorMsg(),true));
		$results = array();
		while ($row = $stmt->fetchArray($type))
		{
			$results[] = $row;
		}
		return $results;
	}

	public function getBugAttachments ($id) {
		$sql = "select count(*) from bt_attachments where bug_id=".intval($id);
		$found = $this->dbh->querySingle($sql);
		if ($found == 0) return array(); // empty record!
		$sql = "select id,file_name,file_size from bt_attachments where bug_id=".intval($id);
		$stmt = $this->dbh->query($sql);
		if (!$stmt) die("SQL ERROR: $sql, ".print_r($this->dbh->lastErrorMsg(),true));
		$results = array();
		while ($row = $stmt->fetchArray())
		{
			$results[] = $row;
		}
		return $results;
	}

	// rec = record array
	public function addAttachment ($id, $filename, $size, $raw_file)
	{
		// id, bug_id, file_name, file_size, attachment, entry_dtm
		extract($rec);
		$sql  = "insert into bt_attachments (bug_id, file_name, file_size, attachment, entry_dtm) values (?,?,?,?,datetime('now'))";
		$stmt = $this->dbh->prepare($sql);
		$params = array(intval($id),$filename,$size." Bytes",$raw_file);
		#echo $sql;
		for ($i=0; $i<count($params); ++$i) $stmt->bindValue($i+1,$params[$i]);
		$result = $stmt->execute();
		if ($result === FALSE) die("SQL ERROR: $sql, ".print_r($this->dbh->lastErrorMsg(),true));
		$count = $his->dbh->changes();
		if ($count == 0) die("ERROR: Record not added! $sql");
		return $this->dbh->lastInsertRowID();
	}

	public function deleteAttachment ($id)
	{
		$sql = "delete from bt_attachments where id=".intval($id);
		$count = $this->dbh->exec($sql);
		if (!$count) die("SQL ERROR: $sql, ".print_r($this->dbh->lastErrorMsg(),true));
	}

	public function getHandle ()
	{
		return $this->dbh;
	}
} // end class BugTrack
?>