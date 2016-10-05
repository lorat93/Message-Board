<?php
// Add your posting code here.
// 
// To send a user to a different page (after possibly executing some code,
// you can use the statement:
//
//     header('Location: view.php');
//
// This will send the user tp view.php. To use this mechanism, the
// statement must be executed before any of the document is output.

      // start session
      session_start();
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
      
      // username and message
      $myusername=$_SESSION['username'];
      $message=htmlspecialchars($_POST['TEXT'], ENT_QUOTES);
      // format
      $date= date("Y-m-d H:i:s");
      
      // limit to 140
      if(strlen($message) < 140 && strlen($message) > 0) {
         if ( $_POST["csrf"] == $_SESSION["token"]) {
            pg_prepare($dbconn, "insert", "INSERT INTO messages (date,username,message) VALUES ($1,$2,$3)");
            pg_execute($dbconn, "insert", array($date,$myusername,$message)); 
         }  
         else {
              header('Location:login.php');
         }         
      }
      else {
           echo "message does not fit required length 1-140 char";
      }
      
      
      header('Location: view.php');

?>
