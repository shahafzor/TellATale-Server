<?php
include_once 'common.php';
include_once 'storyXml.php';
include_once 'classes/InputCheck.php';

define ('REJECT_STORY',	2);
define ('REPLACE_STORY',3);

function getUserName()
{
	return $_GET['username'];
}

function getPassword()
{
	return $_GET['password'];
}

function getAction()
{
	return $_GET['action'];
}

function getStory()
{
	return $_GET['storyName'];
}

// Script starts here
$username = getUserName($xmlObj);
$password = getPassword($xmlObj);
$action = (int)getAction($xmlObj);
if (!InputCheck::validateCredentials($username, $password))
{
	exitError();
}

$user = logIn($username, $password);
if (!$user)
{
	exitError(STATUS_ERROR_CREDENTIALS);
}

$userId = $user->getId();

if ($action == REJECT_STORY or $action == REPLACE_STORY)
{
	// TODO: make rejected story unavailable forever
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

setStatus(STATUS_XML_OK);

// output the story xml file
echo $xmlObj->asXml();

// close connection to database
DbConnection::closeDB();
?>
