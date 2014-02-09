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
	$status = 0;
	foreach ($xmlFile->story_segment as $segment)
	{
		if ((int)$segment->seq_number == ((int)$seqNumber - 1))
		{
			if ((int)$segment->version != (int)$version)
			{
				$segment->addChild('dropped');
			}
			elseif ($status == 0)
			{
				$status = 1;
			}
			else
			{
				$status = 0;
			}
		}
	}
	return $status;
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
		$logMsg = __FILE__ . " line " . __LINE__  . ": " . "xmlObj->asXML(" . STORIES_DIR_PATH . "'$storyName.xml') error";
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

function getLastSeqNum($xmlFile)
{
	return (int)$xmlFile->story_segment[count($xmlFile) - 1]->seq_number;
}

function getNextSeqNum($xmlFile)
{
	return getLastSeqNum($xmlFile) + 1;
}

function getLastVersion($xmlFile)
{
	return (int)$xmlFile->story_segment[count($xmlFile) - 1]->version;
}

function getNextVersion($xmlFile)
{
	return getLastVersion($xmlFile) + 1;
}

function isLegalParallel($xmlObj, $xmlFile)
{
	return ((int)$xmlObj->story_segment->seq_number == getLastSeqNum($xmlFile) and
			(int)$xmlObj->story_segment->version == getNextVersion($xmlFile));
}

function isLegalSegment($xmlObj, $xmlFile)
{
	return ((int)$xmlObj->story_segment->seq_number == getNextSeqNum($xmlFile));
}
?>
