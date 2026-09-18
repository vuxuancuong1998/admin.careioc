<?php
/**
 * Project: xvn.
 * File: config.php.

 */
 
session_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');
//=============== Custom configuration ==================//
define('DB_NAME', 'care_ioc'); //database name
define('DB_USER', 'root'); //database user
define('DB_PASSWORD', ''); //database password
define('DB_HOST', 'localhost'); //sql server

/*** define mailer ***/
define('MAIL_PROTOCOL', 'SMTP');
define('MAIL_HOST', 'smtp.zoho.com');
define('MAIL_ACC', 'support@mmexpress.vn');
define('MAIL_PASS', '');
define('MAIL_PORT', 587);
define('MAIL_AUTH', true);
define('MAIL_SECURE', 'tls');

/*** define Xiao SMS ***/
define('SMS_API_KEY', 'key-c4a8d21f56a2827fa24a41b3a63dcbb7');
define('SMS_API_SECRECT', 'C09E8A7C0D6A47BA117A3964A94EB8');


/*** define Theme ***/

define('ThemeMaster', 'admin'); //Giao diện điều hướng bởi indexController
define('dashboard', 'dashboard');
define('backend', 'backend'); //Replace xpanel by your admin theme's name
define('admin', 'admin'); //Replace xpanel by your admin theme's name

/*** define site path ***/
define('XC_URL','http://localhost/admin.careioc');
$siteurl = XC_URL;
/*** template path ***/
$template_path = XC_URL.'/template/'.ThemeMaster; //Warning: Don't change here
$backend_path = XC_URL.'/template/'.backend; //Warning: Don't change here
$admin_path = XC_URL.'/template/'.admin; //Warning: Don't change here
$upload_path = XC_URL.'/uploads';
$image_path = XC_URL.'/uploads/images';

/*** Set Application Name ***/
$app_name = 'He thong dieu hanh thong minh CARE IOC';
?>
