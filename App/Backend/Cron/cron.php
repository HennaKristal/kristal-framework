<?php defined("ACCESS") or exit("Access Denied");
use Backend\Core\Cron;


/*================================================================================================================*\
|  This file mimics cron jobs. You can create calls to executable php files with given time intervals.             |
|                                                                                                                  |
|  You can create a cron job by calling the Cron class with the following constructors:                            |
|    * (Required) Job name                                                                                         |
|    * (Required) Job script location                                                                              |
|    * (Required) Interval (daily, weekly, monthly, yearly or custom interval [2, "days"], [3, "weeks"], etc.)     |
|    * Execute time during the day in 24 hour format (at H:i:s format), cron job will be fired at this given time  |
|    * Activation date, the cron job won't be executed before this date.                                           |
|                                                                                                                  |
|  Note that these cron jobs will only be ran if the page is visited,                                              |
|  if no one visits the site the cron jobs will not be ran until the next visit.                                   |
|  Because of this you should not use this class for tasks that needs to be done reliably or at a specific time.   |
\*================================================================================================================*/


// Example calls
// new Cron("clean_cache_daily", "App/Backend/Cron/Tasks/clean_cache.php", "daily", "12:00:00", "2019-10-05 11:00:00");
// new Cron("clean_cache_weekly", "App/Backend/Cron/Tasks/clean_cache.php", "weekly", "12:00:00");
// new Cron("clean_cache_monthly", "App/Backend/Cron/Tasks/clean_cache.php", "monthly", "12:00:00");
// new Cron("clean_cache_yearly", "App/Backend/Cron/Tasks/clean_cache.php", "yearly", "12:00:00");
// new Cron("clean_cache_every_120_seconds", "App/Backend/Cron/Tasks/clean_cache.php", [120, "seconds"]);
// new Cron("clean_cache_every_2_days", "App/Backend/Cron/Tasks/clean_cache.php", [2, "days"], "12:00:00");
