<?php
include_once("table_config.php");
$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$crud = new Crud();
if(isset($postdata) && !empty($postdata))
{ 
   $row = array();
  // Validate.
  if(trim($request->user_id) === '' || trim($request->user_input_otp) === '')
  {
    $row['status'] = 0;
	$row['message'] = "Please enter valid OTP.";
  }else{
    $user_id = trim($request->user_id);
	$user_input_otp = trim($request->user_input_otp);
    $sql = "SELECT * FROM $backend_users_table WHERE TRIM(otp)='$user_input_otp' AND id = '$user_id' LIMIT 1";
	$result = $crud->row($sql);
	$userId = isset($result['id']) ? trim($result['id']) : '';
	$userName = isset($result['first_name']) ? trim($result['first_name']) : '';
	$userName .= isset($result['last_name']) ? ' '.trim($result['last_name']) : '';
	$email = isset($result['email']) ? trim($result['email']) : '';
	$phone = isset($result['phone']) ? trim($result['phone']) : '';
	$organization = isset($result['organization']) ? trim($result['organization']) : '';
    if(!empty($userId)) { 

			$row['name'] = $userName;
			$row['email'] = $email;
			$row['id'] = $userId;
			$row['phone'] = $phone;
			$loginUser = array();		
			$loginUser['reg_user_id'] = $userId;
			$loginUser['ipaddress'] = $crud->ip_address();
			$loginUser['logon'] = $crud->current_datetime();
			$loginId = $crud->insert($backend_users_login_table, $loginUser);
			if($loginId > 0){
				$row['status'] = 1;
				$row['message'] = 'You are successfully logged in.';
			}else{
				$row['status'] = 0;
				$row['message'] = "Failed to login, please try again later.";
			}
        
    }else
    {
        $row['status'] = 0;
		$row['message'] = "OTP is invalid. Plesae enter correct OTP.";		
    }
  }
  
  echo json_encode($row);

}
?>