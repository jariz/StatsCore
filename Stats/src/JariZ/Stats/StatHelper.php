<?php
/**
 * Created by PhpStorm.
 * User: JariZ
 * Date: 3-11-13
 * Time: 17:22
 */

namespace JariZ\Stats;


class StatHelper {
    public static function getFile($url, $parse_as_json=false) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);

        $data = curl_exec($ch);
        curl_close($ch);

        if($parse_as_json) $data = json_decode($data);

        return $data;
    }

    const TYPE_SUCCESS = "success";
    const TYPE_WARNING = "warning";
    const TYPE_FATAL = "fatal";
    const TYPE_ERROR = "error";

    public static function returnCollect($type, $val) {
        return array(
            "type"=>$type,
            "val"=>$val
        );
    }
} 