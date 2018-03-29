<?php

namespace app\models;

use yii\db\ActiveRecord;
use app\helpers\DateFormat;

class Timetable extends ActiveRecord {
    
    public function getUnit() {
        return $this->hasOne(Unit::className(), ['unit_id' => 'unit_id']);
    }
    
    /*
    public function setCreate_date($value) {
        $this->create_date = DateFormat::toSqlFormat($value);
    }
    
    public function getCreateDate() {
        return DateFormat::toWebFormat($this->create_date);
    }
     */
    
}