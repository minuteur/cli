<?php

namespace App\Commands\Alfred;

use Exception;
use App\MinuteurClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use Illuminate\Http\Client\RequestException;

class StartTimerCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'alfred:timer:start {query}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Start the timer for a given project from Alfred';

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

            $client->startTimer($projectUuid);

            $this->line('Timer started successfully');
        } catch (RequestException $exception) {
            $this->error($exception->response->json()['error'] ?? '');
        } catch (Exception $exception) {
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
