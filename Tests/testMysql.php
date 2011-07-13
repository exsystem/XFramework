<?php
//set_include_path(get_include_path() . ':/media/ExSystem-HD/Documents/ZendStudioWorkspace/FrameworkDSW');
//set_include_path(get_include_path().';E:\\Documents\\ZendStudioWorkspace\\FrameworkDSW');
set_include_path(get_include_path() . ':/Volumes/ExSystem-HD/Documents/ZendStudioWorkspace/FrameworkDSW');

require_once 'FrameworkDSW/Database_Mysql.php';

$mDriver = new TMysqlDriver();
TMap::PrepareGeneric(array ('K' => 'string', 'V' => 'string'));
$mConfig = new TMap();
$mConfig['Username'] = 'root';
$mConfig['Password'] = '';
$mConfig['ConnectTimeout'] = '2';
$mConfig['Socket']='/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock';


$mConn = $mDriver->Connect('MySQL://localhost/test', $mConfig);

$mDropDDL = <<<'EOD'
DROP TABLE IF EXISTS `tmysqlconnectiontest`
EOD;
$mCreateDDL = <<<'EOD'
CREATE TABLE IF NOT EXISTS `tmysqlconnectiontest` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `_int` int(11) NOT NULL,
  `_bool` tinyint(1) NOT NULL,
  `_vchar` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `_float` float NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1
EOD;
$mTruncateDDL = <<<'EOD'
TRUNCATE TABLE `tmysqlconnectiontest`
EOD;

$mConn->Execute($mDropDDL);
$mConn->Execute($mCreateDDL);
$mConn->Execute($mTruncateDDL);

for ($i = 1; $i < 50; ++$i) {
    echo $mConn->Execute("insert into `tmysqlconnectiontest` values({$i}, 10, 1, '中国hi', 20.5)");
}
$mStmt = $mConn->CreateStatement(TResultSetType::eScrollSensitive(), TConcurrencyType::eUpdatable());
$mRs = $mStmt->Query('select * from tmysqlconnectiontest');
$mRow=$mRs->current();


foreach ($mRs as $mRow) {
    echo $mRow['id']->getValue(); //memory leak
    echo $mRow['_int']->getValue(); //memory leak
    echo $mRow['_bool']->getValue(); //memory leak
    echo $mRow['_vchar']->getValue(); //memory leak
    echo $mRow['_float']->getValue(); //memory leak
    
    TPrimativeParam::PrepareGeneric(array ('T' => 'string'));
    $mRow['_vchar'] = new TPrimativeParam('modified');
    $mRow->Update(); //memory leak
    echo $mRow['_vchar']->getValue(); //memory leak

    //$mRow->Delete();
    $mRow->Delete();
}

Framework::Free($mRs);
Framework::Free($mStmt);
Framework::Free($mConn);
Framework::Free($mDriver);