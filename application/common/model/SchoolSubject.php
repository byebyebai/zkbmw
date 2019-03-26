<?php

namespace app\common\model;
use traits\model\SoftDelete;
class SchoolSubject extends Model
{
    use SoftDelete;
    protected $name = 'school_subject';
    protected $autoWriteTimestamp = true;

}
