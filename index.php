<?php


/**
 * @ Author: Jone Pólvora
 * @ Create Time: 2020-01-13 20:25:49
 * @ Description:
 * @ Modified by: Jone Pólvora
 * @ Modified time: 2020-01-13 20:28:24
 */

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/CsvWorker.php';

if (count($argv) < 2) {
  die('Faltando parametro obrigatório: arquivo de configuração config.json ');
}

var_dump($argv);
$worker = new CsvWorker(__DIR__ . DIRECTORY_SEPARATOR . $argv[1]);
$worker->run();
$worker->dispose();
