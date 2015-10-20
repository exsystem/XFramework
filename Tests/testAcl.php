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
            $acl->Allow($acl->GetRoleById("role{$i}"), $acl->GetResourceById("res{$j}"), '*');
            $test["role{$i}res{$j}"] = true;
        }
    }
}
for ($i = 0; $i < 100; ++$i) {
    for ($j = 0; $j < 100; ++$j) {
        if ($acl->IsAllowed($acl->GetRoleById("role{$i}"), $acl->GetResourceById("res{$j}"), '*')) {
            $test["role{$i}res{$j}"] = false;
        }
    }
}
$test = array_filter($test);
var_dump($test);
Framework::Free($storage);
Framework::Free($acl);

$acl = new TAcl($storage = new TRuntimeStorage());
$acl->AddResource(new TResource('news'));
$acl->AddResource(new TResource('news1'), $acl->GetResourceById('news'));
$acl->AddResource(new TResource('posts'));
$acl->AddRole(new TRole('root'));
$acl->AddRole(new TRole('user'));
$acl->Allow($acl->GetRoleById('root'), null, '*');
$acl->Allow($acl->GetRoleById('user'), new TResource('posts'), '*');
if ($acl->IsAllowed($acl->GetRoleById('root'), $acl->GetResourceById('news'), '*')) {
    echo "news is allowed to root\n";
}
if ($acl->IsAllowed($acl->GetRoleById('root'), $acl->GetResourceById('posts'), '*')) {
    echo "posts is allowed to root\n";
}
if ($acl->IsAllowed($acl->GetRoleById('user'), $acl->GetResourceById('news'), '*')) {
    echo "news is allowed to user\n";
}
if ($acl->IsAllowed($acl->GetRoleById('user'), $acl->GetResourceById('posts'), '*')) {
    echo "posts is allowed to user\n";
}
if ($acl->IsAllowed($acl->GetRoleById('root'), $acl->GetResourceById('news1'), '*')) {
    echo "news1 is allowed to root\n";
}
Framework::Free($storage);
Framework::Free($acl);
echo "END.\n";
