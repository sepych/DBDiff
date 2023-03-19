<?php
namespace DBDiff\Diff;


class AlterTableDropConstraint
{

  private $diff;
  private $name;
  private $table;

  function __construct($table, $name, $diff)
  {
    $this->table = $table;
    $this->name = $name;
    $this->diff = $diff;
  }
}
