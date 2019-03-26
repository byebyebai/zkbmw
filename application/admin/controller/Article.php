<?php
/**
 * 后台首页
 * @author
 */

namespace app\admin\controller;

use Parsedown;
use tools\Sysinfo;
use app\admin\model\ArticleCategory as ArticleCategoryModel;
use app\common\model\Article as ArticleModel;
use app\admin\model\District;
use app\common\model\Attachments;
use app\common\model\Tag;
use app\common\model\ArticleTagList;
class Article extends Base
{

    public function index()
    {

        $model = new ArticleModel();
        $pageParam = ['query' => []];
        if (isset($this->param['keywords']) && !empty($this->param['keywords'])) {
            $pageParam['query']['keywords'] = $this->param['keywords'];
            $model->whereLike('title', "%" . $this->param['keywords'] . "%");
            $this->assign('keywords', $this->param['keywords']);
        }
        if (isset($this->param['_order_']) && !empty($this->param['_order_'])) {
            $pageParam['query']['_order_'] = $this->param['_order_'];
            $order                         = $this->param['_order_'];
            switch ($order) {
                case 'id':
                    $order = 'id';
                    break;
                    break;
                case 'create_time':
                    $order = 'create_time';
                    break;

                default:

            }
            $by = isset($this->param['_by_']) && !empty($this->param['_by_']) ? $this->param['_by_'] : 'desc';
            $model->order($order, $by);
            $this->assign('_order_', $this->param['_order_']);
            $this->assign('_by_', $this->param['_by_']);
        } else {
            $model->order('id', 'desc');
        }

        $model->where('type',0);
        $list    = $model->paginate($this->webData['list_rows'], false, $pageParam);
        
        $this->assign([
            'list' => $list,
            'total' => $list->total(),
            'page'  => $list->render()
        ]);
        return $this->fetch();

    }

    public function add()
    {
        if ($this->request->isPost()) {


            if(!empty($this->param['level2']) || !empty($this->param['level1'])){

                $this->param['category_id'] = !empty($this->param['level2']) ? $this->param['level2'] : $this->param['level1'];
            
            }else{

                return $this->error('请选择栏目');
            
            }
            
            if(isset($_POST['content']) || !empty($_POST['content'])){

                $this->param['content'] = $_POST['content'];

            }else{

                return $this->error('文章内容不能为空');

            }

            $resultValidate = $this->validate($this->param, 'Article.add');
            if (true !== $resultValidate) {
                return $this->error($resultValidate);
            }

            $attachment              = new Attachments();
            $file                    = $attachment->upload('thumb_img');
            if ($file) {

                $this->param['thumb_img'] = $file->url;

            }else{

                return $this->error($attachment->getError());

            }
            $article = new ArticleModel();
            $article->title = $this->param['title'];
            $article->category_id = $this->param['category_id'];
            $article->thumb_img = $this->param['thumb_img'];
            $article->origin = $this->param['origin'];
            $article->keywords = $this->param['keywords'];
            $article->describe = $this->param['describe'];
            $article->content = $this->param['content'];
            $article->province_id = $this->param['province_id'];
            $article->city_id = $this->param['city_id'];
            $article->essentials = $this->param['essentials'];
            $article->type = ArticleCategoryModel::where('id',$this->param['category_id'])->value('type');
            $article->hits = $this->param['hits'];
            $article->add_time = $this->param['add_time'];
            $article->create_time = time();
            if ($article->save()) {
                //处理tag
                $tag_data = [];
                foreach ($this->param['Tag'] as $k => $v) {
                    $tag_data[$k]['aid'] = $article->id;
                    $tag_data[$k]['tid'] = $v;
                    $tag_data[$k]['create_time'] = time();
                }
                $result = (new ArticleTagList)->saveAll($tag_data);
                if(!$result){
                    return $this->error('tag标签添加失败');
                }
                return $this->success();

            }
            return $this->error();
        }
        //获取一级栏目
        $categoryList = ArticleCategoryModel::where('pid',0)->where('type',0)->select();
        //获取已近添加栏目的地区
        // $area = ArticleCategoryModel::group('area')->field('area')->select();
        // $areaArr = [];
        // foreach ($area as $k => $v) {
        //     $areaArr[$k]['id'] = $v['area'];
        //     $areaArr[$k]['name'] = getAreaName($v['area']);
           
        // }
        //获取标签表
        $tag = Tag::field('id,tag_name')->select();
        
        $this->assign('categoryList',$categoryList);
        $this->assign('tag',$tag);
        return $this->fetch();
    }

