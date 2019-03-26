<?php
use think\Db;
use tools\Pinyin;
// 应用公共文件
function getAreaName($id){

	$name = Db::name('district')->where('id',$id)->value('name');
    $res = '';
    if($id == 8){
        $res = mb_substr($name,0,3,'utf-8');
    }else{
        $res = mb_substr($name,0,2,'utf-8');
    }

    return $res ? $res : '全国';
}
function C2PY(){
    $pinyin = new Pinyin();
    $list = Db::name('district')->where('id','<','32')->field('Id,name')->select();

    foreach ($list as $k => $v) {
        if($v['Id'] == 8){
            $list[$k]['name'] = mb_substr($v['name'],0,3,'utf-8');

        }else{
            $list[$k]['name'] = mb_substr($v['name'],0,2,'utf-8');

        }
        $list[$k]['dir'] = $pinyin->getAllPY($list[$k]['name']);
    }
    return $list;
}
function c2PyByAreaid($id){
    $name = Db::name('district')->where('id',$id)->value('name');
    $res = '';
    if($id == 8){
        $res = mb_substr($name,0,3,'utf-8');
    }else{
        $res = mb_substr($name,0,2,'utf-8');
    }
    $pinyin = new Pinyin();
    return $pinyin->getAllPY($res);
}
function getFriendship_link(){
    return Db::name('friendship_link')->field('name,url')->select();
}
function getCategoryName($id){

	return Db::name('article_category')->where('id',$id)->value('category_name');

}

function getAreaListByWhere($where = []){
    
	return Db::name('district')->where($where)->field('Id,name')->select();

}

function getAreaAnohtername($id){
    return Db::name('district')->where('Id',$id)->value('another_name');
}

function getSchoolByProvince_id($id,$length){
    return Db::name('school')->where('province_id',$id)->limit($length)->field('schoolId,schoolName,schoolImage,schoolLogo')->select();
}

function getArticleListByWhere($where = [],$length,$field='*'){
 
    return Db::name('article')->where($where)->limit($length)->field($field)->select();
}
/**
 * 文章、栏目 类型获取器
 */
function getArticleType($type = ''){

	$typeArray = ['普通类','学校类','专业类'];

	if(in_array($type,['0','1','2'])){

		return $typeArray[$type];

	}else{

		return $typeArray;

	}
}
function subjectLevel($key = ''){
	$level = ['专科','本科','一本院校','二本院校','985工程','211工程'];
	if(isset($level[$key])){
		return $level[$key];
	}else{
		return $level;
	}
	
}
function subjectType($key = ''){
	$type = ['经管类','法学类','理工类','文史类','教育类','医学类','艺术类','农学类','机械类'];
	if(isset($type[$key])){
		return $type[$key];
	}else{
		return $type;
	}
}
/**
 * [time2Format description] 2019-01-22  转成指定格式 
 * @param  string $time   [description]
 * @param  string $format [description]
 * @return [type]         [description]
 */
function time2Format($time = '',$format = 'Y-m-d H:i:s'){
	return date($format,strtotime($time));
}

/**
 * [strCut description] 截取字符长度  多出部分用...代替
 * @param  string  $str [description]
 * @param  integer $len [description]
 * @param  boolean $dot [description]
 * @return [type]       [description]
 */
function strCut($str='',$len = 5, $dot = true){

	$i = 0;
    $l = 0;
    $c = 0;
    $a = array();
    while ($l < $len) {
        $t = substr($str, $i, 1);
        if (ord($t) >= 224) {
            $c = 3;
            $t = substr($str, $i, $c);
            $l += 2;
        } elseif (ord($t) >= 192) {
            $c = 2;
            $t = substr($str, $i, $c);
            $l += 2;
        } else {
            $c = 1;
            $l++;
        }
        // $t = substr($str, $i, $c);
        $i += $c;
        if ($l > $len) break;
        $a[] = $t;
    }
    $re = implode('', $a);
    if (substr($str, $i, 1) !== false) {
        array_pop($a);
        ($c == 1) and array_pop($a);
        $re = implode('', $a);
        $dot and $re .= '...';
    }
    return $re;
}
/**
 * [unique_2d_array_by_key description] 二维数组去重复
 * @param  [type] $_2d_array  [description]
 * @param  [type] $unique_key [description]
 * @return [type]             [description]
 */
function unique_2d_array_by_key($_2d_array, $unique_key) {
  $tmp_key[] = array();
  foreach ($_2d_array as $key => &$item) {
    if ( is_array($item) && isset($item[$unique_key]) ) {
      if ( in_array($item[$unique_key], $tmp_key) ) {
        unset($_2d_array[$key]);
      } else {
        $tmp_key[] = $item[$unique_key];
      }
    }
  }
  return $_2d_array;
}

function delFileByFile($file){

    if(file_exists($file)){
        unlink($file);
    }
    return true;
}