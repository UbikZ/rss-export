<?php

namespace UbikZ\Util;

class SimpleIO
{
    const M_INFO = 0;
    const M_ERROR = 1;

    static function msg($msg, $type = null, $bExit = false)
    {
        switch ($type) {
            case self::M_INFO:
                $m = '[INFO] ';
                break;
            case self::M_ERROR:
                $m = '[ERROR] ';
                break;
            default:
                $m = '';
                break;
        }
        echo $m . $msg . PHP_EOL;
        if ($bExit) {
            exit();
        }
    }

    static function error($msg, $target = 'Error')
    {
        self::msg("$target : $msg", self::M_ERROR, true);
    }

    static function info($msg, $target = 'Info')
    {
        self::msg("$target : $msg", self::M_INFO, false);
    }
}
