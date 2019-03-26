<?php
namespace app\admin\controller;
use app\common\model\Article;
use think\Cache;
use app\common\model\School;
use app\common\model\Subject;
use app\admin\model\ArticleCategory;
use app\common\model\Tag;
class Updatedata extends Base
{


    protected $count = 0;
    protected $now = 0;

    public function index(){
        return $this->fetch();
    }
    //栏目更新界面
    public function cate(){
        $list = ArticleCategory::where('pid',0)->field('id,category_name')->select();
        $this->assign('list',$list);
        return $this->fetch();
    }
    public function school(){
        $list = School::all();
        $this->assign('list',$list);
        return $this->fetch();

    }
    public function rmCache(){
        Cache::rm('staticArticle');
    
        if(empty(Cache::get('staticArticle'))){
            return 1;
        }else{
            return 0;
        }
    }
    public function subject(){
        $list = Subject::all();
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function tag(){
        $list = Tag::all();
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function test(){
        return $this->fetch();
    }
}