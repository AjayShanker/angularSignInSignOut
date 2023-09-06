<?php
include_once("table_config.php");
$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$crud = new Crud();
$row = array();
if(isset($postdata) && !empty($postdata))
{ 
   
  // Validate.
  if(trim($request->email) === '' || trim($request->user_id) === '' )
  {
    $row['status'] = 0;
	$row['message'] = "Please upload file.";
  }else{
	  
	
		$userId = $request->user_id; 
		$email = $request->email; 
		$file = $request->file; 
		$sql = "SELECT * FROM $backend_users_table WHERE TRIM(LOWER(email))='$email' AND id = '$userId' LIMIT 1";
		$result = $crud->row($sql);
		$userId = isset($result['id']) ? trim($result['id']) : '';
		if(empty($userId)) { 
			$row['status'] = 0;
			$row['message'] = "Invalid user upload profile pic.";	
		}else
		{
			$updated = $crud->update("UPDATE $backend_users_table SET picture='$file' WHERE id='$userId'");
			if ($updated>0) {
				 $row['status'] = 1; 
				 $row['message'] = 'Profile picture submitted successfully!';
			}	
		}
	
        
    }
  }
  echo json_encode($row);
?>