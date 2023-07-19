<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestMailNew extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
    try {
        $to = ['neha.rajvanshi@neologicx.in'];
        // return $this->to($to)->view('welcome');
        return $this->to($to)->view('mail.testmail');
        }
        catch (Exception $ex) {
            dd($ex);
        }

    }
}
