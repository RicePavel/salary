<?php

namespace app\models;

use yii\db\ActiveRecord;

class Timetable_worker extends ActiveRecord {
    
    public function getTimetable_rows() {
        return $this->hasMany(Timetable_row::className(), ['timetable_worker_id' => 'timetable_worker_id'])->orderBy('number');
    }
    
    public function getWorker() {
        return $this->hasOne(Worker::className(), ['worker_id' => 'worker_id']);
    }
    
}

