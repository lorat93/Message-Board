<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 3.2//EN">
<HEAD>
    <TITLE>Chattr</TITLE>
</HEAD>
<BODY BGCOLOR=WHITE>
<TABLE ALIGN="CENTER">
<TR><TD>
<H1>Chattr</H1>
</TD></TR>

<?php
	// The following <TR> element should only appear if the user is
	// logged in and viewing his own entry.
 
      // start session
      session_start(); 
      $host="localhost";
      $username="student";
      $password="hacktheplanet";
      $dbname="chattr";
      $tbname="members"; 
      // if url contains string
      $urlcontain = explode("=", htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));
      // username on url
      $userpage = $urlcontain[1];
      // user on url
      $otheruser = $urlcontain[0];
      
      // Connecting, selecting database
      $dbconn = pg_connect("host=localhost dbname=chattr user=student password=hacktheplanet");
      if(!$dbconn) {
        echo "fail to connec to db";
      }
      
      // username and password from input
      $myusername=$_SESSION['username'];

     // url
     $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
     // if viewing own page
     $view_own_page = (!$otheruser || $userpage == $myusername);
     // if logged in
     $is_logged_in = (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true);

 
?>

<?php if ($is_logged_in && $view_own_page): ?> 
    <TR><TD>
    <FORM ACTION="post.php" METHOD="POST">
<input type="hidden" name="csrf" value="<?php echo  $_SESSION['token'];  ?>" />    
    <TABLE CELLPADDING=5>
    <TR><TD>Message:</TD><TD><INPUT TYPE="TEXT" NAME="TEXT">
    <INPUT TYPE="SUBMIT" VALUE="Submit"></TD></TR>
    </TABLE>
    </FORM>
    </TD></TR>
<?php endif ?>
 
<?php
	// The following <TR> element should always appear if the user
	// exists.
     if($otheruser) {
       $myusername = $userpage;
     }
     // query table to keyword          
     $user = "SELECT * FROM $tbname WHERE username=$1";
     $userExists = pg_query_params($dbconn, $user, array($userpage));
     $count2=pg_num_rows($userExists);    
     // messages 
     $showmsg = pg_query($dbconn, "SELECT date,username,message FROM messages where username='$myusername'");

?>
<?php if ($count2 == 1 || ($view_own_page && $is_logged_in)): ?>

    <TR><TD>
    <TABLE CELLPADDING=5>
    <TR><TH>When</TH><TH>Who</TH><TH>What</TH></TR>
	<?php
		// Display user's posts here. The structure is:
	
		     // display message
              while($row = pg_fetch_row($showmsg)) {
              echo "
              <TR>
                    <TD>$row[0]</TD>
                    <TD>$row[1]</TD>
                    <TD>$row[2]</TD>
              </TR> ";
              }

    ?>
    </TABLE>
    </TD></TR> 
      
<?php endif ?>
<?php
	// The following <TR> element should be displayed if the user
	// name does not exist. Add code to display user name.
?>


<?php if ((!$is_logged_in && $view_own_page) || ($otheruser && $count2 == 0)): ?>
    <TR><TD>
    <H2>User <?php echo "$userpage" ?> does not exist!</H2>
    </TD></TR>
<?php endif ?>
<?php
	// The following <TR> element should only be shown if the user
	// is logged in.
?>


<?php if ($is_logged_in && ($view_own_page || $count2 == 1)): ?> 
<TR><TD><A HREF="logout.php">Logout</A></TR></TD>
<?php endif ?>
<?php
	// Done!
?>
</TABLE>
</BODY>

