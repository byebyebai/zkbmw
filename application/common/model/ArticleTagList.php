<?php
/**
 * 前台用户等级类
 * @author yupoxiong<i@yufuping.com>
 */

namespace app\common\model;
use traits\model\SoftDelete;
class ArticleTagList extends Model
{
    use SoftDelete;
    protected $name = 'article_tag_list';
    protected $autoWriteTimestamp = true;

}
