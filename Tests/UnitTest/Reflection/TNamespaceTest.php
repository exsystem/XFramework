<?php

namespace UnitTest;
set_include_path(get_include_path() . PATH_SEPARATOR . '../../../');
require_once 'FrameworkDSW/Framework.php';
use FrameworkDSW\Framework\Framework;
use FrameworkDSW\Reflection\ENoSuchNamespace;
use FrameworkDSW\Reflection\TNamespace;
use FrameworkDSW\System\ERuntimeException;

class TNamespaceTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \FrameworkDSW\Reflection\TNamespace
     */
    private $FNamespace = null;

    /**
     *
     */
    protected function setUp() {
        parent::setUp();
        $this->FNamespace = TNamespace::getNamespace('FrameworkDSW\System');
    }

    /**
     *
     */
    protected function tearDown() {
        Framework::Free($this->FNamespace);
        parent::tearDown();
    }

    /**
     *
     */
    public function testGetName() {
        $this->assertEquals('FrameworkDSW\System', $this->FNamespace->getName());
        for ($i = 0; $i < 100; ++$i) {
            $this->assertTrue($this->FNamespace === TNamespace::getNamespace('FrameworkDSW\System'));
        }

        try {
            TNamespace::getNamespace('Foo\bar');
            $this->fail('Namespace Foo\bar should be non-existence.');
        }
        catch (ENoSuchNamespace $Ex) {
            return;
        }

        try {
            new TNamespaceTest('FrameworkDSW\Containers');
            $this->fail('TNamespace should not be created by calling the  constructor.');
        }
        catch (ERuntimeException $Ex) {
            return;
        }
    }
}
