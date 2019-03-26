<?php

namespace app\admin\controller;
use app\admin\model\District;
use app\common\model\Attachments;
use app\admin\model\ArticleCategory as ArticleCategoryModel;

class ArticleCategory extends Base
{

    public function index()
    {

    	$model = new ArticleCategoryModel();
    	$pageParam = ['query' => []];
    	if (isset($this->param['keywords']) && !empty($this->param['keywords'])) {
            $pageParam['query']['keywords'] = $this->param['keywords'];
            $model->whereLike('category_name', "%" . $this->param['keywords'] . "%");
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
        $pageParam['query']['category_type'] = 1; 
        $list    = $model->paginate($this->webData['list_rows'], false, $pageParam);

        $this->assign([
            'list' => $list,
            'total' => $list->total(),
            'page'  => $list->render()
        ]);
        return $this->fetch();
    }

    public function add(){

    	if ($this->request->isPost()) {

    		$this->param['pid'] = $this->param['pid2'] ? $this->param['pid2'] : $this->param['pid1'];
            unset($this->param['pid1']);
            unset($this->param['pid2']);
    		
            $resultValidate = $this->validate($this->param, 'ArticleCategory.add');
            
            if (true !== $resultValidate) {
                return $this->error($resultValidate);
            }
            
            $attachment              = new Attachments();
            $file                    = $attachment->upload('image');
            if ($file) {
                $this->param['image'] = $file->url;
            }else{
                return $this->error($attachment->getError());
            }
            $this->param['create_time'] = time();
            $result = ArticleCategoryModel::create($this->param);

            if ($result) {
                return $this->success();
            }
            return $this->error();
        }else{
        	//获取地区
        	$list = District::where('level',1)->select();
        	//获取一级栏目
        	$category_list = ArticleCategoryModel::where('pid',0)->field('category_name,id')->select();
            $this->assign('list',$list);
        	$this->assign('category_list',$category_list);
        }

        return $this->fetch();
    }
    /**
     * [getCity description]获取市级城市
     * @return [type] [description]
     */
    public function getCity(){
    	//获取城市
    	if($this->request->isPost()){
    		$upid = $this->param['upid'];
    		$list = District::where('upid',$upid)->field('Id,name')->select();
    		return $this->result($list,200,'数据请求成功');
    	}
    }

    public function getAreaById(){
    	if($this->request->isPost()){
            
    		$id = $this->param['id'];
    		return ArticleCategoryModel::where('id',$id)->value('area');
    	
    	}
    }
    
    /**
     * [getCityPidById description] 通过一级栏目id获取子级id
     * @return [type] [description]
     */
    public function getChildCategoryId(){
    	if($this->request->isPost()){
    		$id = $this->param['pid'];
    		$list = ArticleCategoryModel::where('pid',$id)->where('id','neq',$id)->field('id,category_name')->select();
    		if($list){
    			return $this->result($list,200,'数据请求成功');
    		}else{
    			return $this->result($list,204,'数据为空');
    		}
    	}else{
            return $this->result('',0,'请求失败');
        }
    }

    /**
     * [edit description] 编辑
     * @return [type] [description]
     */
    public function edit()
    {
        $info = ArticleCategoryModel::get($this->id);
        if ($this->request->isPost()) {
           
        	$this->param['pid'] = $this->param['pid2'] ? $this->param['pid2'] : $this->param['pid1'];
            unset($this->param['pid1']);
            unset($this->param['pid2']);

            $resultValidate = $this->validate($this->param, 'ArticleCategory.add');
            if (true !== $resultValidate) {
                return $this->error($resultValidate);
            }

            if ($this->request->file('image')) {
                $attachment = new Attachments();
                $file       = $attachment->upload('image');
                if ($file) {
                    $this->param['image'] = $file->url;
                } else {
                    return $this->error($attachment->getError());
                }
            }

            $this->param['update_time'] = time();

            if (false !== $info->save($this->param)) {
                return $this->success();
            }
            return $this->error();
        }

    	$category_list = ArticleCategoryModel::where('pid',0)->field('category_name,id')->select();
        $this->assign('category_list',$category_list);
    	$this->assign('info',$info);
        return $this->fetch('add');
    }


    public function del()
    {

        $id     = $this->id;
        $result = ArticleCategoryModel::destroy(function ($query) use ($id) {
            $query->whereIn('id', $id);
        });
        if ($result) {
            return $this->deleteSuccess();
        }
        return $this->error('删除失败');
    }
    
}