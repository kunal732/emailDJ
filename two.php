<?php
require_once "phar://iron_mq.phar";
require_once('config.php');
require 'vendor/autoload.php';


//setup the queue
$ironmq = new IronMQ(array(
    "token" => $config['iron']['token'],
    "project_id" => $config['iron']['id']
));


$queue_name = "songs";


do
{

//get message
$qmsg = $ironmq->getMessage($queue_name);

$remixInfo = explode("||", $qmsg->body);
$from = $remixInfo[0];
$songOne = $remixInfo[1];
$songTwo = $remixInfo[2];


$newMix = uniqid();

//mashup both songs with echonest
$pycom= "python afromb.py upload/".$songOne." upload/".$songTwo." remixes/".$newMix.".mp3 0.9 env ";
shell_exec($pycom);

//path of remixed file
$filePath ="/var/www/email/remixes/".$newMix.".mp3";


//sending return email with song
$subject ="Enjoy your remix";
$message="your remix of ".$songOne." and ".$songTwo." is attached below";
$sendgrid = new SendGrid($config['sendgrid']['api_user'], $config['sendgrid']['api_key']);

                             $mail = new SendGrid\Mail();


                             $mail->
                             addTo($from)->
                             setFrom($config['sendgrid']['my_email'])->
                             setSubject('Re: '.$subject)->
                             setText($message)->
                             setHtml($message)->
			     addAttachment($filePath);

                             $sendgrid->smtp->send($mail);



//delete message from queue
$ironmq->deleteMessage($queue_name, $qmsg->id);

//get details like size of the current queue
$qinfo = $ironmq->getQueue($queue_name);

}while($qinfo->size >=1);

?>
