<?php
/**
 * 栏目页-地区
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
use app\admin\model\DistrictConfig;

class Region extends Controller
{ 
  public function index(){
    // print_r(C2PY());exit;
    $name = request()->param('name');
    $res = Cache::get('region');
    
    if(empty($res[$name])){
      $this->matchRoute();
    }else{
      //获取地区信息
      $area = District::where('Id',$res[$name])->find();
      //获取地区seo信息
      $ohter = DistrictConfig::where('pid',$res[$name])->find();
      if($res[$name] == 8){
        $area['name'] = mb_substr($area['name'],0,3,'utf-8');
      }else{
        $area['name'] = mb_substr($area['name'],0,2,'utf-8');
      }
      $area['dir'] = $name;
      
      $news_list = Article::where('province_id',$res[$name])->order('add_time desc')->field('id,title,category_id,describe,add_time')->limit(6)->select();

      //获取地区热门动态文章
      $hot_list = Article::where('province_id',$res[$name])->where('category_id',23)->limit(20)->field('id,title,province_id')->select();
      //获取地区院校6个
      $school_list = SchoolModel::where('province_id',$res[$name])->limit(8)->select();
      //获取该地区8个专业
      $localSchools = SchoolModel::where('province_id',$res[$name])->field('schoolId')->select();
      $localSchoolIds = '';
      foreach ($localSchools as $k => $v) {
        $localSchoolIds .= $v['schoolId'].',';
      }
      $subjectArray = SchoolSubject::where('schoolId','in',rtrim($localSchoolIds,','))->field('subjectId')->select();
      $subjectArray = array_unique($subjectArray);
      $subjectIds = '';
      foreach ($subjectArray as $k => $v) {
        $subjectIds .= $v['subjectId'].',';
      }
      $subject = Subject::where('subjectId','in',rtrim($subjectIds))->field('subjectId,subjectName,subjectImage')->limit(8)->select();
      
      $this->view->engine->layout(false);
      $this->assign(['area'=>$area,'ohter'=>$ohter,'news_list'=>$news_list,'hot_list'=>$hot_list,'school_list'=>$school_list,'subject'=>$subject,'province_id'=>$res[$name]]);
      return $this->fetch();
    }
    $this->error('该地区不存在');
  }
}
