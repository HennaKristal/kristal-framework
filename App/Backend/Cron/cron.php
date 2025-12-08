<?php defined("ACCESS") or exit("Access Denied");

use Backend\Core\Cron;

/*================================================================================================================*\
|  This file defines software based cron jobs. Each job runs automatically when a page is visited.                 |
|                                                                                                                  |
|  Cron jobs are created by instantiating the Cron class with the following parameters:                            |
|                                                                                                                  |
|    1. Job name                                                                                                   |
|       Any descriptive name. This is used as the log file name and identifier.                                    |
|                                                                                                                  |
|    2. Job script file                                                                                            |
|       The PHP file to execute. This must be the file name of a script inside                                     |
|       /App/Backend/Cron/Tasks/.                                                                                  |
|                                                                                                                  |
|    3. Time between runs                                                                                          |
|       A relative time description that PHP's strtotime function understands, such as:                            |
|           "30 seconds"                                                                                            |
|           "5 minutes"                                                                                             |
|           "2 hours"                                                                                               |
|           "3 days"                                                                                                |
|           "2 weeks"                                                                                               |
|       Internally this is calculated as: now + <your interval>.                                                   |
|       If the interval is invalid, the system falls back to one year and logs a warning.                          |
|                                                                                                                  |
|    4. Activation date (optional)                                                                                 |
|       The job will not run before this date. Format must be "d.m.Y H:i:s".                                       |
|                                                                                                                  |
|  Important notes:                                                                                                |
|    * Cron jobs only run during page visits.                                                                      |
|      If the site receives no traffic, scheduled jobs will be delayed until the next visit.                       |
|                                                                                                                  |
|    * Do not use this system for time critical or guaranteed execution tasks.                                     |
|      For reliable scheduling, use the server's operating system cron instead.                                    |
|                                                                                                                  |
\*================================================================================================================*/

// Example calls
// new Cron("clean_cache_daily", "example_task.php", "1 day");
// new Cron("clean_cache_monthly", "example_task.php", "1 month");
// new Cron("clean_cache_yearly", "example_task.php", "1 year");
// new Cron("clean_cache_every_30_seconds", "example_task.php", "30 seconds");
// new Cron("clean_cache_every_2_days", "example_task.php", "2 days");
// new Cron("clean_cache_daily_starting_2026", "example_task.php", "1 day", "01.01.2026 00:00:00");
