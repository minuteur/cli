<?php

namespace App\Commands;

use Exception;
use App\MinuteurClient;
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
    protected $signature = 'timer:start {project_uuid}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Start the timer for a given project';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(MinuteurClient $client)
    {
        try {
            $client->startTimer($this->argument('project_uuid'));

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
