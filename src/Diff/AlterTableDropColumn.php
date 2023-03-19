<?php
namespace DBDiff\Diff;


class AlterTableDropColumn
{

  public $diff;
  public $column;
  public $table;

  function __construct($table, $column, $diff)
  {
    $this->table = $table;
    $this->column = $column;
    $this->diff = $diff;
  }
}
