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

function getFacebookId()
{
	return $_GET['facebookId'] ? $_GET['facebookId'] : 0;
}

// Script starts here
$username = getUserName();
$password = getPassword();
$facebookId = getFacebookId();
if (!InputCheck::validateCredentials($username, $password))
{
	exitError();
}

$user = UserTable::logIn($username, $password, $facebookId);
if (!$user)
{
	exitError(STATUS_ERROR_CREDENTIALS);
}

$userId = $user->getId();
$stories = StoryHistoryTable::getMyStories($userId);
if ($stories === false)
{
	$logMsg = __FILE__ . " line " . __LINE__  . ": " . "getMyStories($userId) failed";
	Error::printToLog(ERRLOGFILE, -1, $logMsg);
	exitError();
}

if ($stories === null)
{
	exitError(STATUS_NO_STORY_AVAILABLE);
}

$xmlFile = simplexml_load_string(XML_PREFIX . "<stories></stories>");
foreach ($stories as $story)
{
	$storyName = getStoryName($story);
	$storyFile = getXmlFileName($storyName);
	$xmlObj = simplexml_load_file($storyFile);
	if (!$xmlObj)
	{
		$logMsg = __FILE__ . " line " . __LINE__  . ": " . "simplexml_load_file($storyFile) error";
		Error::printToLog(ERRLOGFILE, -1, $logMsg);
		continue;
	}

	addStory($xmlFile, $xmlObj);
}

exitError(STATUS_XML_OK, $xmlFile->asXml());
?>
