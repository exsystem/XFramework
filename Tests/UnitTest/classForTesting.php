<?php
/**
 * classForTesting
 * @author	ExSystem
 * @version	$Id: classForTesting.php 12 2010-05-20 02:54:05Z exsystemchina@gmail.com $
 * @since	separate file since reversion 23
 */

require_once 'FrameworkDSW/Framework.php';
require_once 'FrameworkDSW/System.php';
require_once 'FrameworkDSW/Containers.php';

class TTest extends TObject { 
	private $FPrivate1;
	private $FPrivate2;
	public $FPublic1 = 'INIT_VALUE';
	public $FPublic2;
	private static $FSPrivate1;
	private static $FSPrivate2;
	public static $FSPublic1 = 'INIT_STATIC_VALUE';
	public static $FSPublic2;

	public function WakeUp() {
		//echo "\nCustomedWakeup\n";
		parent::WakeUp();
	}

	public function Sleep() {
		parent::Sleep();
		//echo "\nCustomedSleep\n";
	}

	public static function ClassWakeUp() {
		//echo "\nCLASS WAKEUP FOR TTEST\n";
	}

	public static function ClassSleep() {
		return array('FSPublic1', 'FSPublic2');
	}

	public function getPrivate($id) {
		if ($id == 1) {
			return $this->FPrivate1;
		}
		if ($id == 2) {
			return $this->FPrivate2;
		}
		return null;
	}

	public function setPrivate($id, $value) {
		if ($id == 1) {
			$this->FPrivate1 = $value;
		}
		if ($id == 2) {
			$this->FPrivate2 = $value;
		}
	}

	public static function getSPrivate($id) {
		if ($id == 1) {
			return self::$FSPrivate1;
		}
		if ($id == 2) {
			return self::$FSPrivate2;
		}
		
		return null;
	}

	public static function setSPrivate($id, $value) {
		if ($id == 1) {
			self::$FSPrivate1 = $value;
		}
		if ($id == 2) {
			self::$FSPrivate2 = $value;
		}
	}
}

class TTest2 extends TTest {
	private $FPrivate = 100;
}