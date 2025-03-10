<? require("class.phpmailer.php");

class mymailer extends PHPMailer{
//$mail = new PHPMailer();
function mymailer()
{
$this->IsSMTP();  
}
                                 // send via SMTP
var $Host     = "192.168.1.250"; // SMTP servers
var $SMTPAuth = true;     // turn on SMTP authentication
var $Username = "root";  // SMTP username
var $Password = "69coins2ms"; // SMTP password
var $WordWrap = 50; 

}
?> 
