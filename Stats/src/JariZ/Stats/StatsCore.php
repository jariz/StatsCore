<?php
/**
 * Created by PhpStorm.
 * User: JariZ
 * Date: 3-11-13
 * Time: 19:58
 */

namespace JariZ\Stats;


class StatsCore {

    public function __construct() {
        $stats = Config::get("stat.stats");
        var_dump(stats);
    }

    public function collect() {

    }
} 