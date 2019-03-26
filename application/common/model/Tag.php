<?php

namespace app\common\model;
use traits\model\SoftDelete;
class Tag extends Model
{
    use SoftDelete;
    protected $name = 'tag';
    protected $autoWriteTimestamp = true;

}
