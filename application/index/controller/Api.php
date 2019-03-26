<?php
/**
 * 异步请求
 */

namespace app\index\controller;

use app\common\model\Article;
use app\admin\model\ArticleCategory;
use app\common\model\ArticleTagList;
use app\common\model\Tag;
use app\common\model\Subject;
use app\common\model\School;
use app\common\model\Enroll;
use app\admin\model\District;
use app\common\model\Register;

use tools\Pinyin;
use think\captcha\Captcha;
use think\Cache;
class Api extends Controller
{

    protected function checkRequest(){
        if(!request()->isPost()){
            return false;
        }else{
            return true;
        }
    }

    //详情-获取热门标签
    public function getTags(){
        
        if(!$this->checkRequest()){
            return $this->jsonData('0','请求不合法');
        }
        $tags = Tag::order('count desc')->select();
        $data = [];
        foreach ($tags as $k => $v) {
            $data[$k]['id'] = $v['id'];
            $data[$k]['tag_name'] = $v['tag_name'];
            $data[$k]['color'] = rand(1,5);
        }

        return $this->jsonData(200,'',$data);

    }
    //详情-获取同省份学校推荐
    public function getSchool(){

        if(!$this->checkRequest()){
            return $this->jsonData('0','请求不合法');
        }
        $province_id = request()->post('province_id');
        if(!isset($province_id)) return $this->jsonData('0','参数有误');
        $schools = School::where('province_id',$province_id)->order('sort desc')->field('schoolName,schoolId')->limit(10)->select();
        return $this->jsonData(200,'',$schools);

    }
    //详情-获取热门专业推荐
    public function getSubjects(){

        if(!$this->checkRequest()){
            return $this->jsonData('0','请求不合法');
        }
        $subjects = Subject::order('sort desc')->field('subjectId,subjectName')->limit(10)->select();
        return $this->jsonData(200,'',$subjects);

    }
    //详情-获取同省份最新动态 自考报名
    public function getArticle(){

        if(!$this->checkRequest()){
            return $this->jsonData('0','请求不合法');
        }
        $province_id = request()->post('province_id');
        if(!isset($province_id)) return $this->jsonData('0','参数有误');
        $schools = Article::where('province_id',$province_id)->order('hits desc')->field('title,id,province_id')->limit(10)->select();
        foreach ($schools as $k => $v) {
            $schools[$k]['province_id'] = c2PyByAreaid($v['province_id']);
        }
        return $this->jsonData(200,'',$schools);

    }
    /**
     * [getDistrictLevel1 description] 获取省 直辖市
     * @return [type] [description]
     */
    public function getDistrictLevel1(){
        if(!$this->checkRequest()){
            return $this->jsonData('0','请求不合法');
        }
        $list = District::where('Id','<','31')->field('Id,name')->select();
        foreach ($list as $k => $v) {
            $name = '';
            if($v['Id'] == 8){
               $name = mb_substr($v['name'],0,3,'utf-8');
            }else{
               $name = mb_substr($v['name'],0,2,'utf-8'); 
            }
            $list[$k]['name'] = $name;
            $list[$k]['area'] = (new Pinyin)->getAllPY($name);
        }
        return $this->jsonData(200,'',$list);
    }
    /**
     * [verify description] 验证码
     * @return [type] [description]
     */
    public function verify()
    {
        $config =    [
            // 验证码字体大小
            'fontSize'    =>    33,    
            // 验证码位数
            'length'      =>    4,   
            // 关闭验证码杂点
            'useNoise'    =>    false, 
        ];

        $captcha = new Captcha($config);
        $captcha->codeSet = '0123456789'; 
        return $captcha->entry();    
    }
    /**
     * [enroll description] 登记报名信息
     * @return [type] [description]
     */
    public function enroll(){
        if(!$this->checkRequest()){
            return $this->jsonData('0','请求不合法');
        }
        $data = request()->post()['data'];
        if(empty($data['enroll_name'])){
            return json(['msg'=>'你的姓名不能为空','code'=>'-1']);
        }
        $captcha = new Captcha();
        if(!preg_match("/^1[3456789]\d{9}$/", $data['enroll_phone'])){
            return json(['msg'=>'手机号码格式不正确','code'=>'-1']);
        }
        if(empty($data['enroll_readType'])){
            return json(['msg'=>'请选择报考层次','code'=>'-1']);
        }
        if($data['enroll_readArea'] == 0){
            return json(['msg'=>'请选择报名地区','code'=>'-1']);
        }
        
        if( !$captcha->check($data['enroll_verifycode']))
        {
            return json(['msg'=>'验证码不正确','code'=>'-1']);
        }
        $data['create_time'] = time();
        $result = Enroll::insert($data);
        if($result){
            return json(['msg'=>'报名成功','code'=>'200']);
        }
    }

    public function register(){
        $region = Cache::get('region');

        if(!$this->checkRequest()){
            return $this->jsonData('0','请求不合法');
        }
        $data = request()->post()['jsonData'];
        
        $data['create_time'] = time();
        
        $data['pid'] = $region[$data['area']];

        unset($data['area']);
        
        $result = Register::insert($data);
        if($result){
            return json(['msg'=>'提交成功','code'=>'200']);
        }else{
            return json(['msg'=>'报名失败','code'=>'-1']);
        }
    }
}