    public function edit(){

        if ($this->request->isPost()) {

            if(!empty($this->param['level2']) || !empty($this->param['level1'])){

                $this->param['category_id'] = !empty($this->param['level2']) ? $this->param['level2'] : $this->param['level1'];
            }else{
                return $this->error('请选择栏目');
            }
            
            if(isset($_POST['content']) || !empty($_POST['content'])){

                $this->param['content'] = $_POST['content'];

            }else{

                return $this->error('文章内容不能为空');

            }
            // print_r($this->param);exit;
            $resultValidate = $this->validate($this->param, 'Article.edit');

            if (true !== $resultValidate) {

                return $this->error($resultValidate);

            }

            $attachment              = new Attachments();
            $file                    = $attachment->upload('thumb_img');

            if ($file) {

                $this->param['thumb_img'] = $file->url;

            }

            $article = ArticleModel::get($this->param['id']);
            $article->title = $this->param['title'];
            $article->category_id = $this->param['category_id'];
            if(isset($this->param['thumb_img'])){
                $article->thumb_img = $this->param['thumb_img'];
            }
            
            $article->origin = $this->param['origin'];
            $article->keywords = $this->param['keywords'];
            $article->describe = $this->param['describe'];
            $article->content = $this->param['content'];
            $article->hits = $this->param['hits'];
            $article->add_time = $this->param['add_time'];
            $article->update_time = time();
            if(empty($this->param['Tag'])) return $this->error('请选择tag标签');
            if ($article->save()) {
                //处理tag
                ArticleTagList::destroy(['aid'=>$this->param['id']]);
                $tag_data = [];
                foreach ($this->param['Tag'] as $k => $v) {
                    $tag_data[$k]['aid'] = $this->param['id'];
                    $tag_data[$k]['tid'] = $v;
                    $tag_data[$k]['create_time'] = time();
                    $tag_data[$k]['update_time'] = time();
                }
                $result = (new ArticleTagList)->saveAll($tag_data);
                if(!$result){
                    return $this->error('tag标签更新失败');
                }
                return $this->success();
            }

        }
        //获取详情信息
        $info = ArticleModel::get($this->id);
        $categoryList = ArticleCategoryModel::where('pid',0)->select();
        $cateInfo = ArticleCategoryModel::where('id',$info['category_id'])->field('id,pid,category_name')->find();
        $info['level2'] = $cateInfo['id'];
        if($cateInfo['pid'] != 0){
            $info['level1'] = ArticleCategoryModel::where('id',$cateInfo['pid'])->value('id'); 
        }
        //获取标签表
        $tag = Tag::field('id,tag_name')->select();
        //判断tag标签选中
        $tag_info = ArticleTagList::field('tid')->where('aid',$this->id)->select();
        $tag_ids = '';
        foreach ($tag_info as $k => $v) {
            $tag_ids .= $v['tid'].',';
        }

        return $this->fetch('add',[
            'categoryList'=>$categoryList,
            'tag'=>$tag,
            'info'=>$info,
            'tag_ids'=>rtrim($tag_ids,',')
        ]);
    }

    public function del(){
        $id     = $this->id;
        $result = ArticleModel::destroy(function ($query) use ($id) {
            $query->whereIn('id', $id);
        });
        $result1 = ArticleTagList::destroy(function ($query) use ($id) {
            $query->whereIn('aid', $id);
        });
        if ($result && $result1) {
            return $this->deleteSuccess();
        }
        return $this->error('删除失败');
    }


    //启用/禁用
    public function disable()
    {
        $user         = ArticleModel::get($this->id);
        $user->status = $user->status == 1 ? 0 : 1;
        $result       = $user->save();
        if ($result) {
            return $this->success();
        }
        return $this->error();
    }

    public function getLevel2List(){
        if($this->request->isPost()){
            $id = $this->id;
            $result = getAreaListByWhere(['upid'=>$id]);
            if($result){
                return $this->result($result,200,'数据请求成功'); 
            }else{
                return $this->result($result,204,'数据为空'); 
            }
            
        }
    }

    public function getAreaLevel1ArticleCategory($id){

        $result = (new ArticleCategoryModel())->getArticleCategoryByWhere(['area'=>$id,'pid'=>0]);
        if($result){
            return $this->result($result,200,'数据请求成功');
        }else{
            return $this->result($result,204,'数据为空');
        }
    }

    public function getAreaLevelArticleCategory($id){
        $result = (new ArticleCategoryModel())->getArticleCategoryByWhere(['pid'=>$id]);
        if($result){
            return $this->result($result,200,'数据请求成功');
        }else{
            return $this->result($result,204,'数据为空');
        }
    }
}