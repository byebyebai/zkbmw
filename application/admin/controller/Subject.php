<?php
namespace app\admin\controller;
use app\common\model\Subject as SubjectModel;
use app\common\model\Attachments;
use app\common\model\Article as ArticleModel;
class Subject extends Base
{

    public function index(){
        $model = new SubjectModel();
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
            $model->order('SubjectId', 'desc');
        }
        $list    = $model->paginate($this->webData['list_rows'], false, $pageParam);
        // print_r($list);
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
    		
            $resultValidate = $this->validate($this->param, 'Subject.add');
            if (true !== $resultValidate) {
                return $this->error($resultValidate);
            }

            $attachment              = new Attachments();
            $file                    = $attachment->upload('subjectImage');

            if ($file) {

                $this->param['subjectImage'] = $file->url;

            }else{

                return $this->error($attachment->getError());

            }
            $result = SubjectModel::create($this->param);
            if ($result) {
                return $this->success();
            }
            return $this->error();
    	}

        return $this->fetch();
    }

    public function edit(){
        $info = SubjectModel::get($this->id);
        if ($this->request->isPost()) {
            $resultValidate = $this->validate($this->param, 'Subject.edit');
            if (true !== $resultValidate) {
                return $this->error($resultValidate);
            }
            if ($this->request->file('subjectImage')) {
                $attachment = new Attachments();
                $file       = $attachment->upload('subjectImage');
                if ($file) {
                    $this->param['subjectImage'] = $file->url;
                } else {
                    return $this->error($attachment->getError());
                }
            }

            if (false !== $info->save($this->param)) {
                return $this->success();
            }
            return $this->error();
        }

        $this->assign([
            'info'       => $info
        ]);
        return $this->fetch('add');
    }

    public function del()
    {

        $id     = $this->id;
        $result = SubjectModel::destroy(function ($query) use ($id) {
            $query->whereIn('subjectId', $id);
        });
        if ($result) {
            return $this->deleteSuccess();
        }
        return $this->error('删除失败');
    }

    /**
     * [indexSchool description] 院校文章管理
     * @return [type] [description]
     */
    public function indexsubject()
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
        $model->where('type',2);
        $list    = $model->paginate($this->webData['list_rows'], false, $pageParam);
        
        $this->assign([
            'list' => $list,
            'total' => $list->total(),
            'page'  => $list->render()
        ]);
        return $this->fetch('article_public/article_subject');

    }

}