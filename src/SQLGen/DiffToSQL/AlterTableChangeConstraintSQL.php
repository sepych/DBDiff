<?php
namespace DBDiff\SQLGen\DiffToSQL;

use DBDiff\SQLGen\SQLGenInterface;


class AlterTableChangeConstraintSQL implements SQLGenInterface
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
    $schema = $this->obj->diff->getNewValue();

    return "ALTER TABLE `$table` DROP CONSTRAINT `$name`;\nALTER TABLE `$table` ADD $schema;";
  }

  public function getDown(): string
  {
    $table = $this->obj->table;
    $name = $this->obj->name;
    $schema = $this->obj->diff->getOldValue();

    return "ALTER TABLE `$table` DROP CONSTRAINT `$name`;\nALTER TABLE `$table` ADD $schema;";
  }

}
