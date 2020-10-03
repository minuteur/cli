<?php

namespace App\Commands\Alfred;

use App\MinuteurClient;
use Alfred\Workflows\Workflow;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class FetchProjectsCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'alfred:projects:fetch {--query=} {--only-running} {--prefix=}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Fetch projects for the Alfred Workflow';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(MinuteurClient $client)
    {
        $workflow = new Workflow;
        $projects = $client->fetchProjects(array_filter([
            'q' => $this->option('query'),
            'only_running' => $this->option('only-running') ? '1' : '0',
        ]));

        foreach ($projects as $project) {
            $workflow->result()
                    ->uid($project['uuid'])
                    ->arg($project['uuid'])
                    ->title($project['name'])
                    ->subtitle($this->option('prefix') . ' for project ' . $project['name'])
                    ->type('default')
                    ->valid(true)
                    ->autocomplete($project['name']);
        }

        $this->line($workflow->output());
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
