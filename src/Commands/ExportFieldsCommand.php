<?php namespace FFerreri\Commands;
/*
 * Copyright 2015 Federico Ferreri
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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use FFerreri\Misc\Tools;

class ExportFieldsCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('ns:fields')
            ->setDescription('Export record fields to the STDOUT for a given file (JSON output from ns:get or ns:dump), no matter the entity type.')
            ->addArgument(
                'filename',
                InputOption::VALUE_REQUIRED,
                'The input file path and name'
            )
            ->addOption(
                'separator',
                null,
                InputOption::VALUE_REQUIRED,
                'A character or string to separate field names.',
                "\n"
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ti = microtime(true);

        $input_file = $input->getArgument('filename');
        $separator  = $input->getOption('separator');

        $output->writeln(sprintf('<info>Exporting fields for "%s"</info>', $input_file));

        if (!file_exists($input_file)) {
            $output->writeln('<error>File not found.</error>');
            return;
        }

        $record = Tools::objectToDotNotationArray(json_decode(file_get_contents($input_file)));

        $output->writeln(implode($separator, array_keys($record)));

        $output->writeln(sprintf("<info>Finished exporting %d field names in %01.1f seconds</info>", count(array_keys($record)), microtime(true) - $ti));
    }
}