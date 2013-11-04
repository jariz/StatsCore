<?php
/**
 * Created by PhpStorm.
 * User: JariZ
 * Date: 3-11-13
 * Time: 17:17
 */

namespace JariZ\Stats\Transport;
use JariZ\Stats\Stat;
use JariZ\Stats\StatHelper;

class PlannedDisturbances extends Stat {

    public $name = "Geplande Vertragingen";

    public $type = "minute";

    public $category = "Transport";

    public function collect() {
        $disturbances = StatHelper::getFile("http://api.9292.nl/0.1/messages/deviations?lang=en-GB", true);

        if(!isset($disturbances->total)) return StatHelper::returnCollect(StatHelper::TYPE_ERROR, 0);

        if($disturbances->total <= 400) return StatHelper::returnCollect(StatHelper::TYPE_SUCCESS, $disturbances->total);
        else if($disturbances->total <= 500) return StatHelper::returnCollect(StatHelper::TYPE_WARNING, $disturbances->total);
        else return StatHelper::returnCollect(StatHelper::TYPE_FATAL, $disturbances->total);
    }
}