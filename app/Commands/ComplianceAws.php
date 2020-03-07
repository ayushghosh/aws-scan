<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class ComplianceAws extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'compliance:aws';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Check compliance of AWS Services';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('I am sorry this is not implemented, use the scan command');
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
