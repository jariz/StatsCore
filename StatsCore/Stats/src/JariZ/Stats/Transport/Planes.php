<?php
/**
 * Created by PhpStorm.
 * User: JariZ
 * Date: 4-11-13
 * Time: 0:15
 */

namespace JariZ\Stats\Transport;


use JariZ\Stats\StatHelper;

class Planes {
    public $name = "Vliegtuigen rond schiphol";

    public $type = "minute";

    public $category = "Transport";

    public function collect() {
        $planes = StatHelper::getFile("http://db8.flightradar24.com/zones/germany_all.js", false);

        $planes = substr($planes, 12);
        $planes = substr($planes, 0, strlen($planes) - 2);
//        var_dump($planes);
        $planes = json_decode($planes);
//        var_dump($planes);

        if($planes == null) return StatHelper::returnCollect(StatHelper::TYPE_ERROR, 0);

        $num_planes = 0;
        foreach($planes as $plane) {
            //is the plane from/to AMS?

            //@TODO use coordinates to estiminate if it is around schiphol or even the netherlands

            if(!isset($plane[11]) || !isset($plane[12])) continue;
            if($plane[11] == "AMS") $num_planes++;
            else if ($plane[12] == "AMS") $num_planes++;
        }

        if($num_planes <= 5) return StatHelper::returnCollect(StatHelper::TYPE_FATAL, $num_planes);
        else if($num_planes <= 10) return StatHelper::returnCollect(StatHelper::TYPE_WARNING, $num_planes);
        else return StatHelper::returnCollect(StatHelper::TYPE_SUCCESS, $num_planes);
    }
} 