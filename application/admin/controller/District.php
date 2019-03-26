<?php

namespace app\admin\controller;

use app\admin\model\District as DistrictModel;
use app\admin\model\DistrictConfig;
use app\common\model\Attachments;

class District extends Base
{

    public function seoconfig(){
        $model = new DistrictConfig();
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
        if ($this->request->isPost()) {

            $res = DistrictConfig::where('pid',$this->param['pid'])->find();
            if($res){
                return $this->error('该地区已经添加过seo信息，请执行修改操作');
            }
            

            $attachment              = new Attachments();
            $file                    = $attachment->upload('thumb');
            if ($file) {

                $this->param['thumb'] = $file->url;

            }else{

                return $this->error($attachment->getError());

            }
            $resultValidate = $this->validate($this->param, 'District.add');
            if (true !== $resultValidate) {
                return $this->error($resultValidate);
            }
            $result = DistrictConfig::create($this->param);
            if ($result) {
                return $this->success();
            }
            return $this->error();
        }
        return $this->fetch();
    }
    public function edit($id){
        $info = DistrictConfig::get($this->id);
        if ($this->request->isPost()) {

            $resultValidate = $this->validate($this->param, 'District.edit');
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
}