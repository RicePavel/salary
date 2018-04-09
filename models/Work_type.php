<?php

namespace app\models;

class Work_type {
    
    // основное место работы
     const MAIN = 1;
     
     // внутреннее совместительство
     const INTERNAL_AFFILIATION = 2;
     
     // внешнее совместительство
     const EXTERNAL_AFFILIATION = 3;
     
     public static function getList() {
        return [self::MAIN, self::INTERNAL_AFFILIATION, self::EXTERNAL_AFFILIATION];
     }
     
     public static function getName($code) {
         switch ($code) {
             case self::MAIN:
                 return 'основное место работы';
                 break;
             case self::INTERNAL_AFFILIATION: 
                 return 'внутреннее совместительство';
             case self::EXTERNAL_AFFILIATION:
                 return 'внешнее совместительство';
             default:
                 return '';
                 break;
         }
     }
    
}

