<?php

namespace app\index\controller;
use app\admin\model\ArticleCategory;
use app\common\model\Article;
use think\Cache;
use app\common\model\ArticleTagList;
use app\common\model\Tag as TagModel;
use think\exception\HttpException;
class Tag extends Controller{   
  public function index(){
    $tid = request()->param('id');
    $tag_info = TagModel::where('id',$tid)->field('id,tag_name,seo_keyword,seo_describe')->find();
    if(!$tag_info){
      abort(404);
    }

    $aList = ArticleTagList::where('tid',$tid)->field('aid')->select();
    $aList = unique_2d_array_by_key($aList,'tid');
    //tag信息
    
    $ids = '';
    foreach ($aList as $k=> $v) {
      $ids .= $v['aid'] . ',';
    }
    $ids = rtrim($ids,',');

    $seo['title'] = $tag_info['tag_name'];
    $seo['keywords'] = $tag_info['seo_keyword'];
    $seo['desc'] = $tag_info['seo_describe'];

    $list= Article::where('id','in',$ids)->select();
    $this->assign(['list'=>$list,'seo'=>$seo,'province_id'=>0,'tag_name'=>$tag_info['tag_name'],'id'=>$tid]);
    return $this->fetch();
  }

  //栏目页处理静态化
    public function createStaticTag(){
      $cid = $this->request->param()['cid'];

      $tids = '';
      if($cid == 'all'){ //批量更新全部数据
        //查询要更新的tag标签
        $tagsInfo = TagModel::field('id,tag_name,seo_keyword,seo_describe')->select();

      }else{
        if(empty($cid)){
          return $this->jsonData(0,'Tag选择不能为空');
        }
        //指定更新的tag标签
        $tagsInfo = TagModel::where('id',$cid)->field('id,tag_name,seo_keyword,seo_describe')->select();
      }

      $data = [];
      foreach ($tagsInfo as $k => $v) {
        
        $seo['title'] = $v['tag_name'];
        $seo['keywords'] = $v['seo_keyword'];
        $seo['desc'] = $v['seo_describe'];
        $data[$k]['seo'] = $seo; 
        $data[$k]['province_id'] = 0; 
        $data[$k]['tag_name'] = $v['tag_name']; 
        $data[$k]['id'] = $v['id']; 
        $aidList = ArticleTagList::where('tid',$v['id'])->field('aid')->select();
        $aids = '';
        foreach ($aidList as $k1 => $v1) {
          $aids .= $v1['aid'].',';
        }

        $item = Article::where('id','in',rtrim($aids,','))->field('id,title,describe,thumb_img,province_id,create_time')->select(); 
        foreach ($item as $k2 => $v2) {
          $item[$k2]['thumb_img'] = request()->server('REQUEST_SCHEME')."://".request()->host().$v2['thumb_img'];
        }

        $data[$k]['item'] = collection($item)->toArray();

      }

      Cache::set('staticTag',$data);
      return $this->jsonData(200,'数据请求成功！');
    }
    protected $now = 0;
    public function doStaticTag(){
      $list = Cache::get('staticTag');

      if($list){
        $file = './tags';
        if(!file_exists($file)){
            mkdir($file);
        }
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
        //手动分页page
        foreach ($htmlarr as $k => $v) {

          if($k<2){
            $pageHtml = "<a class='".$k."' href='".$host."/tags/{$list[$this->now]['id']}.html'>{$k}</a>";
          }else{
            $pageHtml .=  "<a class='".$k."' href='".$host."/tags/{$list[$this->now]['id']}_".$k.".html'>{$k}</a>";
            $nextHtml = "<a href='".$host."/tags/{$list[$this->now]['id']}_".$k.".html'> 下一页 </a>";
            if($onepage == 1){
              $endPage = $allpages-1;
            }else{
              $endPage = $allpages;
            }
            $endHtml = "<a href=".$host."/tags/{$list[$this->now]['id']}_".$endPage.".html> 尾页 </a>";
          }
        }

        foreach ($htmlarr as $k => $v) {
          $this->assign([
            'list'=>$list[$this->now],
            'seo'=>$list[$this->now]['seo'],
            'id'=>$list[$this->now]['id'],
            'tag_name'=>$list[$this->now]['tag_name'],
            'province_id'=>$list[$this->now]['province_id'],
            'item'=>$v,
            'page'=>$pageHtml,
            'nextHtml'=>$nextHtml,
            'endHtml'=>$endHtml,
          ]);
          $result = $this->fetch('index'); 
          // foreach ($v as $k1 => $v1) {
            
            if($k<2){
              if(file_exists($file.'/'.$list[$this->now]['id'].'.html')){
                  unlink($file.'/'.$list[$this->now]['id'].'.html');
              }
              file_put_contents($file.'/'.$list[$this->now]['id'].'.html',$result);
            }else{
              if(file_exists($file.'/'.$list[$this->now]['id'].'_'.$k.'.html')){
                  unlink($file.'/'.$list[$this->now]['id'].'_'.$k.'.html');
              }
              file_put_contents($file.'/'.$list[$this->now]['id'].'_'.$k.'.html',$result);
            }
          // }          
        }

        unset($list[$this->now]);
        $list = array_values($list);
        Cache::rm('staticTag');
        //重新写入缓存
        if(empty($list)){

            Cache::set('staticTag',null);

        }else{

            Cache::set('staticTag',$list);

        }

        return $this->jsonData(100,'数据正在更新',Cache::get('staticTag'));
      
      }else{
        return $this->jsonData(200,'没有数据了');
      }
    }
}