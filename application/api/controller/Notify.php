<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\Log;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use Exception;

/**
 * 接收回調接口
 */
class Notify extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        Log::init(['type' => 'File', 'log_name' => 'Notify']);
        $request = $this->request->request();
        Log::notice("[".__METHOD__."]收到請求:".json_encode($request));
    }

    public function payment($pay = null)
    {
        if($pay == "ecpay") return $this->ecpay();
        if($pay == "newebpay") return $this->newebpay();
        return "fail";
    }

    //綠界
    public function ecpay()
    {
        // $paymentChannel = "ecpay";
        // $post = $this->request->post();
        // $r = false;

        // $postMerchantID = $post['MerchantID'] ?? "-";
        // if ($postMerchantID == $this->ecpay_MerchantID) {

        //     $postData = $post;
        //     unset($postData['CheckMacValue']);
        //     ksort($postData);
        //     $signStr = "";
        //     foreach($postData as $k => $v){
        //         $signStr .= $k."=".$v."&";
        //     }
        //     $signStr = "HashKey=".$this->ecpay_HashKey."&".$signStr."HashIV=".$this->ecpay_HashIV;
        //     $signStr = strtolower(urlencode($signStr));
        //     $signStr = toDotNetUrlEncode($signStr);
        //     $CheckMacValue = strtoupper(hash('sha256', $signStr));

        //     $postCheckMacValue = $post['CheckMacValue']??"-";
        //     $postMerchantTradeNo = $post['MerchantTradeNo']??"-";
        //     $postTradeAmt = $post['TradeAmt']??"-";
        //     if($postCheckMacValue == $CheckMacValue){
        //         $mPayment = model('Payment')->get(['payment_no' => $postMerchantTradeNo, 'payment_channel' => $paymentChannel]);
        //         Log::notice("[".__METHOD__."] mPayment :".json_encode($mPayment));
        //         if($mPayment){
        //             if($mPayment->status == 0){
        //                 if($postTradeAmt == $mPayment->amount){
        //                     try {
        //                         if($post['RtnCode'] == 1){
        //                             $status = 1;
        //                             $paytime = time();
        //                         }else{
        //                             $status = 2;
        //                             $paytime = null;
        //                         }
        //                         $TotalSuccessTimes = null;
        //                         $params = [
        //                             'trans_order_no' => $post['TradeNo']??"",
        //                             'RtnCode' => $post['RtnCode']??null,
        //                             'RtnMsg' => $post['RtnMsg']??"",
        //                             'SimulatePaid' => $post['SimulatePaid']??null,
        //                             'PaymentDate' => $post['PaymentDate']??"",
        //                             'PaymentDate_strtotime' => strtotime($post['PaymentDate'])??null,
        //                             'PaymentType' => $post['PaymentType']??"",
        //                             'CheckMacValue' => $post['CheckMacValue']??"",
        //                             'TotalSuccessTimes' => $TotalSuccessTimes,
        //                             'result' => json_encode($post),
        //                             'status' => $status,
        //                             'paytime' => $paytime
        //                         ];
        //                         Log::notice("[".__METHOD__."] params:". json_encode($params));
        //                         $r = $mPayment->allowField(true)->save($params);
        //                     } catch (ValidateException $e) {
        //                         Log::notice("[".__METHOD__."] ValidateException :".$e->getMessage());
        //                     } catch (PDOException $e) {
        //                         Log::notice("[".__METHOD__."] PDOException :".$e->getMessage());
        //                     } catch (Exception $e) {
        //                         Log::notice("[".__METHOD__."] Exception :".$e->getMessage());
        //                     }
        //                 }else{
        //                     Log::notice("[".__METHOD__."] 金額不符: ".$postTradeAmt);
        //                 }
        //             }else{
        //                 Log::notice("[".__METHOD__."] 付款單狀態不符 status:".$mPayment->status);
        //             }
        //         }else{
        //             Log::notice("[".__METHOD__."] 付款單不存在 : payment_no:".$postMerchantTradeNo);
        //         }
        //     }else{
        //         Log::notice("[".__METHOD__."] 檢查碼不符 CheckMacValue:".$CheckMacValue." | post CheckMacValue:".$postCheckMacValue);
        //     }
        // }else{
        //     Log::notice("[".__METHOD__."] MerchantID 錯誤: ".$postMerchantID);
        // }
        
        // if ($r !== false) {
        //     Log::notice("[".__METHOD__."] 處理付款單完成");
        //     $this->processOrder($mPayment);
        //     Log::notice("[".__METHOD__."] 回調成功");
        //     return "1|OK";
        // }else{
        //     Log::notice("[".__METHOD__."] 回調失敗");
        //     return "回調失敗";
        // }
    }

    //藍新
    public function newebpay()
    {
        $returnText = "success";
        $paymentChannel = "newebpay";
        $post = $this->request->post();
    
        $dbRes = false;
        $mPayment = false;

        //建立付款物件
        $cPay = createPayClass($paymentChannel);
        if(!$cPay){
            Log::notice("[" . __METHOD__ . "] cPay 創建失敗");
            return $returnText;
        }
        $res = $cPay->callback($post);

        if($res === false){
            Log::notice("[".__METHOD__."] ErrorMsg :".$cPay->getErrorMsg());
            return $returnText;
        }
        Log::notice("[".__METHOD__."] cPay res :".json_encode($res));
        
        $mPayment = model('Payment')->get(['payment_no' => $res['order_no'], 'payment_channel' => $paymentChannel]);
        if(!$mPayment){
            Log::notice("[".__METHOD__."] 付款單不存在 : payment_no:".$res['order_no']);
            return $returnText;
        }
        Log::notice("[".__METHOD__."] mPayment :".json_encode($mPayment));
        
        if($mPayment->status != 0){
            Log::notice("[".__METHOD__."] 付款單狀態不符 status:".$mPayment->status);
            return $returnText;
        }
        
        if($res['amount'] != $mPayment->amount){
            Log::notice("[".__METHOD__."] 金額不符,廠商: ".$res['amount']."/我方:".$mPayment->amount);
            return $returnText;
        }

        Log::notice("[".__METHOD__."] 開始處理訂單");
        try {
            if($res['success']){
                Log::notice("[".__METHOD__."] 付款狀態:成功");
                $status = 1;
                $paytime = $res['pay_time'];
            }else{
                Log::notice("[".__METHOD__."] 付款狀態:失敗");
                $status = 2;
                $paytime = null;
            }
            $params = [
                'trans_order_no' => $res['trans_order_no'],
                'PaymentType' => $res['payment_type'],
                'PayerAccount5Code' => $res['PayerAccount5Code'] ?? "",
                'result' => json_encode($res['decrypted_params']),
                'status' => $status,
                'paytime' => $paytime
            ];
            Log::notice("[".__METHOD__."] params:". json_encode($params));
            Log::notice("[".__METHOD__."] 寫入訂單..");
            $dbRes = $mPayment->allowField(true)->save($params);
        } catch (ValidateException $e) {
            Log::notice("[".__METHOD__."] ValidateException :".$e->getMessage());
        } catch (PDOException $e) {
            Log::notice("[".__METHOD__."] PDOException :".$e->getMessage());
        } catch (Exception $e) {
            Log::notice("[".__METHOD__."] Exception :".$e->getMessage());
        }

        if ($dbRes !== false) {
            Log::notice("[".__METHOD__."] 處理付款單完成");
            $this->processOrder($mPayment);
            Log::notice("[".__METHOD__."] 處理訂單成功");
        }else{
            Log::notice("[".__METHOD__."] 處理訂單失敗");
        }
        return $returnText;
    }

    //處理訂單
    private function processOrder($mPayment)
    {
        Log::notice("[".__METHOD__."] 開始處理訂單");
        if($mPayment){
            if($mPayment->status == 1){
                $mOrder = model("Order")->where("order_no = '".$mPayment->order_no."'")->find();
                if(!$mOrder){
                    Log::notice("[".__METHOD__."] 查無對應訂單");
                }
                Log::notice("[".__METHOD__."] 核對訂單");
                if($mOrder->status == 2){
                    Log::notice("[".__METHOD__."] 訂單已取消, 多付錢");
                }
                if($mOrder->status == 1){
                    Log::notice("[".__METHOD__."] 訂單已完成, 重複回調");
                }
                if($mOrder->status == 0){
                    Log::notice("[".__METHOD__."] 訂單未完成, 首次回調");
                    $mOrder->status = 1;
                    $mOrder->confirm = 1;
                    $mOrder->payment_id = $mPayment->id;
                    $mOrder->paytime = $mPayment->paytime;
                    $mOrder->save();
                    Log::notice("[".__METHOD__."] 改變訂單狀態, 付款完成");
                }
            }
            Log::notice("[".__METHOD__."] 處理訂單完成");
        }else{
            Log::notice("[".__METHOD__."] 處理訂單異常");
        }
    }
}
