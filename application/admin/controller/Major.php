<?php
namespace app\admin\controller;
use app\common\model\Tag as TagModel;
use app\common\model\Major as MajorModel;
class Major extends Base
{
    public function index()
    {

        $model = new MajorModel();

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


    		$resultValidate = $this->validate($this->param, 'Major.add');

    		if (true !== $resultValidate) {

                return $this->error($resultValidate);

            }
            $this->param['create_time'] = time();

            $result = MajorModel::create($this->param);

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
        $info = MajorModel::get($this->id);
        if ($this->request->isPost()) {

        	$resultValidate = $this->validate($this->param, 'Major.edit');
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
        $result = MajorModel::destroy(function ($query) use ($id) {
            $query->whereIn('id', $id);
        });
        if ($result) {
            return $this->deleteSuccess();
        }
        return $this->error('删除失败');
    }

    //启用/禁用
    public function disable()
    {
        $user         = MajorModel::get($this->id);
        $user->status = $user->status == 1 ? 0 : 1;
        $result       = $user->save();
        if ($result) {
            return $this->success();
        }
        return $this->error();
    }

}