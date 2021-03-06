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
    public function collect($type, Command $command)
    {
        foreach ($this->stats as $stat) {
            /* @var $stat Stat */
            if ($stat->type == $type) {
                $command->info("Collecting " . $stat->name . " in " . $stat->category);
                $collect = $stat->collect($command);
                echo "Value: ".$collect["val"]." Type: ".$collect["type"]."\n";
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

    public function getFlot($stat, $encode=true) {
        $arr = StatModel::where("timestamp", ">=", time() - (60 * 60 * 24 * 7))->where("class", "=", $stat->class)->get(array("timestamp", "value"))->toArray();
        $ret = array();
        foreach($arr as $a)
            $ret[] = array($a["timestamp"] * 1000, $a["value"]);
        $ret = array($ret);
        if($encode) return json_encode($ret);
        else return $ret;
    }

    /**
     * Get the average status from a category based on the stats in them.
     * @param $category string The category the status needs to be 'calculated' from
     * @return string The average status
     */
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
        if($state == StatHelper::TYPE_FATAL) return "danger";
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