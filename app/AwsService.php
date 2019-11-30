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

    public static function checkEc2SecurityGroups($region = 'ap-south-1', $policies)
    {
        self::validatePolicy($policies);
        //        $security_groups = self::getEc2SecurityGroups($region);
        return random_int(0, 3);
    }

    public static function validatePolicy($policies)
    {
        dump('p');
        foreach ($policies as $policy) {
            list($condition, $port, $cidr) = explode('|', $policy);
//            dump($condition);
//            dump($port);
//            dump($cidr);
            if ($condition != '!' && $condition != '=') {
                return 'Invalid condition in policy, use ! or =';
            }
            if ($port < 1 || $port > 65536) {
                return 'Invalid port in policy';
            }

            if (self::validateCidr($cidr) == false) {
                return 'Invalid CIDR';
            }
        }

        return true;
    }

    public static function validateCidr($cidr)
    {
        $parts = explode('/', $cidr);
        if (count($parts) != 2) {
            return false;
        }

        $ip = $parts[0];
        $netmask = intval($parts[1]);

        if ($netmask < 0) {
            return false;
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return $netmask <= 32;
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return $netmask <= 128;
        }

        return false;
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
