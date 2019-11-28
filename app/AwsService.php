<?php

namespace App;

use Aws\MultiRegionClient;

class AwsService
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

//    public static function checkEc2SecurityGroupsRegions($regions)
//    {
//        $sg_regions = [];
//        foreach ($regions as $region) {
//            $sg_regions[$region] = self::checkEc2SecurityGroups($region);
//        }
//    }

    public static function checkEc2SecurityGroups($region = 'ap-south-1')
    {
        $security_groups = self::getEc2SecurityGroups($region);
        return random_int(0,3);
    }

    public static function getEc2SecurityGroups($region = 'ap-south-1')
    {
        self::make($region);
        $security_groups = self::$awsClient->describeSecurityGroups();

        $security_groups = collect($security_groups['SecurityGroups'])->toArray();
        $sg_arr = [];
        if (count($security_groups)) {
            foreach ($security_groups as $security_group) {
                $ingress_arr = [];
                foreach ($security_group['IpPermissions'] as $ingress_perm) {
                    if ($ingress_perm['IpProtocol'] == '-1' && count($ingress_perm['IpRanges']) == 0) {
                        continue;
                    }
                    if (!isset($ingress_perm['FromPort'])) {
                        continue;
                    }
                    foreach ($ingress_perm['IpRanges'] as $ingress_prem_ip_range) {
                        $ingress_arr[] = [
                            'port_from' => $ingress_perm['FromPort'],
                            'port_to' => $ingress_perm['ToPort'],
                            'cidr' => $ingress_prem_ip_range['CidrIp']
                        ];
                    }
                }
                $sg_arr[] = [
                    'group_name' => $security_group ['GroupName'],
                    'group_id' => $security_group['GroupId'],
                    'vpc_id' => isset($security_group['VpcId']) ? $security_group['VpcId'] : null,
                    'ingress' => $ingress_arr
                ];

            }
        }

        return $sg_arr;
    }
}
