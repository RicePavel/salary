<?php

namespace app\models;

use yii\db\ActiveRecord;

class Worker extends ActiveRecord {
    
    public function getPosition() {
        return $this->hasOne(Position::className(), ['position_id' => 'position_id']);
    }
    
    public function getUnit() {
        return $this->hasOne(Unit::className(), ["unit_id" => "unit_id"]);
    }
    
}


