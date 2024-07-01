<?php

// namespace app\index\controller;

// use app\common\controller\Frontend;
// use think\Log;
// use fast\Http;

// /**
//  * line notify相關設定
//  */
// class LineNotify extends Frontend
// {
//     protected $layout = 'base';
//     protected $noNeedLogin = '*';
//     protected $noNeedRight = '*';
//     protected $lineNotifyClientId = '1IJ0L3d8qsmKXwgzcwTEmU';
//     protected $lineNotifyClientSecret = 'mqE4fZncjKiGGwZSfOB1cLjpSL6DGMbK7H5rWDFzoIh';

//     public function _initialize()
//     {
//         parent::_initialize();
//         Log::init(['type' => 'File', 'log_name' => 'line_notify']);
//     }

//     public function authorize()
//     {
//         $state = "傳遞資料";
//         $redirect_uri = $this->site_url['furl'] . "/index/line_notify/callback";
//         $authorize_url = 'https://notify-bot.line.me/oauth/authorize?response_type=code&scope=notify';
//         $authorize_url .= '&state=' . $state . '&client_id=' . $this->lineNotifyClientId . '&redirect_uri=' . urlencode($redirect_uri);
//         $this->redirect($authorize_url);
//     }

//     public function callback()
//     {
//         $msg = "完成設定,請關閉視窗";
//         $code = $this->request->request('code', null);
//         $state = $this->request->request('state', null);
//         Log::info('------------------Notifycallback--------------------');
//         Log::info('code:' . $code);
//         Log::info('state:' . $state);
//         Log::info('-------------------------------------------');
//         if ($code and $state) {
//             $url = 'https://notify-bot.line.me/oauth/token';
//             $params = [
//                 'grant_type' => 'authorization_code',
//                 'code' => $code,
//                 'redirect_uri' => $this->site_url['furl'] . "/index/line_notify/callback",
//                 'client_id' => $this->lineNotifyClientId,
//                 'client_secret' => $this->lineNotifyClientSecret,
//             ];
//             Log::notice("[" . __METHOD__ . "] notify-bot請求參數:");
//             Log::notice($params);
//             $response = Http::post($url, $params);
//             Log::notice("[" . __METHOD__ . "] notify-bot回應內容:");
//             Log::notice($response);
//             $response = json_decode($response, true);
//             if (isset($response['access_token']) and isset($response['status']) and $response['status'] == 200) {
//                 Log::notice("[" . __METHOD__ . "] 產生Token成功");
//                 $access_token = $response['access_token'] ?? "";
//                 //TODO 儲存access_token至DB
//                 Log::notice("[" . __METHOD__ . "] 發送token至設定群");
//                 $notify_message = $access_token;

//                 //發送notify
//                 $notify_options = [
//                     CURLOPT_HTTPHEADER  => [
//                         'Authorization:Bearer ' . $access_token,
//                         'Content-Type:application/x-www-form-urlencoded',
//                     ]
//                 ];

//                 $notify_params = [
//                     'message' => "綁定成功",
//                 ];
//                 curl_post('https://notify-api.line.me/api/notify', $notify_params, $notify_options);
//                 Log::notice("[" . __METHOD__ . "] 完成綁定");
//             } else {
//                 $msg = "產生Token失敗";
//             }
//         } else {
//             Log::notice("[" . __METHOD__ . "] 缺少參數");
//             $msg = "缺少參數";
//         }

//         $html = "<span style='font-size: 24px;'>$msg</span>";
//         return $html;
//     }
// }
