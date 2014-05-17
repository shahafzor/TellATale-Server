<?php
include_once 'DbConnection.php';
include_once 'User.php';

class UserTable extends DbConnection
{
	public static function getUserId($username, $faceId = 0)
	{
		if (!$username)
		{
			return -1;
		}
		$query = "select user_id from Users where username = '$username' and facebook_id = $faceId";
		$result = self::execute($query);
		if (!$result)
		{
			return -1;
		}
		$row = $result->fetch_array();
		if (!$row)
		{
			return -1;
		}
		return $row['user_id'];
	}

	private static function getUser($query)
	{
		$result = self::execute($query);
		if (!$result)
		{
			return false;
		}
		$row = $result->fetch_array();
		return self::rowToUser($row);
	}

	public static function addUser($user)
	{
		$username = $user->getUsername();
		$password = $user->getPassword();
		$permission = $user->getPermission();
		$faceId = $user->getFacebookId();

		// TODO: add languages
		
		$query = "insert into Users values (null, '$username', '$password', $permission, $faceId)";
		$result = self::execute($query);
		
		if (!self::isConnected())
		{
			return -1;
		}
		return self::getError();
	}
	
	public static function getUserByName($username, $faceId = 0)
	{
		$query = "select * from Users where username = '$username' and facebook_id = $faceId";
		return self::getUser($query);
	}
	
	public static function getRandomUser()
	{
		$query = "SELECT * FROM Users JOIN
			(SELECT (RAND() * 
			(SELECT MAX(user_id) FROM Users)) 
			AS rand_id)
			AS tmp
			WHERE user_id >= rand_id
			limit 1";
		return self::getUser($query);
	}
	
	private static function rowToUser($row)
	{
		if ($row == null)
		{
			return null;
		}
		$username = $row['username'];
		$password = $row['password'];
		$permission = $row['permission'];
		$id = $row['user_id'];
		$faceId = $row['facebook_id'];
		$user = new User($username, $password, $permission, $id);
		$user->setFacebookId($faceId);
		return $user;
		// TODO: add languages
	}
	
	/**
	 * @param string $username
	 * @param string $password
	 * @return false: query failed, null: user was not found or incorrect pasword, User: otherwise
	 */
	public static function logIn($username, $password, $faceId = 0)
	{
		// try to get a specific user from the database by it's name
		$user = self::getUserByName($username, $faceId);

		// user was found and the password is incorrect
		if ($user and $user->getPassword() !== $password)
		{
			return null;
		}
		
		return $user;
	}
}
?>
