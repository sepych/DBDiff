<?php
namespace DBDiff\Params;

use DBDiff\Exceptions\CLIException;
use DBDiff\Exceptions\FSException;
use ReflectionClass;


class ParamsFactory
{
  private static DefaultParams $params;


  /**
   * @return DefaultParams
   * @throws CLIException
   * @throws FSException
   */
  public static function get(): DefaultParams
  {
    if (!isset(self::$params)) {
      self::$params = new DefaultParams;
    } else {
      return self::$params;
    }

    $cli = new CLIGetter;
    $paramsCLI = $cli->getParams();

    if ( ! isset($paramsCLI->debug)) {
      error_reporting(E_ERROR);
    }

    $fs = new FSGetter($paramsCLI);
    $paramsFS = $fs->getParams();
    self::$params = self::mergeParams(self::$params, $paramsFS);
    self::$params = self::mergeParams(self::$params, $paramsCLI);

    if (empty(self::$params->server1)) {
      throw new CLIException("A server is required");
    }

    return self::$params;
  }

  public static function injectParams(DefaultParams $params): void
  {
    self::$params = $params;
  }

  private static function mergeParams(DefaultParams $params, object $paramsFS): DefaultParams
  {
    // merge params using reflection
    $reflection = new ReflectionClass($params);
    $properties = $reflection->getProperties();
    foreach ($properties as $property) {
      $name = $property->getName();
      if (isset($paramsFS->$name)) {
        $params->$name = $paramsFS->$name;
      }
    }
    return $params;
  }
}
