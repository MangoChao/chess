<?php

namespace app\common\model;

use think\Model;

class Product extends Model
{

    // 表名
    protected $name = 'product';
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    // 追加属性
    protected $append = [
    ];

    protected static function init()
    {
        self::afterDelete(function ($row) {
            model('ProductOption')->where('product_id = '.$row->id)->delete();
        });
    }

    public function getStatusList()
    {
        return ['1' => __('Status 1'), '0' => __('Status 0')];
    }

    // public function category()
    // {
    //     return $this->belongsTo('Category', 'category_id', 'id', [], 'LEFT')->setEagerlyType(0);
    // }
}

