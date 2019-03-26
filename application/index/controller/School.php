<?php
/**
 * 栏目页-学校
 * @author 
 */
namespace app\index\controller;
use think\Cache;
use app\common\model\School as SchoolModel;
use app\common\model\SchoolSubject;
use app\common\model\Article;
use app\common\model\Subject;
use app\admin\model\District;
use tools\Pinyin;
class School extends Controller
{ 
  protected $PageNum = 11; //每页显示的数据
  /**
   * [index description]  院校主页
   * @return [type] [description]
   */
  public function index(){
    $model = new SchoolModel();
    $pageParam = ['query' => []];

    if(request()->param()){
      $param = request()->param();
      $where = [];
      if(isset($param['pid'])){
        $where['province_id'] = $param['pid'];
      }
      if(isset($param['level'])){
        $where['level'] = $param['level'];
      }
      if(isset($param['keywords'])){
        $where['schoolName'] = ['like',"%".$param['keywords']."%"];
      }

      $list = SchoolModel::where($where)->paginate(15,false,['query'=>request()->param()]);

      if(isset($param['keywords'])){
        $where['keywords'] = $param['keywords'];
      }

      $this->assign('where',$where);
    }else{
      $list = SchoolModel::paginate(15);
    }
    
    $dist = District::where('level',1)->where('id','<',32)->select();
    $seo['title'] = '院校列表';
    $seo['keywords'] = '院校列表';
    $seo['desc'] = '院校列表';

    $this->assign(['seo'=>$seo,'area'=>false,'list'=>$list,'dist'=>$dist,'nav'=>'school']);
    return $this->fetch();
  }
  //院校主页 
  public function detail(){
    $id = request()->param('id');
    //学校信息
    $host = request()->server('REQUEST_SCHEME')."://".request()->host();
    $schoolInfo = SchoolModel::where('schoolId',$id)->find();
    if(!$schoolInfo){
      throw new \think\exception\HttpException(404, '页面不存在');
    } 
    $schoolInfo['schoolLogo'] = $host.$schoolInfo['schoolLogo'];
    $schoolInfo['schoolImage'] = $host.$schoolInfo['schoolImage'];
    $schoolInfo['area'] = getAreaName($schoolInfo['province_id']);
    //学校seo标题
    $seo['title'] = $schoolInfo['schoolName'];
    $seo['keywords'] = $schoolInfo['seo_keywords'];
    $seo['desc'] = $schoolInfo['seo_description'];
    //获取学校专业
    $subjects = SchoolSubject::where('schoolId',$schoolInfo['schoolId'])->select();
    foreach ($subjects as $k => $v) {
      $subjectInfo = Subject::where('subjectId',$v['subjectId'])->field('subjectName,subjectCode,subjectLevel,subjectType')->find();
      $subjects[$k]['subject_name'] = $subjectInfo['subjectName'];
      $subjects[$k]['subjectCode'] = $subjectInfo['subjectCode'];
      $subjects[$k]['subjectLevel'] = $subjectInfo['subjectLevel'];
      $subjects[$k]['subjectType'] = $subjectInfo['subjectType'];
    }
    //学校动态
    $newSchoolArticle = Article::where('sid',$schoolInfo['schoolId'])->where('category_id',SCHOOLNEWS)->limit(10)->field('id,province_id,title,add_time')->order('add_time desc')->select();
    //招生简章
    $newSchoolArticle_jz = Article::where('sid',$schoolInfo['schoolId'])->where('category_id',SCHOOLJZ)->limit(10)->field('id,province_id,title,add_time')->order('add_time desc')->select();
    //毕业证书
    $diplomas = Article::where('sid',$schoolInfo['schoolId'])->where('category_id',BIYEZHENGSHU)->limit(4)->field('id,province_id,title,add_time,thumb_img')->order('add_time desc')->select();
    foreach ($diplomas as $k => $v) {
      $diplomas[$k]['thumb_img'] = $host.$diplomas[$k]['thumb_img'];
    }

    $this->assign(['schoolInfo'=>$schoolInfo,'seo'=>$seo,'subjects'=>$subjects,'newSchoolArticle'=>$newSchoolArticle,'newSchoolArticle_jz'=>$newSchoolArticle_jz,'diplomas'=>$diplomas]);
    return $this->fetch();
  }
  //院校动态
  public function school_news(){
    $id = request()->param('id');
    $host = request()->server('REQUEST_SCHEME')."://".request()->host();
    
    $schoolInfo = SchoolModel::where('schoolId',$id)->find();
    if(!$schoolInfo){
      throw new \think\exception\HttpException(404, '页面不存在');
    } 
    $schoolInfo['schoolLogo'] = $host.$schoolInfo['schoolLogo'];
    $schoolInfo['schoolImage'] = $host.$schoolInfo['schoolImage'];
    $schoolInfo['area'] = getAreaName($schoolInfo['province_id']);
    //学校seo标题
    $seo['title'] = $schoolInfo['schoolName'];
    $seo['keywords'] = $schoolInfo['seo_keywords'];
    $seo['desc'] = $schoolInfo['seo_description'];
    $list = Article::where('sid',$id)->where('category_id',SCHOOLNEWS)->order('add_time desc')->field('id,title,thumb_img,province_id,describe,add_time')->paginate($this->PageNum);
    $this->assign(['schoolInfo'=>$schoolInfo,'seo'=>$seo,'list'=>$list,'province_id' => $schoolInfo['province_id']]);
    return $this->fetch('school_news');
  }
  //院校简章
  public function school_jz(){
    $id = request()->param('id');

    $host = request()->server('REQUEST_SCHEME')."://".request()->host();
    
    $schoolInfo = SchoolModel::where('schoolId',$id)->find();
    if(!$schoolInfo){
      throw new \think\exception\HttpException(404, '页面不存在');
    } 
    $schoolInfo['schoolLogo'] = $host.$schoolInfo['schoolLogo'];
    $schoolInfo['schoolImage'] = $host.$schoolInfo['schoolImage'];
    $schoolInfo['area'] = getAreaName($schoolInfo['province_id']);
    //学校seo标题
    $seo['title'] = $schoolInfo['schoolName'];
    $seo['keywords'] = $schoolInfo['seo_keywords'];
    $seo['desc'] = $schoolInfo['seo_description'];
    $list = Article::where('sid',$id)->where('category_id',SCHOOLJZ)->order('add_time desc')->field('id,title,thumb_img,province_id,describe,add_time')->paginate($this->PageNum);
    $this->assign(['schoolInfo'=>$schoolInfo,'seo'=>$seo,'list'=>$list,'province_id' => $schoolInfo['province_id']]);
    return $this->fetch('school_jz');
  }
  //院校科目
  public function school_subject(){
    $id = request()->param('id');
    $host = request()->server('REQUEST_SCHEME')."://".request()->host();
    
    $schoolInfo = SchoolModel::where('schoolId',$id)->find();
    if(!$schoolInfo){
      throw new \think\exception\HttpException(404, '页面不存在');
    } 
    $schoolInfo['schoolLogo'] = $host.$schoolInfo['schoolLogo'];
    $schoolInfo['schoolImage'] = $host.$schoolInfo['schoolImage'];
    $schoolInfo['area'] = getAreaName($schoolInfo['province_id']);
    //学校seo标题
    $seo['title'] = $schoolInfo['schoolName'];
    $seo['keywords'] = $schoolInfo['seo_keywords'];
    $seo['desc'] = $schoolInfo['seo_description'];
    $list = Article::where('sid',$id)->where('category_id',ZHAOSHENGZHUANYE)->order('add_time desc')->field('id,title,thumb_img,province_id,describe,add_time')->paginate($this->PageNum);

    $this->assign(['schoolInfo'=>$schoolInfo,'seo'=>$seo,'list'=>$list]);
    return $this->fetch('school_subject');
  }
  public function createStaticSchool(){
    $id = request()->param('cid');
    $data = [];
    $host = request()->server('REQUEST_SCHEME')."://".request()->host();
    //主页
    if($id == 'all'){
      $school = SchoolModel::field('schoolId,schoolName,schoolImage,schoolLogo,province_id,seo_keywords,seo_description')->select();
    }else{
      $school = SchoolModel::where('schoolId',$id)->field('schoolId,schoolName,schoolImage,schoolLogo,province_id,seo_keywords,seo_description')->select();
    }
    if(!$school){
      throw new \think\exception\HttpException(404, '页面不存在');
    }
    $list = collection($school)->toArray();
    //组装要更新的url列表
    foreach ($list as $k => $v) {
      $url = [];
      //院校动态
      array_push($url, $host.'/index.php/school/'.$v['schoolId'].'.html');
      $school_news = Article::where('sid',$id)->where('category_id',SCHOOLNEWS)->order('add_time desc')->field('id,title,thumb_img,province_id,describe,add_time')->select();
      //栏目
      for ($i=1; $i <= ceil(count($school_news)/$this->PageNum); ++$i) { 

        if($i==1){
          array_push($url, $host.'/index.php/school_news/'.$v['schoolId'].'.html');
        }else{
          array_push($url, $host.'/index.php/school_news/'.$v['schoolId'].'.html?page='.$i);
        }

      }
      //院校简章
      $school_jz = Article::where('sid',$id)->where('category_id',SCHOOLJZ)->order('add_time desc')->field('id,title,thumb_img,province_id,describe,add_time')->select();
      for ($i=1; $i <= ceil(count($school_jz)/$this->PageNum); ++$i) { 
        if($i==1){
          array_push($url, $host.'/index.php/school_jz/'.$v['schoolId'].'.html');
        }else{
          array_push($url, $host.'/index.php/school_jz/'.$v['schoolId'].'.html?page='.$i);
        }
      }
      //招生专业
      $school_subject = Article::where('sid',$id)->where('category_id',ZHAOSHENGZHUANYE)->order('add_time desc')->field('id,title,thumb_img,province_id,describe,add_time')->paginate(1);
      for ($i=1; $i <= ceil(count($school_subject)/$this->PageNum); ++$i) { 
        if($i==1){
          array_push($url, $host.'/index.php/school_subject/'.$v['schoolId'].'.html');
        }else{
          array_push($url, $host.'/index.php/school_subject/'.$v['schoolId'].'.html?page='.$i);
        }
      }
      $data = $url;
    }
    Cache::set('school',$data);

    return $this->jsonData(200,'数据请求成功！',['cid'=>$id]);
  }

  public function doStaticSchool(){
    $data = Cache::get('school');

    $param = request()->param();
    
    if(empty($data[$param['index']])){
      return json(['status'=>200,'msg'=>'更新完成']);
    }

    $host = request()->server('REQUEST_SCHEME')."://".request()->host();

    $preg = "/(\w*)\/([0-9]*)\.html/i";

    preg_match($preg,$data[$param['index']],$match);
    $html = file_get_contents($data[$param['index']]);

    if($match[1] == 'school'){ //主页栏目

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
