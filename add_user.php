<?php
include_once 'common.php';
include_once 'classes/InputCheck.php';

function addUser($user)
{
	$status = UserTable::addUser($user);
	switch ($status)
	{
		case 0:	// Success
			return STATUS_LOGIN_OK;
		case DUPLICATE_ENTRY:	// Duplicate user name
			return STATUS_DUPLICATE_USER;
		case -1:	// db connection error
			return STATUS_ERROR;
		default:	// Other error
			Error::printToLog(ERRLOGFILE, $status, __METHOD__ . ": " . UserTable::getErrorMsg());
			return  STATUS_ERROR;
	}
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
$status = addUser($user);

exitError($status);
?>
