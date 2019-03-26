<?php
/**
 * 后台文章验证类
 */

namespace app\admin\validate;

class ArticleCategory extends Admin
{
    protected $rule = [
        'category_name|栏目名称'      => 'require',
        'keywords|关键词'      => 'require',
        'desc|描述'      => 'require',

    ];

    protected $message = [
        'name.require'  => '栏目名称不能为空',
        'keywords.require'  => '关键词不能为空',
        'desc.require'  => '描述不能为空'
    ];

    protected $scene = [
        'add'   => ['category_name', 'keywords', 'desc'],
        'edit'  => ['id','category_name', 'keywords', 'desc']
    ];
}