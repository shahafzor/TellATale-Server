<?php
// 685063249
include_once 'common.php';
include_once 'classes/InputCheck.php';

// Script starts here
$username = $_GET['username'];
$password = $_GET['password'];
$faceId = $_GET['password'];

// check input
if (!InputCheck::validateCredentials($username, $password))
{
	exitError(STATUS_ILLEGAL_INPUT);
}

// create a user object
$user = new User($username, $password, PERMISION_BASIC);
$user->setFacebookId($faceId);

// add the user to the database
$status = addUser($user);

if ($status == STATUS_DUPLICATE_USER)
{
	$user = UserTable::logIn($username, $password, $faceId);
	
	if ($user) // successfull login
	{
		exitError(STATUS_LOGIN_OK, $user->getPermission());
	}
	elseif ($user === null) // unseccssful login
	{
		exitError(STATUS_ERROR_CREDENTIALS);
	}
	else
	{
		exitError(STATUS_ERROR);
	}
}

exitError($status);
?>
