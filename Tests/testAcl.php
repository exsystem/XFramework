<?php
require_once 'FrameworkDSW/Framework.php';

use FrameworkDSW\Acl\TAcl;
use FrameworkDSW\Acl\TResource;
use FrameworkDSW\Acl\TRole;
use FrameworkDSW\Acl\TRuntimeStorage;
use FrameworkDSW\Framework\Framework;


$acl = new TAcl($storage = new TRuntimeStorage());
for ($i = 0; $i < 100; ++$i) {
    $acl->AddResource(new TResource("res{$i}"));
}
for ($i = 0; $i < 100; ++$i) {
    $acl->AddRole(new TRole("role{$i}"));
}
$test = array();
for ($i = 0; $i < 100; ++$i) {
    for ($j = 0; $j < 100; ++$j) {
        if (rand(0, 10) > 5) {
            $acl->Allow(new TRole("role{$i}"), new TResource("res{$j}"), '*');
            $test["role{$i}res{$j}"] = true;
        }
    }
}
for ($i = 0; $i < 100; ++$i) {
    for ($j = 0; $j < 100; ++$j) {
        if ($acl->IsAllowed(new TRole("role{$i}"), new TResource("res{$j}"), '*')) {
            $test["role{$i}res{$j}"] = false;
        }
    }
}
$test = array_filter($test);
var_dump($test);

$acl = new TAcl($storage = new TRuntimeStorage());
$acl->AddResource(new TResource('news'));
$acl->AddResource(new TResource('posts'));
$acl->AddRole(new TRole('root'));
$acl->AddRole(new TRole('user'));
$acl->Allow(new TRole('root'), null, '*');
$acl->Allow(new TRole('user'), new TResource('posts'), '*');
if ($acl->IsAllowed(new TRole('root'), new TResource('news'), '*')) {
    echo "news is allowed to root\n";
}
if ($acl->IsAllowed(new TRole('root'), new TResource('posts'), '*')) {
    echo "posts is allowed to root\n";
}
if ($acl->IsAllowed(new TRole('user'), new TResource('news'), '*')) {
    echo "news is allowed to user\n";
}
if ($acl->IsAllowed(new TRole('user'), new TResource('posts'), '*')) {
    echo "posts is allowed to user\n";
}
Framework::Free($storage);
Framework::Free($acl);
echo "END.\n";
