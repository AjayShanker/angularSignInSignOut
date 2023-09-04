<?php
include_once("table_config.php");
$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$crud = new Crud();
if(isset($postdata) && !empty($postdata))
{ 
   $row = array();
  // Validate.
  if(trim($request->email) === '' || trim($request->password) === '')
  {
    $row['status'] = 0;
	$row['message'] = "Please enter valid email and password.";
  }else{
    $email = trim(strtolower($request->email));
	$password = trim($request->password);
    $sql = "SELECT * FROM $backend_users_table WHERE TRIM(LOWER(email))='$email' LIMIT 1";
	$result = $crud->row($sql);
	$userId = isset($result['id']) ? trim($result['id']) : '';
	$userRoleId = isset($result['role_id']) ? trim($result['role_id']) : '';
	$userPassword = isset($result['password']) ? trim($result['password']) : '';
	$first_name = isset($result['first_name']) ? trim($result['first_name']) : '';
	$last_name = isset($result['last_name']) ? trim($result['last_name']) : '';
	$userPicture = isset($result['picture']) ? trim($result['picture']) : 'assets/images/avatars/user.png';
    if(!empty($userId) && !empty($userRoleId)) { 

		if($userPassword==$password && $userRoleId == 1)
		{
			$row['name'] = $first_name . ' ' . $last_name;
			$row['email'] = $email;
			$row['id'] = $userId;
			$row['picture'] = $userPicture;
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
		}
		else if($userRoleId > 1)
		{
			$row['status'] = 0;
			$row['message'] = "You role is not Admin, You not allowed to use support portal.";
		}
		else
		{
			$row['status'] = 0;
			$row['message'] = "Incorrect Password.";
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