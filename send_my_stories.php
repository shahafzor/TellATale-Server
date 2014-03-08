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

// Script starts here
$username = getUserName($xmlObj);
$password = getPassword($xmlObj);
if (!InputCheck::validateCredentials($username, $password))
{
	exitError();
}

$user = UserTable::logIn($username, $password);
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
		exitError();
	}

	addStory($xmlFile, $xmlObj);
}

setStatus(STATUS_XML_OK);

// output the story xml file
echo $xmlFile->asXml();

// close connection to database
DbConnection::closeDB();
?>
