<?php

use FrameworkDSW\Containers\TMap;
use FrameworkDSW\Database\Mysql\TMysqlConnection;
use FrameworkDSW\Database\Mysql\TMysqlDriver;
use FrameworkDSW\DataObjects\IEntity;
use FrameworkDSW\DataObjects\TObjectContext;
use FrameworkDSW\Linq\IQueryProvider;
use FrameworkDSW\System\EInvalidParameter;
use FrameworkDSW\System\TBoolean;
use FrameworkDSW\System\TInteger;
use FrameworkDSW\System\TObject;
use FrameworkDSW\System\TString;
use FrameworkDSW\Utilities\TType;

require_once 'FrameworkDSW/Framework.php';

$RESULT  = array();
$CHANGED = array();
$ADD     = array();
$DELETE  = array();

/**
 * StudentNo: int? AS PRIMARY KEY, Name: string?, Gender: boolean?
 *
 * @author 许子健
 */
class TStudent extends TObject implements IEntity {
    /**
     *
     * @var TObjectContext
     */
    public $FContext = null;

    /**
     *
     * @var TInteger
     */
    public $FStudentNo = -1;
    /**
     *
     * @var TString
     */
    public $FName = null;
    /**
     *
     * @var TBoolean
     */
    public $FGender = null;

    /**
     * descHere
     *
     * @return TObjectContext
     */
    public function getContext() {
        return $this->FContext;
    }

    /**
     *
     * @param TObjectContext $Context
     */
    public function __construct($Context) {
        global $RESULT;
        TType::Object($Context, 'TObjectContext');
        $this->FContext = $Context;
        $RESULT[]       = $this;
    }

    /**
     *
     * @return string[] TODO {string}
     */
    public static function getPrimaryKeys() {
        return array('FStudentNo');
    }

    /**
     *
     * @return array TODO {<string, string>}
     */
    public static function getColumns() {
        return array('FStudentNo' => 'StudentNo', 'FName' => 'Name',
                     'FGender'    => 'Gender');
    }

    /**
     *
     * @return array TODO {<string, string>}
     */
    public static function getColumnsType() {
        return array('FStudentNo' => 'TInteger', 'FName' => 'TString',
                     'FGender'    => 'TBoolean');
    }

    /**
     *
     * @return string
     */
    public static function getTableName() {
        return 'Student';
    }

    /**
     *
     * @return TInteger
     */
    public function getStudentNo() {
        return $this->FStudentNo;
    }

    /**
     *
     * @param TInteger $Value
     */
    public function setStudentNo($Value) {
        TType::Object($Value, 'TInteger');
        TObject::Dispatch(array($this->FContext, 'PreChange'), array($this,
            array('FStudentNo')));
        $this->FStudentNo = $Value;
        TObject::Dispatch(array($this->FContext, 'PostChange'), array($this,
            array('FStudentNo')));
    }

    /**
     *
     * @return TString
     */
    public function getName() {
        return $this->FName;
    }

    /**
     *
     * @param TString $Value
     */
    public function setName($Value) {
        TType::Object($Value, 'TString');
        TObject::Dispatch(array($this->FContext, 'PreChange'), array($this,
            array('FName')));
        $this->FName = $Value;
        TObject::Dispatch(array($this->FContext, 'PostChange'), array($this,
            array('FName')));
    }

    /**
     *
     * @return TBoolean
     */
    public function getGender() {
        return $this->FGender;
    }

    /**
     *
     * @param TString $Value
     */
    public function setGender($Value) {
        TType::Object($Value, 'TString');
        TObject::Dispatch(array($this->FContext, 'PreChange'), array($this,
            array('FGender')));
        $this->FGender = $Value;
        TObject::Dispatch(array($this->FContext, 'PostChange'), array($this,
            array('FGender')));
    }
}

/**
 * for database named `test`.
 * @author 许子健
 *
 */
class TTestContext extends TObjectContext {

    /**
     *
     * @var TMysqlConnection
     */
    private $FConn = null;

    /**
     * descHere
     *
     * @param $Entity IEntity
     */
    protected function DoAddObject($Entity) {
        global $ADD;
        $ADD[] = $Entity;
    }

    /**
     * descHere
     *
     * @param $Entity IEntity
     */
    protected function DoDeleteObject($Entity) {
        global $DELETE;
        $DELETE[] = $Entity;
    }

    /**
     *
     * @param IQueryProvider $QueryProvider
     */
    public function __construct($QueryProvider) {
        parent::__construct($QueryProvider);

        TType::Object($QueryProvider, 'IQueryProvider');
        if ($QueryProvider->ObjectType() !== 'TMysqlQueryProvider') {
            throw new EInvalidParameter();
        }

        $mDriver = new TMysqlDriver();
        TMap::PrepareGeneric(array('K' => 'string', 'V' => 'string'));
        $mConfig                   = new TMap();
        $mConfig['Username']       = 'root';
        $mConfig['Password']       = '';
        $mConfig['ConnectTimeout'] = '2';
        $mConfig['Socket']         = '/opt/lampp/var/mysql/mysql.sock'; //LINUX ONLY
        #$mConfig['Socket'] = '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock'; // MACOSX
        // ONLY
        $this->FConn = $mDriver->Connect('MySQL://localhost/test', $mConfig);

        $this->FProvider->UseConnection($this->FConn);
        $this->FProvider->UseContext($this);
    }

    /**
     * (non-PHPdoc)
     *
     * @see TObject::Destroy()
     */
    public function Destroy() {
        Framework::Free($this->FConn); // TODO what about result sets & rows?
        parent::Destroy();
    }

    /**
     * (non-PHPdoc)
     *
     * @see TObjectContext::SaveChanges()
     */
    public function SaveChanges() {
        global $RESULT;
        global $ADD;
        global $DELETE;
        global $CHANGED;

        function display($obj) {
            var_dump(array($obj->getStudentNo()->getValue(),
                $obj->getName()->getValue(), $obj->getGender()->getValue()));
        }

        foreach ($ADD as $addItem) {
            display($addItem);
        }

        foreach ($DELETE as $deleteItem) {
            display($deleteItem);
        }

        foreach ($CHANGED as $changeItem) {
            display($changeItem);
        }

        foreach ($ADD as $item) {
            Framework::Free($item);
        }

        foreach ($RESULT as $item) {
            Framework::Free($item);
        }
        foreach ($DELETE as $item) {
            Framework::Free($item);
        }
        foreach ($CHANGED as $item) {
            Framework::Free($item);
        }

        $RESULT  = array();
        $ADD     = array();
        $DELETE  = array();
        $CHANGED = array();
    }
}