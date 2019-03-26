<?php
/**
 * 标签验证类
 */

namespace app\admin\validate;

class Subject extends Admin
{
    protected $rule = [
        'subjectName|专业名称'      => 'require',
        'subjectCode|专业代码'      => 'require',
        'subjectDescribe|专业描述'      => 'require',
        'subjectLevel|专业层次'      => 'require',
        'subjectType|专业类型'      => 'require',
    ];

    protected $scene = [
        'add'  => ['subjectName','subjectCode','subjectDescribe','subjectLevel','subjectType'],
        'edit'  => ['subjectName','subjectCode','subjectDescribe','subjectLevel','subjectType']
    ];
}