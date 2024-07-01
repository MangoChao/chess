<?php
namespace app\queue\controller;
use app\queue\controller\Base;
use think\Log;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use think\Exception;

class Test extends Base
{
    protected $taskName = "Test"; //任務名稱
    protected $attemptsMax = 5; //重試上限

    //執行任務
    public function doJob($data)
    {
        try{
            Log::info("queue dojob data:".$data);

        } catch (ValidateException $e) {
            Log::info("ValidateException, msg:".$e->getMessage());
            $this->attemptsMax = 0; //不重試
            return false;
        } catch (PDOException $e) {
            Log::info("PDOException, msg:".$e->getMessage());
            $this->attemptsMax = 0; //不重試
            return false;
        } catch (Exception $e) {
            Log::info("Exception, msg:".$e->getMessage());
            $this->attemptsMax = 0; //不重試
            return false;
        }
        return true;
    }
    
    //檢查任務需求
    public function checkJob($data)
    {
        return true;
    }
}
