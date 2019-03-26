<?php
/**
 * 后台文章验证类
 */

namespace app\admin\validate;

class ArticleTagList extends Admin
{
    protected $rule = [
        'name|帐号'      => 'require|unique:admin_users',
        'parent_id|角色' => 'require',
    ];

    protected $message = [
        'email.email'  => '邮箱格式错误',
        'mobile.regex' => '手机格式错误',
    ];

    protected $scene = [
        'add'   => ['parent_id', 'name', 'password', 'nickname'],
        'edit'  => ['parent_id', 'name', 'nickname'],
        'login' => ['name'=>'require', 'password'],
    ];
}