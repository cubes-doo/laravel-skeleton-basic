<?php

namespace App\Lib;

/**
 * DatetimeTrait - methods for datetime manipulation
 */
trait DatetimeTrait {
    
    /**
     * @var string $datetimepickerFormat | Custom format for jquery.datetimepicker
     */
    protected $serbianDateFormat = "d.m.Y"; 
    
    protected $serbianDateTimeFormat = "d.m.Y H:i"; 
    
    
    /**
     * Convert custom datetime format to MYSQL timestamp format.
     * 
     * @param string $customFormat
     * @param string $datetimeStr
     * 
     * @return string
     */
    protected function customToMysqlDatetimeFormat($customFormat, $datetimeStr)
    {
        $datetime = \DateTime::createFromFormat($customFormat, $datetimeStr);
        return $datetime->format("Y-m-d H:i:s");
    }
    
    
    /**
     * Convert MYSQL timestamp format to custom datetime format.
     * 
     * @param string $timestamp
     * 
     * @return string
     */
    protected function timestampToCustomFormat($timestamp, $customFormat)
    {
        return $timestamp->format($customFormat);
    }
    
    
    /**
     * Convert time format > 02:25 (minutes:seconds) to total number of seconds
     * as an integer value
     * 
     * @param string $minSecTime | format 'mm:ss'
     * 
     * @return integer
     */
    protected function minSecToTotalSeconds($minSecTime) 
    {
        $parts = explode(':', $minSecTime);
        if(count($parts) != 2) {
            return 0;
        }
        return $parts[0] * 60 + $parts[1];
    }
}
