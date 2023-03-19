<?php

namespace DBDiff;

use DBDiff\Exceptions\CLIException;
use DBDiff\Exceptions\FSException;
use DBDiff\Params\ParamsFactory;
use DBDiff\DB\DiffCalculator;
use DBDiff\SQLGen\SQLGenerator;
use DBDiff\Exceptions\BaseException;
use DBDiff\Logger;
use DBDiff\Templater;
use Exception;


class DBDiff
{

  /**
   * @return void
   * @throws CLIException
   * @throws FSException
   */
  public function run(): void
  {
    // Increase memory limit
    ini_set('memory_limit', '512M');

    try {
      $params = ParamsFactory::get();

      // Diff
      $diffCalculator = new DiffCalculator;
      $diff = $diffCalculator->getDiff($params);

      // Empty diff
      if (empty($diff['schema']) && empty($diff['data'])) {
        Logger::info("Identical resources");
      } else {
        // SQL
        $sqlGenerator = new SQLGenerator($diff);
        $up = '';
        $down = '';
        if ($params->include !== 'down') {
          $up = $sqlGenerator->getUp();
        }
        if ($params->include !== 'up') {
          $down = $sqlGenerator->getDown();
        }

        // Generate
        $template = new Templater($params, $up, $down);
        $template->output();
      }

      Logger::success("Completed");
    } catch (BaseException $e) {
      Logger::error($e->getMessage(), true);
    } catch (Exception $e) {
      Logger::error("Unexpected error: ".$e->getMessage());
      throw $e;
    }
  }
}
