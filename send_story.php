<?php
include_once 'common.php';
include_once 'storyXml.php';
include_once 'classes/InputCheck.php';

define ('REJECT_STORY',	2);
define ('REPLACE_STORY',3);

function getAction()
{
	return $_GET['action'];
}

function getStory()
{
	return $_GET['storyName'];
}

// Script starts here
$username = getUserName();
$password = getPassword();
$facebookId = getFacebookId();
$action = (int)getAction();
if (!InputCheck::validateCredentials($username, $password))
{
	exitError(STATUS_ILLEGAL_INPUT);
}

$user = UserTable::logIn($username, $password, $facebookId);
if (!$user)
{
	exitError(STATUS_ERROR_CREDENTIALS);
}

$userId = $user->getId();

if ($action == REJECT_STORY or $action == REPLACE_STORY)
{
	$storyId = getStoryId(getStory());
	$result = StoryTable::changeStoryStatus($storyId, STORY_AVAILABLE, NO_USER_ID);
	if (!$result)
	{
		exitError();
	}
	
	if ($action == REJECT_STORY)
	{
		StoryHistoryTable::addReject($storyId, $userId);
	}
	
	$storyId = StoryTable::getNextAvailableStory(getStoryId(getStory()), $userId);
}
else
{
	// first, try to get the story that currently belongs to this user
	$storyId = StoryTable::getStoryByUserId($userId);
	
	// if not found, try to find an available story to assign to this user
	if (! $storyId)
	{
		$storyId = StoryTable::getAvailableStory($userId);
	}
}

// if still not found, return with error code
if (!$storyId)
{
	exitError(STATUS_NO_STORY_AVAILABLE);
}

// if a story is available for this user, it will be loaded from an xml file
// and sent to the user in xml format
$storyName = getStoryName($storyId);
$storyFile = getXmlFileName($storyName);
$xmlObj = simplexml_load_file($storyFile);
if (!$xmlObj)
{
	$logMsg = __FILE__ . " line " . __LINE__  . ": " . "simplexml_load_string($storyFile) error";
	Error::printToLog(ERRLOGFILE, -1, $logMsg);
	exitError();
}

// change the story status to unavailable
$result = StoryTable::changeStoryStatus($storyId, STORY_UNAVAILABLE, $userId);
if (!$result)
{
	exitError();
}

exitError(STATUS_XML_OK, $xmlObj->asXml());
?>
