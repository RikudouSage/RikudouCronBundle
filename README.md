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
+----------------------------+---------+-----------------+--------+---------------------+
| Class                      | Enabled | Cron expression | Is due | Next run            |
+----------------------------+---------+-----------------+--------+---------------------+
| App\CronJobs\MyTestCronJob | yes     | */5 * * * *     | no     | 2018-08-07 18:30:00 |
+----------------------------+---------+-----------------+--------+---------------------+

```

The listing will also let you know if the cron expression is invalid:

```
+----------------------------+---------+-----------------+----------------------------+----------------------------+
| Class                      | Enabled | Cron expression | Is due                     | Next run                   |
+----------------------------+---------+-----------------+----------------------------+----------------------------+
| App\CronJobs\MyTestCronJob | yes     | */5 * * * * *   | Cron expression is invalid | Cron expression is invalid |
+----------------------------+---------+-----------------+----------------------------+----------------------------+
```

## Disabling a cron job

If you have a cron job that you don't want to delete but at the same time you don't
want to trigger it, you can define method `isEnabled()` and let it return false.

You can also use the packaged helper trait `Rikudou\CronBundle\Helper\DisabledCronJob`.

**Example 1 (wihout trait):**

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

    public function isEnabled() {
        return false;
    }
}
```

**Example 2 (using trait):**

```php
<?php

namespace App\CronJobs;

use Psr\Log\LoggerInterface;
use Rikudou\CronBundle\Cron\CronJobInterface;
use Rikudou\CronBundle\Helper\DisabledCronJob;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MyTestCronJob implements CronJobInterface
{

    use DisabledCronJob;

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

In both cases the output will be similar to:

```
+----------------------------+---------+-----------------+--------+---------------------+
| Class                      | Enabled | Cron expression | Is due | Next run            |
+----------------------------+---------+-----------------+--------+---------------------+
| App\CronJobs\MyTestCronJob | no      | */5 * * * *     | no     | 2018-08-07 18:40:00 |
+----------------------------+---------+-----------------+--------+---------------------+
```

## Running commands as cron jobs

If you want to run commands as cron jobs, you must configure them in yaml file.

Example:

```yaml
rikudou_cron:
    commands:
      clearCache:
        command: cache:clear
        cron_expression: "*/2 * * * *"
```

This setting will run cache:clear every two minutes.

The config file will be created (if it doesn't already exist) when you first
run any cron command (`cron:list`, `cron:run`).

Example output from `cron:list`:

```
Classes
+----------------------------+---------+-----------------+--------+---------------------+
| Class                      | Enabled | Cron expression | Is due | Next run            |
+----------------------------+---------+-----------------+--------+---------------------+
| App\CronJobs\MyTestCronJob | no      | */5 * * * *     | no     | 2018-09-06 15:25:00 |
+----------------------------+---------+-----------------+--------+---------------------+
Commands
+-------------+-----------------+--------+---------------------+
| Command     | Cron expression | Is due | Next run            |
+-------------+-----------------+--------+---------------------+
| cache:clear | */2 * * * *     | yes    | 2018-09-06 15:24:00 |
+-------------+-----------------+--------+---------------------+
```