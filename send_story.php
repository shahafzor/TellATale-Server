<?php
include_once 'common.php';
include_once 'storyXml.php';
include_once 'classes/InputCheck.php';

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

$storyTable = new StoryTable();
if ($storyTable->getError() != 0)
{
	exitError();
}

$userId = $user->getId();

if ($action == STORY_REJECTED or $action == STORY_REPLACED)
{
	// TODO: make rejected story unavailable forever
	$user = $action == STORY_REJECTED ? $userId : 0;
	$result = $storyTable->changeStoryStatus(getStoryId(getStory()), STORY_AVAILABLE, $user);
	if (!$result)
	{
		exitError();
	}
	
	$storyId = $storyTable->getNextAvailableStory(getStoryId(getStory()), $userId);
}
else
{
	// first, try to get the story that currently belongs to this user
	$storyId = $storyTable->getStoryByUserId($userId);
	
	// if not found, try to find an available story to assign to this user
	if (! $storyId)
	{
		$storyId = $storyTable->getAvailableStory($userId);
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
$result = $storyTable->changeStoryStatus($storyId, STORY_UNAVAILABLE, $userId);
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
