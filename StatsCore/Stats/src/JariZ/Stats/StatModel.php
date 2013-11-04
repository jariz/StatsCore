<?php
/**
 * Created by PhpStorm.
 * User: JariZ
 * Date: 3-11-13
 * Time: 23:27
 */

namespace JariZ\Stats;


class StatModel extends \Eloquent {
    protected $table = 'stats';
    protected $guarded = array();

    public function save(array $options = array()) {
        $this->timestamp = time();
        parent::save($options);
    }
} 