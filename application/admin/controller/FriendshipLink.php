<?php
/**
 * 后台首页
 * @author
 */
namespace app\admin\controller;
use app\common\model\School as SchoolModel;
use app\common\model\SchoolSubject;
use app\common\model\Attachments;
use app\common\model\Subject;
use app\common\model\FriendshipLink as FriendshipLinkModel;
class FriendshipLink extends Base
{

    public function index()
    {
        

        $model = new FriendshipLinkModel();

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
            $resultValidate = $this->validate($this->param, 'FriendshipLink.add');
            if (true !== $resultValidate) {
                return $this->error($resultValidate);
            }
            $result = FriendshipLinkModel::create($this->param);
            if ($result) {
                return $this->success();
            }
            return $this->error();
        }

        return $this->fetch();
    
    }

    public function edit(){
        $info = FriendshipLinkModel::get($this->id);
        if ($this->request->isPost()) {
            $resultValidate = $this->validate($this->param, 'FriendshipLink.edit');
            if (true !== $resultValidate) {
                return $this->error($resultValidate);
            }
            $result = $info->save($this->param);
            if ($result) {
                return $this->success();
            }
            return $this->error();
        }
        $this->assign(['info'=>$info]);
        return $this->fetch('add');
        
    }

    public function del(){
        $id     = $this->id;
        $result = SchoolModel::destroy(function ($query) use ($id) {
            $query->whereIn('schoolId', $id);
        });

        if ($result) {
            return $this->deleteSuccess();
        }
        return $this->error('删除失败');
    }

}