<?php
require ("mailConfig.php");
/*


	this class encapsulates the PHP mail() function.
	implements CC, Bcc, Priority headers


@version	1.3 

- added ReplyTo( $address ) method
- added Receipt() method - to add a mail receipt
- added optionnal charset parameter to Body() method. this should fix charset problem on some mail clients
	     
@example

	include "libmail.php";
	
	$m= new Mail; // create the mail
	$m->From( "leo@isp.com" );
	$m->To( "destination@somewhere.fr" );
	$m->Subject( "the subject of the mail" );	

	$message= "Hello world!\nthis is a test of the Mail class\nplease ignore\nThanks.";
	$m->Body( $message);	// set the body
	$m->Cc( "someone@somewhere.fr");
	$m->Bcc( "someoneelse@somewhere.fr");
	$m->Priority(4) ;	// set the priority to Low 
	$m->Attach( "/home/leo/toto.gif", "image/gif" ) ;	// attach a file of type image/gif
	$m->Send();	// send the mail
	echo "the mail below has been sent:<br><pre>", $m->Get(), "</pre>";

	
LASTMOD
	Fri Oct  6 15:46:12 UTC 2000

@author	Leo West - lwest@free.fr

*/


class Mail extends mymailer
{

/*

Define the subject line of the email
@param string $subject any monoline string

*/
function Subject( $subject )
{
	$this->Subject=$subject;
}


/*

set the sender of the mail
@param string $from should be an email address

*/
 
function From( $from )
{
$this->From=$from;
	$this->FromName=$from;
}

/*
 set the Reply-to header 
 @param string $email should be an email address

*/ 
function ReplyTo( $address )
{

	$this->AddReplyTo($address,"Information");
		
}


/*
add a receipt to the mail ie.  a confirmation is returned to the "From" address (or "ReplyTo" if defined) 
when the receiver opens the message.

@warning this functionality is *not* a standard, thus only some mail clients are compliants.

*/
 
function Receipt()
{
	$this->receipt = 1;
}


/*
set the mail recipient
@param string $to email address, accept both a single address or an array of addresses

*/

function To( $to )
{
$this->AddAddress($to);
}



function Priority( $aaao )
{

}

/*		Cc()
 *		set the CC headers ( carbon copy )
 *		$cc : email address(es), accept both array and string
 */


/*		Body( text [, charset] )
 *		set the body (message) of the mail
 *		define the charset if the message contains extended characters (accents)
 *		default to us-ascii
 *		$mail->Body( "mél en français avec des accents", "iso-8859-1" );
 */
function Body( $body, $charset="" )
{
	$this->Body =$body;
}



/*	
 Attach a file to the mail
 
 @param string $filename : path of the file to attach
 @param string $filetype : MIME-type of the file. default to 'application/x-unknown-content-type'
 @param string $disposition : instruct the Mailclient to display the file if possible ("inline") or always as a link ("attachment") possible values are "inline", "attachment"
 */

function Attach( $filename, $filetype = "", $disposition = "inline" )
{
	$this->AddAttachment($filename);  
}

} // class Mail


?>
