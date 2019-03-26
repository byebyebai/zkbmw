<?php
/**
 * 标签验证类
 */

namespace app\admin\validate;

class Tag extends Admin
{
    protected $rule = [
        'tag_name|Tag标签'      => 'require'
    ];

    protected $scene = [
        'add'  => ['tag_name'],
    ];
}