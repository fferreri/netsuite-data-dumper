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

use Fungku\NetSuite\Classes\SearchMoreWithIdRequest;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Fungku\NetSuite\Classes\SearchRequest;

class GetRecordsCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('ns:get')
            ->setDescription('Download records from NetSuite')
            ->addArgument(
                'entity',
                InputOption::VALUE_REQUIRED,
                'The entity type name to count'
            )
            ->addOption(
                'pageSize',
                null,
                InputOption::VALUE_OPTIONAL,
                'The page size',
                50
            )
            ->addOption(
                'startPage',
                null,
                InputOption::VALUE_OPTIONAL,
                'The start page index (index base is 1)',
                1
            )
            ->addOption(
                'endPage',
                null,
                InputOption::VALUE_OPTIONAL,
                'The end page index (index base is 1)',
                9999999999
            )
            ->addOption(
                'count',
                null,
                InputOption::VALUE_NONE,
                'Counts records and prints the result.'
            );


    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->getApplication()->initializeNetsuiteService();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ti = microtime(true);
        $service = $this->getApplication()->getNetsuiteService();

        $entity_type = $input->getArgument('entity');
        $start_page_index = $input->getOption('startPage');
        $end_page_index = $input->getOption('endPage');
        $page_size = $input->getOption('pageSize');
        $is_counting = $input->getOption('count');

        if ($is_counting) {
            $page_size = 5; // set page size to the smallest possible size.
        }

        $output->writeln(sprintf('<info>Processing the "%s" record type</info>', $entity_type));


        $className = sprintf("Fungku\\NetSuite\\Classes\\%sSearchBasic", ucfirst($entity_type));
        if (!class_exists($className)) {
            $output->writeln(sprintf("Search for \"%s\" entity type is not supported.", $className));
            return;
        }
        $search = new $className();

        $request = new SearchRequest();
        $request->searchRecord = $search;

        $service->setSearchPreferences(false, $page_size);
        $searchResponse = $service->search($request);

        if (!$searchResponse->searchResult->status->isSuccess) {
            $output->writeln(sprintf("<error>SEARCH ERROR\n%s</error>", json_encode($searchResponse->searchResult->status, JSON_PRETTY_PRINT)));
        } else {
            $result = $searchResponse->searchResult;
            $totalRecords = $result->totalRecords;
            $records = $result->recordList->record;
            $searchId = $result->searchId;

            $out_dir = sprintf("%s/output/%s", $this->getApplication()->getAppPath(), $entity_type);
            if (!file_exists($out_dir)) {
                mkdir($out_dir);
            }

            if ($is_counting) {
                $output->writeln(sprintf("<fg=black;bg=white;options=bold>* Total records: %d </fg=black;bg=white;options=bold>", $totalRecords));

                $downloadedFiles = count(glob($out_dir."/*.json"));

                $output->writeln(sprintf("<fg=black;bg=white;options=bold>* Downloaded records: %d (%d remaining) </fg=black;bg=white;options=bold>", $downloadedFiles, $totalRecords - $downloadedFiles));
                return;
            }

            if ($start_page_index > 1 && $start_page_index <= $result->totalPages) {
                $next = new SearchMoreWithIdRequest();
                $next->searchId = $searchId;
                $next->pageIndex = $start_page_index;

                $searchResponse = $service->searchMoreWithId($next);

                $result = $searchResponse->searchResult;
                $records = $result->recordList->record;
                $searchId = $result->searchId;
            }


            $output->writeln(sprintf("<info>%d records were found.</info>", $totalRecords));

            $idx = 1;
            while ($totalRecords > 0 && $result->pageIndex <= $result->totalPages && $result->pageIndex <= $end_page_index) {
                foreach ($records as $record)  {
                    $out_file = sprintf("%s/%s.json", $out_dir, str_pad($record->internalId, 12, '0', STR_PAD_LEFT));

                    file_put_contents($out_file, json_encode($record, JSON_PRETTY_PRINT));

                    echo $output->writeln(sprintf('<info>Saved %d of %d (page %d of %d)</info>', $idx++, $totalRecords, $result->pageIndex, $result->totalPages));
                }

                $next = new SearchMoreWithIdRequest();
                $next->searchId = $searchId;
                $next->pageIndex = $result->pageIndex + 1;

                $searchResponse = $service->searchMoreWithId($next);

                $result = $searchResponse->searchResult;
                $records = $result->recordList->record;
                $searchId = $result->searchId;
            }
            $idx--;
            $output->writeln(sprintf("<fg=black;bg=white;options=bold>*** Finished downloading %d (%d%%) \"%s\" records in %01.1f seconds ***</fg=black;bg=white;options=bold>", $totalRecords, ($totalRecords > 0) ? 100*$idx/$totalRecords : 100, $entity_type, microtime(true) - $ti));
        }
    }
}