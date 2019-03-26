<?php

namespace app\common\model;
use traits\model\SoftDelete;
class School extends Model
{
    use SoftDelete;
    protected $name = 'school';
    protected $autoWriteTimestamp = true;

}
