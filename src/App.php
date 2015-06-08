<?php namespace FFerreri;
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

use Fungku\NetSuite\NetSuiteService;
use Noodlehaus\Config;
use Symfony\Component\Console\Application;

class App extends Application {
    private $netsuite_service;
    private $app_dir;
    private $app_conf;

    public function __construct($name = 'UNKNOWN', $version = 'UNKNOWN') {
        $this->app_dir = realpath(__DIR__ . '/../');

        try {
            $this->app_conf = Config::load([
                $this->app_dir . '/config/general.ini',
                $this->app_dir . '/config/record_types.ini',
                $this->app_dir . '/config/excluded_record_types.ini'
            ]);
        } catch(\Exception $e) {
            print "\n+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++\nPlease check your configuration files.  You must create a valid\n'general.ini' (you can use the supplied 'general.ini.template')\nfile with your Netsuite  credentials in order to run this tool.\n+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++\n";
            exit(1);
        }

        $ns_config = array(
            "endpoint" => $this->app_conf->get('netsuite.endpoint'),
            "host"     => $this->app_conf->get('netsuite.host'),
            "email"    => $this->app_conf->get('netsuite.email'),
            "password" => $this->app_conf->get('netsuite.password'),
            "role"     => $this->app_conf->get('netsuite.role'),
            "account"  => $this->app_conf->get('netsuite.account'),
        );

        $this->netsuite_service = new NetSuiteService($ns_config);

        $this->netsuite_service->setLogPath($this->app_dir . "/logs");
        if ($this->app_conf->get('debug.enabled', false)) {
            $this->netsuite_service->logRequests(true);
        }

        parent::__construct($name, $version);
    }

    public function getAppPath()
    {
        return $this->app_dir;
    }

    public function getNetsuiteService()
    {
        return $this->netsuite_service;
    }

    public function getConfig()
    {
        return $this->app_conf;
    }
}