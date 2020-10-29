<?php

namespace App\Commands\Alfred;

use Exception;
use App\MinuteurClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use Illuminate\Http\Client\RequestException;

class StopTimerCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'alfred:timer:stop {query}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Stop the timer for a given project from Alfred';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(MinuteurClient $client)
    {
        try {
            preg_match('/\[(.+)\]\s\|\s(.+)?/', $this->argument('query'), $matches);

            $projectUuid = $matches[1];
            $sessionName = $matches[2] ?? '';

            $client->stopTimer(
                $projectUuid,
                $sessionName
            );

            $this->line('Timer stopped successfully.');
        } catch (RequestException $exception) {
            Log::debug($exception->getMessage());

            $this->error($exception->response->json()['error'] ?? '');
        } catch (Exception $exception) {
            Log::debug($exception->getMessage());

            $this->error($exception->getMessage());
        }
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
