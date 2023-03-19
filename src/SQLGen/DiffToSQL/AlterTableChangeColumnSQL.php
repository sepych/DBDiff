<?php
namespace DBDiff\SQLGen\DiffToSQL;

use DBDiff\SQLGen\SQLGenInterface;


class AlterTableChangeColumnSQL implements SQLGenInterface
{

  function __construct($obj)
  {
    $this->obj = $obj;
  }

  public function getUp(): string
  {
    $table = $this->obj->table;
    $column = $this->obj->column;
    $schema = $this->obj->diff->getNewValue();

    return "ALTER TABLE `$table` CHANGE `$column` $schema;";
  }

  public function getDown(): string
  {
    $table = $this->obj->table;
    $column = $this->obj->column;
    $schema = $this->obj->diff->getOldValue();

    return "ALTER TABLE `$table` CHANGE `$column` $schema;";
  }

}
