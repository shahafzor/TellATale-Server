<?php
include_once 'local_settings.php';

define ('STATUS_XML_OK',				1);
define ('STATUS_NO_STORY_AVAILABLE',	2);
define ('STATUS_RESPONSE_OK', 			3);
define ('STATUS_ERROR', 				4);
define ('STATUS_ERROR_CREDENTIALS',		5);
define ('STATUS_ILEGAL_SEGMENT',		6);
define ('STATUS_LOGIN_OK', 				7);
define ('STATUS_ILLEGAL_INPUT', 		8);
define ('STATUS_DUPLICATE_USER', 		9);
define ('STATUS_NO_PERMISSION', 		10);

define ('PERMISION_BASIC',			0);
define ('PERMISION_BEGIN_STORY',	1);
define ('PERMISION_ADMIN',			2);


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

function exitError($code, $output)
{
	if (!isset($code))
	{
		$code = STATUS_ERROR;
	}
	
	// close connection to database
	DbConnection::closeDB();
	
	// set 'status_code' header
	setStatus($code);
	
	if (isset($output))
	{
		echo $output;
	}
	
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

function addUser($user)
{
	$status = UserTable::addUser($user);
	switch ($status)
	{
		case 0:	// Success
			return STATUS_LOGIN_OK;
		case DUPLICATE_ENTRY:	// Duplicate user name
			return STATUS_DUPLICATE_USER;
		case -1:	// db connection error
			return STATUS_ERROR;
		default:	// Other error
			Error::printToLog(ERRLOGFILE, $status, __METHOD__ . ": " . UserTable::getErrorMsg());
			return  STATUS_ERROR;
	}
}
?>
