<?php
/**
 * tag首页
 * @author
 */

namespace app\admin\controller;
use app\common\model\ArticleTagList as ArticleTagListModel;
class ArticleTagList extends Base
{

    public function index()
    {

    	$list = ArticleTagListModel::select();
        var_dump($list);
        //echo 'tag页面';
    }
    /**
     * [add description]添加tag标签
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $resultValidate = $this->validate($this->param, 'User.admin_add');
            if (true !== $resultValidate) {
                return $this->error($resultValidate);
            }
            $this->param['password'] = md5(md5($this->param['password']));
            $attachment              = new Attachments();
            $file                    = $attachment->upload('headimg');
            if ($file) {
                $this->param['headimg'] = $file->url;
            }else{
                return $this->error($attachment->getError());
            }

            $result = Users::create($this->param);
            if ($result) {
                return $this->success();
            }
            return $this->error();
        }

        return $this->fetch();
    }
}