<?php
include_once 'Error.php';
include_once 'local_settings.php';

define ('DUPLICATE_ENTRY', 1062);

class DbConnection
{
	const DBNAME = DB_NAME;
	const HOSTNAME = DB_HOSTNAME;
	const USERNAME = DB_USERNAME;
	const PASSWORD = DB_PASSWORD;
	
	private static $DB = null;
	
	private function __construct() {}
	
	private static function connect()
	{
		if (self::$DB != null)
		{
			return;
		}
		Error::printToLog(ERRLOGFILE, 0, "connecting to DB...");
		self::$DB = new mysqli(self::HOSTNAME, self::USERNAME, self::PASSWORD, self::DBNAME);
		if (self::getConnectError())
		{
			$logMsg = __METHOD__ . " line " . __LINE__  . ": " . self::getConnectErrorMsg();
			Error::printToLog(ERRLOGFILE, self::getConnectError(), $logMsg);
			self::closeDB();
		}
	}
	
	protected static function execute($query)
	{
		if (self::$DB == null)
		{
			self::connect();
			if (self::$DB == null)
			{
				return false;
			}
		}
		
		Error::printToLog(ERRLOGFILE, 0, "query: $query");
		$result = self::$DB->query($query);
		if (!$result)
		{
			$logMsg = __METHOD__ . " line " . __LINE__  . ": " . self::getErrorMsg() . "query: $query";
			Error::printToLog(ERRLOGFILE, self::getError(), $logMsg);
		}
		
		return $result;
	}
	
	public static function closeDB()
	{
		if (self::$DB)
		{
			Error::printToLog(ERRLOGFILE, 0, "closing DB...");
			self::$DB->close();
			self::$DB = null;
		}
	}
	
	public static function getError()
	{
		return self::$DB->errno;
	}
	
	public static function getErrorMsg()
	{
		return self::$DB->error;
	}
	
	public static function getConnectError()
	{
		return self::$DB->connect_errno;
	}
	
	public static function getConnectErrorMsg()
	{
		return self::$DB->connect_error;
	}
	
	public static function isConnected()
	{
		return (self::$DB != null and self::getConnectError() == 0);
	}
	
	public static function escapeString($str)
	{
		return self::$DB->real_escape_string($str);
	}
	
	protected static function lockTable($tableName)
	{
		$query = "lock tables $tableName write";
		return self::execute($query);
	}
	
	public static function unlockTables()
	{
		$query = "unlock tables";
		self::execute($query);
	}
}
?>
