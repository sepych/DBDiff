<?php
namespace DBDiff\SQLGen\DiffToSQL;

use DBDiff\SQLGen\SQLGenInterface;


class AlterTableDropColumnSQL implements SQLGenInterface
{

  private $obj;

  function __construct($obj)
  {
    $this->obj = $obj;
  }

  public function getUp(): string
  {
    $table = $this->obj->table;
    $column = $this->obj->column;

    return "ALTER TABLE `$table` DROP `$column`;";
  }

  public function getDown(): string
  {
    $table = $this->obj->table;
    $schema = $this->obj->diff->getOldValue();

    return "ALTER TABLE `$table` ADD $schema;";
  }

}
