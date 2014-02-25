<?php
include_once 'DbConnection.php';
include_once 'StoryHistoryTable.php';

define ('STORY_AVAILABLE',	0);
define ('STORY_UNAVAILABLE',1);

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
		$query = "select * from
				(select id from Stories where status=" . STORY_AVAILABLE . " and user!=$userId) as t1
				left join
				(select * from story_history where user_id=$userId and relation=" . STORY_REJECTED . ") as t2
				on t1.id=t2.story_id
				where (t2.story_id is NULL)";
		return self::getStory($query);
	}
	
	public static function getNextAvailableStory($storyId, $userId)
	{
		$query = "select * from
				(select id from (select id from Stories where
				id > $storyId and
				status = " . STORY_AVAILABLE . " and 
				user != $userId
				order by id asc) as t1
				union
				select id from (select id from Stories where
				id <= $storyId and
				status = " . STORY_AVAILABLE . " and 
				user != $userId
				order by id asc) as t2) as t3
				left join
				(select * from story_history where user_id=$userId and relation=" . STORY_REJECTED . ") as t4
				on t3.id=t4.story_id
				where (t4.story_id is NULL)";
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
