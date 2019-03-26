<?php
/**
 * 栏目页
 * @author 
 */
namespace app\index\controller;
use app\admin\model\ArticleCategory;
use app\common\model\Article;
use think\Cache;
class Category extends Controller
{   
    //
    public function index(){

      throw new \think\exception\HttpException(404, '页面不存在');

    	$cid = request()->param('id');
    	$category_info = ArticleCategory::where('id',$cid)->find();
    	$seo['title'] = $category_info['category_name'];
    	$seo['keywords'] = $category_info['keywords'];
    	$seo['desc'] = $category_info['desc'];
      $province_id = $category_info['province_id'];
      $articles = Article::where('category_id',$cid)->field('id,title,describe,province_id,create_time')->select();
    	
      $this->assign([
    		'category_info'=>$category_info,
    		'seo'=>$seo,
        'province_id'=>$province_id,

    	]);
    	return $this->fetch();
    }
    //栏目页处理静态化
    public function createStaticCate(){
      $cid = $this->request->param()['cid'];

      if($cid == 'all'){ //批量更新全部数据
        $list = ArticleCategory::field('id,category_name,pid,keywords,desc,image,province_id')
        ->select();
      }else{

        if(empty($cid)){
          return $this->jsonData(0,'栏目选择不能为空');
        }

        $list = ArticleCategory::where('pid',$cid)
        ->whereOr('id',$cid)
        ->field('id,category_name,pid,keywords,desc,image,province_id')
        ->select();
      }
      
      $list = collection($list)->toArray();
      
      foreach ($list as $k => $v) {
        $seo['title'] = $v['category_name'];
        $seo['keywords'] = $v['keywords'];
        $seo['desc'] = $v['desc'];
        $list[$k]['seo'] = $seo;
        $list[$k]['province_id'] = $v['province_id'];
        $list[$k]['image'] = request()->server('REQUEST_SCHEME')."://".request()->host().$list[$k]['image'];
        $item = Article::where('category_id',$v['id'])->field('id,title,describe,thumb_img,province_id,create_time')->select();
        foreach ($item as $k1 => $v1) {
          $item[$k1]['thumb_img'] = request()->server('REQUEST_SCHEME')."://".request()->host().$item[$k1]['thumb_img'];
        }
        $list[$k]['item'] = collection($item)->toArray();
      }
      Cache::set('staticCate',$list);
      return $this->jsonData(200,'数据请求成功！');
    }

    protected $now = 0;
    public function doActionCreateHtml(){
      $list = Cache::get('staticCate');

      if($list){
        
        if(empty($list[$this->now]['item'])) return $this->jsonData(200,'数据更新完成！');
        //分页内容
        $onepage = 11; //每页显示条数
        $num = count($list[$this->now]['item']);//总条数
        $allpages = ceil ($num / $onepage);
        $htmlarr = [];
        $page = '';
        for($i=1;$i<=$allpages;++$i){
            $html = [];
            if($i==1){

                for($j=0;$j<$i*$onepage;$j++){
                  if($j<$num){
                    $html[$j] = $list[$this->now]['item'][$j];
                  }  
                }
            }else{
                for($j=$onepage*($i-1);$j<$onepage*$i;$j++){
                    if($j<$num){
                        $html[$j] = $list[$this->now]['item'][$j];
                    }
                }
            }
            $htmlarr[$i] = $html;
        }
        $host = request()->server('REQUEST_SCHEME')."://".request()->host();
        $pageHtml = '';
        $preHtml = '';
        $nextHtml = '';
        $endHtml = '';
        //$file = './'.c2PyByAreaid($list[$this->now]['item'][0]['province_id']);
        $file = './cate';
        if(!file_exists($file)){
            mkdir($file);
        }
  
        $count = count($htmlarr);
        if($count < 2){ 
          $pageHtml = "<a class='1' href='".$host."/cate/{$list[$this->now]['id']}.html'>1</a>";
        }else{
          for($i=1;$i<=$count;++$i){
            if($i == 1){
              $pageHtml .= "<a class='".$i."' href='".$host."/cate/".$list[$this->now]['id'].".html'>{$i}</a>";
            }else{
              $pageHtml .= "<a class='".$i."' href='".$host."/cate/{$list[$this->now]['id']}_".$i.".html'>{$i}</a>";
            }
            if(($i+1)>$count){
              $nextHtml = "<a class='".$i."' href='".$host."/cate/{$list[$this->now]['id']}_".$count.".html'>下一页</a>";
            }else{
              $nextHtml = "<a href='".$host."/cate/{$list[$this->now]['id']}_".($i+1).".html'>下一页</a>";
            }

            $endHtml = "<a href='".$host."/cate/{$list[$this->now]['id']}_".$count.".html'> 尾页 </a>";
          }
          
        }

        foreach ($htmlarr as $k => $v) {
          $this->assign([
            'category_info'=>$list[$this->now],
            'seo'=>$list[$this->now]['seo'],
            'province_id'=>$list[$this->now]['item'][0]['province_id'],
            'item'=>$v,
            'page'=>$pageHtml,
            'nextHtml'=>$nextHtml,
            'endHtml'=>$endHtml,
            'nav'=>$list[$this->now]['id']
          ]);
          $result = $this->fetch('index'); 
          if(file_exists($file.'/'.$list[$this->now]['id'].'.html')){
              unlink($file.'/'.$list[$this->now]['id'].'.html');
          }
          if($k<2){
            file_put_contents($file.'/'.$list[$this->now]['id'].'.html',$result);
          }else{
            file_put_contents($file.'/'.$list[$this->now]['id'].'_'.$k.'.html',$result);
          }
       
        }
        unset($list[$this->now]);
        $list = array_values($list);
        Cache::rm('staticCate');
        //重新写入缓存
        if(empty($list)){
            Cache::set('staticCate',null);
        }else{
            Cache::set('staticCate',$list);
        }
        return $this->jsonData(100,'数据正在更新',Cache::get('staticCate'));
        

      }else{
        return $this->jsonData(200,'没有数据了');
      }
    }
}