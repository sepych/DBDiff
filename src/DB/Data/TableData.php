<?php
namespace DBDiff\DB\Data;

use DBDiff\DB\DBManager;
use DBDiff\Diff\InsertData;
use DBDiff\Diff\DeleteData;
use DBDiff\Exceptions\DataException;
use DBDiff\Logger;
use Diff\DiffOp\DiffOpAdd;
use Diff\DiffOp\DiffOpRemove;
use Illuminate\Database\Connection;


class TableData
{

  private DBManager $manager;
  private Connection $source;
  private Connection $target;
  private DistTableData $distTableData;
  private LocalTableData $localTableData;

  function __construct(DBManager $manager)
  {
    $this->manager = $manager;
    $this->source = $this->manager->getDB('source');
    $this->target = $this->manager->getDB('target');
    $this->distTableData = new DistTableData($manager);
    $this->localTableData = new LocalTableData($manager);
  }

  public function getIterator($connection, $table): TableIterator
  {
    return new TableIterator($this->{$connection}, $table);
  }

  public function getNewData($table): array
  {
    Logger::info("Now getting new data from table `$table`");
    $diffSequence = [];
    $iterator = $this->getIterator('source', $table);
    $key = $this->manager->getKey('source', $table);
    while ($iterator->hasNext()) {
      $data = $iterator->next(ArrayDiff::$size);
      foreach ($data as $entry) {
        $diffSequence[] = new InsertData($table, [
          'keys' => array_only((array)$entry, $key),
          'diff' => new DiffOpAdd((array)$entry),
        ]);
      }
    }

    return $diffSequence;
  }

  public function getOldData($table): array
  {
    Logger::info("Now getting old data from table `$table`");
    $diffSequence = [];
    $iterator = $this->getIterator('target', $table);
    $key = $this->manager->getKey('target', $table);
    while ($iterator->hasNext()) {
      $data = $iterator->next(ArrayDiff::$size);
      foreach ($data as $entry) {
        $diffSequence[] = new DeleteData($table, [
          'keys' => array_only((array)$entry, $key),
          'diff' => new DiffOpRemove((array)$entry),
        ]);
      }
    }

    return $diffSequence;
  }

  public function getDiff($table): array
  {
    $server1 = $this->source->getConfig('host').':'.$this->source->getConfig('port');
    $server2 = $this->target->getConfig('host').':'.$this->target->getConfig('port');
    $sourceKey = $this->manager->getKey('source', $table);
    $targetKey = $this->manager->getKey('target', $table);
//        $this->checkKeys($table, $sourceKey, $targetKey);

    if ($server1 == $server2) {
      return $this->localTableData->getDiff($table, $sourceKey);
    } else {
      return $this->distTableData->getDiff($table, $sourceKey);
    }
  }

  private function checkKeys($table, $sourceKey, $targetKey): void
  {
    if (empty($sourceKey) || empty($targetKey)) {
      throw new DataException("No primary key found in table `$table`");
    }
    if ($sourceKey != $targetKey) {
      throw new DataException("Unmatched primary keys in table `$table`");
    }
  }
}
