<?php
namespace App\Model;

use PhalApi\Model\DataModel;

class DietDiary extends DataModel
{
    public function getTableName(){
        return 'diet_diary';
    }
    
}