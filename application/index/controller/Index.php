<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use think\Log;

class Index extends Frontend
{
    protected $layout = 'base';
    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';

    protected $chess = [
        0 => '將', // 黑方
        1 => '帥', // 紅方
        2 => '士', // 黑方
        3 => '士', // 黑方
        4 => '仕', // 紅方
        5 => '仕', // 紅方
        6 => '象', // 黑方
        7 => '象', // 黑方
        8 => '相', // 紅方
        9 => '相', // 紅方
        10 => '馬', // 黑方
        11 => '馬', // 黑方
        12 => '傌', // 紅方
        13 => '傌', // 紅方
        14 => '車', // 黑方
        15 => '車', // 黑方
        16 => '俥', // 紅方
        17 => '俥', // 紅方
        18 => '炮', // 黑方
        19 => '炮', // 黑方
        20 => '砲', // 紅方
        21 => '砲', // 紅方
        22 => '兵', // 黑方
        23 => '兵', // 黑方
        24 => '兵', // 黑方
        25 => '兵', // 黑方
        26 => '兵', // 黑方
        27 => '卒', // 紅方
        28 => '卒', // 紅方
        29 => '卒', // 紅方
        30 => '卒', // 紅方
        31 => '卒', // 紅方
    ];

    public function index($cho = "")
    {
        $this->view->assign('cho', $cho);
        $this->view->assign('chess', $this->chess);
        return $this->view->fetch();
    }

}
