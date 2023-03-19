<?php
namespace DBDiff\SQLGen\DiffToSQL;

use DBDiff\SQLGen\SQLGenInterface;


class AlterTableDropConstraintSQL implements SQLGenInterface
{

  private $obj;

  function __construct($obj)
  {
    $this->obj = $obj;
  }

  public function getUp(): string
  {
    $table = $this->obj->table;
    $name = $this->obj->name;

    return "ALTER TABLE `$table` DROP CONSTRAINT `$name`;";
  }

  public function getDown(): string
  {
    $table = $this->obj->table;
    $schema = $this->obj->diff->getOldValue();

    return "ALTER TABLE `$table` ADD $schema;";
  }

}
