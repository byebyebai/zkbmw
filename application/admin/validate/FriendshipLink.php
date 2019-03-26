<?php
/**
 * 标签验证类
 */

namespace app\admin\validate;

class FriendshipLink extends Admin
{
    protected $rule = [
        'name|名称不能为空'      => 'require',
        'url|链接地址'      => 'require',
    ];

    protected $scene = [
        'add'  => ['name','url'],
        'edit'  => ['name','url'],
    ];
}