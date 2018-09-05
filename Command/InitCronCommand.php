<?php

namespace Rikudou\CronBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class InitCronCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName("cron:init")
            ->setDescription("Creates yaml config file");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $this->getContainer()->getParameter("kernel.project_dir") . "/config/packages/rikudou_cron.yaml";
        if(file_exists($file)) {
            return 0;
        }
        $yaml = Yaml::dump([
            "rikudou_cron" => [
                "commands" => [
                    "clearCache" => [
                        "command" => "cache:clear",
                        "cron_expression" => "* * * * *"
                    ]
                ]
            ]
        ], 10);

        $rows = explode("\n", $yaml);

        foreach ($rows as $key => $row) {
            if($key > 1 && $key < count($rows) - 1) {
                $rows[$key] = "#$row";
            }
        }

        $yaml = implode("\n", $rows);

        file_put_contents($file, $yaml);
        return 0;
    }

}