<?php

namespace App\Jobs;

use App\Mail\ComplaintTransferredmail;
use App\Traits\SmsTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ComplaintTransferred implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, SmsTrait;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $toemails, $tosms, $title, $transferredby, $createdby, $transferredto, $reason;

    public function __construct($toemails, $tosms, $title, $transferredby, $createdby, $transferredto, $reason)
    {
        $this->toemails = $toemails;
        $this->tosms = $tosms;
        $this->title = $title;
        $this->transferredby = $transferredby;
        $this->createdby = $createdby;
        $this->transferredto = $transferredto;
        $this->reason = $reason;
        
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to('online@bikaji.com')
            ->cc($this->toemails)
            ->send(new ComplaintTransferredmail($this->title, $this->transferredby, $this->createdby, $this->transferredto, $this->reason));
        $message = 'Dear Sir, A complaint has been transferred to you, as it was related to your department. You can check the complaint at this link http://care.bikaji.com/auth/login by logging in. Bikaji Foods International Limited';
        $this->sendsms($this->tosms, $message);

$message ='Hello,

A complaint has been internally transferred to you, as it was related to your department.
You can check the complaint at this link http://care.bikaji.com/auth/login by logging in using your credentials.

Please resolve it within specified timeline to avoid escalation.';

$header='Internal Complaint Transfer: Bikaji Foods';
$footer = 'Bikaji Foods International Limited';

        $this->whatsappMessage($message,$this->tosms,$header,$footer);

    }
}
