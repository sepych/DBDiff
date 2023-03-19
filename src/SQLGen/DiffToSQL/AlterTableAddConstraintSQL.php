<?php
namespace DBDiff\SQLGen\DiffToSQL;

use DBDiff\SQLGen\SQLGenInterface;


class AlterTableAddConstraintSQL implements SQLGenInterface
{

  private $obj;

  function __construct($obj)
  {
    $this->obj = $obj;
  }

  public function getUp(): string
  {
    $table = $this->obj->table;
    $schema = $this->obj->diff->getNewValue();

    return "ALTER TABLE `$table` ADD $schema;";
  }

  public function getDown(): string
  {
    $table = $this->obj->table;
    $name = $this->obj->name;

    return "ALTER TABLE `$table` DROP CONSTRAINT `$name`;";
  }

}
