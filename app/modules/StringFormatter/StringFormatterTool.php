<?php

namespace StringFormatter;

/*
 * LingTalfi 2015-12-11
 */
use ArrayToString\ArrayToStringUtil;
use ArrayToString\SymbolManager\PhpArrayToStringSymbolManager;
use VariableToString\AuthorVariableToStringUtil;

class StringFormatterTool
{

    private static $arrayToString;
    /**
     * other means:
     *
     * - not an array
     * - not a string
     * - not a numeric
     * - not an object with the __toString method
     */
    private static $otherToString;


    // 
    private static $arrayToStringUtil;
    private static $varToStringUtil;

    public static function format($format, array $tags = [])
    {
        self::init();
        foreach ($tags as $k => $v) {
            if (is_array($v)) {
                $v = call_user_func(self::$arrayToString, $v);
            }
            elseif (is_object($v) && method_exists($v, '__toString')) {
                // we will cast an object with a __toString method with (string)$var, see below
            }
            elseif (!is_string($v) && !is_numeric($v)) {
                $v = call_user_func(self::$otherToString, $v);
            }
            $format = str_replace($k, (string)$v, $format);
        }
        return $format;
    }


    public static function setArrayToStringCallable(\Closure $f)
    {
        self::$arrayToString = $f;
    }

    public static function setOtherToStringCallable(\Closure $f)
    {
        self::$otherToString = $f;
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    private static function init()
    {
        if (null === self::$arrayToString) {
            self::$arrayToStringUtil = ArrayToStringUtil::create()
                ->setValueFormatter(function ($m) {
                    return AuthorVariableToStringUtil::create()->toString($m);
                })
                ->setSymbolManager(new PhpArrayToStringSymbolManager());

            self::$arrayToString = function ($m) {
                return self::$arrayToStringUtil->toString($m);
            };
        }

        if (null === self::$otherToString) {
            self::$varToStringUtil = $v = AuthorVariableToStringUtil::create();

            self::$otherToString = function ($m) {
                return self::$varToStringUtil->toString($m);
            };
        }
    }


}
