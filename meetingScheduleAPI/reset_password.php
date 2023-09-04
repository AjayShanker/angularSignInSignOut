<?php
$rdir = str_replace("\\", "/", __DIR__);   
include_once("table_config.php");
require $rdir.'/sendMailer/index.php';
$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$crud = new Crud();

##Email Required files              
require $rdir.'/sendMailer/vendor/phpmailer/phpmailer/src/Exception.php';
require $rdir.'/sendMailer/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require $rdir.'/sendMailer/vendor/phpmailer/phpmailer/src/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;	
require $rdir.'/sendMailer/vendor/autoload.php';
require $rdir.'/sendMailer/credential.php';
$mail = new PHPMailer(true);


if(isset($postdata) && !empty($postdata))
{ 
   $row = array();
  // Validate.
  if(trim($request->old_password) === '' || trim($request->new_password) === '' || trim($request->token) === '')
  {
    $row['status'] = 0;
	$row['message'] = "Please enter current password and new password.";
  }else{
    $email = base64_decode(trim($request->token)); 
	$old_password = $crud->escape_string(isset($request->old_password)?trim($request->old_password):'');
	$new_password = $crud->escape_string(isset($request->new_password)?trim($request->new_password):'');
    $sql = "SELECT * FROM $backend_users_table WHERE TRIM(LOWER(email))='$email' LIMIT 1";
	$result = $crud->row($sql);
	$userId = isset($result['id']) ? trim($result['id']) : '';
	$userPassword = isset($result['password']) ? trim($result['password']) : '';
	$userRoleId = isset($result['role_id']) ? trim($result['role_id']) : '';
	$insert_id = 0;
    if(!empty($userId) && !empty($userRoleId)) {
		if($userPassword == $old_password && $userRoleId == 1)
		{
			
			$insert_id = $crud->update("UPDATE $backend_users_table SET password='$new_password' WHERE id='$userId'");		
		
			if(!empty($insert_id)){
			
				$subject = 'Successfully updated password for Recruiter App!';
				$message = "<p style='font-size: 13px;color: #39394d;font-family: Arial;line-height: 2em;'>Your password successfully updated. Your new password is : </p><table width='100%' border='0' cellspacing='0' cellpadding='0'><tr><td align='center'><span style='height:30px; width:150px; border-radius:8px;padding:10px;font-size:20px;height:52px;cursor:pointer;background-color:wheat;white-space: nowrap;'>$new_password</span></td></tr></table><p style='font-size: 13px;color: #39394d;font-family: Arial;line-height: 2em;'>Please use this password for login.</p>";
				
				$mailsent = sendEmailToUser($mail, $email, $subject, $message);
				if($mailsent == 1)
				{
					$emailData = array();
					$emailData['reg_user_id'] = $userId;
					$emailData['email'] = $crud->escape_string($email);
					$emailData['body'] = $crud->escape_string('Subject: '.$subject.'<br />'.$message);
					$emailData['status'] = 'sent';
					$emailData['ipaddress'] = $crud->ip_address();
					$emailData['regon'] = $crud->current_datetime();
					$emailData['reason'] = '';
					$emailId = $crud->insert("$backend_users_mail_table", $emailData);
					$row['status'] = 1;
					$row['message'] = 'Successfully changed passord and send a mail with password.';
				}else{
					
					$emailData = array();
					$emailData['reg_user_id'] = $userId;
					$emailData['email'] = $crud->escape_string($email);
					$emailData['body'] = $crud->escape_string('Subject: '.$subject.'<br />'.$message);
					$emailData['status'] = 'not sent';
					$emailData['ipaddress'] = $crud->ip_address();
					$emailData['regon'] = $crud->current_datetime();
					$emailData['reason'] = $crud->escape_string($mailsent);
					$emailId = $crud->insert("$backend_users_mail_table", $emailData);
					$row['status'] = 0;
					$row['message'] = 'Failed to send email. Please try after some time.';			
				}
			}
		}else if($userRoleId > 1)
		{
			$row['status'] = 0;
			$row['message'] = "You role is not Admin, You not allowed to use support portal.";
		}else{
			$row['status'] = 0;
			$row['message'] = 'Your current password do not match. Please type your valid current password.';			
		}
        
    }else
    {
        $row['status'] = 0;
		$row['message'] = "This email address is not registered with us. Please register your account...";		
    }
  }
  
  echo json_encode($row);

}
?>