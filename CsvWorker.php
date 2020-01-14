<?php

use Keboola\Csv\CsvOptions;

/**
 * @ Author: Jone Pólvora
 * @ Create Time: 2020-01-13 20:25:49
 * @ Description:
 * @ Modified by: Jone Pólvora
 * @ Modified time: 2020-01-13 20:28:24
 */

class CsvWorker
{
  private $configFileName = '';
  private $delimiter = CsvOptions::DEFAULT_DELIMITER;
  private $config = [];

  public function __construct(string $configFileName, $delimiter = ';')
  {
    $this->configFileName = $configFileName;
    $this->delimiter = $delimiter;
  }

  public function run()
  {
    $this->parseConfig();

    $csvReader = new Keboola\Csv\CsvReader($this->config['input'], $this->delimiter, "", "", $this->config['skip_lines']);
    $csvWriter = new Keboola\Csv\CsvWriter($this->config['output']);

    foreach ($csvReader as $row) {
      var_dump($row);
      //$convertedRow = $this->convertRow($row);

      $csvWriter->writeRow($row);
    }
  }

  private function parseConfig()
  {
    if (!is_file($this->configFileName)) throw new Exception('invalid filename. terminate.');
    $contents = file_get_contents($this->configFileName);
    $conf = $this->config = json_decode($contents, true);
    var_dump($conf);

    //validate required options and fill with defaults

    if (!$conf['input']) throw new Exception('Missing \'input\' config');
    if (!$conf['output']) throw new Exception('Missing \'output\' config.');
    if (!$conf['mapping']) throw new Exception('Missing \'mapping\' config.');
    if (!$conf['skip_lines']) throw new Exception('Missing \'skip_lines\' config');
  }

  private function convertRow($row)
  {
    $columns = explode(';', $row);
    foreach ($columns as $col) {
      echo $col . PHP_EOL;
    }
    return $row;
  }

  public function dispose()
  {
    //clear resources and exit
    exit(0);
  }
}
