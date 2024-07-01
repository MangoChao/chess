<?php

namespace app\common\model;

use think\Model;

class Item extends Model
{

    // 表名
    protected $name = 'item';
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    protected $createTime = false;
    protected $updateTime = 'updatetime';
    // 追加属性
    protected $append = [
    ];

    public function getOriginData()
    {
        return $this->origin;
    }
    
    // protected static function init()
    // {
    //     self::afterUpdate(function ($row) {
    //         if(isset($row->stock)){
    //             $mProduct = model("Product")->alias('p')
    //             ->join("product_option po","p.id = po.product_id")
    //             ->field('p.id')
    //             ->where('po.item_id = '.$row->id)->select();
    //             if($mProduct){
    //                 foreach($mProduct as $p){
    //                     $mProductStock = model("Product")->alias('p')
    //                     ->join("product_option po","p.id = po.product_id")
    //                     ->join("item i","i.id = po.item_id")
    //                     ->field('p.id, p.has_stock, SUM(i.stock) as stock')
    //                     ->where('p.id = '.$p->id)->group("p.id")->find();
    //                     if($mProductStock){
    //                         if($mProductStock->stock > 0){
    //                             $mProductStock->has_stock = 1;
    //                         }else{
    //                             $mProductStock->has_stock = 0;
    //                         }
    //                         $mProductStock->save();
    //                     }
    //                 }
    //             }
    //         }
    //     });
    //     self::afterDelete(function ($row) {
    //         if($row->stock > 0){
    //             $mProduct = model("Product")->alias('p')
    //             ->join("product_option po","p.id = po.product_id")
    //             ->field('p.id')
    //             ->where('po.item_id = '.$row->id)->select();
    //             if($mProduct){
    //                 foreach($mProduct as $p){
    //                     $mProductStock = model("Product")->alias('p')
    //                     ->join("product_option po","p.id = po.product_id")
    //                     ->join("item i","i.id = po.item_id")
    //                     ->field('p.id, p.has_stock, SUM(i.stock) as stock')
    //                     ->where('p.id = '.$p->id)->group("p.id")->find();
    //                     if($mProductStock){
    //                         if($mProductStock->stock > 0){
    //                             $mProductStock->has_stock = 1;
    //                         }else{
    //                             $mProductStock->has_stock = 0;
    //                         }
    //                         $mProductStock->save();
    //                     }
    //                 }
    //             }
    //         }
    //         model('ProductOption')->where('item_id = '.$row->id)->delete();
    //     });
    // }

}

