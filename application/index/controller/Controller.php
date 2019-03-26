<?php
namespace app\index\controller;

use think\Controller as Tk;
use tools\Pinyin;
use app\admin\model\District;
use think\Cache;
class Controller extends Tk
{

	public function _initialize(){
		$this->initializeSchoolCate();
        $this->matchRoute();
	}

    public function jsonData($status=200,$msg = '',$array = []){
    	$data = [
    		'status'=>$status,
    		'msg'=>$msg,
    		'data'=>$array,
    		'request_time'=>time()
    	];
    	echo json_encode($data);
    }
    //初始化设置院校栏目变量
     protected function initializeSchoolCate(){
    	//院校动态
    	defined('SCHOOLNEWS')          		or define('SCHOOLNEWS',6);
    	//院校简章
    	defined('SCHOOLJZ')          		or define('SCHOOLJZ',7);
    	//毕业证书
        defined('BIYEZHENGSHU')             or define('BIYEZHENGSHU',8);
        //招生专业
        defined('ZHAOSHENGZHUANYE')             or define('ZHAOSHENGZHUANYE',9);
        //专业介绍
        defined('SUBJECTABOUT')             or define('SUBJECTABOUT',13);
        //毕业流程
        defined('SUBJECTPROCESS')             or define('SUBJECTPROCESS',12);
        //就业前景
        defined('SUBJECTPROSPECT')             or define('SUBJECTPROSPECT',11);
        //报考科目
    	defined('SUBJECTS')          	or define('SUBJECTS',10);
    }

    protected function matchRoute(){

        if(empty(Cache::get('region'))){
            $pinyin = new Pinyin();
            $list = District::where('id','<','32')->field('Id,name')->select();
            $region = [];
            foreach ($list as $k => $v) {
                if($v['Id'] == 8){
                    $list[$k]['name'] = mb_substr($v['name'],0,3,'utf-8');

                }else{
                    $list[$k]['name'] = mb_substr($v['name'],0,2,'utf-8');

                }
                $region[$pinyin->getAllPY($list[$k]['name'])] = $v['Id']; 
            }
            Cache::set('region',$region);
        }
    }
}