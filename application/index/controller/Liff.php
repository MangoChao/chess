<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use think\Log;

class Liff extends Frontend
{
    protected $layout = 'liff';
    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';

    public function index()
    {
        return $this->view->fetch();
    }

}
