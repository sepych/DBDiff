<?php
namespace DBDiff\SQLGen\DiffToSQL;

use DBDiff\SQLGen\SQLGenInterface;


class DeleteDataSQL implements SQLGenInterface
{

  private $obj;

  function __construct($obj)
  {
    $this->obj = $obj;
  }

  public function getUp(): string
  {
    $table = $this->obj->table;
    $keys = $this->obj->diff['keys'];
    array_walk($keys, function (&$value, $column) {
      $value = '`'.$column."` = '".addslashes($value)."'";
    });
    $condition = implode(' AND ', $keys);

    return "DELETE FROM `$table` WHERE $condition;";
  }

  public function getDown(): string
  {
    $table = $this->obj->table;
    $values = $this->obj->diff['diff']->getOldValue();
    $values = array_map(function ($el) {
      return "'".addslashes($el)."'";
    }, $values);

    return "INSERT INTO `$table` VALUES(".implode(',', $values).");";
  }

}
