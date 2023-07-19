<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPassword extends Mailable
{
    use Queueable, SerializesModels;
    public $token,$email;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email,$token)
    {
        $this->email=$email;
        $this->token=$token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.auth.forgotpass',['email'=>$this->email,'token'=>$this->token]);
    }
}
