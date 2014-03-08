<?php
include_once 'common.php';
include_once 'classes/InputCheck.php';

function addUser($user)
{
	$status = UserTable::addUser($user);
	switch ($status)
	{
		case 0:	// Success
			setStatus(STATUS_LOGIN_OK);
			break;
		case DUPLICATE_ENTRY:	// Duplicate user name
			setStatus(STATUS_DUPLICATE_USER);
			break;
		case -1:	// db connection error
			setStatus(STATUS_ERROR);
			break;
		default:	// Other error
			setStatus(STATUS_ERROR);
			Error::printToLog(ERRLOGFILE, $status, __METHOD__ . ": " . UserTable::getErrorMsg());
			break;
	}
	// close database connection after use
	UserTable::closeDB();
	
	return $status;
}

// Script starts here
$username = $_GET['username'];
$password = $_GET['password'];

// check input
if (!InputCheck::validateCredentials($username, $password))
{
	exitError(STATUS_ILLEGAL_INPUT);
}

// create a user object
$user = new User($username, $password, PERMISION_BASIC);

// add the user to the database
addUser($user);
?>
