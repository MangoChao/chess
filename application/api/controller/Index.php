<?php

namespace app\api\controller;

use app\common\controller\Api;

/**
 * 首页接口
 */
class Index extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    /**
     * 首页
     *
     */
    public function index()
    {
        $this->success('請求成功');
    }
    
    /**
     * 測試加入queue
     */
    // public function addQueue()
    // {
    //     $job  = "app\\queue\\controller\\Test";
    //     $data = [];
    //     queue($job, 'a');
    //     queue($job, 'b');
    //     queue($job, 'w');
    // }
}
