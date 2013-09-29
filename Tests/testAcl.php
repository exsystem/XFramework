<?php
require_once 'FrameworkDSW/Framework.php';

use FrameworkDSW\Acl\TAcl;
use FrameworkDSW\Acl\TRuntimeAclStorage;
use FrameworkDSW\Acl\TAclResource;
use FrameworkDSW\Acl\TAclRole;


$acl = new TAcl($storage = new TRuntimeAclStorage());
for ($i = 0; $i < 100; ++$i) {
	$acl->AddResource(new TAclResource("res{$i}"));
}
for ($i = 0; $i < 100; ++$i) {
	$acl->AddRole(new TAclRole("role{$i}"));
}
$test = array();
for ($i = 0; $i < 100; ++$i) {
	for ($j = 0; $j < 100; ++$j) {
		if (rand(0, 10) > 5) {
			$acl->Allow(new TAclRole("role{$i}"), new TAclResource("res{$j}"), '*');
			$test["role{$i}res{$j}"] = true;
		}
	}
}
for ($i = 0; $i < 100; ++$i) {
	for ($j = 0; $j < 100; ++$j) {
		if ($acl->IsAllowed(new TAclRole("role{$i}"), new TAclResource("res{$j}"), '*')) {
			$test["role{$i}res{$j}"] = false;
		}
	}
}
$test = array_filter($test);
var_dump($test);

$acl = new TAcl($storage = new TRuntimeAclStorage());
$acl->AddResource(new TAclResource('news'));
$acl->AddResource(new TAclResource('posts'));
$acl->AddRole(new TAclRole('root'));
$acl->AddRole(new TAclRole('user'));
$acl->Allow(new TAclRole('root'), null, '*');
$acl->Allow(new TAclRole('user'), new TAclResource('posts'), '*');
if ($acl->IsAllowed(new TAclRole('root'), new TAclResource('news'), '*')) {
	echo "news is allowed to root\n";
}
if ($acl->IsAllowed(new TAclRole('root'), new TAclResource('posts'), '*')) {
	echo "posts is allowed to root\n";
}
if ($acl->IsAllowed(new TAclRole('user'), new TAclResource('news'), '*')) {
	echo "news is allowed to user\n";
}
if ($acl->IsAllowed(new TAclRole('user'), new TAclResource('posts'), '*')) {
	echo "posts is allowed to user\n";
}
Framework::Free($storage);
Framework::Free($acl);
echo "END.\n";
