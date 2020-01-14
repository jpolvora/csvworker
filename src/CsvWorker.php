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
  private $delimiter = '';
  private $config = null;

  public function __construct(string $configFileName, $delimiter = ';')
  {
    $this->configFileName = $configFileName;
    $this->delimiter = $delimiter;
  }

  public function run()
  {
    $this->parseConfig();

    $csvReader = new Keboola\Csv\CsvReader($this->config['input'], $this->delimiter, "", "", $this->config['skip_lines']);
    $csvWriter = new Keboola\Csv\CsvWriter($this->config['output'], $this->delimiter, "");

    $mapping = $this->config['mapping'];
    $line = 0;
    $error_message = '';
    foreach ($csvReader as $row) {
      try {
        $line++;
        $convertedRow = $this->convertRow($mapping, $row);
        $csvWriter->writeRow($convertedRow);
      } catch (\Throwable $th) {
        $error_message = sprintf("Erro na linha %d : %s %s", $line, PHP_EOL, $th->getMessage());
        break;
      }
    }
    if (empty($error_message)) return true;

    throw new Exception($error_message);
  }

  private function parseConfig()
  {
    if (!is_file($this->configFileName)) throw new Exception('Arquivo inválido: ' . $this->configFileName);
    $contents = file_get_contents($this->configFileName);
    $conf = $this->config = json_decode($contents, true);
    //var_dump($conf);

    //validate required options and fill with defaults

    if (!$conf['input']) throw new Exception('Missing \'input\' config');
    if (!$conf['output']) throw new Exception('Missing \'output\' config.');
    if (!$conf['mapping']) throw new Exception('Missing \'mapping\' config.');
    if (!$conf['skip_lines']) throw new Exception('Missing \'skip_lines\' config');
  }

  private function convertRow($mapping, $row)
  {
    $result = array();
    $cols = count($row);
    foreach ($mapping as $map) {
      $src_index = $map['src'];
      if ($src_index >= $cols) continue;
      $col = $row[$src_index];
      if (array_key_exists('transform', $map)) {
        switch ($map['transform']) {
          case "uppercase":
            $col = strtoupper($row[$src_index]);
            break;
          case "lowercase":
            $col = strtolower($row[$src_index]);
            break;
          case "only_numbers":
            $col = filter_var($col, FILTER_SANITIZE_NUMBER_INT);
            break;
          default:
            break;
        }
      }

      if (array_key_exists('maxlength', $map)) {
        $maxlen = $map['maxlength'];
        if (is_numeric($maxlen)) {
          $col_length = strlen($col);
          if ($col_length > (int) $maxlen) {
            $col = substr($col, 0, (int) $maxlen);
          }
        }
      }

      if (array_key_exists('minlength', $map)) {
        $minlen = $map['minlength'];
        if (is_numeric($minlen)) {
          $col_length = strlen($col);
          if ($col_length < (int) $minlen) {
            $col = str_pad($col, $minlen, ' ');
          }
        }
      }

      if (array_key_exists('required', $map)) {
        $required = (bool) $map['required'];
        if ($required === true && empty($col)) {
          if (!array_key_exists('default', $map)) {
            throw new Exception('Coluna obrigatória sem valor default');
          }
          $defval = $map['default'];
          $col = $defval;
        }
      }

      array_push($result, $col);
    }

    return $result;
  }


  public function dispose()
  {
    //clear resources and exit
    exit(0);
  }
}