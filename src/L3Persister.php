<?php declare(strict_types=1);

namespace ESolution\LaravelLokiLogging;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class L3Persister extends Command
{
    protected $signature = 'loki:persist';
    protected $description = 'Persist recent log messages to loki';

    public function handle()
    {
        $file = $file = storage_path(L3ServiceProvider::LOG_LOCATION);
        if (!file_exists($file)) {
            return;
        }

        $content = file_get_contents($file);
        file_put_contents($file, '');

        $messages = explode("\n", $content);
        if (count($messages) === 0) {
            return;
        }

        $http = Http::asJson();

        if (config('l3.loki.username') !== null && config('l3.loki.password') !== null) {
            $http = $http->withBasicAuth(
            config('l3.loki.username'),
            config('l3.loki.password')
        );
        }

        if (config('l3.loki.tenant_id') !== null) {
            $http = $http->withHeaders([
                'X-Scope-OrgID' => config('l3.loki.tenant_id'),
            ]);
        }

        $path = config('l3.loki.server') . "/loki/api/v1/push";
        foreach ($messages as $message) {
            if ($message === "") {
                continue;
            }
            $data = json_decode($message);
            $resp = $http->post($path, [
                'streams' => [[
                    'stream' => $data->tags,
                    'values' => [[
                        strval($data->time * 1000),
                        $data->message
                    ]]
                ]]
            ]);
        }
    }
}
