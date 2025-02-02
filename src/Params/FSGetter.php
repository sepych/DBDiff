<?php
namespace DBDiff\Params;

use DBDiff\Exceptions\FSException;
use Exception;
use StdClass;
use Symfony\Component\Yaml\Yaml;


class FSGetter implements ParamsGetter
{

  private $params;

  function __construct($params)
  {
    $this->params = $params;
  }

  public function getParams(): StdClass
  {
    $params = new StdClass;
    $configFile = $this->getFile();

    if ($configFile) {
      try {
        $config = Yaml::parse(file_get_contents($configFile));
        foreach ($config as $key => $value) {
          $this->setIn($params, $key, $value);
        }
      } catch (Exception) {
        throw new FSException("Error parsing config file");
      }
    }

    return $params;
  }

  protected function getFile()
  {
    $configFile = false;

    if (isset($this->params->config)) {
      $configFile = $this->params->config;
      if ( ! file_exists($configFile)) {
        throw new FSException("Config file not found");
      }
    } else {
      if (file_exists(getcwd().'/.dbdiff')) {
        $configFile = getcwd().'/.dbdiff';
      }
    }

    return $configFile;
  }

  protected function setIn($obj, $key, $value)
  {
    if (str_contains($key, '-')) {
      $parts = explode('-', $key);
      $array = &$obj->$parts[0];
      $array[$parts[1]] = $value;
    } else {
      $obj->$key = $value;
    }
  }

}
