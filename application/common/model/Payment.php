<?php

namespace app\common\model;

use think\Model;

class Payment extends Model
{

    // 表名
    protected $name = 'payment';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    public function getStatusList()
    {
        return ['1' => __('Status 1'), '0' => __('Status 0'),'2' => __('Status 2')];
    }
    
    public function order()
    {
        return $this->belongsTo('Order', 'order_no', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
