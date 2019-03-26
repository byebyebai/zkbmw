<?php
/**
 * 栏目页-学校
 * @author 
 */
namespace app\index\controller;
use think\Cache;
use app\common\model\Subject as SubjectModel;
use app\common\model\Article;
use app\admin\model\ArticleCategory;
class Subject extends Controller
{ 
  protected $PageNum = 11; //每页显示的数据
  public function index(){
    $seo['title'] = '专业列表';
    $seo['keywords'] = '专业列表';
    $seo['desc'] = '专业列表';
    $pageParam = ['query' => []];

    if(request()->param()){
      $where = [];
      $param = request()->param();
     
      if(isset($param['type'])){
        $where['subjectType'] = $param['type'];
      }
      if(isset($param['level'])){
        $where['subjectLevel'] = $param['level'];
      }
      if(isset($param['keywords'])){
        $where['subjectName'] = ['like',"%".$param['keywords']."%"];
      }
      $subjects = SubjectModel::where($where)->paginate(15,false,['query'=>request()->param()]);
      if(isset($param['keywords'])){
        $where['keywords'] = $param['keywords'];
      }
      
      $this->assign('where',$where);
    }else{
      $subjects = SubjectModel::paginate(15);
    }

    $this->assign(['seo'=>$seo,'area'=>false,'subjects'=>$subjects,'nav'=>'subject']);
    return $this->fetch();
  }
  //专业主页 
  public function detail(){
    $id = request()->param('id');
    
    //学校信息
    $host = request()->server('REQUEST_SCHEME')."://".request()->host();
    $subjectInfo = SubjectModel::where('subjectId',$id)->find();
    
    if(!$subjectInfo){
      throw new \think\exception\HttpException(404, '页面不存在');
    } 
    //专业主页 id 14
    $articleInfo = ArticleCategory::where('id',13)->field('image,category_name')->find();
    $articleInfo['category_name'] = $host.$articleInfo['category_name'];
    $subjectInfo['subjectImage'] = $host.$subjectInfo['subjectImage'];
    //专业主页-专业动态
    $list_dt = Article::where('category_id',14)->field('id,title,province_id,thumb_img,add_time')->limit(10)->select();
    foreach ($list_dt as $k => $v) {
      $list_dt[$k]['thumb_img'] = $host.$v['thumb_img'];
    }
    //专业主页-考试安排
    $list_ap = Article::where('category_id',15)->field('id,title,province_id,thumb_img,add_time')->limit(10)->select();
    //专业主页-专业课程安排
    $list_kcap = Article::where('category_id',16)->field('id,title,province_id,thumb_img,add_time')->limit(10)->select();
    //专业seo标题
    $seo['title'] = $subjectInfo['subjectName'];
    $seo['keywords'] = $subjectInfo['seo_keywords'];
    $seo['desc'] = $subjectInfo['seo_description'];

    $this->assign(['subjectInfo'=>$subjectInfo,'seo'=>$seo,'list_dt'=>$list_dt,'list_ap'=>$list_ap,'articleInfo'=>$articleInfo,'list_kcap'=>$list_kcap]);
    return $this->fetch('detail');
  }
  //报考科目
  public function subject_bk(){
    $id = request()->param('id');
    $host = request()->server('REQUEST_SCHEME')."://".request()->host();
    
    $subjectInfo = SubjectModel::where('subjectId',$id)->find();

    if(!$subjectInfo){
      throw new \think\exception\HttpException(404, '页面不存在');
    }
    //学校seo标题
    $seo['title'] = $subjectInfo['subjectName'];
    $seo['keywords'] = $subjectInfo['seo_keywords'];
    $seo['desc'] = $subjectInfo['seo_description'];
    $list_bkkm = Article::where('category_id',10)->where('type',2)->order('add_time desc')->field('id,title,thumb_img,province_id,describe,add_time')->paginate($this->PageNum);

    $this->assign(['list_bkkm'=>$list_bkkm,'seo'=>$seo,'subjectInfo'=>$subjectInfo]);
    return $this->fetch();
  }
  public function subject_jy(){
    $id = request()->param('id');

    $host = request()->server('REQUEST_SCHEME')."://".request()->host();
    
    $subjectInfo = SubjectModel::where('subjectId',$id)->find();

    if(!$subjectInfo){
      throw new \think\exception\HttpException(404, '页面不存在');
    }
    //学校seo标题
    $seo['title'] = $subjectInfo['subjectName'];
    $seo['keywords'] = $subjectInfo['seo_keywords'];
    $seo['desc'] = $subjectInfo['seo_description'];
    $list_jy = Article::where('category_id',11)->order('add_time desc')->field('id,title,thumb_img,province_id,describe,add_time')->paginate($this->PageNum);

    $this->assign(['list_jy'=>$list_jy,'seo'=>$seo,'subjectInfo'=>$subjectInfo]);
    return $this->fetch();
  }
  
  public function subject_lc(){
    $id = request()->param('id');

    $host = request()->server('REQUEST_SCHEME')."://".request()->host();
    
    $subjectInfo = SubjectModel::where('subjectId',$id)->find();

    if(!$subjectInfo){
      throw new \think\exception\HttpException(404, '页面不存在');
    }
    //学校seo标题
    $seo['title'] = $subjectInfo['subjectName'];
    $seo['keywords'] = $subjectInfo['seo_keywords'];
    $seo['desc'] = $subjectInfo['seo_description'];
    $list_lc = Article::where('category_id',12)->order('add_time desc')->field('id,title,thumb_img,province_id,describe,add_time')->paginate($this->PageNum);

    $this->assign(['list_lc'=>$list_lc,'seo'=>$seo,'subjectInfo'=>$subjectInfo]);
    return $this->fetch();
  }

