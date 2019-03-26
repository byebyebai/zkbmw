<?php

namespace app\common\model;
use traits\model\SoftDelete;
class Article extends Model
{
    use SoftDelete;
    protected $name = 'article';
    protected $autoWriteTimestamp = true;

}
