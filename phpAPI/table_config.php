<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Content-Type: application/json; charset=UTF-8");

include_once '/var/www/DBConnection/Crud.php';
include_once '/var/www/DBConnection/sendEmail_fromemail.php';
date_default_timezone_set('Asia/Kolkata');

$backend_users_table = 'zoommeetingdb.backend_users';
$backend_users_login_table = 'zoommeetingdb.backend_users_login';
$backend_users_mail_table = 'zoommeetingdb.backend_users_mails';
?>