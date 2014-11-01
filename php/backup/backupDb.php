#!/usr/bin/php
<?php
require_once('../classes/ConfigCls.php');
require_once('mysqldump.php');


$dbConfig = ConfigCls::getDbConfig();

$dumpSettings = array(
    'include-tables' => array('nurses', 'patients', 'rounds', 'visits'),
    'exclude-tables' => array(),
    'compress' => CompressMethod::NONE, /* CompressMethod::[GZIP, BZIP2, NONE] */
    'no-data' => false,             /* http://dev.mysql.com/doc/refman/5.1/en/mysqldump.html#option_mysqldump_no-data */
    'add-drop-table' => false,      /* http://dev.mysql.com/doc/refman/5.1/en/mysqldump.html#option_mysqldump_add-drop-table */
    'single-transaction' => true,   /* http://dev.mysql.com/doc/refman/5.1/en/mysqldump.html#option_mysqldump_single-transaction */
    'lock-tables' => false,         /* http://dev.mysql.com/doc/refman/5.1/en/mysqldump.html#option_mysqldump_lock-tables */
    'add-locks' => true,            /* http://dev.mysql.com/doc/refman/5.1/en/mysqldump.html#option_mysqldump_add-locks */
    'extended-insert' => true       /* http://dev.mysql.com/doc/refman/5.1/en/mysqldump.html#option_mysqldump_extended-insert */
);

$dump = new MySQLDump($dbConfig['database'],$dbConfig['username'],$dbConfig['password'],$dbConfig['hostname'], $dumpSettings);
$dump->start('infidi'.date('Y-m-d', time()).'.sql');

?>

