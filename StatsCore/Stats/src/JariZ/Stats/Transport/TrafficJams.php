<?php
/**
 * Created by PhpStorm.
 * User: JariZ
 * Date: 3-11-13
 * Time: 21:42
 */

namespace JariZ\Stats\Transport;


use Illuminate\Console\Command;
use JariZ\Stats\StatHelper;

class TrafficJams {
    public $name = "Files";

    public $type = "minute";

    public $category = "Transport";

    public function collect(Command $command) {
        $jams = StatHelper::getFile("http://lbs.tomtom.com/lbs/services/traffic/pois/202405.2508991,7155728.8399450,969221.5186560,6495312.9155611,NLD:/0/nl/json,$/f51151bf-c607-4ddf-bb19-032fd38f8238", true);

        if(!isset($jams->traffic->overview->count)) return StatHelper::returnCollect(StatHelper::TYPE_ERROR, 0);

        $jams = $jams->traffic->overview->count;

        if($jams <= 200) return StatHelper::returnCollect(StatHelper::TYPE_SUCCESS, $jams);
        else if($jams <= 300) return StatHelper::returnCollect(StatHelper::TYPE_WARNING, $jams);
        else return StatHelper::returnCollect(StatHelper::TYPE_FATAL, $jams);
    }
} 
