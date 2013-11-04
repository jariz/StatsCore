<?php
/**
 * Created by PhpStorm.
 * User: JariZ
 * Date: 3-11-13
 * Time: 21:42
 */

namespace JariZ\Stats\Transport;


use JariZ\Stats\StatHelper;

class TrafficJamLength {
    public $name = "Meter files";

    public $type = "minute";

    public $category = "Transport";

    public function collect() {
        $jams = StatHelper::getFile("http://lbs.tomtom.com/lbs/services/traffic/pois/202405.2508991,7155728.8399450,969221.5186560,6495312.9155611,NLD:/0/nl/json,$/f51151bf-c607-4ddf-bb19-032fd38f8238", true);

        if(!isset($jams->traffic->overview->totalLengthMeters)) return StatHelper::returnCollect(StatHelper::TYPE_ERROR, 0);

        $jams = $jams->traffic->overview->totalLengthMeters;

        if($jams <= 200000) /*200km*/ return StatHelper::returnCollect(StatHelper::TYPE_SUCCESS, $jams);
        else if($jams <= 400000) return StatHelper::returnCollect(StatHelper::TYPE_WARNING, $jams);
        else return StatHelper::returnCollect(StatHelper::TYPE_FATAL, $jams);
    }
} 
