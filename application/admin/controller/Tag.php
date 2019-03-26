<?php
namespace app\admin\controller;
use app\common\model\Tag as TagModel;
class Tag extends Base
{
    public function index()
    {
        $model = new TagModel();

    	$pageParam = ['query' => []];
    	if (isset($this->param['keywords']) && !empty($this->param['keywords'])) {
            $pageParam['query']['keywords'] = $this->param['keywords'];
            $model->whereLike('tag_name', "%" . $this->param['keywords'] . "%");
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
        
        $list    = $model->paginate($this->webData['list_rows'], false, $pageParam);
        // print_r($list);exit;
        $this->assign([
            'list' => $list,
            'total' => $list->total(),
            'page'  => $list->render()
        ]);
        return $this->fetch();
    }

    public function add(){

    	if ($this->request->isPost()) 
    	{
            
    		$resultValidate = $this->validate($this->param, 'Tag.add');
    		if (true !== $resultValidate) {
                return $this->error($resultValidate);
            }

            $result = TagModel::create($this->param);
            if ($result) {
                return $this->success();
            }
            return $this->error();
    	}
        return $this->fetch();
    }
        /**
     * [edit description] 编辑
     * @return [type] [description]
     */
    public function edit()
    {
        $info = TagModel::get($this->id);
        if ($this->request->isPost()) {

        	$resultValidate = $this->validate($this->param, 'Tag.add');
    		if (true !== $resultValidate) {
                return $this->error($resultValidate);
            }
            $this->param['update_time'] = time();

            if (false !== $info->save($this->param)) {
                return $this->success();
            }
            return $this->error();
        }else{
        	
        	$this->assign('info',$info);
        }

        return $this->fetch('add');
    }

    public function del()
    {

        $id     = $this->id;
        $result = TagModel::destroy(function ($query) use ($id) {
            $query->whereIn('id', $id);
        });

        if ($result) {
            //删除tag标签同时删除生成的页面
            delFileByFile(request()->domain().'/tags/'.$id.'.html');
            return $this->deleteSuccess();
        }
        return $this->error('删除失败');
    }
    public function tag_response(){
        if ($this->request->isPost()) 
        {
            $this->param = $this->param['data'];
            if(!$this->param['tag_name']) return json(['code'=>'-1','Message'=>'tag名称不能为空']);
            if(!$this->param['seo_keyword']) return json(['code'=>'-1','Message'=>'seo关键词不能为空']);
            if(!$this->param['seo_describe']) return json(['code'=>'-1','Message'=>'seo描述不能为空']);

            $tag = new TagModel();
            $info = $tag->where('tag_name','like',"%".$this->param['tag_name']."%")->field('id,tag_name')->find();
            if($info) return json(['code'=>'1','Message'=>'标签已经存在','data'=>['id'=>$info['id'],'tag_name'=>$info['tag_name']]]);
            $tag->tag_name = $this->param['tag_name'];
            $tag->seo_keyword = $this->param['seo_keyword'];
            $tag->seo_describe = $this->param['seo_describe'];
            $tag->create_time = time();

            if($tag->save()){
                return json(['code'=>'200','Message'=>'添加成功','data'=>['id'=>$tag->id,'tag_name'=>$tag->tag_name]]);
            }else{
                return json(['code'=>'-1','Message'=>'添加失败']);
            }

        }
    }
}