<?php

namespace app\models;

use yii\db\ActiveRecord;
use app\helpers\DateFormat;

class Timetable extends ActiveRecord {
    
    public function rules() {
        return [
            ['unit_id', 'required', 'message' => 'подразделение должно быть задано']
        ];
    }
    
    public function getUnit() {
        return $this->hasOne(Unit::className(), ['unit_id' => 'unit_id']);
    }
    
    public function getTimetable_workers() {
        return $this->hasMany(Timetable_worker::className(), ['timetable_id' => 'timetable_id']);
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