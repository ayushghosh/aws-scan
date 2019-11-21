<?php

namespace App\Commands;

use App\AwsRegion;
use App\Service\AwsService;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class ScanAws extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'scan';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Scan AWS account';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $regions = [];
        $this->task("Getting AWS Regions", function () use (&$regions) {
            $regions_arr = \App\AwsService::getRegions();
            $regions = $regions_arr;
            return true;
        });

//        $this->line('Hello');
//        $this->notify("Hello Web Artisan", "Love beautiful..", "icon.png");

        $this->info('Checking all regions');

        foreach ($regions as $region){
            $this->task("Checking Region: ".$region, function () use (&$region) {
                $security_fails = \App\AwsService::checkEc2SecurityGroups($region);
//                sleep(2);
                return true;
            });
//            $this->error('4 issue found');

        }
//        dump($x);

//        dump($regions);



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
