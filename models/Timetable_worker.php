<?php

namespace app\models;

use yii\db\ActiveRecord;

class Timetable_worker extends ActiveRecord {
    
    public function getDays_info() {
        return $this->hasMany(Day_info::className(), ['timetable_worker_id' => 'timetable_worker_id']);
    }
    
}

