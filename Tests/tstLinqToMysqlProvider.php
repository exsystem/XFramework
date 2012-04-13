<?php

require_once 'FrameworkDSW/Linq_LinqToMysql.php';
require_once 'Tests/helperForLinqToMysql.php';

echo PHP_VERSION;

$MysqlQueryProvider = new TMysqlQueryProvider();
$c = new TTestContext($MysqlQueryProvider);
$c->PrepareMethodGeneric(array ('T' => 'TStudent'));

$q = $c->CreateQuery();

TList::PrepareGeneric(array ('T' => 'TParameterExpression'));

$params = new TList();
$params->Add(TExpression::Parameter('t', 'TStudent'));
$expr = TExpression::NotEqual(TExpression::MakeMember(TExpression::Parameter('t', 'TStudent'), 'FGender', 'TBoolean'), TExpression::Constant(null, 'TBoolean'));
TExpression::PrepareGeneric(array (
    'T' => array (
        'TPredicateDelegate' => array (
            'E' => array (
                'TPair' => array ('K' => 'integer', 'V' => 'TStudent'))))));
$expr = TExpression::TypedLambda($expr, $params);

$selector = TExpression::Parameter('t', 'TStudent');
TExpression::PrepareGeneric(array (
    'T' => array (
        'TSelectorDelegate' => array (
            'S' => array (
                'TPair' => array ('K' => 'integer', 'V' => 'TStudent')),
            'D' => 'TStudent'))));
$selector = TExpression::TypedLambda($selector, $params);

$orderby = TExpression::MakeMember(TExpression::Parameter('t', 'TStudent'), 'FName', 'string');
TExpression::PrepareGeneric(array (
    'T' => array (
        'TSelectorDelegate' => array ('S' => 'TStudent', 'D' => 'string'))));
$orderby = TExpression::TypedLambda($orderby, $params);

$q->PrepareMethodGeneric(array ('R' => 'TStudent', 'K' => 'string'));
foreach ($q->Select($selector)->Where($expr)->OrderByDescending($orderby) as $s) {
    echo $s->getName()->getValue();
    $c->DeleteObject($s);
}
$c->SaveChanges();