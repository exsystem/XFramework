<?php
/**
 * Database.php
 * @author	许子健
 * @version	$Id$
 * @since	separate file since reversion 1
 */

require_once 'FrameworkDSW/System.php';
require_once 'FrameworkDSW/Utilities.php';
require_once 'FrameworkDSW/Containers.php';

/**
 * EDatabaseException
 * @author	许子健
 */
class EDatabaseException extends EException {}
/**
 * 
 * @author 许子健
 */
class EFailedToConnectDb extends EDatabaseException {
    /**
     * @var	string
     */
    const CMsg = 'Failed to connect the database: ';
}
/**
 * 
 * @author 许子健
 */
class EDisconnected extends EDatabaseException {}
/**
 * EFailedToGetDbPropertyInfo
 * @author	许子健
 */
class EFailedToGetDbPropertyInfo extends EDatabaseException {
    /**
     * @var	string
     */
    const CMsg = 'Failed to get property info.';
}
/**
 * 
 * @author 许子健
 */
class EUnsupportedDbFeature extends EDatabaseException {}

/**
 * EDatabaseWarning
 * @author	许子健
 */
class EDatabaseWarning extends EDatabaseException {}
/**
 * 
 * @author 许子健
 */
class EUnableToCommit extends EDatabaseWarning {}

/**
 * TConcurrencyType
 * @author	许子健
 */
final class TConcurrencyType extends TEnum {
    /**
     * @var	integer
     */
    const eReadOnly = 0;
    /**
     * @var	integer
     */
    const eUpdatable = 1;
}

/**
 * TCurrentResultOption
 * @author	许子健
 */
final class TCurrentResultOption extends TEnum {
    /**
     * @var	integer
     */
    const eCloseCurrentResult = 0;
    /**
     * @var	integer
     */
    const eKeepCurrentResult = 1;
    /**
     * @var	integer
     */
    const eCloseAllResults = 2;
}

/**
 * TDriverPropertyInfo
 * @author	许子健
 */
final class TDriverPropertyInfo extends TRecord {
    /**
     * @var	string[]
     */
    public $Choices = array ();
    /**
     * @var	string
     */
    public $Description;
    /**
     * @var	string
     */
    public $Name;
    /**
     * @var	boolean
     */
    public $Required;
    /**
     * @var	string
     */
    public $Value;
}

/**
 * TFetchDirection
 * @author	许子健
 */
final class TFetchDirection extends TEnum {
    /**
     * @var	integer
     */
    const eReverse = 1;
    /**
     * @var	integer
     */
    const eForward = 0;
    /**
     * @var	integer
     */
    const eUnkown = 2;
}

/**
 * THoldability
 * @author	许子健
 */
final class THoldability extends TEnum {
    /**
     * @var	integer
     */
    const eHoldCursorsOverCommit = 0;
    /**
     * @var	integer
     */
    const eCloseCursorsAtCommit = 1;
}

/**
 * TResultSetType
 * @author	许子健
 */
final class TResultSetType extends TEnum {
    /**
     * @var	integer
     */
    const eForwardOnly = 0;
    /**
     * @var	integer
     */
    const eScrollInsensitive = 1;
    /**
     * @var	integer
     */
    const eScrollSensitive = 2;
}

/**
 * TTransactionIsolationLevel
 * @author	许子健
 */
final class TTransactionIsolationLevel extends TEnum {
    /**
     * @var	integer
     */
    const eNone = 0;
    /**
     * @var	integer
     */
    const eReadUncommitted = 1;
    /**
     * @var	integer
     */
    const eReadCommitted = 2;
    /**
     * @var	integer
     */
    const eRepeatableRead = 3;
    /**
     * @var	integer
     */
    const eSerializable = 4;
}

/**
 * IParam
 * param	<T>
 * @author	许子健
 */
interface IParam extends IInterface {

    /**
     * descHere
     * @return	TObject
     */
    public function getObjectValue();

    /**
     * descHere
     * @return	string
     */
    public function getType();

    /**
     * descHere
     * @return	T
     */
    public function getValue();

    /**
     * descHere
     * @param	TObject	$Value
     */
    public function setObjectValue($Value);

    /**
     * descHere
     * @param	T	$Value
     */
    public function setValue($Value);
}

