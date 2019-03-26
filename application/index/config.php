<?php
/**
 * 前台配置文件
 */

return [
    // 模板参数替换
    'view_replace_str' => [
        '__STATIC__'  => '/static/index',
        '__CSS__'     => '/static/index/css',
        '__JS__'      => '/static/index/js',
        '__IMAGES__'  => '/static/index/images',
        '__FONTS__'   => '/static/index/fonts',
        '__PLUGINS__' => '/static/index/plugins'
    ],

    'template'                   => [
        'layout_on'       => true,
        'layout_name'     => 'template/layout',
        'layout_item'     => '[__REPLACE__]',
    ],
    //分页配置
    'paginate'                   => [
        'type'      => '\tools\Bearpage',
        'var_page'  => 'page',
        'list_rows' => 10,
    ]

];
