<?php namespace Backend\Core;
defined("ACCESS") or exit("Access Denied");


class Cron
{
    const INTERVAL_DAY = 'day';
    const INTERVAL_WEEK = 'week';
    const INTERVAL_MONTH = 'month';
    const INTERVAL_YEAR = 'year';

    private $name;
    private $task;
    private $interval;
    private $run_time;


    public function __construct($name, $task, $interval, $run_time = null, $start_date = null)
    {
        if (!is_string($name) || !is_string($task))
        {
            if (PRODUCTION_MODE)
            {
                debuglog("Cron name or task name was invalid or empty.", "error");
                return;
            }
            else
            {
                exit("Cron name or task name was invalid or empty.");
            }
        }

        if (is_array($interval))
        {
            $this->interval = array(
                "amount" => $interval[0],
                "type" => $interval[1]
            );
        }
        else
        {
            switch (strtolower($interval))
            {
                case "daily":
                    $this->interval = ["type" => self::INTERVAL_DAY, "amount" => 1];
                    break;
                case "weekly":
                    $this->interval = ["type" => self::INTERVAL_WEEK, "amount" => 1];
                    break;
                case "monthly":
                    $this->interval = ["type" => self::INTERVAL_MONTH, "amount" => 1];
                    break;
                case "yearly":
                    $this->interval = ["type" => self::INTERVAL_YEAR, "amount" => 1];
                    break;
                default:
                    $this->interval = ["type" => self::INTERVAL_DAY, "amount" => 1];
                    debuglog("invalid interval for cron job: $name Value given was $interval", "warning");
            }
        }

        $this->name = $name;
        $this->task = $task;
        $this->run_time = isset($run_time) ? $run_time : "H:i:s";

        // Do not run cron job if activation date has not been reached
        if (isset($start_date) && strtotime(date('Y-m-d H:i:s')) < strtotime($start_date))
        {
            return;
        }

        if ($this->readyToExecute())
        {
            $this->updateTaskTimer();
            $this->executeTask();
        }
    }


    private function executeTask()
    {
        $taskFile = $this->task;

        if (file_exists($taskFile) && is_readable($taskFile))
        {
            require_once($taskFile);
        }
        else
        {
            if (PRODUCTION_MODE)
            {
                debuglog("Task file not found or not readable: $taskFile", "error");
                return;
            }
            else
            {
                exit("Task file not found or not readable: $taskFile");
            }
        }
    }
    

    private function readyToExecute()
    {
        $file = $this->getLogLocation();
    
        if (file_exists($file))
        {
            $value = include $file;
    
            if ($value === null)
            {
                return true;
            }
    
            return strtotime(date('Y-m-d H:i:s')) > strtotime($value);
        }
        else
        {
            return true;
        }
    }
    

    private function updateTaskTimer()
    {
        $nextRunDate = date("Y-m-d {$this->run_time}", strtotime(date("Y-m-d {$this->run_time}") . "+{$this->interval['amount']} {$this->interval['type']}"));
        $content = "<?php\n\n" . "// Last run at: " . date('Y-m-d H:i:s') . "\n" . "// Next run at: $nextRunDate" . "\n\n" . "return '$nextRunDate';";
        file_put_contents($this->getLogLocation(), $content);
    }
    
    
    private function getLogLocation()
    {
        return WEBSITE_ROOT . "/App/Backend/Cron/Logs/" . sanitizeFileName($this->name) . ".php";
    }
}
