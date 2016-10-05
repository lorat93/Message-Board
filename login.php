<?php

// The login.php is invoked when the user is either trying to create a new
// account or to login. If it's the former, the NEW parameter will be set.
// To send a user to a different page (after possibly executing some code,
// you can use the statement:
//
//     header('Location: view.php');
//
// This will send the user tp view.php. To use this mechanism, the
// statement must be executed before any of the document is output.

      $host="localhost";
      $username="student";
      $password="hacktheplanet";
      $dbname="chattr";
      $tbname="members"; 

      // Connecting, selecting database
      $dbconn = pg_connect("host=localhost dbname=chattr user=student password=hacktheplanet");
      if(!$dbconn) {
        echo "fail to connec to db";
      }
      
      // username and password from input
      $myusername=htmlspecialchars($_POST['USER'], ENT_QUOTES);
      $mypassword=htmlspecialchars($_POST['PASS'], ENT_QUOTES);
      
      // hash and salt
function generateHash($password) {
    if (defined("CRYPT_BLOWFISH") && CRYPT_BLOWFISH) {
        $salt = '$2y$11$' . substr(md5(uniqid(rand(), true)), 0, 22);
        return crypt($password, $salt);
    }
}
function verify($password, $hashedPassword) {
    return crypt($password, $hashedPassword) == $hashedPassword;
}           
 
if(isset($_POST['NEW'])) {
	// Your new user creation code goes here. If the user name
	// already exists, then display an error. Otherwise, create a new
	// user account and send him to view.php.

      // Performing SQL query for new user
      $query = "SELECT * FROM $tbname WHERE username=$1";
      $result = pg_query_params($dbconn, $query, array($myusername)); 
      $count=pg_num_rows($result);

    // Check if usename doesn't exists then insert into table 
       if($count == 0) {
         if(strlen($myusername) < 21 && strlen($myusername) > 0) {
           if(strlen($mypassword) < 21 && strlen($mypassword) > 0) {
           
             $hashed_pass = generateHash($mypassword);             
             // insert into table
             $insert = pg_prepare($dbconn, "insert", "INSERT INTO members (username, password) VALUES ($1,$2)");
             $insert = pg_execute($dbconn,"insert", array($myusername,$hashed_pass));
             
             // start session
             if (!isset($_SESSION)) {
                session_start();
             $_SESSION['loggedin'] = true;
             $_SESSION['username'] = $myusername;
             $myusername = $_SESSION['username'];
             }
             if (!isset($_SESSION['token'])){
                $token = md5(uniqid(rand(), TRUE));
                $_SESSION['token'] = $token;
                }          
             else{
                  $token = $_SESSION['token'];
             }      
             header('Location: view.php');
                          
                                                                                                                                  
           }
           else {
             // error message
             $errmsg = "password does not fit required length 1-20 char";
           }
         }
         else {
         // error message
           $errmsg = "username does not fit required length 1-20 char";
         }
       }
       else {
       // error message
         $errmsg = "User $myusername already exists!";
       }
       
} else {
	// Your user login code goes here. If the user name and password
	// are not correct, then display an error. Otherwise, log in the
	// user and send him to view.php.
 
      // Performing SQL query for existing user      
      $query2 = "SELECT * FROM $tbname WHERE username=$1";
      $query3 = pg_query_params($dbconn, $query2, array($myusername)); 
      $count2=pg_num_rows($query3);
      $password = pg_prepare($dbconn, "pass", "SELECT password FROM $tbname WHERE username=$1");
      $result2 = pg_execute($dbconn,"pass", array($myusername));
      $row = pg_fetch_row($result2);

     // If result matched $myusername and $mypassword, table row must be 1 row
     if($count2 == 1 && verify($mypassword, $row[0])){
       
       // start session and logged in

             if (!isset($_SESSION)) {
                session_start();
             $_SESSION['loggedin'] = true;
             $_SESSION['username'] = $myusername;
             $myusername = $_SESSION['username'];
             }
             if (!isset($_SESSION['token'])){
                $token = md5(uniqid(rand(), TRUE));
                $_SESSION['token'] = $token;
                
                }          
             else{
                  $token = $_SESSION['token'];
             }      
       header('Location:view.php');
       
     }
     else {
       // start session but not logged in
       session_start();
       $_SESSION['logged-in'] = false;
       $errmsg = "Login Failed!";
     }     
     
}
?>
<DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 3.2//EN">
<HEAD>
    <TITLE>Chattr</TITLE>
</HEAD>
<BODY BGCOLOR=WHITE>
<TABLE ALIGN="CENTER">
<TR><TD>
<H1>Chattr</H1>
</TD></TR>
<TR><TD>
<H2><?php echo "$errmsg" ?></H2>
<a href="index.php">Back</a>
</TD></TR>
</TABLE>
</BODY>
