<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\Log;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use Exception;

/**
 * 訂單
 */
class Order extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    public function _initialize()
    {
        parent::_initialize();
        Log::init(['type' => 'File', 'log_name' => 'Order']);
    }

    /**改變購物車 */
    public function setCart($id, $num = 0)
    {
        $mUser = model('User')->get(['id' => $this->auth->id, 'status' => 1]);
        if (!$mUser) {
            $this->error('無權操作');
        }

        $mShop = model('Shop')->get(['id' => $mUser->shop_id, 'status' => 1]);
        if (!$mShop) {
            $this->error('無權操作');
        }
        
        $mProductOption = model("ProductOption")->get($id);
        if (!$mProductOption) {
            $mCart = model("Cart")->where("user_id = ".$this->auth->id." AND option_id = ".$id)->find();
            if($mCart){
                $mCart->delete();
            }
            $this->error('查無選項, 請重整畫面');
        }

        $mCart = model("Cart")->where("user_id = ".$this->auth->id." AND option_id = ".$id)->find();
        if($mCart){
            if($num > 0){
                $mCart->num = $num;
                $mCart->save();
            }else{
                $mCart->delete();
            }
        }elseif($num > 0){
            $params = [
                'user_id' => $this->auth->id,
                'option_id' => $id,
                'num' => $num
            ];
            $mCart = model('Cart')::create($params);
        }
        $this->success('已修改購物車', $this->getCartNum());
    }

    /**移除購物車 */
    public function delCart($id)
    {
        $mUser = model('User')->get(['id' => $this->auth->id, 'status' => 1]);
        if (!$mUser) {
            $this->error('無權操作');
        }

        $mShop = model('Shop')->get(['id' => $mUser->shop_id, 'status' => 1]);
        if (!$mShop) {
            $this->error('無權操作');
        }
        
        $mCart = model("Cart")->where("user_id = ".$this->auth->id." AND option_id = ".$id)->find();
        if($mCart){
            $mCart->delete();
        }
        $this->success('已移除', $this->getCartNum());
    }

    /**加入購物車 */
    public function addCart($id, $num = 1)
    {
        $mUser = model('User')->get(['id' => $this->auth->id, 'status' => 1]);
        if (!$mUser) {
            $this->error('無權操作');
        }

        $mShop = model('Shop')->get(['id' => $mUser->shop_id, 'status' => 1]);
        if (!$mShop) {
            $this->error('無權操作');
        }

        $mProductOption = model("ProductOption")->get($id);
        if (!$mProductOption) {
            $this->error('查無選項, 請重整畫面');
        }

        $mCart = model("Cart")->where("user_id = ".$this->auth->id." AND option_id = ".$id)->find();
        if($mCart){
            $mCart->num += $num;
            $mCart->save();
        }else{
            $params = [
                'user_id' => $this->auth->id,
                'option_id' => $id,
                'num' => $num 
            ];
            $mCart = model('Cart')::create($params);
        }
        
        $this->success('已加入購物車', $this->getCartNum());
    }

    
    /**取得購物車數量 */
    public function getCartNum()
    {
        $cartNum = "";
        $num = model("Cart")->where("user_id = ".$this->auth->id)->sum("num");
        if($num > 0) $cartNum = $num;
        if($num > 99) $cartNum = "99+";
        return $cartNum;
    }

    /**檢查額度 */
    public function checkoutLimit()
    {
        $mUser = model('User')->get(['id' => $this->auth->id, 'status' => 1]);
        if (!$mUser) {
            $this->error('無權操作');
        }

        $mShop = model('Shop')->get(['id' => $mUser->shop_id, 'status' => 1]);
        if (!$mShop) {
            $this->error('無權操作');
        }

        //取得購物車內商品
        $mCart = model('Cart')->alias('c')
        ->join("product_option op","op.id = c.option_id")
        ->join("product p","p.id = op.product_id AND p.status = 1")
        ->join("item i","i.id = op.item_id")
        ->field('op.*, p.product_name, i.img, i.cost, i.stock, c.num')
        ->where("c.user_id = ".$this->auth->id)->select();

        if(!$mCart) $this->error('購物車是空的');

        $total = 0;

        //團主折扣
        if ($mShop->discount_rate > 0) {
            $discountRate = $mShop->discount_rate / 100;
        } else {
            $discountRate = 0;
        }

        //計算購物車內商品
        foreach ($mCart as $v) {
            if($v->num > $v->stock){
                $this->error('['.$v->product_name.' '.$v->option_name.']庫存不足');
            }

            //折扣
            $sale_price = round($v->price * (1 - $discountRate));
            if($sale_price < $v->cost) $sale_price = $v->cost;
            $sub_total = $sale_price*$v->num;
            $total += $sub_total;

        }

        //總額
        $costBefore = $total + $mUser->cost;
        if ($costBefore > $mUser->cost_max_limit) {
            $this->error('超出消費額度');
        }else{
            $this->success();
        }
    }

    /**結帳 */
    public function checkout()
    {
        $mUser = model('User')->get(['id' => $this->auth->id, 'status' => 1]);
        if (!$mUser) {
            $this->error('無權操作');
        }

        $amount = $this->request->request('amount', 0);
        if($amount <= 0){
            $this->error('金額需大於0');
        }

        //產生訂單號
        $orderNo = createOrderNo("O");
        $paramsProduct = [];
        $total = $amount;

        $paymentName = "測試結帳"; //結帳商品名
        $paymentDesc = "測試結帳內容"; //結帳商品內容
        $userName = "王曉明"; //購買人
        $userMobile = "0988666444"; //購買人手機
        $taxIdNumber = ""; //統編

        Db::startTrans();
        try {
            //產生訂單
            $params = [
                'user_id' => $mUser->id,
                'order_no' => $orderNo,
                'total' => $total, //總價
                'product_num' => sizeof($paramsProduct),
                'payment_name' => $paymentName,
                'payment_desc' => $paymentDesc,
                'user_name' => $userName,
                'user_mobile' => $userMobile,
                'tax_id_number' => $taxIdNumber,
                'status' => 0,
            ];
            Log::notice("[" . __METHOD__ . "] 創建訂單:".json_encode($params));
            $mOrder = model('Order')::create($params);
            if ($mOrder === false) {
                Db::rollback();
                Log::notice("[" . __METHOD__ . "] 創建訂單失敗");
                $this->error('系統異常');
            }

            Log::notice("[" . __METHOD__ . "] 創建訂單商品記錄:".json_encode($paramsProduct));
            //產生訂單商品記錄
            $mOrderproduct = model('Orderproduct')->saveAll($paramsProduct);
            if ($mOrderproduct === false) {
                Db::rollback();
                Log::notice("[" . __METHOD__ . "] 創建訂單商品記錄失敗");
                $this->error('系統異常');
            }
            Db::commit();
        } catch (ValidateException $e) {
            Db::rollback();
            Log::notice("[" . __METHOD__ . "] ValidateException :" . $e->getMessage());
            $this->error('系統異常');
        } catch (PDOException $e) {
            Db::rollback();
            Log::notice("[" . __METHOD__ . "] PDOException :" . $e->getMessage());
            $this->error('系統異常');
        } catch (Exception $e) {
            Db::rollback();
            Log::notice("[" . __METHOD__ . "] Exception :" . $e->getMessage());
            $this->error('系統異常');
        }

        $this->success('訂單產生, 前往結帳', $orderNo);
    }
    
    /**取消 */
    public function cancel()
    {
        $mUser = model('User')->get(['id' => $this->auth->id, 'status' => 1]);
        if (!$mUser) {
            $this->error('無權操作');
        }

        $mShop = model('Shop')->get(['id' => $mUser->shop_id, 'status' => 1]);
        if (!$mShop) {
            $this->error('無權操作');
        }
        $cancelRes = false;
        $orderNo = $this->request->request('order', '-');
        $mOrder = model("Order")->where("order_no = '".$orderNo."'")->find();
        if(!$mOrder) $this->error('查無訂單');
        
        //未結帳
        if($mOrder->status == 0){
            $cancelRes = true;
        }elseif($mOrder->status == 1){//已結帳
            if($mOrder->payment_id){
                $mPayment = model('Payment')->get($mOrder->payment_id);
                if($mPayment){
                    if($mPayment->return_amount < $mPayment->amount){
                        if($mPayment->payment_channel == "ecpay"){
                            $cancelRes = $this->ecpayCancel($mPayment);
                        }elseif($mPayment->payment_channel == "newebpay"){
                            $cancelRes = $this->newebpayCancel($mPayment);
                        }
                        if($cancelRes){
                            $mPayment->return_amount = $mPayment->amount;
                            $mPayment->status = 3;
                            $mPayment->save();
                        }
                    }else{ //已取消過,沒有額度可以退款
                        $cancelRes = true;
                    }
                }else{
                    $this->error('查無付款單');
                }
            }else{
                $this->error('查無付款單');
            }
        }elseif($mOrder->status == 2){
            $this->error('訂單已是取消狀態');
        }else{
            $this->error('訂單已刪除');
        }
        
        $itemStock = []; //庫存變動
        $itemStockLog = []; //紀錄庫存變動

        if($cancelRes){
            Db::startTrans();
            try {
                $mOrder->status = 2;
                $mOrder->save();

                // //計算庫存變動
                // $mOrderproduct = model("Orderproduct")->alias('op')
                // ->join("item i","i.id = op.iid")
                // ->field('i.*,op.num')
                // ->where("op.order_no = '".$orderNo."'")->select();
                // if($mOrderproduct){
                //     foreach($mOrderproduct as $v){
                //         $itemStock[] = ['id' => $v->id, 'stock' => $v->stock + $v->num];
                //         $itemStockLog[] = ['item_id' => $v->id, 'amount' => $v->num, 'before' => $v->stock, 'after' => $v->stock + $v->num, 'memo' => '取消訂單'];
                //     }
                // }

                // Log::notice("[" . __METHOD__ . "] 更變庫存:".json_encode($itemStock));
                // $res = model('Item')->saveAll($itemStock);
                // if ($res === false) {
                //     Db::rollback();
                //     Log::notice("[" . __METHOD__ . "] 更變庫存失敗");
                //     $this->error('系統異常');
                // }
    
                // Log::notice("[" . __METHOD__ . "] 紀錄庫存變動:".json_encode($itemStockLog));
                // $res = model('ItemStockLog')->saveAll($itemStockLog);
                // if ($res === false) {
                //     Db::rollback();
                //     Log::notice("[" . __METHOD__ . "] 紀錄庫存變動失敗");
                //     $this->error('系統異常');
                // }

                Db::commit();
            } catch (ValidateException $e) {
                Db::rollback();
                Log::notice("[" . __METHOD__ . "] ValidateException :" . $e->getMessage());
                $this->error('系統異常');
            } catch (PDOException $e) {
                Db::rollback();
                Log::notice("[" . __METHOD__ . "] PDOException :" . $e->getMessage());
                $this->error('系統異常');
            } catch (Exception $e) {
                Db::rollback();
                Log::notice("[" . __METHOD__ . "] Exception :" . $e->getMessage());
                $this->error('系統異常');
            }
            $this->success('訂單已取消');
        }else{
            Log::notice("[" . __METHOD__ . "] 取消訂單失敗");
            $this->error('取消訂單失敗');
        }
    }
    
    /**綠界取消訂單 */
    public function ecpayCancel($mPayment)
    {
        $this->error('尚無取消訂單功能(綠界)');
    }
    
    /**藍新取消訂單 */
    public function newebpayCancel($mPayment)
    {
        if($mPayment->payment_merchant_id != getPaymentMerchantId() || $mPayment->payment_channel != pay_type){
            Log::notice("[" . __METHOD__ . "] 退款失敗 當前使用的金流商店與訂單不同");
            return false;
        }
        //建立付款物件
        $cPay = createPayClass();
        if(!$cPay){
            Log::notice("[" . __METHOD__ . "] 取消訂單失敗 cPay 創建失敗");
            $this->error('取消失敗:環境異常');
        }
        $orderNo = $mPayment->payment_no;
        $extraParams = [
            'Amt' => $mPayment->amount,
        ];
        $cPayRes = $cPay->cancel($orderNo, $extraParams);
        if(!$cPayRes){
            Log::notice("[" . __METHOD__ . "] 取消訂單失敗 cPayRes errorMsg:" . $cPay->getErrorMsg());
            $this->error('取消失敗:'.$cPay->getErrorMsg());
        }
        return true;
    }
    
    /**列印 */
    public function setPrintTime($o = '')
    {
        $orderNoMd5List = explode(",", $o);   
        model('Order')->where("MD5(order_no) IN ('".implode("','", $orderNoMd5List)."') ")->update(["printtime" => time()]);
    }
    
    /**退貨 */
    public function returnOrder()
    {
        $orderNo = $this->request->request('order_no', '-');
        $mUser = model('User')->get(['id' => $this->auth->id, 'status' => 1]);
        if (!$mUser) {
            $this->error('無權操作', $orderNo);
        }

        $mShop = model('Shop')->get(['id' => $mUser->shop_id, 'status' => 1]);
        if (!$mShop) {
            $this->error('無權操作', $orderNo);
        }

        $reason = $this->request->request('reason', '');
        $reasonImgList = $this->request->request('reason_img/a', []);
        $returnType = $this->request->request('return_type', 1); //1換貨 2:退貨
        $returnProductList = $this->request->request('return_product/a', []);

        $mOrder = model("Order")->where("order_no = '".$orderNo."'")->find();
        if(!$mOrder) $this->error('查無訂單', $orderNo);
        
        //非 付款完成 , 已到貨 或 已完成
        if($mOrder->status != 1 || ($mOrder->ship_status != 2 && $mOrder->ship_status != 3 )){
            $this->error('訂單狀態不允許', $orderNo);
        }

        Log::notice("[" . __METHOD__ . "] 進行退貨流程 orderNo:".$orderNo.", returnType:".$returnType);
        Log::notice("[" . __METHOD__ . "] 商品選擇:".json_encode($returnProductList));
        
        $productLastNum = [];
        //剩餘有效數量
        $mOrderproduct = model("Orderproduct")->alias('op')
        ->join("order_product_return opr","op.id = opr.opid","LEFT")
        ->field('op.id, (op.num - IFNULL(SUM(opr.num),0)) as last_num')
        ->where("op.order_no = '$orderNo'")->group("op.id")->select();
        if($mOrderproduct){
            foreach($mOrderproduct as $v){
                $productLastNum[$v->id] = $v->last_num;
            }
        }

        $returnProductData = [];
        foreach($returnProductList as $opid => $num){
            if($num > 0){
                $lastNum = $productLastNum[$opid] ?? 0;
                if($lastNum < $num){
                    $this->error('商品選擇數量異常', $orderNo);
                }

                $reasonImg = $reasonImgList[$opid] ?? "";
                if($reasonImg == ""){
                    $this->error('請上傳照片', $orderNo);
                }
                
                $returnProductData[] = [
                    'opid' => $opid,
                    'return_type' => $returnType,
                    'order_no' => $orderNo,
                    'reason' => $reason,
                    'reason_img' => $reasonImg,
                    'num' => $num,
                ];
            }
        }

        if(!$returnProductData){
            $this->error('請選擇數量', $orderNo);
        }

        try{
            Log::notice("[" . __METHOD__ . "] 創建退貨商品:".json_encode($returnProductData));
            model('Orderproductreturn')->saveAll($returnProductData);
            
            if($returnType == 1){
                $mOrder->return1_status = 1;
                if($mOrder->return2_status == 3){
                    $mOrder->return2_status = 5;
                }
            }else{
                $mOrder->return2_status = 1;
            }
            $mOrder->save();
            
            Db::commit();
        } catch (ValidateException $e) {
            Db::rollback();
            Log::notice("[" . __METHOD__ . "] ValidateException :" . $e->getMessage());
            $this->error('系統異常', $orderNo);
        } catch (PDOException $e) {
            Db::rollback();
            Log::notice("[" . __METHOD__ . "] PDOException :" . $e->getMessage());
            $this->error('系統異常', $orderNo);
        } catch (Exception $e) {
            Db::rollback();
            Log::notice("[" . __METHOD__ . "] Exception :" . $e->getMessage());
            $this->error('系統異常', $orderNo);
        }

        $this->success('處理成功');
    }
}
