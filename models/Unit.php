<?php

namespace app\models;

use yii\db\ActiveRecord;

class Unit extends ActiveRecord {
    
    private $children = [];
    
    public function addChild($unit) {
        $this->children[] = $unit;
    }
    
    public function getChildren() {
        return $this->children;
    }
    
}