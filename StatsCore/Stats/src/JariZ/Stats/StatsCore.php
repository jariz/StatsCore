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

    public $stats;

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

    public function getCategories() {
        $ret = array();
        foreach($this->stats as $stat) {
            /* @var $stat Stat */
            if(!in_array($stat->category, $ret))
                $ret[] = $stat->category;
        }
        return $ret;
    }

    public function getStatsInCategory($category) {
        $ret = array();
        foreach($this->stats as $stat) {
            /* @var $stat Stat */
            if(strtolower($stat->category) == strtolower($category))
                $ret[] = $stat;
        }
        return $ret;
    }

    private function getModel($stat) {
        return StatModel::where("class", "=", $stat->class)->whereNotIn("type", array('error'))->get()->last();
    }

    public function getStatus($stat) {
        return $this->getModel($stat)->type;
    }

    public function getValue($stat) {
        return $this->getModel($stat)->value;
    }

    public function getAverageStatus($category) {
        $statuses = array();
        $stats = $this->getStatsInCategory($category);
        foreach($stats as $stat)
            /* @var $stat Stat */
            $statuses[] = $this->getStatus($stat);

        if(in_array(StatHelper::TYPE_FATAL, $statuses)) return StatHelper::TYPE_FATAL;
        else if(in_array(StatHelper::TYPE_WARNING, $statuses)) return StatHelper::TYPE_WARNING;
        else return StatHelper::TYPE_SUCCESS;
    }

    public function _2bootstrap($state) {
        if($state == StatHelper::TYPE_FATAL) return "error";
        else return $state;
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