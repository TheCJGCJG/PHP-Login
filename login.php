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
    private $_connection;
    private $_last_query;
    private $_info;

    public function __construct($server, $user, $pass, $database, $port, $prefix)
    {
        $this->_info = array(
            'Server' => $server,
            'Username' => $user,
            'Password' => $pass,
            'Database' => $database,
            'Port'     => $port,
            'Prefix'   => $prefix
        );

        $this->_connection = new mysqli(
            $this->_info['Server'],
            $this->_info['Username'],
            $this->_info['Password'],
            $this->_info['Database'],
            $this->_info['Port']
        );

        if ($this->_connection->connect_error)
        {
            exit('Error connecting to MySQL: ' . $this->_connection->connect_error);
        }

        return $this->_info;
    }

    public function Connection()
    {
        return $this->_connection;
    }

    public function sqlQuery($sql)
    {
        $this->_last_query = $sql;
        $result = $this->Connection()->query($sql);
        return $result;
    }


    public function hashPassword($password)
    {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        return $hashed;
    }

    public function createAccount($username, $password)
    {
        $username = $this->Connection()->real_escape_string($username);
        $password = $this->Connection()->real_escape_string($this->hashPassword($password));


        $userCheck = $this->retrieveUser($username, TRUE);
        if ($userCheck['status'] == TRUE)
        {
            return FALSE;
        }

        $result = $this->sqlQuery("INSERT INTO " . $this->_info['Prefix'] . "accounts (username, password) VALUES ('$username', '$password')");

        if ($result)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    public function retrieveUser($username, $createAccount = NULL)
    {
        if ($createAccount == TRUE)
        {
        $username = $this->Connection()->real_escape_string($username);
        $query = "SELECT * FROM " . $this->_info['Prefix'] . "accounts WHERE username= '$username';";
        $result = $this->sqlQuery($query);
        $userinfo = array();
        if ($result->num_rows == 0) {
            $userinfo['status'] = false;
          }
            return $userinfo;
        }

            while ($row = $result->fetch_array()) {
                $username = $row['username'];
                $password = $row['password'];
            }

            $userinfo['status'] = true;
            $userinfo['username'] = $username;
            $userinfo['password'] = $password;

        return $userinfo;
    }

    public function checkPassword($username, $password)
    {
        $userinfo = $this->retrieveUser($username);
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
    public function deleteAccount($username)
    {
        $userinfo = $this->retrieveUser($username);
        if ($userinfo['status'] == false) {
            return false;
        }
        $username = $this->Connection()->real_escape_string($username);
        $query = "DELETE FROM " . $this->_info['Prefix'] . "accounts WHERE username='$username';";
        if ($result = $this->sqlQuery($query)) {
            return true;
        } else {
            return false;
        }
    }
    public function changePassword($username, $password) {
        $userinfo = $this->retrieveUser($username);
        if ($userinfo['status'] == false) {
            return false;
        }
        $username = $this->Connection()->real_escape_string($username);
        $newPassword = $this->Connection()->real_escape_string($this->hashPassword($password));
        $query = "UPDATE " . $this->_info['Prefix'] . "accounts SET password='$newPassword' WHERE username='$username';";
        echo $query;
        if ($result = $this->sqlQuery($query)) {
            return true;
        } else {
            return false;
        }
    }
}
