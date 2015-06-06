<?php namespace FFerreri;

use Fungku\NetSuite\NetSuiteService;
use Noodlehaus\Config;
use Symfony\Component\Console\Application;

class App extends Application {
    private $netsuite_service;
    private $app_dir;
    private $app_conf;

    public function __construct($name = 'UNKNOWN', $version = 'UNKNOWN') {
        $this->app_dir = realpath(__DIR__ . '/../');

        $this->app_conf = Config::load([
            $this->app_dir . '/config/general.ini',
            $this->app_dir . '/config/record_types.ini',
            $this->app_dir . '/config/excluded_record_types.ini'
        ]);

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