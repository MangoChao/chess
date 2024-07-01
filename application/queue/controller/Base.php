<?php
namespace app\queue\controller;
use think\queue\Job;
use think\Log;
use think\Config;

class Base
{
    protected $taskName = "Base"; //任務名稱
    protected $attemptsMax = 3; //重試上限
    
    private $logPrefix = ""; //log前綴
    private $siteUrl = [];

    public function __construct()
    {
        Log::init(['type' => 'File', 'log_name' => 'queue_Notify']);
        $this->logPrefix = "[".$this->taskName."]";
    }
    
    //取得site url
    public function getSiteUrl()
    {
        if(!$this->siteUrl){
            $site = Config::get("site");
            $this->siteUrl = $site['url'];
        }
        return $this->siteUrl;
    }

    public function fire(Job $job, $data)
    {
        Log::info($this->logPrefix." Starting the job...".date("H:i:s"));
        //檢查任務需求
        if(!$this->checkJob($data)){
            Log::info($this->logPrefix." Job checkJob falid, Job delete.");
            $job->delete();
            return;
        }

        //執行任務
        if($this->doJob($data)){ //成功
            Log::info($this->logPrefix." Job completed.");
            $job->delete();
        }else{ //失敗
            Log::info($this->logPrefix." Job falid. Times:".$job->attempts());
            if($job->attempts() >= $this->attemptsMax){ //執行超過就刪除
                Log::info($this->logPrefix." Job delete.");
                $job->delete();
            }else{
                Log::info($this->logPrefix." Job release.");
                $job->release(2); //2秒後重新加入
            }
        }
    }
    
    //執行任務
    public function doJob($data)
    {
        return true;
    }
    
    //檢查任務需求
    public function checkJob($data)
    {
        return true;
    }
}
