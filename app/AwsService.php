<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Aws\MultiRegionClient;

class AwsService extends Model
{
    public static $awsClient;

    public static function getRegions()
    {
        self::make();
        $regions = self::$awsClient->describeRegions();
        $regions_arr = collect($regions['Regions'])->pluck('RegionName')->toArray();
        return $regions_arr;
    }

    public static function make($region = 'ap-south-1', $service = 'ec2')
    {
        $awsClient = new \Aws\MultiRegionClient([
                                                    'region' => $region,
                                                    'version' => 'latest',
                                                    'profile' => 'default',
                                                    "service" => $service
                                                ]);

//        dump($awsClient);
//        die();
        self::$awsClient = $awsClient;
    }

    public static function getEc2SecurityGroups($region = 'ap-south-1')
    {
        self::make($region);
        $security_groups = self::$awsClient->describeSecurityGroups();

        return $security_groups;
    }

    public static function checkEc2SecurityGroups($region = 'ap-south-1'){
        $security_groups = self::getEc2SecurityGroups($region);


    }
}
