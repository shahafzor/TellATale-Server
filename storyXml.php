<?php
function incDislikes($xmlFile, $seqNumber)
{
	foreach ($xmlFile->story_segment as $segment)
	{
		if ((int)$segment->seq_number == (int)$seqNumber)
		{
			$segment->dislike++;
		}
	}
}

function drop($xmlFile, $seqNumber, $version)
{
	foreach ($xmlFile->story_segment as $segment)
	{
		if ((int)$segment->seq_number == ((int)$seqNumber - 1))
		{
			if ((int)$segment->version != (int)$version)
			{
				$segment->addChild('dropped');
			}
		}
	}
}

function addSegment($xmlFile, $xmlObj)
{
	$storySegment = $xmlFile->addChild('story_segment', '');
	$storySegment->addChild('seq_number', $xmlObj->story_segment->seq_number);
	$storySegment->addChild('version', $xmlObj->story_segment->version);
	$storySegment->addChild('text', $xmlObj->story_segment->text);
	$storySegment->addChild('user_name', $xmlObj->story_segment->user_name);
	$storySegment->addChild('dislike', 0);
}

function saveXmlFile($xmlObj, $storyName)
{
	$result = $xmlObj->asXML(STORIES_DIR_PATH . "$storyName.xml");
	if (!$result)
	{
		$logMsg = __FILE__ . " line " . __LINE__  . ": " . "xmlObj->asXML(" . STORIES_DIR_PATH . "'$name.xml') error";
		Error::printToLog(ERRLOGFILE, -1, $logMsg);
	}
	return $result;
}

function removeFile($storyName)
{
	$result = unlink(STORIES_DIR_PATH . "$storyName.xml");
	if (!$result)
	{
		$logMsg = __FILE__ . " line " . __LINE__  . ": " . "unlink($filename) error";
		Error::printToLog(ERRLOGFILE, -1, $logMsg);
	}
	return $result;
}

function getXmlFileName($storyName)
{
	return STORIES_DIR_PATH . "$storyName.xml";
}
?>
