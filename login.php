<?php
/**
* A basic login class - Allows for MySQL Storage of Usernames & Hashed 
* Passwords. Performs basic input validation to prevent against SQL
* Injection, however does no more. The user is expected to protect against
* these types of attacks
*
* LICENSE: This source file is subject to version 3.01 of the PHP license
* that is available through the world-wide-web at the following URI:
* http://www.php.net/license/3_01.txt.  If you did not receive a copy of
* the PHP License and are unable to obtain it through the web, please
* send a note to license@php.net so we can mail you a copy immediately.
*
* @author Charles Gillham <charlesg.github@gmail.com>
*
*/

/*
* mysql_host
* mysql_user
* mysql_password
* mysql_database
* mysql_login_prefix
*/
class login

{
	function init($host, $user, $password, $db, $prefix)
	{
		define("mysql_host", $host);
		define("mysql_user", $user);
		define("mysql_password", $password);
		define("mysql_database", $db);
		define("mysql_login_prefix", $prefix);
		if ($this::testSql() == true) {
			return true;
		}
		else {
			return false;
		}
	}

	static
	function sqlQuery($query)
	{
		$sql = mysqli_connect(mysql_host, mysql_user, mysql_password, mysql_database);
		$result = mysqli_query($sql, $query);
		return $result;
	}

	function testSql()
	{
		if (mysqli_connect_errno()) {
			die("Failed to connect to MySQL: " . mysqli_connect_error());
		}

		if (mysqli_num_rows($this::sqlQuery("SHOW TABLES LIKE '" . mysql_login_prefix . "accounts'")) == 1) {
		}
		else {
			if ($this::sqlQuery("CREATE TABLE `sshkeys`.`" . mysql_login_prefix . "accounts`(`ID` INT NOT NULL AUTO_INCREMENT,`username` VARCHAR(45)NOT NULL,`password` VARCHAR(150)NOT NULL,PRIMARY KEY(`ID`));")) {
			}
			else {
				die("An SQL Error occurred");
			}
		}

		return true;
	}

	static
	function hashPassword($password)
	{
		$hashed = password_hash($password, PASSWORD_DEFAULT);
		return $hashed;
	}

	function createAccount($username, $password)
	{
		$sql = mysqli_connect(mysql_host, mysql_user, mysql_password, mysql_database);
		$username = mysqli_real_escape_string($sql, $username);
		$password = $this::hashPassword($password);
		if ($this::retrieveUser($username)['status'] == true) {
			return false;
		}
		$query = "INSERT INTO " . mysql_login_prefix . "accounts (username, password) VALUES ('" . $username . "', '" . $password . "');";
		if ($this::sqlQuery($query)) {
			return true;
		} else {
			return false;
		}
	}

	function retrieveUser($username)
	{
		$sql = mysqli_connect(mysql_host, mysql_user, mysql_password, mysql_database);
		$username = mysqli_real_escape_string($sql, $username);
		$query = "SELECT * FROM " . mysql_login_prefix . "accounts WHERE username='" . $username . "';";
		$result = $this::sqlQuery($query);
		$userinfo = Array();
		if (mysqli_num_rows($result) == 0) {
			$userinfo['status'] = false;
		}
		else {
			while ($row = mysqli_fetch_array($result)) {
				$username = $row['username'];
				$password = $row['password'];
			}

			$userinfo['status'] = true;
			$userinfo['username'] = $username;
			$userinfo['password'] = $password;
		}

		return $userinfo;
	}

	function checkPassword($username, $password)
	{
		$userinfo = $this::retrieveUser($username);
		if ($userinfo['status'] == false) {
			die('The given username could not be found');
		}

		$check = password_verify($password, $userinfo['password']);
		if ($check) {
			return true;
		}
		else {
			return false;
		}
	}
	function deleteAccount($username)
	{
		$userinfo = $this::retrieveUser($username);
		if ($userinfo['status'] == false) {
			return false;
		}
		$sql = mysqli_connect(mysql_host, mysql_user, mysql_password, mysql_database);
		$username = mysqli_real_escape_string($sql, $username);
		$query = "DELETE FROM " . mysql_login_prefix . "accounts WHERE username='" . $username . "';";
		if ($result = $this::sqlQuery($query)) {
			return true;
		} else {
			return false;
		}
	}
	function changePassword($username, $password) {
		$userinfo = $this::retrieveUser($username);
		if ($userinfo['status'] == false) {
			return false;
		}
		$sql = mysqli_connect(mysql_host, mysql_user, mysql_password, mysql_database);
		$username = mysqli_real_escape_string($sql, $username);
		$newPassword = $this::hashPassword($password);
		$query = "UPDATE " . mysql_login_prefix . "accounts SET password='" . $newPassword . "' WHERE username='" . $username . "';";
		echo $query;
		if ($result = $this::sqlQuery($query)) {
			return true;
		} else {
			return false;
		}
	}
}

?>
