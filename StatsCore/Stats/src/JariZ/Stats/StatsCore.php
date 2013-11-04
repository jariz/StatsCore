<?php
/**
 * Created by PhpStorm.
 * User: JariZ
 * Date: 3-11-13
 * Time: 19:58
 */

namespace JariZ\Stats;


use Illuminate\Console\Command;

class StatsCore
{

    /**
     * Stat loader
     */
    public function __construct()
    {
        $config_stats = \Config::get("stat.stats");

        foreach ($config_stats as $stat) {
            $class = new $stat;
            $class->class = $stat;
            $this->stats[] = $class;
        }
    }

    private $stats;

    /**
     * Trigger all stats to collect, depending on type.
     * @param $type string Required, specifies the stat type, for example: minute, daily, hourly
     * @param Command $command Command parent that's calling this function
     */
    public function collect($type, Command $command=null)
    {
        foreach ($this->stats as $stat) {
            /* @var $stat Stat */
            if ($stat->type == $type) {
                if($command != null) $command->info("Collecting " . $stat->name . " in " . $stat->category);
                $collect = $stat->collect();
                if($command != null) echo "Value: ".$collect["val"]." Type: ".$collect["type"]."\n";
                $this->save($collect, $stat);
            }
        }
    }

    /**
     * Save stat results to the db
     * @param $return array The return value of the collect() function
     * @param $stat Stat The stat we're saving
     */
    private function save($return, $stat) {
        $model = new StatModel();
        $model->class = $stat->class;
        $model->value = $return["val"];
        $model->type = $return["type"];
        $model->save();
    }
}