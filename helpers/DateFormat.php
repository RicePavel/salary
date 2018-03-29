<?php

namespace app\helpers;

class DateFormat {
    
    public static function toSqlFormat($dateWeb) {
        $arr = explode('.', $dateWeb);
        $result = '';
        if (count($arr) == 3) {
            $result = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
        }
        return $result;
    }
    
    public static function toWebFormat($dateSql) {
        $arr = explode('-', $dateSql);
        $result = '';
        if (count($arr) == 3) {
            $result = $arr[2] . '.' . $arr[1] . '.' . $arr[0];
        }
        return $result;
    }
    
}

