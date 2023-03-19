<?php
namespace DBDiff\Diff;


class AlterTableChangeColumn
{

  private $diff;
  private $column;
  private $table;

  function __construct($table, $column, $diff)
  {
    $this->table = $table;
    $this->column = $column;
    $this->diff = $diff;
  }
}
