<?php

namespace app\models;

use yii\db\ActiveRecord;

class Timetable_element extends ActiveRecord {
    
    public function getEmployment_type() {
        return $this->hasOne(Employment_type::className(), ['employment_type_id' => 'employment_type_id']);
    }
    
}