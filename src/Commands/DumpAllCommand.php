<?php namespace FFerreri\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DumpAllCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('ns:dump')
            ->setDescription('Download all records from all NetSuite known record types')
            ->addOption(
                'pageSize',
                null,
                InputOption::VALUE_OPTIONAL,
                'The page size',
                100
            )
            ->addOption(
                'count',
                null,
                InputOption::VALUE_NONE,
                'Counts records and prints the result.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $record_types = array_filter($this->getApplication()->getConfig()->get('record_types'), function($item) {
           return !in_array($item, $this->getApplication()->getConfig()->get('excluded_record_types'));
        });

        $pageSize = $input->getOption('pageSize');
        $output->writeln("<fg=red;bg=white;options=bold> Started dumping all records, please wait...</fg=red;bg=white;options=bold>");

        $command = $this->getApplication()->find('ns:get');

        foreach ($record_types as $recordType) {
            $arguments = [
                'command'    => 'ns:get',
                'entity'     => $recordType,
                '--pageSize' => $pageSize,
                '--count'    => $input->getOption('count'),
            ];

            $downloadInput = new ArrayInput($arguments);
            $command->run($downloadInput, $output);
        }

        $output->writeln("<fg=red;bg=white;options=bold> Finished dumping all records. </fg=red;bg=white;options=bold>");
    }
}