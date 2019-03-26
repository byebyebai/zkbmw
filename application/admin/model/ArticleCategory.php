<?php
/**
 * 后台模型文章栏目 
 */

namespace app\admin\model;

use app\admin\model\Model;
class ArticleCategory extends Admin
{


	public function getArticleCategoryByWhere($where){
		return $this->where($where)->select();
	}
}