/**
 * IDriver
 * @author	许子健
 */
interface IDriver extends IInterface {

    /**
     * descHere
     * @param	string	$Url
     * @param	TProperties	$Properties
     * @return	IConnection
     */
    public function Connect($Url, $Properties);

    /**
     * descHere
     * @param	string					$Url
     * @param	TProperties				$Properties
     * @return	TDriverPropertyInfo[]
     */
    public function GetPropertyInfo($Url, $Properties);

    /**
     * descHere
     * @return	TVersion
     */
    public function getVersion();

    /**
     * descHere
     * @param	string	$Url
     * @return	boolean
     */
    public function ValidateUrl($Url);
}

/**
 * IConnection
 * @author	许子健
 */
interface IConnection extends IInterface {

    /**
     * descHere
     */
    public function ClearWarnings();

    /**
     * descHere
     */
    public function Commit();

    /**
     * descHere
     * @param	string	$Name
     * @return	ISavepoint
     */
    public function CreateSavepoint($Name = '');

    /**
     * descHere
     * @param	TResultSetType	$ResultSetType
     * @param	TConcurrencyType	$ConcurrencyType
     * @param	THoldability	$Holdability
     * @return	IStatement
     */
    public function CreateStatement($ResultSetType, $ConcurrencyType, $Holdability);

    /**
     * descHere
     */
    public function Disconnect();

    /**
     * descHere
     * @param	string	$SqlStatement
     * @return	integer
     */
    public function Execute($SqlStatement);

    /**
     * descHere
     * @return	boolean
     */
    public function getAutoCommit();

    /**
     * descHere
     * @return	THoldability
     */
    public function getHoldability();

    /**
     * descHere
     * @return	boolean
     */
    public function getIsConnected();

    /**
     * descHere
     * @return	TDatabaseMetaData
     */
    public function getMetaData();

    /**
     * descHere
     * @return	boolean
     */
    public function getReadOnly();

    /**
     * descHere
     * @return	TTransactionIsolationLevel
     */
    public function getTransactionIsolation();

    /**
     * descHere
     * @return	EDatabaseWarning
     */
    public function getWarnings();

    /**
     * descHere
     * @param	TResultSetType	$ResultSetType
     * @param	TConcurrencyType	$ConcurrencyType
     * @param	THoldability	$Holdability
     * @return	IPreparedStatement
     */
    public function PrepareStatement($ResultSetType, $ConcurrencyType, $Holdability);

    /**
     * descHere
     * @param	ISavepoint	$Savepoint
     */
    public function RemoveSavepoint($Savepoint);

    /**
     * descHere
     * @param	ISavepoint	$Savepoint
     */
    public function Rollback($Savepoint = null);

    /**
     * descHere
     * @param	boolean	$Value
     */
    public function setAutoCommit($Value);

    /**
     * descHere
     * @param	string	$Value
     */
    public function setCatalog($Value);

    /**
     * descHere
     * @param	THoldability	$Value
     */
    public function setHoldability($Value);

    /**
     * descHere
     * @param	boolean	$Value
     */
    public function setReadOnly($Value);

    /**
     * descHere
     * @param	TTransactionIsolation	$Value
     */
    public function setTransactionIsolation($Value);
}

/**
 * ISavepoint
 * @author	许子健
 */
interface ISavepoint extends IInterface {

    /**
     * descHere
     * @return	integer
     */
    public function getId();

    /**
     * descHere
     * @return	string
     */
    public function getName();
}

/**
 * IStatement
 * @author	许子健
 */
interface IStatement extends IInterface {

    /**
     * descHere
     * @param	string	$Command
     * @return	integer
     */
    public function Execute($Command = '');

    /**
     * @return	IParam <T: ?>
     */
    public function FetchAsScalar();

    /**
     * descHere
     * @return	TList <T: string>
     */
    public function getCommands();

    /**
     * descHere
     * @return	IConnection
     */
    public function getConnection();

    /**
     * descHere
     * @return	IResultSet
     */
    public function GetCurrentResult();

    /**
     * descHere
     * @param	integer	$Index
     * @return	IResultSet
     */
    public function getResult($Index);

    /**
     * descHere
     * @param	TCurrentResultOption	$Options
     */
    public function NextResult($Options);

