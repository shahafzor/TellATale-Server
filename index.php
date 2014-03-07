<?php
include_once 'common.php';
include_once 'classes/InputCheck.php';


$username = $_GET ['username'];
$password = $_GET ['password'];

// validate user input
if (! InputCheck::validateCredentials ( $username, $password ))
{
	exitError ( STATUS_ILLEGAL_INPUT );
}

// try to login with username and password
$user = UserTable::logIn ( $username, $password );

// close database connection after use
UserTable::closeDB ();

if ($user) // successfull login
{
	setStatus ( STATUS_LOGIN_OK );
	echo $user->getPermission ();
}
elseif ($user === null) // unseccssful login
{
	setStatus ( STATUS_ERROR_CREDENTIALS );
}
else
{
	setStatus ( STATUS_ERROR );
}
?>
