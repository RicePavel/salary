<?php

namespace app\models;

use yii\db\ActiveRecord;

class Timetable_row extends ActiveRecord {

    public function getDays_info() {
        return $this->hasMany(Day_info::className(), ['timetable_row_id' => 'timetable_row_id']);
    }

}