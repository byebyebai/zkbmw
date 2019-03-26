<?php
/**
 * 后台文章验证类
 */

namespace app\admin\validate;

class Article extends Admin
{
    protected $rule = [
        'title|标题'      => 'require',
        'category_id|栏目' => 'require',
        'origin|文章来源' => 'require',
        'keywords|关键词'  => 'require',
        'describe|描述' => 'require'
    ];

    protected $message = [
        'title.require'  => '标题不能为空',
        'category_id.require'  => '请选择栏目',
        'origin.require'  => '文章来源不能为空',
        'keywords.require'  => '文章关键词不能为空',
        'describe.require'  => '请输入文章描述',
    ];

    protected $scene = [
        'add'   => ['title', 'category_id', 'origin', 'keywords', 'describe', 'content'],
        'edit'  => ['title', 'category_id', 'origin', 'keywords', 'describe', 'content'],
    ];
}