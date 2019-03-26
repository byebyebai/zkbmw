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
use app\common\model\Article as ArticleModel;
class School extends Base
{

    public function index()
    {

        $model = new SchoolModel();

        $pageParam = ['query' => []];
        if (isset($this->param['keywords']) && !empty($this->param['keywords'])) {
            $pageParam['query']['keywords'] = $this->param['keywords'];
            $model->whereLike('schoolName', "%" . $this->param['keywords'] . "%");
            $this->assign('keywords', $this->param['keywords']);
        }
         if (isset($this->param['province_id']) && !empty($this->param['province_id'])) {
            $pageParam['query']['province_id'] = $this->param['province_id'];
            $model->whereLike('province_id', $this->param['province_id']);
            $this->assign('province_id', $this->param['province_id']);
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
            $model->order('schoolId', 'desc');
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


        $subjects = Subject::field('subjectId,subjectName,subjectLevel')->select();
        $area = getAreaListByWhere(['level'=>1]);
        if ($this->request->isPost()){
            $resultValidate = $this->validate($this->param, 'School.add');
            if (true !== $resultValidate) {
                return $this->error($resultValidate);
            }
            $attachment = new Attachments();
            $logo       = $attachment->upload('schoolLogo');
            $Image       = $attachment->upload('schoolImage');
            if($logo && $Image){
                $this->param['schoolLogo'] = $logo->url;
                $this->param['schoolImage'] = $Image->url;
            }else{
                return $this->error($attachment->getError());
            }
            $school = new SchoolModel();
            $school->schoolName = $this->param['schoolName'];
            $school->schoolImage = $this->param['schoolImage'];
            $school->seo_keywords = $this->param['seo_keywords'];
            $school->seo_description = $this->param['seo_description'];
            $school->schoolLogo = $this->param['schoolLogo'];
            $school->province_id = $this->param['province_id'];
            $school->city_id = $this->param['city_id'];
            $school->schoolDescribe = $this->param['schoolDescribe'];
            $school->create_time = time();
            $school->update_time = time();
            $data['level'] = $this->param['level'];
            
            if($school->save()){
                $schoolId = $school->schoolId;
                $list = [];
                foreach ($this->param['subjects'] as $k => $v) {
                    $list[$k]['schoolId']  =  $schoolId;  
                    $list[$k]['subjectId']  =  $v;  
                    $list[$k]['create_time']  =  time();  
                    $list[$k]['update_time']  =  time();  
                }
                if((new SchoolSubject())->saveAll($list)){
                    return $this->success();
                }
            }
            return $this->error('添加失败');

        } 
        $this->assign(['area'=>$area,'subjects'=>$subjects]);
        return $this->fetch();
    
    }

    public function edit(){

        $subjects = Subject::field('subjectId,subjectName,subjectLevel')->select();
        
        $area = getAreaListByWhere(['level'=>1]);
        $info = SchoolModel::get($this->id);
        $schoolsubject = SchoolSubject::where('schoolId',$this->id)->field('id,schoolId,subjectId')->select();
        $schoolsubjectIds = '';
        foreach ($schoolsubject as $k => $v) {
            $schoolsubjectIds .= $v['subjectId'] .',';
        }
        if ($this->request->isPost()){
            $resultValidate = $this->validate($this->param, 'School.edit');
            if (true !== $resultValidate) {
                return $this->error($resultValidate);
            }
            if ($this->request->file('schoolLogo') || $this->request->file('schoolImage')) {
                $attachment = new Attachments();
                $logo       = $attachment->upload('schoolLogo');
                $Image       = $attachment->upload('schoolImage');
                if($logo && $Image){
                    $data['schoolLogo'] = $logo->url;
                    $data['schoolImage'] = $Image->url;
                }else{
                    return $this->error($attachment->getError());
                }
            }
            
            $school = new SchoolModel();
            $data['schoolName'] = $this->param['schoolName'];
            $data['seo_keywords'] = $this->param['seo_keywords'];
            $data['seo_description'] = $this->param['seo_description'];
            $data['province_id'] = $this->param['province_id'];
            $data['city_id'] = $this->param['city_id'];
            $data['schoolDescribe'] = $this->param['schoolDescribe'];
            $data['update_time'] = time();
            $data['level'] = $this->param['level'];
            
            if($school->where('schoolId',$this->id)->update($data)){
                $schoolId = $this->id;
                (new SchoolSubject())->where('schoolId',$schoolId)->delete();
                $list = [];
                foreach ($this->param['subjects'] as $k => $v) {
                    $list[$k]['schoolId']  =  $schoolId;  
                    $list[$k]['subjectId']  =  $v;  
                    $list[$k]['update_time']  =  time();  
                }
                if((new SchoolSubject())->saveAll($list)){
                    return $this->success();
                }
            }
            return $this->error('更新失败');

        } 
 
        $this->assign([
            'area'=>$area,
            'subjects'=>$subjects,
            'info'=>$info,
            'schoolsubjectIds'=> rtrim($schoolsubjectIds,','),
            'schoolsubject'=>$schoolsubject
        ]);
        
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

    public function getAreaLevel1ArticleCategory($id){

        $result = (new ArticleCategoryModel())->getArticleCategoryByWhere(['area'=>$id,'pid'=>0]);
        if($result){
            return $this->result($result,200,'数据请求成功');
        }else{
            return $this->result($result,204,'数据为空');
        }
    }

    public function getAreaLevel2ArticleCategory($id){
        $result = (new ArticleCategoryModel())->getArticleCategoryByWhere(['pid'=>$id]);
        if($result){
            return $this->result($result,200,'数据请求成功');
        }else{
            return $this->result($result,204,'数据为空');
        }
    }
    /**
     * [indexSchool description] 院校文章管理
     * @return [type] [description]
     */
    public function indexschool()
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
        $model->where('type',1);
        $list    = $model->paginate($this->webData['list_rows'], false, $pageParam);
        
        $this->assign([
            'list' => $list,
            'total' => $list->total(),
            'page'  => $list->render()
        ]);
        return $this->fetch('article_public/article_school');

    }

    public function articledel(){
        $id     = $this->id;
        $result = ArticleModel::destroy(function ($query) use ($id) {
            $query->whereIn('id', $id);
        });

        if ($result) {
            return $this->deleteSuccess();
        }
        return $this->error('删除失败');
    }

}