<?php
//set_include_path(get_include_path() . ':/media/ExSystem-HD/Documents/ZendStudioWorkspace/FrameworkDSW');
//set_include_path(get_include_path().';E:\\Documents\\ZendStudioWorkspace\\FrameworkDSW');
//set_include_path(get_include_path() . ':/Volumes/ExSystem-HD/Documents/ZendStudioWorkspace/FrameworkDSW');
set_include_path(get_include_path() . ':/Users/exsystem/Documents/ZendStudioWorkspace/FrameworkDSW');
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


$mSt = $mConn->PrepareStatement(TResultSetType::eScrollSensitive(), TConcurrencyType::eReadOnly());
$mSt->setCommand('insert into tmysqlconnectiontest (_int, _bool, _vchar, _float) values (:a, :b, :c, :d)');
TPrimitiveParam::PrepareGeneric(array('T'=>'integer'));
$a=new TPrimitiveParam(10);
$mSt->BindParam(':a', $a);
TPrimitiveParam::PrepareGeneric(array('T'=>'boolean'));
$b=new TPrimitiveParam(true);
$mSt->BindParam(':b', $b);
TPrimitiveParam::PrepareGeneric(array('T'=>'string'));
$c=new TPrimitiveParam('string');
$mSt->BindParam(':c', $c);
TPrimitiveParam::PrepareGeneric(array('T'=>'float'));
$d=new TPrimitiveParam(1.5);
$mSt->BindParam(':d', $d);
$rrr=$mSt->Execute();
echo $rrr, "\r\n";

Framework::Free($mSt);

echo $mConn->Execute("PREPARE sDel FROM 'DELETE FROM `tmysqlconnectiontest` WHERE `id`=?'");
echo $mConn->Execute("SET @idd = 1");
echo $mConn->Execute("EXECUTE sDel USING @idd");
echo 'DONE!!', "\r\n";

$mStmt = $mConn->CreateStatement(TResultSetType::eScrollSensitive(), TConcurrencyType::eUpdatable());
$mRs = $mStmt->Query('select id,_int,_bool,_vchar,_float from tmysqlconnectiontest order by id');
$mRow=$mRs->current();


foreach ($mRs as $mRow) {
    echo $mRow['id']->getValue(); //memory leak
    echo $mRow['_int']->getValue(); //memory leak
    echo $mRow['_bool']->getValue(); //memory leak
    echo $mRow['_vchar']->getValue(); //memory leak
    echo $mRow['_float']->getValue(); //memory leak

    TPrimitiveParam::PrepareGeneric(array ('T' => 'string'));
    $mRow['_vchar'] = new TPrimitiveParam('modified');
    $mRow->Update(); //memory leak
    echo $mRow['_vchar']->getValue(); //memory leak

    try {
        $mRow->Delete();
    }
    catch(EException $Ex) {
        var_dump($Ex);
    }
}

Framework::Free($mRs);
Framework::Free($mStmt);
Framework::Free($mConn);
Framework::Free($mDriver);