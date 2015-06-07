<?php namespace FFerreri\Commands;
/*
 * Copyright [yyyy] [name of copyright owner]
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

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
                50
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