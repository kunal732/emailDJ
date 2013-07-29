# EmailDJ - Remix Songs by Email
An application to remix two of your .mp3 files into a mashup. This uses EchoNest's AfromB.py program to synthesize the new song. 


## Setting UP
To use, clone the repo and setup the following requirements:

EchoNest Remix:
http://echonest.github.io/remix/

Iron.io
https://github.com/iron-io/iron_mq_php

SendGrid
https://github.com/sendgrid/sendgrid-php

Then go to the [Parse Webhook section of the SendGrid control panel](http://sendgrid.com/docs/API_Reference/Webhooks/parse.html). Enter your app's information (namely to POST data to your domain), and point your MX record to `mx.sendgrid.net`.


###Testing
You can try out a live version by sending two mp3 files to remix@emaildj.com . You will receive your mashup shortly afterwards. 

