<?php
namespace DBDiff\Diff;


class AlterTableDropConstraint
{

  public $diff;
  public $name;
  public $table;

  function __construct($table, $name, $diff)
  {
    $this->table = $table;
    $this->name = $name;
    $this->diff = $diff;
  }
}
