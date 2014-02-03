<?php
include_once 'Error.php';

class DbConnection
{
	//const DBNAME = "1570618_story";
	//const HOSTNAME = "fdb3.awardspace.net";
	//const USERNAME = "1570618_story";
	//const PASSWORD = "aqswdefr1";
	
	const DBNAME = "story";
	const HOSTNAME = "localhost";
	const USERNAME = "shahaf";
	const PASSWORD = "";
	
	protected static $DB = null;
	protected $Error = 0;
	protected $ErrorMsg;
	
	protected function __construct()
	{
		if (self::$DB == null)
		{
			self::$DB = new mysqli(self::HOSTNAME, self::USERNAME, self::PASSWORD, self::DBNAME);
			$this->Error = self::$DB->connect_errno;
			$this->ErrorMsg = self::$DB->connect_error;
			if (self::$DB->connect_error)
			{
				$logMsg = __METHOD__ . " line " . __LINE__  . ": " . self::$DB->connect_error;
				Error::printToLog(ERRLOGFILE, self::$DB->connect_errno, $logMsg);
				self::closeDB();
			}
		}
	}
	
	public static function closeDB()
	{
		if (self::$DB)
		{
			self::$DB->close();
			self::$DB = null;
		}
	}
	
	public function getError()
	{
		return $this->Error;
	}
	
	public function getErrorMsg()
	{
		return $this->ErrorMsg;
	}
	
	public static function escapeString($str)
	{
		return self::$DB->real_escape_string($str);
	}
	
	protected function lockTable($tableName)
	{
		$query = "lock tables $tableName write";
		self::$DB->query($query);
	}
	
	public static function unlockTables()
	{
		$query = "unlock tables";
		self::$DB->query($query);
	}
}
?>
