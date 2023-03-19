<?php
namespace DBDiff\SQLGen\DiffToSQL;

use DBDiff\SQLGen\SQLGenInterface;


class AlterTableEngineSQL implements SQLGenInterface
{

  private $obj;

  function __construct($obj)
  {
    $this->obj = $obj;
  }

  public function getUp(): string
  {
    $table = $this->obj->table;
    $engine = $this->obj->engine;

    return "ALTER TABLE `$table` ENGINE = $engine;";
  }

  public function getDown(): string
  {
    $table = $this->obj->table;
    $prevEngine = $this->obj->prevEngine;

    return "ALTER TABLE `$table` ENGINE = $prevEngine;";
  }

}
