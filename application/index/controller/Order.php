<?php

// namespace app\index\controller;

// use app\common\controller\Frontend;
// use think\Log;

// /**
//  * 訂單產生頁
//  */
// class Order extends Frontend
// {
//     protected $layout = 'base';
//     protected $noNeedRight = ['*'];

//     public function _initialize()
//     {
//         parent::_initialize();
//     }

//     /**收銀台 */
//     public function orderPage($order = '')
//     {
//         if(pay_type == "ecpay") return $this->ecpayOrderPage($order);
//         if(pay_type == "newebpay") return $this->newebpayOrderPage($order);
//     }
    
//     /**
//      * 綠界收銀台(未測試,需調整) 
//      * */
//     public function ecpayOrderPage($order = '-')
//     {
//         // $mOrder = model('Order')->get(['order_no'=> $order, 'status' => 0]);
//         // if(!$mOrder){
//         //     $this->error('查無訂單');
//         // }

//         // $returnUrl = $this->site_url['furl'].'/index/user/index';
//         // $notifyUrl = $this->site_url['api'].'/notify/payment/pay/ecpay';

//         // $paymentNo = createOrderNo("P");

//         // //產生結帳單
//         // $params = [
//         //     'order_no' => $mOrder->order_no,
//         //     'payment_no' => $paymentNo,
//         //     'amount' => $mOrder->total, //實付
//         //     'payment_channel' => "ecpay",
//         //     'return_url' => $returnUrl,
//         //     'notify_url' => $notifyUrl,
//         // ];
//         // $mPayment = model('Payment')::create($params);

//         // if(!$mPayment) $this->error('訂單產生異常');

//         // $TradeDesc = $mOrder->payment_desc;
//         // $ItemName = $mOrder->payment_name;

//         // $TotalAmount = $mPayment->amount;
//         // $CheckMacValue = "";
//         // $OrderResultURL = "";
//         // $postData = [
//         //     'MerchantID' => getPaymentMerchantId(),
//         //     'MerchantTradeNo' => $mPayment->payment_no,
//         //     'MerchantTradeDate' => date("Y/m/d H:i:s"),
//         //     'PaymentType' => 'aio',
//         //     'TotalAmount' => $TotalAmount,
//         //     'TradeDesc' => $TradeDesc,
//         //     'ItemName' => $ItemName,
//         //     'ReturnURL' => $notifyUrl,
//         //     'ChoosePayment' => 'ALL',
//         //     'IgnorePayment' => 'ATM#CVS#BARCODE',
//         //     'EncryptType' => 1,
//         //     'ClientBackURL' => $returnUrl,
//         //     'OrderResultURL' => $OrderResultURL,
//         //     // 'PaymentInfoURL' => $ReturnURL
//         // ];

//         // ksort($postData);
//         // $signStr = "";
//         // foreach($postData as $k => $v){
//         //     $signStr .= $k."=".$v."&";
//         // }
//         // $signStr = "HashKey=".$this->ecpay_HashKey."&".$signStr."HashIV=".$this->ecpay_HashIV;
//         // $signStr = strtolower(urlencode($signStr));
//         // $signStr = toDotNetUrlEncode($signStr);
//         // $CheckMacValue = strtoupper(hash('sha256', $signStr));

//         // $szHtml = '<!doctype html>';
//         // $szHtml .= '<html>';
//         // $szHtml .= '<head>';
//         // $szHtml .= '<meta charset="utf-8">';
//         // $szHtml .= '</head>';
//         // $szHtml .= '<body>';
//         // $szHtml .= '<form name="ebpay" id="ebpay" method="post" action="' . $this->ecpay_url['pay'] . '" style="display:none;">';
//         // $szHtml .= '<input name="MerchantID" value="' . $postData['MerchantID'] . '" type="hidden">';
//         // $szHtml .= '<input name="MerchantTradeNo" value="' . $postData['MerchantTradeNo'] . '"   type="hidden">';
//         // $szHtml .= '<input name="MerchantTradeDate" value="' . $postData['MerchantTradeDate'] . '"   type="hidden">';
//         // $szHtml .= '<input name="PaymentType" value="' . $postData['PaymentType'] . '" type="hidden">';
//         // $szHtml .= '<input name="TotalAmount" value="' . $postData['TotalAmount'] . '" type="hidden">';
//         // $szHtml .= '<input name="TradeDesc" value="' . $postData['TradeDesc'] . '" type="hidden">';
//         // $szHtml .= '<input name="ItemName" value="' . $postData['ItemName'] . '" type="hidden">';
//         // $szHtml .= '<input name="ReturnURL" value="' . $postData['ReturnURL'] . '" type="hidden">';
//         // $szHtml .= '<input name="ChoosePayment" value="' . $postData['ChoosePayment'] . '" type="hidden">';
//         // $szHtml .= '<input name="IgnorePayment" value="' . $postData['IgnorePayment'] . '" type="hidden">';
//         // $szHtml .= '<input name="EncryptType" value="' . $postData['EncryptType'] . '" type="hidden">';
//         // $szHtml .= '<input name="ClientBackURL" value="' . $postData['ClientBackURL'] . '" type="hidden">';
//         // $szHtml .= '<input name="OrderResultURL" value="' . $postData['OrderResultURL'] . '" type="hidden">';
//         // // $szHtml .= '<input name="PaymentInfoURL" value="' . $postData['PaymentInfoURL'] . '" type="hidden">';
//         // $szHtml .= '<input name="CheckMacValue"  value="' . $CheckMacValue . '" type="hidden">';
//         // $szHtml .= '</form>';
//         // $szHtml .= '<script type="text/javascript">';
//         // $szHtml .= 'document.getElementById("ebpay").submit();';
//         // $szHtml .= '</script>';
//         // $szHtml .= '</body>';
//         // $szHtml .= '</html>';

//         // return $szHtml;
//     }
    
//     /**藍新收銀台 */
//     public function newebpayOrderPage($order = '-')
//     {
//         $mOrder = model('Order')->get(['order_no'=> $order, 'status' => 0]);
//         if(!$mOrder){
//             $this->error('查無訂單');
//         }
        
//         $returnUrl = $this->site_url['furl'].'/index/user/index';
//         $notifyUrl = $this->site_url['api'].'/notify/payment/pay/newebpay';

//         $paymentNo = createOrderNo("P");

//         //產生結帳單
//         $params = [
//             'order_no' => $mOrder->order_no,
//             'payment_no' => $paymentNo,
//             'payment_merchant_id' => getPaymentMerchantId(),
//             'amount' => $mOrder->total, //實付
//             'payment_channel' => pay_type,
//             'return_url' => $returnUrl,
//             'notify_url' => $notifyUrl,
//         ];
//         $mPayment = model('Payment')::create($params);

//         if(!$mPayment) $this->error('訂單產生異常');

//         $ItemDesc = $mOrder->payment_desc;
//         $Email = $this->auth->email ?? "";

//         $TotalAmount = $mPayment->amount;

//         //建立付款物件
//         $cPay = createPayClass();
//         if(!$cPay){
//             Log::notice("[" . __METHOD__ . "] cPay 創建失敗");
//             $this->error('訂單產生異常');
//         }
//         $params = [
//             'order_no' => $mPayment->payment_no,
//             'amount' => $TotalAmount,
//             'return_url' => $returnUrl,
//             'notify_url' => $notifyUrl,
//         ];

//         $extraParams = [
//             'Email' => $Email,
//             'ItemDesc' => $ItemDesc,
//         ];
//         return $cPay->pay($params, $extraParams);
//     }
// }
