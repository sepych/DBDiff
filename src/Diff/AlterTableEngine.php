<?php
namespace DBDiff\Diff;


class AlterTableEngine
{

  public $prevEngine;
  public $engine;
  public $table;

  function __construct($table, $engine, $prevEngine)
  {
    $this->table = $table;
    $this->engine = $engine;
    $this->prevEngine = $prevEngine;
  }
}
