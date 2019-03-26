<?php
/**
 * 标签验证类
 */

namespace app\admin\validate;

class Major extends Admin
{
    protected $rule = [
        'type_name|名称'      => 'require',
        'status|显示状态'      => 'require',
    ];

    protected $scene = [
        'add'  => ['type_name','status'],
        'edit'  => ['type_name','status'],
    ];
}