<?php

namespace App\Console\Commands;

use App\Jobs\SmsJob;
use Illuminate\Console\Command;

class GenerateKey extends Command
{
    protected $signature = 'send:sms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sending test sms with MHB Header';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

    }

}
