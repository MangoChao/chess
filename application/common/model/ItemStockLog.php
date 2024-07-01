<?php

namespace app\common\model;

use think\Model;

class ItemStockLog extends Model
{

    // 表名
    protected $name = 'item_stock_log';
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    // 追加属性
    protected $append = [
    ];

}
