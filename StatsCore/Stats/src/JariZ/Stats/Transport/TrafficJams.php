<?php
/**
 * Created by PhpStorm.
 * User: JariZ
 * Date: 3-11-13
 * Time: 21:42
 */

namespace JariZ\Stats\Transport;


use JariZ\Stats\StatHelper;

class TrafficJams {
    public $name = "Files";

    public $type = "minute";

    public $category = "Transport";

    public function collect() {
        $jams = StatHelper::getFile("http://verkeerstatic.anwb.nl/ANWBMap/anwb/GetTMCJSON", true);

        if(!isset($jams->data)) return StatHelper::returnCollect(StatHelper::TYPE_ERROR, 0);

        $jams = count($jams->data);

        if($jams <= 200) return StatHelper::returnCollect(StatHelper::TYPE_SUCCESS, $jams);
        else if($jams <= 300) return StatHelper::returnCollect(StatHelper::TYPE_WARNING, $jams);
        else return StatHelper::returnCollect(StatHelper::TYPE_FATAL, $jams);
    }
} 
