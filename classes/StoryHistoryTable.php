<?php
include_once 'DbConnection.php';

define ('STORY_REJECTED',	2);
define ('STORY_REPLACED',	3);
define ('STORY_CONTRIBUTED',4);

class StoryHistoryTable extends DbConnection
{
	public static function addContribute($storyId, $userId)
	{
		$query = "insert into story_history values ('$storyId', '$userId', " . STORY_CONTRIBUTED . ")";
		return self::execute($query);
	}
	
	public static function addReject($storyId, $userId)
	{
		$query = "insert into story_history values ('$storyId', '$userId', " . STORY_REJECTED . ")";
		return self::execute($query);
	}
}
?>
