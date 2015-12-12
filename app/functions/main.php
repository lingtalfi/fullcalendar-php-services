<?php


use StringFormatter\StringFormatterTool;

function dateMysqlTime2Iso8601($mysqlTime)
{
    $date = new \DateTime($mysqlTime);
    return $date->format(DATE_ISO8601);
}


/**
 * @param string $msg
 */
function appLog($msg, array $tags = [])
{
    $string = StringFormatterTool::format($msg, $tags);
    /**
     * Do something with string if necessary...
     */

}