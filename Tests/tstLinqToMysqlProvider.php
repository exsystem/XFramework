<?php

use Foo\TStudent;
use Foo\TTestContext;
use FrameworkDSW\Containers\TList;
use FrameworkDSW\Containers\TPair;
use FrameworkDSW\Framework\Framework;
use FrameworkDSW\Linq\Expressions\TExpression;
use FrameworkDSW\Linq\Expressions\TParameterExpression;
use FrameworkDSW\Linq\LinqToMysql\TMysqlQueryProvider;
use FrameworkDSW\Linq\TPredicateDelegate;
use FrameworkDSW\Linq\TSelectorDelegate;
use FrameworkDSW\System\TBoolean;

require_once 'FrameworkDSW/Framework.php';
require_once 'Tests/helperForLinqToMysql.php';
Framework::Debug();
echo PHP_VERSION;
echo "\n";

echo memory_get_usage(true); //echo 4718592 bytes.
echo "\n";

$MysqlQueryProvider = new TMysqlQueryProvider();
$c                  = new TTestContext($MysqlQueryProvider);
$c->PrepareMethodGeneric(['T' => TStudent::class]);

$q = $c->CreateQuery();

TList::PrepareGeneric(['T' => TParameterExpression::class]);

$params = new TList();
$params->Add(TExpression::Parameter('t', Framework::Type(TStudent::class)));
$expr = TExpression::NotEqual(TExpression::MakeMember(TExpression::Parameter('t', Framework::Type(TStudent::class)), 'FGender', Framework::Type(TBoolean::class)), TExpression::Constant(null, Framework::Type(TBoolean::class)));
TExpression::PrepareGeneric([
    'T' => [
        TPredicateDelegate::class => [
            'E' => [
                TPair::class => ['K' => Framework::Integer, 'V' => TStudent::class]]]]]);
$expr = TExpression::TypedLambda($expr, $params);

$selector = TExpression::Parameter('t', Framework::Type(TStudent::class));
//$selector = TExpression::MakeMember(TExpression::Parameter('t', Framework::Type(TStudent::class)), 'FName', Framework::Type(Framework::String));
TExpression::PrepareGeneric([
    'T' => [
        TSelectorDelegate::class => [
            'S' => [
                TPair::class => ['K' => Framework::Integer, 'V' => TStudent::class]],
            'D' => TStudent::class]]]);
$selector = TExpression::TypedLambda($selector, $params);
$orderby  = TExpression::MakeMember(TExpression::Parameter('t', Framework::Type(TStudent::class)), 'FName', Framework::Type(Framework::String));
TExpression::PrepareGeneric([
    'T' => [
        TSelectorDelegate::class => ['S' => TStudent::class, 'D' => Framework::String]]]);
$orderby = TExpression::TypedLambda($orderby, $params);

$q->PrepareMethodGeneric(['R' => TStudent::class, 'K' => Framework::String]);
foreach ($q->Select($selector)->Where($expr)->OrderByDescending($orderby) as $s) {
    echo $s->getName()->Unbox();
    echo "\n";
    //$c->DeleteObject($s);
}
//$c->SaveChanges();
Framework::Free($selector);
Framework::Free($expr);
Framework::Free($orderby);
Framework::Free($q);
Framework::Free($c);

echo memory_get_usage(true); //echo 5242880 bytes; total difference: 524288 bytes = 0.75 MB
echo "\n";

echo memory_get_peak_usage(true);
echo "\n";
echo 'ENDED';