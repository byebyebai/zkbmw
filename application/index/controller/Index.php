<?php
/**
 * 网站首页
 *
 */

namespace app\index\controller;

use Lcobucci\JWT\Builder as TokenBuilder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use think\Cache;
use tools\Pinyin;
use app\common\model\Article;
use app\admin\model\ArticleCategory;
use app\common\model\ArticleTagList;
use app\common\model\Tag;
use app\admin\model\DistrictConfig;
use app\common\model\Subject;
class Index extends Controller
{
    public function index()
    {

        //自考报考 取最新的12条数据
        $article = new Article;
        $pinyin = new Pinyin();
        $news = $article->where('status',1)->order('add_time')->field('id,title,province_id,add_time')->limit(12)->select();
        foreach ($news as $k => $v) {

            if($v['province_id'] == 8){
                $news[$k]['area'] = mb_substr(getAreaName($v['province_id']), 0,3,'utf-8');
            }else{
                $news[$k]['area'] = mb_substr(getAreaName($v['province_id']), 0,2,'utf-8');
            }
            //访问路径目录
            $news[$k]['dir'] = $pinyin->getAllPY($news[$k]['area']);
        }

        //获取专业 15条
        $subjects = Subject::order('sort desc')->field('subjectId,subjectName,subjectImage')->limit(15)->select();
        //获取报考资讯栏目下的文章
        $news_bk = $article->where('status',1)->where('category_id',17)->order('add_time')->field('id,title,province_id,add_time')->limit(20)->select();
        foreach ($news_bk as $k => $v) {

            if($v['province_id'] == 8){
                $news_bk[$k]['area'] = mb_substr(getAreaName($v['province_id']), 0,3,'utf-8');
            }else{
                $news_bk[$k]['area'] = mb_substr(getAreaName($v['province_id']), 0,2,'utf-8');
            }
            //访问路径目录
            $news_bk[$k]['dir'] = $pinyin->getAllPY($news_bk[$k]['area']);
        }
        $ohter = DistrictConfig::where('pid',0)->find();//全国seo信息设置 

        //关闭继承模板
        $this->assign(['news'=>$news,'subjects'=>$subjects,'news_bk'=>$news_bk,'ohter'=>$ohter,'nav'=>'index']);
        $this->view->engine->layout(false);
        return $this->fetch();
    }

    public function hello()
    {
        // return 'hello';
        $signer = new Sha256();
        $token = (new TokenBuilder())
        ->setIssuedAt(time())
        ->setNotBefore(time())
        ->setExpiration(time() + 3600)
        ->set('uid',3)
        ->sign($signer, config('app_key'))
        ->getToken();
        Cache::set('token',$token,600);
        $token = Cache::get('token');
        echo $token;
    }

    public function testcache(){
        Cache::set('kuangxiang',['kuangxiang'=>'kuangxiang'],30);
        
    }
    public function aaa(){
        $py = new Pinyin();
        echo $py->getAllPY("江西"); //shuchuhanzisuoyoupinyin
    }

