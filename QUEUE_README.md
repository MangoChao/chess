# 停止所有Queue ============================================
#!/bin/bash
# 程式路徑
project_path="/www/wwwroot/base_site/"
# 佇列名稱
queue_name="general"

php $project_path/think queue:restart &


# 執行監聽 ============================================
#!/bin/bash

# 程式路徑
project_path="/www/wwwroot/base_site"
# 佇列名稱
queue_name="general"

queue_command="php $project_path/think queue:listen --queue $queue_name"
# 检查是否有指定队列的进程在运行
queue_process=$(ps -ef | grep "$queue_command" | grep -v "grep")

if [ -z "$queue_process" ]; then
    # 如果没有在运行，则启动监听进程
    $queue_command &
fi


# 執行監聽work ============================================
#!/bin/bash

# 程式路徑
project_path="/www/wwwroot/base_site"
# 佇列名稱
queue_name="general"

# 自動循環 最多重排3次 空佇列睡眠10秒
queue_command="php $project_path/think queue:work --queue $queue_name --daemon --tries 10 --sleep 10"
# 检查是否有指定队列的进程在运行
queue_process=$(ps -ef | grep "$queue_command" | grep -v "grep")

if [ -z "$queue_process" ]; then
    # 如果没有在运行，则启动监听进程
    $queue_command &
    echo "Start $queue_command"
else
    echo "Is Started"
fi



