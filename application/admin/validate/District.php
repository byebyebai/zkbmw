<?php
/**
 * 验证类
 */

namespace app\admin\validate;

class District extends Admin
{
    protected $rule = [
        'pid|地区选择不能为空'      => 'require',
        'title|标题不能为空'      => 'require',
        'seo_keywords|关键词不能为空'      => 'require',
        'seo_describe|描述不能为空'      => 'require',
        'url|链接地址不能为空'      => 'require',
    ];

    protected $scene = [
        'add'  => ['pid','title','seo_keywords','seo_describe','url'],
        'edit'  => ['pid','title','seo_keywords','seo_describe','url'],
    ];
}