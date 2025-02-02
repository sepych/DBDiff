<?php
namespace DBDiff\SQLGen;

use DBDiff\Logger;

class SQLGenerator implements SQLGenInterface
{

  private DiffSorter $diffSorter;
  private array $diff;

  function __construct($diff)
  {
    $this->diffSorter = new DiffSorter;
    $this->diff = array_merge($diff['schema'], $diff['data']);
  }

  public function getUp(): string
  {
    Logger::info("Now generating UP migration");
    $diff = $this->diffSorter->sort($this->diff, 'up');

    return MigrationGenerator::generate($diff, 'getUp');
  }

  public function getDown(): string
  {
    Logger::info("Now generating DOWN migration");
    $diff = $this->diffSorter->sort($this->diff, 'down');

    return MigrationGenerator::generate($diff, 'getDown');
  }
}
