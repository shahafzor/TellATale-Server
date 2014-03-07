<?php
include_once 'local_settings.php';

define ('STATUS_XML_OK', 1);
define ('STATUS_NO_STORY_AVAILABLE', 2);
define ('STATUS_RESPONSE_OK', 3);
define ('STATUS_ERROR', 4);
define ('STATUS_ERROR_CREDENTIALS',	5);
define ('STATUS_ILEGAL_SEGMENT',	6);
define ('STATUS_LOGIN_OK', 7);
define ('STATUS_ILLEGAL_INPUT', 8);
define ('STATUS_DUPLICATE_USER', 9);

define ('STORIES_DIR_PATH', $_SERVER['DOCUMENT_ROOT'] . ROOT_DIR . 'stories/');
define ('XML_PREFIX', "<?xml version='1.0' encoding='UTF-8' standalone='yes'?>");
define ('NO_USER_ID',	0);

include_once 'classes/StoryTable.php';
include_once 'classes/UserTable.php';

function exitErrorUnlockTables($code)
{
	// unlock tables
	DbConnection::unlockTables();
	exitError($code);
}

function exitError($code)
{
	if (!isset($code))
	{
		setStatus(STATUS_ERROR);
	}
	else
	{
		setStatus($code);
	}
	
	// close connection to database
	DbConnection::closeDB();
	exit();
}

function setStatus($code)
{
	header('status_code: ' . $code);
}

function getStoryName($storyId)
{
	return "story_$storyId";
}

function getStoryId($storyName)
{
	return ltrim($storyName, "story_");
}
?>
