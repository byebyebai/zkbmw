<?php
/**
 * 标签验证类
 */

namespace app\admin\validate;

class School extends Admin
{
    protected $rule = [
        'schoolName|学校名称'      => 'require',
        'province_id|省份'      => 'require',
        'city_id|城市'      => 'require',
        'schoolDescribe|专业描述'      => 'require',
        'subjects|专业选择'      => 'require',
    ];

    protected $scene = [
        'add'  => ['schoolName','province_id','city_id','schoolDescribe','subjects'],
        'edit'  => ['schoolName','province_id','city_id','schoolDescribe','subjects'],
    ];
}