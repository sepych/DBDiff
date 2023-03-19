<?php
namespace DBDiff\Diff;


class AlterTableEngine
{

  private $prevEngine;
  private $engine;
  private $table;

  function __construct($table, $engine, $prevEngine)
  {
    $this->table = $table;
    $this->engine = $engine;
    $this->prevEngine = $prevEngine;
  }
}
