<?php

$cdir	= getcwd().'/images/';
$curl	= $_POST['curl'];

$mmid		= $_POST['memeid'];
$to 		= 'user@dbk.com';//$_POST['to'];
$subject	= 'New Meme Submitted';
$message	= 'Dear Admin,'.
				"\r\n".
				'New Meme submitted for approval.'."\r\n".
				'Meme ID : '.$mmid."\r\n".
				'Link : <a href="'.$curl.'waiting/'.$mmid.'.jpg" >'.$curl.'waiting/'.$mmid.'.jpg</a> 
				';
$headers	= 'From: <Meme Generator>meme-generator@'.$_SERVER['HTTP_HOST'].'' . "\r\n" ;
$headers  	.= 'MIME-Version: 1.0' . "\r\n";
$headers 	.= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

$main		= @mail( $to, $subject, $message, $headers);
if($main)
{
	echo "Your meme is submitted for approval";
	
	@copy($cdir.'meme/'.$mmid.'.jpg',$cdir.'waiting/'.$mmid.'.jpg');
}
else
	echo "Something went wrong. Please try later."


?>