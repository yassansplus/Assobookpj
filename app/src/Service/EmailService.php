<?php

namespace App\Service;

use Swift_Mailer;

class EmailService
{
    private $mailer;

    public function __construct(Swift_Mailer $mailer){
        $this->mailer = $mailer;
    }

    public function sendMail(string $title, string $email, $body)
    {
        $elem = $body;
        $type = '';

        if(is_array($body)){
            list($elem,$type) = $body;
        }

        $myEmail = (new \Swift_Message($title))
            ->setFrom('assobookpa@gmail.com')
            ->setTo($email)
            ->setBody($elem,$type);
        //Send Swift_Message Object
        $this->mailer->send($myEmail);
    }
}