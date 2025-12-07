<?php namespace Backend\Core;
defined("ACCESS") or exit("Access Denied");

class Cron
{
    private $tasksPath = WEBSITE_ROOT . "/App/Backend/Cron/Tasks/";
    private $logsPath = WEBSITE_ROOT . "/App/Backend/Cron/Logs/";
    private $jobName;
    private $taskFileName;
    private $interval;
    private $startDate;
    private $lockHandle;

    public function __construct($jobName, $taskFileName, $interval = "1 day", $startDate = null)
    {
        $this->jobName = sanitizeFileName((string)$jobName);
        $this->taskFileName = (string)$taskFileName;
        $this->interval = $interval;
        $this->startDate = $startDate;
        $this->validate();
    }

    private function validate()
    {
        // Job name can not be empty
        if (trim($this->jobName) === "")
        {
            debuglog("One of the cron job names was empty. Skipped cron job execution.", "warning");
            return;
        }

        // Task file can not be empty
        if (trim($this->taskFileName) === "")
        {
            debuglog("Cron job called '{$this->jobName}' had an empty task file name. Skipped cron job execution.", "warning");
            return;
        }

        // Do not execute before activation date
        if ($this->startDate !== null && time() < strtotime($this->startDate))
        {
            return;
        }

        $this->run();
    }

    private function run()
    {
        if (!$this->isReadyToExecute())
        {
            return;
        }

        if (!$this->acquireLock())
        {
            return;
        }

        // Prevention: Try to close user connection so they don't wait
        if (function_exists('fastcgi_finish_request'))
        {
            session_write_close();
            fastcgi_finish_request();
        }

        try
        {
            $this->updateExecutionLog();
            $this->executeTask();
        }
        catch (\Throwable $e)
        {
            debuglog("Cron Job '{$this->jobName}' failed to execute due to error", "error");
        }

        $this->releaseLock();
    }

    private function executeTask()
    {
        $taskPath = realpath($this->$tasksPath . $this->taskFileName);
        $taskRoot = realpath($this->$tasksPath);

        if ($taskPath === false || strpos($taskPath, $taskRoot) !== 0)
        {
            debuglog("Cron task path was invalid or outside task directory: {$this->taskFileName}", "error");
            return;
        }

        if (!is_readable($taskPath))
        {
            debuglog("Cron task file was not readable: {$this->taskFileName}", "error");
            return;
        }

        include $taskPath;
    }

    // Check Execution Window -----------------------------------------------------
    private function isReadyToExecute()
    {
        $logFile = $this->getLogFilePath();

        if (!file_exists($logFile))
        {
            return true;
        }

        $data = json_decode(file_get_contents($logFile), true);

        if (!isset($data["nextRun"]))
        {
            return true;
        }

        return time() >= strtotime($data["nextRun"]);
    }

    private function updateExecutionLog()
    {
        $currentTime = time();
        $nextRunStamp = strtotime("+" . $this->interval, $currentTime);
    
        if ($nextRunStamp === false)
        {
            debuglog("Cron interval was invalid for job {$this->jobName}. Using default fallback of 1 year.", "warning");
            $nextRunStamp = strtotime("+1 year", $currentTime);
        }
    
        $data = [
            "lastRun" => date("Y-m-d H:i:s", $currentTime),
            "nextRun" => date("Y-m-d H:i:s", $nextRunStamp)
        ];
    
        file_put_contents($this->getLogFilePath(), json_encode($data, JSON_PRETTY_PRINT));
    }

    private function acquireLock()
    {
        $lockPath = $this->getLockFilePath();
        $handle = fopen($lockPath, "c");

        if (!$handle)
        {
            return false;
        }

        if (!flock($handle, LOCK_EX | LOCK_NB))
        {
            fclose($handle);
            return false;
        }

        $this->lockHandle = $handle;
        return true;
    }

    private function releaseLock()
    {
        if ($this->lockHandle)
        {
            flock($this->lockHandle, LOCK_UN);
            fclose($this->lockHandle);
        }
    }

    private function getLogFilePath()
    {
        return $this->logsPath . $this->jobName . ".json";
    }

    private function getLockFilePath()
    {
        return $this->logsPath . $this->jobName . ".lock";
    }
}
