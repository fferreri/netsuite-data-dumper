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
use League\Csv\Writer;
use FFerreri\Misc\Tools;

class ExportRecordsCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('ns:export')
            ->setDescription('Export records to CSV format')
            ->addArgument(
                'entity',
                null,
                InputOption::VALUE_REQUIRED,
                'The entity type name to export'
            )
            ->addOption(
                'fields',
                null,
                InputOption::VALUE_REQUIRED,
                'The fields to export'
            )
            ->addOption(
                'outfile',
                null,
                InputOption::VALUE_OPTIONAL,
                'The output file path and name'
            )
            ->addOption(
                'skip',
                null,
                InputOption::VALUE_OPTIONAL,
                'The number of records to skip',
                0
            )
            ->addOption(
                'max',
                null,
                InputOption::VALUE_OPTIONAL,
                'The number of records to export',
                9999999999
            );


    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ti = microtime(true);

        $entity_type   = $input->getArgument('entity');
        $export_fields = explode(",", $input->getOption('fields'));
        $max_records   = $input->getOption('max');
        $skip_records  = $input->getOption('skip');
        $output_file   = $input->getOption('outfile');

        $output->writeln(sprintf('<info>Exporting "%s"</info>', $entity_type));

        $out_dir = sprintf("%s/output/%s", $this->getApplication()->getAppPath(), $entity_type);
        if (!file_exists($out_dir)) {
            $output->writeln(sprintf('<error>Directory not found. [%s]</error>', $out_dir));
            return;
        }

        $writer = ($output_file) ? Writer::createFromPath($output_file, 'w') : $output;

        // process fields and build CSV headers
        foreach($export_fields as $i => $field) {
            @list($field, $field_as) = explode(" as ", $field);
            $headers[] = $field_as ?: $field;
            $export_fields[$i] = $field;
        }
        $this->write($writer, $headers);

        // process each json file file
        $idx = 0;
        foreach (glob($out_dir . '/*.json') as $file_name) {
            if ($idx++ < $skip_records) continue;
            $data = [];
            $record = Tools::objectToDotNotationArray(json_decode(file_get_contents($file_name)));

            foreach ($export_fields as $field) {
                $data[] = (isset($record[$field])) ? $record[$field] : null;
            }
            $this->write($writer, $data);

            if ($idx == $max_records + $skip_records) break;
        }

        $output->writeln(sprintf("<info>Finished exporting %d \"%s\" records in %01.1f seconds</info>", $idx, $entity_type, microtime(true) - $ti));
    }

    private function write($writer, array $data)
    {
        if ($writer instanceof Writer) {
            $writer->insertOne($data);
        } else {
            $writer->writeln(implode(",", $data));
        }
    }
}