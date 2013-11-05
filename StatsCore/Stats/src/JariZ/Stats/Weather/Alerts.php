<?php
/**
 * JARIZ.PRO
 * Date: 05/11/13
 * Time: 10:09
 * Author: JariZ
 */

namespace JariZ\Stats\Weather;


use Illuminate\Console\Command;
use JariZ\Stats\StatHelper;

class Alerts {
    public $name = "Weeralarm Niveau";

    public $type = "hourly";

    public $category = "Weather";

    public function collect(Command $command) {
        try {
            $command->info("Downloading file...");
            $rss = StatHelper::getFile("http://www.meteoalarm.eu/documents/rss/nl.rss", false);
            $rss = preg_replace("/\<\!\[CDATA\[(.*?)\]\]\>/ies", "base64_encode('$1')", $rss);
            $command->info("Parsing XML...");
            $xml = new \SimpleXMLElement($rss);
            $netherlands = base64_decode($xml->channel->item[0]->description);
            $command->info("Parsing HTML...");
            $dom = new \DOMDocument();
            $dom->loadHTML(stripcslashes($netherlands));
            $img = $dom->getElementsByTagName("img");
            $today = $img->item(0);
            $url = $today->attributes->getNamedItem("src")->nodeValue;
            preg_match_all('%wflag-([1-4l]{2})-t1\.jpg$%m', $url, $result, PREG_PATTERN_ORDER);
            switch($result[1][0]) {
                case "l1":
                    return StatHelper::returnCollect(StatHelper::TYPE_SUCCESS, 0);
                case "l2":
                    return StatHelper::returnCollect(StatHelper::TYPE_WARNING, 30);
                case "l3":
                    return StatHelper::returnCollect(StatHelper::TYPE_WARNING, 70);
                case "l4":
                    return StatHelper::returnCollect(StatHelper::TYPE_FATAL, 100);
            }
        }
        catch(\Exception $e) {
            $command->error($e->getMessage());
            return StatHelper::returnCollect(StatHelper::TYPE_ERROR, 0);
        }
    }
}