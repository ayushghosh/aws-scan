<?php

namespace App;


class AwsRegion
{
    public  $regions = [];

    /**
     * AwsRegion constructor.
     * @param array $regions
     */
    public function __construct() {


    }

    public static function get(){

        $awsClient = new AwsService();

    }


}
