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
    protected $signature = 'scan {--policy=* : Policy to check in format: name|condition[=,!]|port|cidr}';

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
        if (count($policies) > 0) {
            $valid_policy_status = \App\AwsService::validatePolicyFormat($policies);
            if ($valid_policy_status !== true) {
                $this->error($valid_policy_status);
                exit(1);
            }
        }

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
        $security_fails = [];
        foreach ($regions as $region) {
            $this->task("Checking Region: " . $region, function () use (&$region, &$security_fail_count, &$security_fails) {
                //Set Active region to create array
                \App\AwsService::$active_region = $region;

                $security_fails_region = \App\AwsService::checkEc2SecurityGroups($region);
//                dump($security_fails_region);
                $security_fails[$region] = $security_fails_region;

                $security_fail_count = count($security_fails_region);
                return true;
            });
            if ($security_fail_count > 0) {
                $this->error($security_fail_count . ' issues found');
            }
            else {
                $this->info('No issue found');
            }
//            dump($security_fails);
        }
        foreach ($regions as $region) {
            if (isset($security_fails[$region]) && count($security_fails[$region])) {
                $this->table(['region', 'sg_name', 'sg_id', 'policy', 'port', 'cidr'], $security_fails[$region]);
            }
        }

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
