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
    protected $signature = 'scan {--policy=* : Policy format to check, ex: =|22|0.0.0.0/32}';

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
        $policies = $this->option('policy');
        $valid_policy_status = \App\AwsService::validatePolicy($policies);
        dump($valid_policy_status);
        if ($valid_policy_status !== true) {
            $this->error($valid_policy_status);
        }
        exit();
        $regions = [];
        $this->task("Getting AWS Regions", function () use (&$regions) {
            $regions_arr = \App\AwsService::getRegions();
            $regions = $regions_arr;
            return true;
        });

//        $this->line('Hello');
//        $this->notify("Hello Web Artisan", "Love beautiful..", "icon.png");

        $this->info('Checking all regions');
        $security_fail_count = 0;
        foreach ($regions as $region) {
            $this->task("Checking Region: " . $region, function () use (&$region, &$security_fail_count, $policies) {
                $security_fail_count = \App\AwsService::checkEc2SecurityGroups($region, $policies);
//                sleep(2);
                return true;
            });
            if ($security_fail_count > 0) {
                $this->error($security_fail_count . ' issues found');
            }
            else {
                $this->info('No issue found');
            }
//            die();
//            $this->error('4 issue found');

        }
//        dump($x);

//        dump($regions);


    }

    /**
     * Define the command's schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule)
    : void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
