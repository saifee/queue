<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Log;

class SendSmsJob  implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     * 
     */
    protected $token;
    protected $text;
    protected $settings;
    protected $from_call;

    public function __construct($token, $text, $settings, $from_call)
    {
        $this->token = $token;
        $this->text = $text;
        $this->from_call = $from_call;
        $this->settings = $settings;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (strpos($this->settings->sms_url, '$phone$') !== false && strpos($this->settings->sms_url, '$text$') !== false) {
            $text = '';
            if ($this->from_call == 'issue_token') {
                $search = array('$token_number$', '$service_name$', '$date$', '$position$');
                $replace = array($this->token->token_number, $this->token->service->name, Carbon::parse($this->token->created_at)->timezone($this->settings->timezone)->toDateString(), $this->token->position);
                $text = str_replace($search, $replace, $this->token->service->optin_message_format);
            } else if ($this->from_call == 'status_message') {
                $search = array('$token_number$', '$service_name$', '$position$');
                $replace = array($this->token->token_number, $this->token->service->name, $this->token->position);
                $text = str_replace($search, $replace, $this->token->service->status_message_format);
            } else if ($this->from_call == 'call_next' || $this->from_call == 'noshow' || $this->from_call == 'served') {
                $search = array('$token_number$', '$service_name$', '$date$', '$counter_name$');
                $replace = array($this->token->token_number, $this->token->service->name, Carbon::parse($this->token->created_at)->timezone($this->settings->timezone)->toDateString(), $this->token->call->counter->name);
                if ($this->from_call == 'call_next') $text = str_replace($search, $replace, $this->token->service->call_message_format);
                else if ($this->from_call == 'noshow') $text = str_replace($search, $replace, $this->token->service->noshow_message_format);
                else if ($this->from_call == 'served') $text = str_replace($search, $replace, $this->token->service->completed_message_format);
            }
            $text = urlencode($text);
            $search = array('$phone$', '$text$');
            $replace = array($this->token->phone, $text);
            $url = str_replace($search, $replace, $this->settings->sms_url);
            try {
                Http::get($url);
            } catch (\Exception $e) {
            }
        }
    }
}