    /**
     * descHere
     * @param	string	$Command
     * @return	IResultSet
     */
    public function Query($Command = '');

    /**
     * descHere
     * @param	string	$Value
     */
    public function setCommand($Value);
}

/**
 * IPreparedStatement
 * @author	许子健
 */
interface IPreparedStatement extends IStatement {

    /**
     * descHere
     * @param	string	$Name
     * @param	IParam	$Param <T: ?>
     */
    public function BindParam($Name, $Param);

    /**
     * descHere
     */
    public function ClearParams();
}

interface ICallableStatement extends IPreparedStatement {

    /**
     * 
     * @param	string			$Name
     * @return	IParam <T: ?>
     */
    public function GetParam($Name);
}

/**
 * IResultSet
 * extends IArrayAccess<K: integer, V: IRow>, IIterator<T: IRow>
 * @author	许子健
 */
interface IResultSet extends IArrayAccess {

    /**
     * descHere
     */
    public function ClearWarnings();

    /**
     * descHere
     */
    public function Close();

    /**
     * descHere
     * @param	integer	$RowId
     * @return	IRow
     */
    public function FetchAbsolute($RowId);

    /**
     * descHere
     * @param	integer	$Offset
     * @return	IRow
     */
    public function FetchRelative($Offset);

    /**
     * descHere
     * @return	integer
     */
    public function getCount();

    /**
     * descHere
     * @return	string
     */
    public function getCursorName();

    /**
     * descHere
     * @return	TFetchDirection
     */
    public function getFetchDirection();

    /**
     * descHere
     * @return	integer
     */
    public function getFetchSize();

    /**
     * descHere
     * @return	IRow
     */
    public function getInsertRow();

    /**
     * descHere
     * @return	boolean
     */
    public function getIsClosed();

    /**
     * descHere
     * @return	boolean
     */
    public function getIsEmpty();

    /**
     * descHere
     * @return	IReusltMetaData
     */
    public function getMetaData();

    /**
     * descHere
     * @return	IStatement
     */
    public function getStatement();

    /**
     * descHere
     * @return	EDatabaseWarning
     */
    public function getWarnings();

    /**
     * descHere
     * @param	TFetchDirection	$Value
     */
    public function setFetchDirection($Value);

    /**
     * descHere
     * @param	integer	$Value
     */
    public function setFetchSize($Value);
}

/**
 * IRow
 * extends IArrayAccess <K: integer, V: IParam<T: ?>>
 * @author	许子健
 */
interface IRow extends IArrayAccess {

    /**
     * descHere
     */
    public function Delete();

    /**
     * descHere
     * @return	TConcurrencyType
     */
    public function getConcurrencyType();

    /**
     * descHere
     * @return	THoldability
     */
    public function getHoldability();

    /**
     * descHere
     * @return	IResultSet
     */
    public function getResultSet();

    /**
     * descHere
     * @return	TResultSetType
     */
    public function getType();

    /**
     * descHere
     * @return	boolean
     */
    public function getWasDeleted();

    /**
     * descHere
     * @return	boolean
     */
    public function getWasUpdated();

    /**
     * descHere
     */
    public function Refresh();

    /**
     * descHere
     * @return	void
     */
    public function UndoUpdates();

    /**
     * descHere
     */
    public function Update();
}

/**
 * TAbstractParam
 * param	<T>
 * @author	许子健
 */
abstract class TAbstractParam extends TObject {
    /**
     * @var	T
     */
    protected $FValue;

    //public abstract function getType(); //returns a string.
    //TODO: SEE http://bugs.php.net/bug.php?id=51826 to know why this method was commentted.
    

    /**
     * descHere
     * @return	T
     */
    public function getValue() {
        return $this->FValue;
    }

    /**
     * descHere
     * @param	T	$Value
     */
    public function setValue($Value) {
        TType::Type($Value, $this->GenericArg('T'));
        $this->FValue = $Value;
    }

}

/**
 * TPrimativeParam
 * param	<T>
 * @author	许子健
 */
final class TPrimativeParam extends TAbstractParam implements IParam {
    /**
     * 
     * @var	string
     */
    private $FType = '';

