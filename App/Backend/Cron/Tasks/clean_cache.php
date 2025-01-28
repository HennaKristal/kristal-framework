
<?php defined("ACCESS") or exit("Access Denied");

// Example task for cron jobs
// Deletes files from cache folder

foreach (glob("App/Public/Cache/*") as $file)
{
    if (is_file($file))
    {
        unlink($file);
    }
}
