<?php
include_once("table_config.php");
$postdata = file_get_contents("php://input");

$crud = new Crud;
$mail = new sendEmail();

$row = array();
$randomOTPNumber = rand(100000, 999999);
if(isset($postdata) && !empty($postdata))
{
	$row['status'] = 0;
	$row['message'] = "Some error occured. Please try again after some time...";
    $request = json_decode($postdata);
	$first_name = $crud->escape_string(isset($request->first_name)?trim(ucwords(strtolower($request->first_name))):'');
	$last_name = $crud->escape_string(isset($request->last_name)?trim(ucwords(strtolower($request->last_name))):'');
    $email = $crud->escape_string(isset($request->email)?trim(strtolower($request->email)):'');
    $phone = $crud->escape_string(isset($request->phone)?trim($request->phone):'');
    $organization = $crud->escape_string(isset($request->organization)?trim($request->organization):'');
     
	 $subject = 'OTP for Vouchpro Videocall registration';
     $emailBody = "<meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=no'><div style='font-family: Helvetica,Arial,sans-serif;min-width:600px;overflow:auto;line-height:2;padding-top: 30px;padding-bottom: 10px;background-color: #f4f5f9;'><div style='margin:20px auto;width:70%;padding:20px 60px;background-color: #ffffff;padding-bottom: 37px;'><p style='font-size: 13px;color: #39394d;font-family: Arial;'>Hello,</p><p style='font-size: 13px;color: #39394d;font-family: Arial;line-height:5px;'><strong>$randomOTPNumber</strong> is your OTP to join the https://cdn.vouchpro.tv/videocall/ session. OTP is valid for 5 minutes.</p><br /><hr style='border:none;border-top:1px solid #eee' /></div></div>";
        
	 if(!empty($first_name) && !empty($email) && !empty($phone) && !empty($last_name) && !empty($organization)){

        //Check email exist in database
        $sql = "SELECT * FROM $backend_users_table WHERE TRIM(LOWER(email))='$email' LIMIT 1";
        $result = $crud->row($sql);
        $userId = isset($result['id']) ? trim($result['id']) : '';
        if(!empty($userId)) { 
				$mailsent = $mail->send_email_reply($email,$subject,$emailBody,'otp@vouchpro.online','VouchPro Videocall','donotreply@vouchpro.com','donotreply');	
				$mailStatus = 'sent';
				if($mailsent)
				{
					$mailStatus = 'not sent';
				}
				$emailData = array();
				$emailData['reg_user_id'] = $userId;
				$emailData['email'] = $email;
				$emailData['body'] = $crud->escape_string('Subject: '.$subject.'<br />'.$emailBody);
				$emailData['status'] = $mailStatus;
				$emailData['ipaddress'] = $crud->ip_address();
				$emailData['regon'] = $crud->current_datetime();
				$emailData['reason'] = $crud->escape_string($mailsent);
				$emailId = $crud->insert("$backend_users_mail_table", $emailData);
				
				$updateon = $crud->current_datetime();
				$updateId = $crud->update("UPDATE $backend_users_table SET otp='".$randomOTPNumber."', updated_at='".$updateon."' WHERE id='".$userId."' "); 
				
				$loginData = array();
				$loginData['reg_user_id'] = $userId;
				$loginData['ipaddress'] = $crud->ip_address();
				$loginData['logon'] = $crud->current_datetime();
				//$loginId = $crud->insert("$backend_users_login_table", $loginData);
				
				$row = [
                    'first_name' => (isset($result['first_name']) ? trim($result['first_name']) : ''),
                    'last_name' => (isset($result['last_name']) ? trim($result['last_name']) : ''),
                    'email' => (isset($result['email']) ? trim($result['email']) : ''),
                    'phone' => (isset($result['phone']) ? trim($result['phone']) : ''),
					'organization' => (isset($result['organization']) ? trim($result['organization']) : ''),
                    'Id' => $userId
                    ];
				$row['status'] = 1;
				$row['message'] = "OTP is send through mail. Please verify account.";	
        }else
        {
            //Insert new user record
            $user = array();
            $user['first_name'] = $first_name;
            $user['last_name'] = $last_name;
            $user['email'] = $email;
            $user['phone'] = $phone;
            $user['organization'] = $organization;
			$user['otp'] = $randomOTPNumber;
			$user['is_active'] = 1;
            $user['created_at'] = $crud->current_datetime();
			$user['updated_at'] = $crud->current_datetime();
            $userId = $crud->insert($backend_users_table, $user);
            if ($userId>0) {
				$mailsent = $mail->send_email_reply($email,$subject,$emailBody,'otp@vouchpro.online','VouchPro Videocall','donotreply@vouchpro.com','donotreply');	
				$mailStatus = 'sent';
				if($mailsent)
				{
					$mailStatus = 'not sent';
				}
				$emailData = array();
				$emailData['reg_user_id'] = $userId;
				$emailData['email'] = $email;
				$emailData['body'] = $crud->escape_string('Subject: '.$subject.'<br />'.$emailBody);
				$emailData['status'] = $mailStatus;
				$emailData['ipaddress'] = $crud->ip_address();
				$emailData['regon'] = $crud->current_datetime();
				$emailData['reason'] = $crud->escape_string($mailsent);
				$emailId = $crud->insert("$backend_users_mail_table", $emailData);
				
				$loginData = array();
				$loginData['reg_user_id'] = $userId;
				$loginData['ipaddress'] = $crud->ip_address();
				$loginData['logon'] = $crud->current_datetime();
				//$loginId = $crud->insert("$backend_users_login_table", $loginData);
				
                http_response_code(201);
                $row = [
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $email,
                    'phone' => $phone,
					'organization' => $organization,
                    'Id' => $userId
                    ];
				$row['status'] = 1;
				$row['message'] = "OTP is send through mail. Please verify account.";
            } else {
                $row['status'] = 0;
				$row['message'] = "Some error occured. Please try again after some time...";
            }
        }
        
    }
		echo json_encode($row);
}

?>