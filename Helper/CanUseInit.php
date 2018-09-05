<?php

namespace Rikudou\CronBundle\Helper;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Trait CanUseInit
 * @internal
 */
trait CanUseInit
{

    private function init()
    {
        /** @var Command $command */
        $command = $this->getApplication()->find("cron:init");
        $command->run(new ArrayInput([]), new BufferedOutput());
    }

}