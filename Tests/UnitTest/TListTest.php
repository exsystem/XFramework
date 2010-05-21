<?php
/**
 * TListTest
 * @author	ExSystem
 * @version	$Id: TListTest.php 12 2010-05-20 02:54:05Z exsystemchina@gmail.com $
 * @since	separate file since reversion 23
 */

require_once 'FrameworkDSW\Containers.php';
require_once 'PHPUnit\Framework\TestCase.php';

/**
 * TList test case.
 */
class TListTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * @var TList
	 */
	private $FList;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp();
		$this->FList = new TList(15);	
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		$this->FList = null;		
		parent::tearDown();
	}

	/**
	 * Constructs the test case.
	 */
	public function __construct() {
	}

	/**
	 * Tests TList::FromArray()
	 */
	public function testFromArray() {
		$arr = array(1, 2, 3, 4, 5, 6, 7, 8);
		$list = TList::FromArray($arr, true);
		for ($index = 0; $index < $list->Size(); $index++) {
			$this->assertEquals($arr[$index], $list[$index]);
		}
	}

	/**
	 * Tests TList->ToArray()
	 */
	public function testToArray() {
		for ($i = 1; $i < 10; $i++) {
			$this->FList->Add($i);
		}
		
		$arr = $this->FList->ToArray();
		$this->assertEquals(array(1, 2, 3, 4, 5, 6, 7, 8, 9), $arr);
	}

	/**
	 * Tests TList->First()
	 */
	public function testFirst() {
		$this->FList->Add('string');
		$this->FList->Add('0001');
		$this->FList->Add('0002');
		$this->FList->Add('0003');
		$this->FList->Add('0004');
		
		$this->assertEquals('string', $this->FList->First());
	}

	/**
	 * Tests TList->Last()
	 */
	public function testLast() {
		// TODO Auto-generated TListTest->testLast()
		$this->markTestIncomplete("Last test not implemented");
		
		$this->FList->Last(/* parameters */);
	
	}

	/**
	 * Tests TList->getCapacity()
	 */
	public function testGetCapacity() {
		// TODO Auto-generated TListTest->testGetCapacity()
		$this->markTestIncomplete("getCapacity test not implemented");
		
		$this->FList->getCapacity(/* parameters */);
	
	}

	/**
	 * Tests TList->setCapacity()
	 */
	public function testSetCapacity() {
		// TODO Auto-generated TListTest->testSetCapacity()
		$this->markTestIncomplete("setCapacity test not implemented");
		
		$this->FList->setCapacity(/* parameters */);
	
	}

}

