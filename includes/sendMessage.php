<?php
      if(isset($_POST['submit'])){
        $name = htmlspecialchars(stripslashes(trim($_POST['name'])));
        $subject = htmlspecialchars(stripslashes(trim($_POST['subject'])));
        $email = htmlspecialchars(stripslashes(trim($_POST['eMail'])));
        $message = htmlspecialchars(stripslashes(trim($_POST['message'])));
        if(!preg_match("/^[A-Za-z .'-]+$/", $name)){
          $name_error = 'Invalid name';
        }
        if(!preg_match("/^[A-Za-z .'-]+$/", $subject)){
          $subject_error = 'Invalid subject';
        }
        if(!preg_match("/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/", $email)){
          $email_error = 'Invalid email';
        }
        if(strlen($message) === 0){
          $message_error = 'Your message should not be empty';
        }
      }
  
	
	if(isset($_POST['submit']) && !isset($name_error) && !isset($subject_error) && !isset($email_error) && !isset($message_error)){
          $to = 'sales@CornishWoodware.co.uk'; // edit here
          $body = "Name: ".$name."\nE-mail: ".$email."\n\nMessage:\n".$message."\n\nSent: ".date("F j, Y, g:i a");
		  $headers = "From: website@CornishWoodware.co.uk";
          if(mail($to, $subject, $body, $headers)){
            header("Location: /mailSentPage.php");
			die();
          }else{
            header("Location: /mailSentPage.php?error=mail");
			die();
          }
        }else{
			header("Location: /mailSentPage.php?error=form");
			die();
		}
		
?>