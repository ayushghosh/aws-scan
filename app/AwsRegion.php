<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AwsRegion extends Model
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