  public function createStaticSubject(){
    $id = request()->param('cid');
    
    $data = [];
    $host = request()->server('REQUEST_SCHEME')."://".request()->host();
    //主页
    if($id == 'all'){
      $subject = SubjectModel::field('subjectId,subjectName,subjectImage,subjectDescribe,subjectType,seo_keywords,seo_description')->select();
    }else{
      $subject = SubjectModel::where('subjectId',$id)->field('subjectId,subjectName,subjectImage,subjectDescribe,subjectType,seo_keywords,seo_description')->select();
    }
    if(!$subject){
      throw new \think\exception\HttpException(404, '页面不存在');
    }
    $list = collection($subject)->toArray();

    //组装要更新的url列表
    foreach ($list as $k => $v) {
      $url = [];
      //院校动态
      array_push($url, $host.'/index.php/subject/'.$v['subjectId'].'.html');

      //报考科目
      $subject_bk = Article::where('category_id',10)->order('add_time desc')->field('id,title,thumb_img,province_id,describe,add_time')->select();
      //栏目
      for ($i=1; $i <= ceil(count($subject_bk)/$this->PageNum); ++$i) { 

        if($i==1){
          array_push($url, $host.'/index.php/subject_bk/'.$v['subjectId'].'.html');
        }else{
          array_push($url, $host.'/index.php/subject_bk/'.$v['subjectId'].'.html?page='.$i);
        }

      }
      //就业前景
      $subject_jy = Article::where('category_id',11)->order('add_time desc')->field('id,title,thumb_img,province_id,describe,add_time')->select();
      for ($i=1; $i <= ceil(count($subject_jy)/$this->PageNum); ++$i) { 
        if($i==1){
          array_push($url, $host.'/index.php/subject_jy/'.$v['subjectId'].'.html');
        }else{
          array_push($url, $host.'/index.php/subject_jy/'.$v['subjectId'].'.html?page='.$i);
        }
      }
      //毕业流程
      $school_subject = Article::where('category_id',12)->order('add_time desc')->field('id,title,thumb_img,province_id,describe,add_time')->paginate(1);
      for ($i=1; $i <= ceil(count($school_subject)/$this->PageNum); ++$i) { 
        if($i==1){
          array_push($url, $host.'/index.php/subject_lc/'.$v['subjectId'].'.html');
        }else{
          array_push($url, $host.'/index.php/subject_lc/'.$v['subjectId'].'.html?page='.$i);
        }
      }
      $data = $url;
    }

    if(!empty(Cache::get('subject'))){
      Cache::set('subject',$data);
    }
    
    return $this->jsonData(200,'数据请求成功！',['cid'=>$id]);
  }

  public function doStaticSubject(){
    $data = Cache::get('subject');

    $param = request()->param();
    
    if(empty($data[$param['index']])){
      return json(['status'=>200,'msg'=>'更新完成']);
    }

    $host = request()->server('REQUEST_SCHEME')."://".request()->host();

    $preg = "/(\w*)\/([0-9]*)\.html/i";

    preg_match($preg,$data[$param['index']],$match);
    $html = file_get_contents($data[$param['index']]);

    if($match[1] == 'subject'){ //主页栏目

      $path = './'.$match[1].'/';
      if(!file_exists($path)){
        mkdir($path);
      }
      $filename = $path.'/'.$param['cid'].'.html';
      if(file_exists($filename)){
          unlink($filename);
      }
      file_put_contents($filename,$html);
    }else{

      $htmlpreg = "/(\w*)\/([0-9]*)\.html\?page=([0-9]*)/i";
      //匹配当前页面所有的分页信息
      preg_match_all($htmlpreg,$html,$matchs);

      if(strpos($data[$param['index']],'page') !== false){
        preg_match($htmlpreg,$data[$param['index']],$urls);
        for($i=0;$i<count($matchs[0]);++$i){

          $path = './'.$matchs[1][$i].'/';
          if(!file_exists($path)){
            mkdir($path);
          }
          if($matchs[3][$i] == 1){
            $replace = $host.'/'.$matchs[1][$i].'/'.$param['cid'].'.html';
          
          }else{
            $replace = $host.'/'.$matchs[1][$i].'/'.$param['cid'].'_'.$matchs[3][$i].'.html';
          }
          

          $html = str_replace('/index.php/'.$matchs[0][$i],$replace,$html);

        }

        $filename = "./".$urls[1].'/'.$param['cid'].'_'.$urls[3].'.html';

      }else{
        if($matchs[0]){
          for($i=0;$i<count($matchs[0]);++$i){
            $path = './'.$matchs[1][$i].'/';
            if(!file_exists($path)){
              mkdir($path);
            }
            $replace = $host.'/'.$matchs[1][$i].'/'.$param['cid'].'_'.$matchs[3][$i].'.html';
            $filename = "./".$matchs[1][$i].'/'.$param['cid'].'.html';
            $html = str_replace('/index.php/'.$matchs[0][$i],$replace,$html);
          }
        }else{
          $path = './'.$match[1];
          if(!file_exists($path)){
            mkdir($path);
          }
          $filename = $path.'/'.$param['cid'].'.html';
        } 
      }
      if(file_exists($filename)){
          unlink($filename);
      }
      file_put_contents($filename,$html);
    }
    return json(['status'=>1,'msg'=>$filename.'创建成功！','index'=>++$param['index'],'cid'=>$param['cid']]);
  }
}