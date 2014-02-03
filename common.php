<?php
define ('STATUS_XML_OK', 300);
define ('STATUS_NO_STORY_AVAILABLE', 301);
define ('STATUS_RESPONSE_OK', 302);
define ('STATUS_ERROR', 303);
define ('STATUS_ERROR_CREDENTIALS',	307);
define ('STORIES_DIR_PATH', $_SERVER['DOCUMENT_ROOT'] . '/TellATale/stories/');
define ('XML_PREFIX', "<?xml version='1.0' encoding='UTF-8' standalone='yes'?>");

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
		header(':', true, STATUS_ERROR);
	}
	else
	{
		header(':', true, $code);
	}
	
	// close connection to database
	DbConnection::closeDB();
	exit();
}

/**
 * login with username and password
 * return user object on success, null on failure
 */
function logIn($username, $password)
{
	// open connection to 'Users' table in the database
	$userTable = new UserTable();
	if ($userTable->getError() != 0)
	{
		return null;
	}
	// try to login with username and password
	$user = $userTable->logIn($username, $password);
	return $user;
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
