<?php

namespace App\Console\Commands;

use App\PasswordPolicy;
use App\Rules\MediumPassword;
use App\Rules\StrongPassword;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CheckPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'password:validate {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This validates password ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $policy = PasswordPolicy::query()->where('status', 1)->first();
        $password = $this->argument('password');
        $rules = [
            'password' => ['required', 'min:' . $policy->min_length]
        ];

        switch ($policy->complexity) {
            case 'strong':
                $rules['password'][] = new StrongPassword();
                break;
            case 'medium':
                $rules['password'][] = new MediumPassword();
                break;
            default:
                break;
        }

        $validator = Validator::make(['password' => $password], $rules);

        if ($validator->fails()) {
            $this->error($validator->errors()->first());
        } else {
            $this->info("Password is valid");
        }
    }
}
