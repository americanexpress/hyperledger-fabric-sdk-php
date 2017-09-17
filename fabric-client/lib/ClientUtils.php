<?php

class ClientUtils{

    /**
    * Function for getting current timestamp
    * @return Object containing Seconds & Nanoseconds
    */
    public static function buildCurrentTimestamp()
    {
        $TimeStamp = new Google\Protobuf\Timestamp();
        $microtime = microtime(true);
        $time = explode(".", $microtime);
        $seconds = $time[0];
        $nanos = (($microtime*1000) % 1000) * 1000000;

        $TimeStamp->setSeconds($seconds);
        $TimeStamp->setNanos($nanos);

        return $TimeStamp;
    }

}