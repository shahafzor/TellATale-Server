<?php
include_once 'local_settings.php';
define ('ERRLOGFILE',	$_SERVER['DOCUMENT_ROOT'] . ROOT_DIR . '/logs/myError.log');

class Error
{
	public static function printToUser($msg)
	{
		echo $msg . " <br> ";
	}
	
	public static function printToLog($destination, $err, $msg)
	{
		$message = date(DATE_ATOM) . " Err#" . $err . ": " . $msg . "\n";
		error_log($message, 3, $destination);
	}
	
	public static function sendErrorMail($mailAddress, $err, $msg)
	{
		$message = date(DATE_ATOM) . " Err#" . $err . ": " . $msg . "\n";
		error_log($message, 1, $mailAddress);
	}	
}
?>
