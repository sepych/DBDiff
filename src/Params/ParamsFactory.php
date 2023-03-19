<?php
namespace DBDiff\Params;

use DBDiff\Exceptions\CLIException;


class ParamsFactory
{

  public static function get(): object
  {
    $params = new DefaultParams;

    $cli = new CLIGetter;
    $paramsCLI = $cli->getParams();

    if ( ! isset($paramsCLI->debug)) {
      error_reporting(E_ERROR);
    }

    $fs = new FSGetter($paramsCLI);
    $paramsFS = $fs->getParams();
    $params = (new ParamsFactory)->merge($params, $paramsFS);

    $params = (new ParamsFactory)->merge($params, $paramsCLI);

    if (empty($params->server1)) {
      throw new CLIException("A server is required");
    }

    return $params;
  }

  protected function merge($obj1, $obj2): object
  {
    return (object)array_merge((array)$obj1, (array)$obj2);
  }
}
