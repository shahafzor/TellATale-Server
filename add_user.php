<html>
<head>
	<title>Add user</title>
</head>
<body>

<?php
define ('BASIC_PERMISION',	0);
include_once 'classes/UserTable.php';
include_once 'classes/InputCheck.php';

function addUser($user)
{
	// open connection to 'Users' table in the database
	$userTable = new UserTable();
	if ($userTable->getError() != 0)
	{
		echo "Oops... Try later";
		return 1;
	}
	
	$status = $userTable->addUser($user);
	switch ($status)
	{
		case 0:	// Success
			$username = $user->getUsername();
			$permission = $user->getPermission();
			$password = $user->getPassword();
			echo "<script> " .
				"Android.login('$username', $permission, '$password');" .
			"</script>";
			break;
		case 1062:	// Duplicate user name
			echo "This username is taken, please try a new username <br> <br>";
			$status = -1;
			break;
		default:	// Other error
			echo "We have a small problem...<br>";
			Error::printToLog(ERRLOGFILE, $status, __METHOD__ . ": " . $dblink->error);
			break;
	}
	// close database connection after use
	$userTable->closeDB();
	
	return $status;
}

// Script starts here
if (isset($_POST['add_user']))
{
	$username = $_POST['username'];
	$password = $_POST['password'];
	
	// check input
	if (InputCheck::validateCredentials($username, $password))
	{
		// create a user object
		$user = new User($username, $password, BASIC_PERMISION);
		
		// add the user to the database
		$status = addUser($user);
		if ($status != -1)
		{
			// exit for success and all errors excpet duplicate entry error
			exit();
		}
	}
}
?>
	<a href="index.php"> LogIn </a> <br> <br>
	
	<form action="add_user.php" method="post">
		username:
		<input type="text" name="username" value="<?php echo $username ?>"> <br>
		password:
		<input type="password" name="password"> <br>
		<input type="submit" name="add_user" value="add user">
	</form>

</body>
</html>
