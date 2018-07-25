This is a simple cron implementation just for my testing.

It shouldn't really be used anywhere near production.

## How to install

Via composer: `composer require rikudou/cron-bundle`.

The bundle should be enabled automatically if you use the Symfony Flex.

## How to use

Every cron job must implement the `Rikudou\CronBundle\Cron\CronJobInterface`
(every class that implements the interface is automatically registered as cron job),
which defines two methods:

- `string getCronExpression()` - the [cron expression](https://en.wikipedia.org/wiki/Cron#Overview)
- `execute(InputInterface $input, OutputInterface $output, LoggerInterface $logger)` - the
method that will be executed, you can write to console output, use console input and use logger

Example:

```php
<?php

namespace App\CronJobs;

use Psr\Log\LoggerInterface;
use Rikudou\CronBundle\Cron\CronJobInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MyTestCronJob implements CronJobInterface
{

    /**
     * The cron expression for triggering this cron job
     * @return string
     */
    public function getCronExpression(): string
    {
        return "*/5 * * * *";
    }

    /**
     * This method will be executed when cron job runs
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param LoggerInterface $logger
     * @return mixed
     */
    public function execute(InputInterface $input, OutputInterface $output, LoggerInterface $logger)
    {
        $logger->debug("The cron job was executed!");
    }
}

```

The cron jobs are executed using the console command `cron:run`, this command
should be executed every minute.

Example for crontab:

`* * * * * php /path/to/project/bin/console cron:run`


## Listing cron jobs

If you want to see list of all jobs, run `cron:list` command from Symfony console.

Example: `php bin/console cron:list`

Example output:

```
+----------------------------+-----------------+--------+---------------------+
| Class                      | Cron expression | Is due | Next run            |
+----------------------------+-----------------+--------+---------------------+
| App\CronJobs\MyTestCronJob | */5 * * * *     | no     | 2018-07-25 14:10:00 |
+----------------------------+-----------------+--------+---------------------+
```

The listing will also let you know if the cron expression is invalid:

```
+----------------------------+-----------------+----------------------------+----------------------------+
| Class                      | Cron expression | Is due                     | Next run                   |
+----------------------------+-----------------+----------------------------+----------------------------+
| App\CronJobs\MyTestCronJob | * * * * * *     | Cron expression is invalid | Cron expression is invalid |
+----------------------------+-----------------+----------------------------+----------------------------+
```