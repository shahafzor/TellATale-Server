<?php
include_once 'DbConnection.php';
include_once 'User.php';

class UserTable extends DbConnection
{
	public function __construct()
	{
		parent::__construct();
	}

	public function getUserId($username)
	{
		if (!$username)
		{
			return -1;
		}
		$query = "select user_id from Users where username = '$username'";
		$result = self::$DB->query($query);
		if (!$result)
		{
			$logMsg = __METHOD__ . " line " . __LINE__  . ": " . self::$DB->error;
			Error::printToLog(ERRLOGFILE, self::$DB->errno, $logMsg);
			return -1;
		}
		$row = $result->fetch_array();
		if (!$row)
		{
			return -1;
		}
		return $row['user_id'];
	}

	private function getUser($query)
	{
		$result = self::$DB->query($query);
		if (!$result)
		{
			$logMsg = __METHOD__ . " line " . __LINE__  . ": " . self::$DB->error;
			Error::printToLog(ERRLOGFILE, self::$DB->errno, $logMsg);
			return null;
		}
		$row = $result->fetch_array();
		return $this->rowToUser($row);
	}

	public function addUser($user)
	{
		$username = $user->getUsername();
		$password = $user->getPassword();
		$permission = $user->getPermission();

		// TODO: add languages
		
		$query = "insert into Users values (null, '$username', '$password', $permission)";
		$result = self::$DB->query($query);
		if (!$result and self::$DB->errno != 1062)
		{
			$logMsg = __METHOD__ . " line " . __LINE__  . ": " . self::$DB->error;
			Error::printToLog(ERRLOGFILE, self::$DB->errno, $logMsg);
		}
		return self::$DB->errno;
	}
	
	public function getUserByName($username)
	{
		$query = "select * from Users where username = '$username'";
		return $this->getUser($query);
	}
	
	public function getRandomUser()
	{
		$query = "SELECT * FROM Users JOIN
			(SELECT (RAND() * 
			(SELECT MAX(user_id) FROM Users)) 
			AS rand_id)
			AS tmp
			WHERE user_id >= rand_id
			limit 1";
		print ($query . "<br>");
		return $this->getUser($query);
	}
	
	private function rowToUser($row)
	{
		if ($row == null)
		{
			return null;
		}
		$username = $row['username'];
		$password = $row['password'];
		$permission = $row['permission'];
		$id = $row['user_id'];
		return new User($username, $password, $permission, $id);
		
		// TODO: add languages
	}
	
	public function logIn($username, $password)
	{
		// try to get a specific user from the database by it's name
		$user = $this->getUserByName($username);

		// check if this username exists and the password is correct
		if ($user and $password === $user->getPassword())
		{
			return $user;
		}
		else
		{
			return null;
		}
	}
}
?>
