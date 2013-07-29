<?php

require_once('config.php');
require_once "phar://iron_mq.phar";
require 'vendor/autoload.php';

//Get Post from SendGrid                                                                        

$from = $_POST["from"];
$subject = $_POST["subject"];

//Get Attached Songs                                                                            
$fh = fopen('/tmp/parse.log', 'a+');
if ( $fh )
  {
	$counter=0;
    fwrite($fh, print_r($_POST, true) . print_r($_FILES, true));
    foreach ($_FILES as $key => $file)
      {
        //$name = preg_replace('/\s+/', '', $file['name']);
	//$name = str_replace(' ', '', $file['name']);
	$name = uniqid();
	move_uploaded_file($file['tmp_name'], "upload/".$name);
      	$songName[$counter]=$file['name'];
	$song[$counter]= $name;
	$counter++;
	}
    fclose($fh);
  }


//Put songs in queue
$ironmq = new IronMQ(array(
    "token" => $config['iron']['token'],
    "project_id" => $config['iron']['id']
));

$queue_name= "songs";
$qmsg = $from."||".$song[0]."||".$song[1];
$ironmq->postMessage($queue_name, $qmsg);




//Send reply email with attached text document
$subject="You will get your remix shortly";
$message="We have placed your songs ".$songName[0]." and ".$songName[1]." in the queue and will email you back your mashup when its done :)";
                                                  
$sendgrid = new SendGrid($config['sendgrid']['api_user'], $config['sendgrid']['api_key']);

                             $mail = new SendGrid\Mail();


                             $mail->
                             addTo($from)->
                             setFrom($config['sendgrid']['my_email'])->
                             setSubject('Re: '.$subject)->
                             setText($message)->
                             setHtml($message);
                            

                             $sendgrid->smtp->send($mail);


//Check Queue Size
$qinfo = $ironmq->getQueue($queue_name);
if($qinfo->size < 2)
	file_get_contents('http://localhost/email/two.php');


?>
