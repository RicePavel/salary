<?php

namespace app\models;

use yii\db\ActiveRecord;

class Employment_type extends ActiveRecord {
    
    public function rules() {
        return [
            ['short_name', 'unique', 'message' => 'Сокращение должно быть уникально! Уже есть элемент с таким сокращением.']
        ];
    }
    
}