<?php
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class ConfigHelper extends AbstractHelper
{
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getServerInstance()
    {
        $result = "";

        if(isset($this->config["serverInstance"])){
            $result = $this->config["serverInstance"]["id"];
        }

        return $result;
    }
}
