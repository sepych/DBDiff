<?php
namespace DBDiff\DB\Data;

use DBDiff\DB\DBManager;
use DBDiff\Params\ParamsFactory;
use DBDiff\Diff\SetDBCollation;
use DBDiff\Exceptions\DataException;
use DBDiff\Logger;


class DBData
{

  private DBManager $manager;

  function __construct(DBManager $manager)
  {
    $this->manager = $manager;
  }

  function getDiff(): array
  {
    $params = ParamsFactory::get();

    $diffSequence = [];

    // Tables
    $tableData = new TableData($this->manager);

    $sourceTables = $this->manager->getTables('source');
    $targetTables = $this->manager->getTables('target');

    if (!empty($params->tablesToIgnore)) {
      $sourceTables = array_diff($sourceTables, $params->tablesToIgnore);
      $targetTables = array_diff($targetTables, $params->tablesToIgnore);
    }

    $commonTables = array_intersect($sourceTables, $targetTables);
    foreach ($commonTables as $table) {
      $diffs = $tableData->getDiff($table);
      $diffSequence = array_merge($diffSequence, $diffs);
    }

    $addedTables = array_diff($sourceTables, $targetTables);
    foreach ($addedTables as $table) {
      $diffs = $tableData->getNewData($table);
      $diffSequence = array_merge($diffSequence, $diffs);
    }

    $deletedTables = array_diff($targetTables, $sourceTables);
    foreach ($deletedTables as $table) {
      $diffs = $tableData->getOldData($table);
      $diffSequence = array_merge($diffSequence, $diffs);
    }

    return $diffSequence;
  }

}
