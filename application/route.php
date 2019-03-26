<?php
use think\Route;

Route::get('cate/:id','index/Category/index');
Route::get('school/:id','index/School/detail');
Route::get('school/index','index/School/index');
Route::get('school_news/:id','index/School/school_news');
Route::get('school_jz/:id','index/School/school_jz');
Route::get('school_subject/:id','index/School/school_subject');
Route::get('subject/:id','index/Subject/detail');
Route::get('subject_bk/:id','index/Subject/subject_bk');
Route::get('subject_jy/:id','index/Subject/subject_jy');
Route::get('subject_lc/:id','index/Subject/subject_lc');
Route::get('tags/:id','index/Tag/index');
Route::get('subject/index','index/Subject/index');
Route::get('dq/:name','index/region/index');
Route::get('/','index/index/index');
Route::get('index/search','index/index/search');
// Route::get('/admin','admin/index/index');




