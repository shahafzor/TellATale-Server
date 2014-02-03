<?php
class StorySegment
{
	private $SeqNumber;
	private $Username;
	private $Date;
	private $Text;
	
	
	function __construct($seqNumber, $username, $text)
	{
		$this->SeqNumber = $seqNumber;
		$this->Username = $username;
		$this->Text = $text;
		$this->Date = date(DateTime::ATOM );
	}
	
	/*function addStorySegmentToDB($dblink, $storyName)
	{
		$seqNumber = $this->getSeqNumber();
		$username = $this->getUsername();
		$date = $this->getDate();
		$text = $this->getText();
		
		
		$query = "insert into $storyName values ($seqNumber, '$username', '$date', '$text')";
		$result = $dblink->query($query);
		if (!$result)
		{
			$logMsg = __METHOD__ . " line " . __LINE__  . ": " . $dblink->error;
			$error = new Error($dblink->errno, $logMsg, "Please try later");
			$error->printToLog(ERRLOGFILE);
		}
		return $dblink->errno;
	}*/
	
	function convertFromArray($storySegmentArray)
	{
		$this->SeqNumber = $storySegmentArray['seqNumber'];
		$this->Username = $storySegmentArray['username'];
		$this->Text = $storySegmentArray['text'];
		$this->Date = $storySegmentArray['date'];
	}
	
	function printXml()
	{
		$string = "<?xml version='1.0'?> <story_segment> </story_segment>";
		$xmlObj = simplexml_load_string($string);
		
		$xmlObj->addChild('$seqNumber', $this->SeqNumber);
		$xmlObj->addChild('username', $this->Username);
		$xmlObj->addChild('text', $this->Text);
		$xmlObj->addChild('date', $this->Date);
		echo "<BR>" . $xmlObj->asXML() . "<BR>";
		print_r($xmlObj);
	}
}
?>