    /**
     * descHere
     * @param	T	$Value
     */
    public function __construct($Value) {
        TType::Type($Value, $this->GenericArg('T'));
        $mMapping = array ('boolean' => 'BIT', 'integer' => 'INTEGER', 'float' => 'FLOAT', 'string' => 'VARCHAR');
        $this->FType = $mMapping[$this->GenericArg('T')];
        $this->FValue = $Value;
    }

    /**
     * descHere
     * @return	TObject
     */
    public function getObjectValue() {
        switch ($this->FType) {
            case 'BIT' :
                return new TBoolean($this->FValue);
                break;
            case 'INTEGER' :
                return new TInteger($this->FValue);
                break;
            case 'FLOAT' :
                return new TFloat($this->FValue);
                break;
            case 'VARCHAR' :
                return new TString($this->FValue);
                break;
            default :
                return null;
                break;
        }
    }

    /**
     * descHere
     * @return	string
     */
    public function getType() {
        return $this->FType;
    }

    /**
     * descHere
     * @param	TObject	$Value
     */
    public function setObjectValue($Value) {
        TType::Object($Value, 'IPrimitive');
        switch ($this->FType) {
            case 'BIT' :
                $this->FValue = $Value->UnboxToBoolean();
                break;
            case 'INTEGER' :
                $this->FValue = $Value->UnboxToInteger();
                break;
            case 'FLOAT' :
                $this->FValue = $Value->UnboxToFloat();
                break;
            case 'VARCHAR' :
                $this->FValue = $Value->UnboxToString();
                break;
        }
    }
}

/**
 * TAbstractPdoDriver
 * @author	许子健
 */
abstract class TAbstractPdoDriver extends TObject {
    
    /**
     * @var	TProperties
     */
    protected $FProperties = null;
    /**
     * @var	string
     */
    protected $FProtocol = '';
    /**
     * 
     * @var	string
     */
    protected $FServer = '';
    /**
     * 
     * @var	string
     */
    protected $FDbName = '';

    /**
     * descHere
     * @param	string		$Url
     * @param	TProperties	$Properties
     * @return	IConnection
     */
    public function Connect($Url, $Properties) {
        TType::String($Url);
        TType::Object($Properties, 'TProperties');
        
        if ($this->ValidateUrl($Url)) {
            return $this->DoConnect();
        }
        throw new EFailedToConnectDb(EFailedToConnectDb::CMsg . $Url);
    }

    /**
     * descHere
     * @return	IConnection
     */
    protected abstract function DoConnect();

    /**
     * descHere
     * @return	TDriverPropertyInfo
     */
    protected abstract function DoGetPropertyInfo();

    /**
     * descHere 
     * @return	TVersion
     */
    protected abstract function DoGetVersion();

    /**
     * descHere
     * @return	boolean
     */
    protected abstract function DoValidateUrl();

    /**
     * descHere
     * @param	string		$Url
     * @param	TProperties	$Properties
     * @return	TDriverPropertyInfo[]
     */
    public function GetPropertyInfo($Url, $Properties) {
        TType::String($Url);
        TType::Object($Properties, 'TProperties');
        
        if ($this->ValidateUrl($Url)) {
            $this->FProperties = $Properties;
            return $this->DoGetPropertyInfo();
        }
        throw new EFailedToGetDbPropertyInfo(EFailedToGetDbPropertyInfo::CMsg);
    }

    /**
     * descHere
     * @param	string	$Url
     * @return	boolean
     */
    public function ValidateUrl($Url) {
        TType::String($Url);
        
        list ($mProtocol, $mServer) = explode('://', $Url, 2);
        list ($mServer, $mDbName) = explode('/', $mServer, 2);
        if ($mProtocol != '' && $mServer != '' && $mDbName != '') {
            $this->FProtocol = $mProtocol;
            $this->FServer = $mServer;
            $this->FDbName = $mDbName;
            return $this->DoValidateUrl();
        }
        return false;
    }

}

/**
 * TAbstractPdoConnection
 * @author	许子健
 */
abstract class TAbstractPdoConnection extends TObject {
    /**
     * 
     * @var	string
     */
    const CNullDriverOrPdoObj = 'The driver or/and the PDO object given is null.';
    
    /**
     * 
     * @var	IDriver
     */
    protected $FDriver = null;
    
