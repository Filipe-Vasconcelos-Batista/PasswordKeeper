<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailService
{
    private string $email;
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->email = $_ENV['EMAIL'];
        $this->mailer = $mailer;
    }
    public function sendEmail(string $userEmail,string $pincode){
        try {
            $email = (new Email())
                ->from("passwordkeeper123@outlook.com")
                ->to($userEmail)
                ->subject('your PasswordKeeper one use pincode')
                ->text('your secret pincode is the following: ' . $pincode . ' if it doesnt work you can request another here');

        $this->mailer->send($email);
        }catch(\Exception $e){
            error_log('failed to send email'. $e->getMessage());
        }
    }


}