    protected $count = 0;
    protected $now = 0;
    public function updateAllArticle(){

        $this->count = Article::count();

        if(empty(Cache::get('staticArticle'))){
            $list = Article::all();
            $list = collection($list)->toArray();
            Cache::set('staticArticle',$list);
        }

        $res = $this->ArticleCreateHtml();

        if($res){
            $result['msg'] = $res['msg'];
            
            $result['count'] = $this->count;
            $result['id'] = $res['id'];
            $data = Cache::get('staticArticle');
            $result['status'] = $res['status'];
            
            if(empty($data)){
                $result['now'] = $this->count;
                $result['status'] = 1;
                $result['nowLoading'] = 100;
            }else{
                $result['now'] = $this->count-count(Cache::get('staticArticle'));
                $result['nowLoading'] = round($result['now']/$this->count,4)*100;
            }
            
            return json($result);
        }
    }
    /**静态外部调用
     * [ArticleCreateHtml description] 
     */
    protected function ArticleCreateHtml(){

        $list = Cache::get('staticArticle');
      
        $arr = [];
        if(!empty($list)){
            $file = './'.c2PyByAreaid($list[$this->now]['province_id']);
            if(!file_exists($file)){
                mkdir($file);
            }
            //上一篇下一篇
            $pre = Article::where('id','<',$list[$this->now]['id'])->order(['id'=>'desc'])->field('id,province_id,title')->find();
    
            $next = Article::where('id','>',$list[$this->now]['id'])->order(['id'=>'asc'])->field('id,province_id,title')->find();

            //当前栏目面包屑
            //子栏目
            $category = ArticleCategory::where('id',$list[$this->now]['id'])->field('id,category_name,pid,province_id')->find();
            if($category){
                $category = $category->toArray();
                $this->getParentCategory($category);
                rsort($this->categorys);
            }
            

            //获取文章标签
            $tag = ArticleTagList::where('aid',$list[$this->now]['id'])->field('aid,tid')->select();
            $tags = [];
            foreach ($tag as $k => $v) {
                $tags[$k]['tag_name'] = Tag::where('id',$v['tid'])->value('tag_name');
                $tags[$k]['aid'] = $v['aid'];
                $tags[$k]['tid'] = $v['tid'];
            }
            $allTagsResult = Tag::field('id,tag_name')->select();
            $allTags = [];
            foreach ($allTagsResult as $k => $v) {
                $allTags[$k]['id'] = $v['id'];
                $allTags[$k]['tag_name'] = $v['tag_name'];
                $allTags[$k]['color'] = rand(1,5);
            }
            //相关阅读
            $aboutArticles = Article::where('category_id',$list[$this->now]['category_id'])->field('id,province_id,title')->limit(10)->select();
            $province_id = $list[$this->now]['province_id'];
            $seo['title'] = $list[$this->now]['title'];
            $seo['keywords'] = $list[$this->now]['keywords'];
            $seo['desc'] = $list[$this->now]['describe'];
            $this->assign(['province_id'=>$province_id,'info'=>$list[$this->now],'categorys'=>$this->categorys,'pre'=>$pre,'next'=>$next,'tags'=>$tags,'allTags'=>$allTags,'aboutArticles'=>$aboutArticles,'seo'=>$seo]);
            $result = $this->fetch('articleTemplate/index'); //此处引入前端模板

            $arr['msg'] = 'id:'.$list[$this->now]['id'].'，目录：'.$file.',标题:'.$list[$this->now]['title'].'操作成功';
            $arr['status'] = 200;
            $arr['id'] = $list[$this->now]['id'];
            if(file_exists($file.'/info_'.$list[$this->now]['id'].'.html')){
                unlink($file.'/info_'.$list[$this->now]['id'].'.html');
            }
   
            file_put_contents($file.'/info_'.$list[$this->now]['id'].'.html',$result);
            //删除该记录
            unset($list[$this->now]);
            $list = array_values($list);
            Cache::rm('staticArticle');
            //重新写入缓存
            if(empty($list)){
                Cache::set('staticArticle',null);
            }else{
                Cache::set('staticArticle',$list);
            }
            
        }else{
            $arr['msg'] = '全部生成成功！';
            $arr['id'] = '';
            $arr['status'] = 1;
            //删除缓存
            Cache::rm('staticArticle');
        }
        return $arr;
    }
    protected $index = 0;
    protected $categorys = [];
    protected function getParentCategory($array = []){
        
        if($array){
   
            $this->categorys[$this->index] = $array;
            
            $res = ArticleCategory::where('id',$array['pid'])->field('id,category_name,pid,province_id')->find();
            $category = [];
            if($res){
                $category['id'] = $res['id'];
                $category['category_name'] = $res['category_name'];
                $category['pid'] = $res['pid'];
                $category['province_id'] = $res['province_id'];
                $this->index++;
            }

            $this->getParentCategory($category);

        }
         
    }

    public function tt(){
        $categorys = ArticleCategory::where('id',1)->field('id,category_name,pid')->find()->toArray();
        $this->getParentCategory($categorys);
        print_r($this->categorys);

    }

    public function search(){
        if(request()->param()){
            $param = request()->param();
            $where = [];

            if(isset($param['keywords'])){
                $where['title'] = ['like',"%".$param['keywords']."%"];
            }

            $item = Article::where($where)->field('id,title,add_time,thumb_img,province_id')->paginate(15,false,['query'=>request()->param()]);
            $where['keywords'] = $param['keywords'];
            $seo['title'] =$param['keywords'];
            $seo['keywords'] = $param['keywords'];
            $seo['desc'] = $param['keywords'];
            $page = $item->render();
            $this->assign(['item'=>$item,'where'=>$where,'seo'=>$seo,'page'=>$page]);

        }

        return $this->fetch();
    }

    public function sitemap(){
        echo 'sitemap';
    }
}