    /**
     * @var	PDO
     */
    protected $FPdo = null;
    /**
     * 
     * @var	boolean
     */
    protected $FIsConnected = false;
    /**
     * 
     * @var	boolean
     */
    protected $FIsAutoCommit = false;

    /**
     * 
     */
    protected function EnsureConnected() {
        if (!$this->FIsConnected) {
            throw new EDisconnected();
        }
    }

    /**
     * 
     * @param	IDriver	$Driver
     * @param	PDO		$Pdo
     */
    public function __construct($Driver, $Pdo) {
        TType::Object($Driver, 'IDriver');
        TType::Object($Pdo, 'PDO');
        
        if ($Driver !== null && $Pdo !== null) {
            $this->FDriver = $Driver;
            $this->FPdo = $Pdo;
            $this->FIsConnected = true;
            $this->FIsAutoCommit = true;
        }
        else {
            $this->FIsConnected = false;
            throw new EIsNotNullable(self::CNullDriverOrPdoObj);
        }
    }

    /**
     * descHere
     */
    public function ClearWarnings() {
        $this->EnsureConnected();
    }

    /**
     * descHere
     */
    public function Commit() {
        $this->EnsureConnected();
        if ($this->FIsAutoCommit || !$this->FPdo->commit()) {
            //PUSHBACK EUnableToCommit;  
        }
        $this->FIsAutoCommit = true;
    }

    /**
     * descHere
     * @param	string	$Name
     * @return	ISavepoint
     */
    public function CreateSavepoint($Name = '') {
        throw new EUnsupportedDbFeature();
    }

    /**
     * descHere
     * @param	TResultSetType	$ResultSetType
     * @param	TConcurrencyType	$ConcurrencyType
     * @param	THoldability	$Holdability
     * @return	IStatement
     */
    public function CreateStatement($ResultSetType, $ConcurrencyType, $Holdability) {
        $this->EnsureConnected();
    }

    /**
     * descHere
     */
    public function Disconnect() {
        Framework::Free($this->FPdo);
        Framework::Free($this->FDriver);
        $this->FIsConnected = false;
    }

    /**
     * descHere
     * @param	string	$SqlStatement
     * @return	integer
     */
    public function Execute($SqlStatement) {
        TType::String($SqlStatement);
        $this->EnsureConnected();
        $mResult = $this->FPdo->exec($SqlStatement);
        if (is_bool($mResult)) {
            //PUSHBACK EDatabaseWarning;
        }
        return $mResult;
    }

    /**
     * descHere
     * @return	boolean
     */
    public function getAutoCommit() {
        $this->EnsureConnected();
        return $this->FIsAutoCommit;
    }

    /**
     * descHere
     * @return	THoldability
     */
    public function getHoldability() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function getIsConnected() {
        return $this->FIsConnected;
    }

    /**
     * descHere
     * @return	TDatabaseMetaData
     */
    public function getMetaData() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function getReadOnly() {
    }

    /**
     * descHere
     * @return	TTransactionIsolationLevel
     */
    public function getTransactionIsolation() {
    }

    /**
     * descHere
     * @return	EDatabaseWarning
     */
    public function getWarnings() {
    }

    /**
     * descHere
     * @param	TResultSetType	$ResultSetType
     * @param	TConcurrencyType	$ConcurrencyType
     * @param	THoldability	$Holdability
     * @return	IPreparedStatement
     */
    public function PrepareStatement($ResultSetType, $ConcurrencyType, $Holdability) {
    }

    /**
     * descHere
     * @param	ISavepoint	$Savepoint
     */
    public function RemoveSavepoint($Savepoint) {
    }

    /**
     * descHere
     * @param	ISavepoint	$Savepoint
     */
    public function Rollback($Savepoint = null) {
    }

    /**
     * descHere
     * @param	boolean	$Value
     */
    public function setAutoCommit($Value) {
    }

    /**
     * descHere
     * @param	string	$Value
     */
    public function setCatalog($Value) {
    }

    /**
     * descHere
     * @param	THoldability	$Value
     */
    public function setHoldability($Value) {
    }

    /**
     * descHere
     * @param	boolean	$Value
     */
    public function setReadOnly($Value) {
    }

    /**
     * descHere
     * @param	TTransactionIsolation	$Value
     */
    public function setTransactionIsolation($Value) {
    }

}