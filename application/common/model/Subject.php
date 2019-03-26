<?php

namespace app\common\model;
use traits\model\SoftDelete;

class Subject extends Model
{
    use SoftDelete;
    protected $name = 'subject';
    protected $autoWriteTimestamp = true;

}
