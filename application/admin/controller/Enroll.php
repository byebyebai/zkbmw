<?php
namespace app\admin\controller;
use app\common\model\Enroll as EnrollModel;
class Enroll extends Base
{
    public function index()
    {

        $model = new EnrollModel();

    	$pageParam = ['query' => []];
    	if (isset($this->param['keywords']) && !empty($this->param['keywords'])) {
            $pageParam['query']['keywords'] = $this->param['keywords'];
            $model->whereLike('name', "%" . $this->param['keywords'] . "%");
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

    public function del()
    {

        $id     = $this->id;
        $result = EnrollModel::destroy(function ($query) use ($id) {
            $query->whereIn('id', $id);
        });
        if ($result) {
            return $this->deleteSuccess();
        }
        return $this->error('删除失败');
    }

}