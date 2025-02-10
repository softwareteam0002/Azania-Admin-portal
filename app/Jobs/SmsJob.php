<?php

namespace App\Jobs;

use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $text, $phoneNumber, $credentials, $payload, $message;
    private const MHB_SMS_HEADER = "MHB BANK";

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($text, $phoneNumber)
    {
        $this->credentials['channel'] = env('SMS_CHANNEL');
        $this->credentials['password'] = env('SMS_CREDENTIAL');

        $this->text = $text;
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->sendSms($this->text, $this->phoneNumber);
    }

    public function sendSms($text, $phoneNumber): void
    {
        $url = env('SMS_URL');
        $client = new Client;

        $payload = [
            "channel" => $this->credentials,
            "messages" => $this->message($text, $phoneNumber)
        ];

        Log::channel('sms')->info("SMS-REQUEST: " . json_encode($payload));

        $result = $client->request('POST', $url, [
            'json' => $payload
        ]);

        $response = json_decode($result->getBody());

        Log::channel('sms')->info("SMS-RESPONSE: " . json_encode($response));
    }

    private function message($text, $phoneNumber): array
    {
        if (str_starts_with($phoneNumber, '0')) {
            $phoneNumber = '255' . substr($phoneNumber, 1);
        }

        $this->message[] = [
            'text' => $text,
            'msisdn' => $phoneNumber,
            'source' => self::MHB_SMS_HEADER,
        ];

        return $this->message;
    }

}
