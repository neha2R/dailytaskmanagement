<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoginVerification extends Mailable
{
    use Queueable, SerializesModels;

    public $email, $otp, $url, $name;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email, $otp, $name){
        $this->name = $name;
        $this->otp = $otp;
        $this->email = $email;
        $this->url = route('VerifyOtp');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.login_verification',[
            'email'=>$this->email,
            'otp'=>$this->otp,
            'url'=>$this->url,
            'name'=>$this->name
        ]);
    }
}
