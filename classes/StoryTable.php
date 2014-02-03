<?php
include_once 'DbConnection.php';

define ('STORY_AVAILABLE',	0);
define ('STORY_UNAVAILABLE',1);

class StoryTable extends DbConnection
{
	public function __construct()
	{
		parent::__construct();
	}
	
	private function getStory($query)
	{
		$result = self::$DB->query($query);
		if (!$result)
		{
			$logMsg = __METHOD__ . " line " . __LINE__  . ": self::$DB->error query: $query";
			Error::printToLog(ERRLOGFILE, self::$DB->errno, $logMsg);
			return null;
		}
		$story = $result->fetch_array();
		if ($story)
		{
			return $story['id'];
		}
		return null;
	}
	
	/*public function getAvailableStory()
	{
		$query = "select id from Stories where status=" . STORY_AVAILABLE;
		return $this->getStory($query);
	}*/
	
	public function getAvailableStory($userId)
	{
		$query = "select id from Stories where status=" . STORY_AVAILABLE . " and user!=$userId";
		return $this->getStory($query);
	}
	
	public function getStoryByUserId($userId)
	{
		$query = "select id from Stories where status=" . STORY_UNAVAILABLE . " and user=$userId";
		return $this->getStory($query);
	}
	
	public function changeStoryStatus($storyId, $newStatus, $userId)
	{
		if ($userId)
		{
			$query = "UPDATE Stories SET status=$newStatus, user=$userId WHERE id=$storyId";
		}
		else
		{
			$query = "UPDATE Stories SET status=$newStatus WHERE id=$storyId";
		}
		$result = self::$DB->query($query);
		if (!$result)
		{
			$logMsg = __METHOD__ . " line " . __LINE__  . ": " . self::$DB->error;
			Error::printToLog(ERRLOGFILE, self::$DB->errno, $logMsg);
		}
		
		return $result;
	}
	
	public function addStoryToDb($userId)
	{
		$query = "insert into Stories values (null, 0, $userId)";
		$result = self::$DB->query($query);
		if (!$result)
		{
			$logMsg = __METHOD__ . " line " . __LINE__  . ": " . self::$DB->error;
			Error::printToLog(ERRLOGFILE, self::$DB->errno, $logMsg);
		}
		return $result;
	}

	public function getLastStoryId()
	{
		$query = "select max(id) from Stories";
		$result = self::$DB->query($query);
		if (!$result)
		{
			$logMsg = __METHOD__ . " line " . __LINE__  . ": " . self::$DB->error;
			Error::printToLog(ERRLOGFILE, self::$DB->errno, $logMsg);
			return -1;
		}
		$val = $result->fetch_array();
		return $val['max(id)'];
	}
	
	public function lockTable()
	{
		parent::lockTable("Stories");
	}
	
	public function getNextId()
	{
		$query = "show table status like 'Stories'";
		$result = self::$DB->query($query);
		if (!$result)
		{
			$logMsg = __METHOD__ . " line " . __LINE__  . ": " . self::$DB->error;
			Error::printToLog(ERRLOGFILE, self::$DB->errno, $logMsg);
			return -1;
		}
		$info = $result->fetch_array();
		if ($info)
		{
			return $info['Auto_increment'];
		}
		
		return -1;
	}
}
?>
