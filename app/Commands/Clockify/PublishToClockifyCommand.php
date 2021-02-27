<?php

namespace App\Commands\Clockify;

use App\Clockify;
use App\Freshbooks;
use App\MinuteurClient;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class PublishToClockifyCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'clockify:publish';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Publish the hours to your Clockify account';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Clockify $clockify, MinuteurClient $minuteurClient)
    {
        $summary = $minuteurClient->summaryFromProjects();
        $clockify->postHours($summary);

        $this->info('Hours successfully posted to Clockify.');

        if (count($clockify->errors) > 0) {
            $this->comment('With some errors, though...');

            foreach ($clockify->errors as $error) {
                $this->error($error);
            }
        }
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule)
    {

    }
}
