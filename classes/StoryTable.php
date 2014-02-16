<?php
include_once 'DbConnection.php';

define ('STORY_AVAILABLE',	0);
define ('STORY_UNAVAILABLE',1);
define ('REJECT_STORY',	2);
define ('REPLACE_STORY',3);

class StoryTable extends DbConnection
{
	private static function getStory($query)
	{
		$result = self::execute($query);
		if (!$result)
		{
			return null;
		}
		$story = $result->fetch_array();
		if ($story)
		{
			return $story['id'];
		}
		return null;
	}
	
	public static function getAvailableStory($userId)
	{
		$query = "select id from Stories where status=" . STORY_AVAILABLE . " and user!=$userId";
		return self::getStory($query);
	}
	
	public static function getNextAvailableStory($storyId, $userId)
	{
		$query = "select id from (select id from Stories where
				id > $storyId and
				status = " . STORY_AVAILABLE . " and 
				user != $userId
				order by id asc) as t1
				union
				select id from (select id from Stories where
				id <= $storyId and
				status = " . STORY_AVAILABLE . " and 
				user != $userId
				order by id asc) as t2";
		return self::getStory($query);
	}
	
	public static function getStoryByUserId($userId)
	{
		$query = "select id from Stories where status=" . STORY_UNAVAILABLE . " and user=$userId";
		return self::getStory($query);
	}
	
	public static function changeStoryStatus($storyId, $newStatus, $userId)
	{
		$query = "UPDATE Stories SET status=$newStatus, user=$userId WHERE id=$storyId";
		$result = self::execute($query);
		return $result;
	}
	
	public static function addStoryToDb($userId)
	{
		$query = "insert into Stories values (null, 0, $userId)";
		$result = self::execute($query);
		return $result;
	}

	public static function getLastStoryId()
	{
		$query = "select max(id) from Stories";
		$result = self::execute($query);
		if (!$result)
		{
			return -1;
		}
		$val = $result->fetch_array();
		return $val['max(id)'];
	}
	
	public static function lockTable()
	{
		return parent::lockTable("Stories");
	}
	
	public static function getNextId()
	{
		$query = "show table status like 'Stories'";
		$result = self::execute($query);
		if (!$result)
		{
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
