<?php
/**
 * Created by PhpStorm.
 * User: JariZ
 * Date: 3-11-13
 * Time: 17:17
 */

namespace JariZ\Stats\Transport;
use Illuminate\Console\Command;
use JariZ\Stats\Stat;
use JariZ\Stats\StatHelper;

class UnplannedDisturbances extends Stat {

    public $name = "OV Ongeplande Vertragingen";

    public $type = "minute";

    public $category = "Transport";

    public function collect(Command $command) {
        $disturbances = StatHelper::getFile("http://api.9292.nl/0.1/messages/disturbances?lang=en-GB", true);

        if(!isset($disturbances->total)) return StatHelper::returnCollect(StatHelper::TYPE_ERROR, 0);

        if($disturbances->total <= 3) return StatHelper::returnCollect(StatHelper::TYPE_SUCCESS, $disturbances->total);
        else if($disturbances->total <= 6) return StatHelper::returnCollect(StatHelper::TYPE_WARNING, $disturbances->total);
        else return StatHelper::returnCollect(StatHelper::TYPE_FATAL, $disturbances->total);
    }
}