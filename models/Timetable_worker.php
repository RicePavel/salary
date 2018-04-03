<?php

namespace app\models;

use yii\db\ActiveRecord;

class Timetable_worker extends ActiveRecord {
    
    public function getTimetable_elements() {
        return $this->hasMany(Timetable_element::className(), ['timetable_worker_id' => 'timetable_worker_id']);
    }
    
}

