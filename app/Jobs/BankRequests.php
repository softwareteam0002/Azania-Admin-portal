<?php

namespace App\Jobs;

use App\Mail\BankRequestMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class BankRequests implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $body,$recipient;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($body,$recipient)
    {
        $this->body = $body;
        $this->recipient = $recipient;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            $email = new BankRequestMail($this->body);
            Mail::to($this->recipient)->send($email);
        }catch (\Exception $e)
        {
            dd($e);
        }
    }
}
