<?php
/*
 * Super class for all types of stats in dashboard
 */

namespace JariZ\Stats;
use Illuminate\Console\Command;

class Stat {

    /**
     * @var $name string Statistic name
     */
    public $name;

    /**
     * @var $type string Can be of 3 types: 'minute', 'hourly', 'daily'
     */
    public $type;

    /**
     * @var $category string The category this statistic is in (transport, weather)
     */
    public $category;

    /**
     * @var $class string Class name including namespace. (gets auto filled in by StatCore)
     */
    public $class;

    /**
     * This will be called whenever the data needs to be collected (how many times is based on the $type property)<br>
     * Needs to return an array with indexes int 'val' and array 'status' which can contain one of 3 values 'success', 'warning', 'critical', and 'error' with 'error' being not able to get the data from the feed.
     * @param \Illuminate\Console\Command $command The commandline that can be used for ->info and ->error
     * @returns array
     */
    public function collect(Command $command) {}
}