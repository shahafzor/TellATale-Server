<?php
/*
 * This script handles the reception of a story segment from users. Each time a user adds a segment
 * to an existing story or creates a new story, the segment is posted to this script as an xml file.
 * First the script determines if it is a segment of an existing story or the first segment of a new story.
 * If it is a new story a new entry in the 'stories' table in the database is added, and the new story is
 * saved in a new xml file.
 * If it's a segment of an existing story, it is added to the story's xml file, and changes this story's
 * status in the 'stories' table from 'unavailable (1)' to 'available (0)'.
 */
include_once 'common.php';
include_once 'storyXml.php';
include_once 'classes/InputCheck.php';

// Script starts here:
// load the received story segment xml file as an xml object
$postdata = file_get_contents("php://input");
$xmlObj = simplexml_load_string($postdata);
if (!$xmlObj)
{
	$logMsg = __FILE__ . " line " . __LINE__  . ": " . "simplexml_load_string({$postdata}) error";
	Error::printToLog(ERRLOGFILE, -1, $logMsg);
	exitError();
}

$username = getUserName();
$password = getPassword();
$faceId = getFacebookId();
if (!InputCheck::validateCredentials($username, $password))
{
	exitError();
}

$user = UserTable::logIn($username, $password, $faceId);
if (!$user)
{
	exitError(STATUS_ERROR_CREDENTIALS);
}

// retrieve the story's name from the story xml object
$name = $xmlObj['name'];

// if a name doesn't exist, it means it is a new story, so we:
// 1. add the new story to the 'story' table in the database
// 2. create an xml file to save the new story
if (!$name)
{
	/** TODO:
	 * - check duplicate story
	 */
	if ($user->getPermission() < PERMISION_BEGIN_STORY)
	{
		exitError(STATUS_NO_PERMISSION);
	}
	
	if (!StoryTable::lockTable())
	{
		exitError();
	}
	
	$storyId = StoryTable::getNextId();
	if ($storyId == -1)
	{
		exitErrorUnlockTables();
	}
	
	$xmlFile = simplexml_load_string(XML_PREFIX . "<story></story>");
	
	$name = getStoryName($storyId);
	$xmlFile['name'] = $name;
	
	// add the received segment to the xml object
	addSegment($xmlFile, $xmlObj);
	
	// save the xml object that represents the story as xml file
	$result = saveXmlFile($xmlFile, $name);
	if (!$result)
	{
		exitErrorUnlockTables();
	}
	
	$result = StoryTable::addStoryToDb($user->getId());
	if (!$result)
	{
		removeFile($name);
		exitErrorUnlockTables();
	}

	StoryTable::unlockTables();
}

// this story segment belongs to an existing story, so we:
// 1. load the xml file of this story
// 2. add the received segment to the story
// 3. save the story with the new segment as xml file
else
{
	// load the xml file of the story that this segment belongs to as an xml object
	$fileName = getXmlFileName($name);
	$xmlFile = simplexml_load_file($fileName);
	if (!$xmlFile)
	{
		$logMsg = __FILE__ . " line " . __LINE__  . ": " . "simplexml_load_file($fileName) error";
		Error::printToLog(ERRLOGFILE, -1, $logMsg);
		exitError();
	}
	
	//TODO
	// ? check the status of the story is STORY_UNAVAILABLE
	// ? check the user is the current owner of the story
	// ? story input validation
	// - test file/database error
	
	// the segment is a new version of the last segment
	if ($xmlObj->story_segment->parallel)
	{
		// check seq==seq and ver==ver+1
		if (!isLegalParallel($xmlObj, $xmlFile))
		{
			exitError(STATUS_ILEGAL_SEGMENT);
		}
		
		incDislikes($xmlFile, $xmlObj->story_segment->seq_number);
	}
	// need to drop all the versions that were not chosen
	else
	{
		// check seq==seq+1
		if (!isLegalSegment($xmlObj, $xmlFile))
		{
			exitError(STATUS_ILEGAL_SEGMENT);
		}
		
		// drop and check ver==some ver
		if (!drop($xmlFile, $xmlObj->story_segment->seq_number, $xmlObj->story_segment->version))
		{
			exitError(STATUS_ILEGAL_SEGMENT);
		}
		
		//if ($xmlObj->story_segment->need_drop){}
	}
		
	
	// add the received segment to the xml object
	addSegment($xmlFile, $xmlObj);
	
	// save the xml object that represents the story as xml file
	$result = saveXmlFile($xmlFile, $name);
	if (!$result)
	{
		exitError();
	}
	
	// change the story's status from 'unavailable' to 'available'.
	$storyId = getStoryId($name);
	$result = StoryTable::changeStoryStatus($storyId, STORY_AVAILABLE, $user->getId());
	if (!$result)
	{
		// TODO: decide what to do on error
		exitError();
	}
}

// TODO: decide when to add and remove contribute
StoryHistoryTable::addContribute($storyId, $user->getId());

exitError(STATUS_RESPONSE_OK);
?>
