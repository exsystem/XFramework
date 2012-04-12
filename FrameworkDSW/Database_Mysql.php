<?php
/**
 * Database_Mysql.php
 * @author	许子健
 * @version	$Id$
 * @since	separate file since reversion 17
 */

require_once 'FrameworkDSW/Database.php';
require_once 'FrameworkDSW/Containers.php';

/**
 *
 * Enter description here ...
 * @author	许子健
 */
abstract class TBaseMysqlObject extends TObject {
    /**
     *
     * Enter description here ...
     * @var	TMysqlDriver
     */
    protected $FDriver = null;
    /**
     *
     * Enter description here ...
     * @var	mysqli
     */
    protected $FMysqli = null;

    /**
     *
     * Enter description here ...
     * @param	TMysqlDriver	$Driver
     */
    public function __construct($Driver) {
        TType::Object($Driver, 'TMysqlDriver');
        parent::__construct();
        $this->FDriver = $Driver;
        $this->FMysqli = $Driver->getMysqli($this);
    }

    /**
     *
     * Enter description here ...
     * @return	TMysqlDriver
     */
    protected function getDriver() {
        return $this->FDriver;
    }

}

/**
 *
 * Enter description here ...
 * @author	许子健
 */
class TMysqlValueTypeMapper extends TObject {
    /**
     *
     * Enter description here ...
     * @var	array
     */
    private static $FTypeMappingTable = array ();

    /**
     *
     * Enter description here ...
     * @param	integer	$MysqliType
     * @param	integer	$FieldLength
     * @return	string
     */
    public static function MapType($MysqliType, $FieldLength) {
        if ($FieldLength == 1 && ($MysqliType == MYSQLI_TYPE_BIT || $MysqliType == MYSQLI_TYPE_TINY)) {
            return 'boolean';
        }

        if (count(self::$FTypeMappingTable) == 0) {
            self::$FTypeMappingTable = array (MYSQLI_TYPE_BIT => 'integer',
                MYSQLI_TYPE_BLOB => 'string', MYSQLI_TYPE_CHAR => 'string',
                MYSQLI_TYPE_DATE => 'todo', MYSQLI_TYPE_DATETIME => 'todo',
                MYSQLI_TYPE_DECIMAL => 'string', MYSQLI_TYPE_DOUBLE => 'string',
                MYSQLI_TYPE_ENUM => 'integer', MYSQLI_TYPE_FLOAT => 'float',
                MYSQLI_TYPE_GEOMETRY => 'todo', MYSQLI_TYPE_INT24 => 'integer',
                MYSQLI_TYPE_INTERVAL => 'integer', MYSQLI_TYPE_LONG => 'integer',
                MYSQLI_TYPE_LONG_BLOB => 'string',
                MYSQLI_TYPE_LONGLONG => 'integer',
                MYSQLI_TYPE_MEDIUM_BLOB => 'todo', MYSQLI_TYPE_NEWDATE => 'todo',
                MYSQLI_TYPE_NEWDECIMAL => 'float', MYSQLI_TYPE_SET => 'integer',
                MYSQLI_TYPE_SHORT => 'integer', MYSQLI_TYPE_STRING => 'string',
                MYSQLI_TYPE_TIME => 'todo', MYSQLI_TYPE_TIMESTAMP => 'todo',
                MYSQLI_TYPE_TINY => 'integer', MYSQLI_TYPE_TINY_BLOB => 'todo',
                MYSQLI_TYPE_VAR_STRING => 'string', MYSQLI_TYPE_YEAR => 'todo');
        }
        //TODO: more mapping to do...
        return self::$FTypeMappingTable[$MysqliType];
    }
}

/**
 * TMysqlWarningContext
 * @author	许子健
 */
class TMysqlWarningContext extends TObject implements IDatabaseWarningContext {

    /**
     * @var	string
     */
    private $FErrorMessage = '';
    /**
     * @var	string
     */
    private $FErrorCode = '';
    /**
     * @var	string
     */
    private $FSqlState = '';

    /**
     * descHere
     * @param	string	$SqlState
     * @param	string	$ErrorCode
     * @param	string	$ErrorMessage
     */
    public function __construct($SqlState, $ErrorCode, $ErrorMessage) {
        parent::__construct();
        TType::String($SqlState);
        TType::String($ErrorCode);
        TType::String($ErrorMessage);

        $this->FSqlState = $SqlState;
        $this->FErrorCode = $ErrorCode;
        $this->FErrorMessage = $ErrorMessage;
    }

    /**
     * descHere
     * @return	string
     */
    public function getErrorCode() {
        return $this->FErrorCode;
    }

    /**
     * descHere
     * @return	string
     */
    public function getSqlState() {
        return $this->FSqlState;
    }

    /**
     *
     * Enter description here ...
     * @return	string
     */
    public function getErrorMessage() {
        return $this->FErrorMessage;
    }
}

/**
 * TMysqlDriver
 * @author	许子健
 */
final class TMysqlDriver extends TObject implements IDriver {

    /**
     * @var	string
     */
    const CInvalidServer = 'Bad form of the server string.';

    /**
     *
     * Enter description here ...
     * @var	string
     */
    private $FServer = '';

    /**
     *
     * Enter description here ...
     * @var	integer
     */
    private $FPort = 3306;

    /**
     *
     * Enter description here ...
     * @var	string
     */
    private $FDbName = '';

    /**
     *
     * Enter description here ...
     * @var	string
     */
    private $FSocket = '';

    /**
     * @var	TMap <K: string, V: string>
     */
    private $FProperties = null;

    /**
     *
     * Enter description here ...
     * @var array
     */
    private $FMysqliOptions = array ();

    /**
     *
     * Enter description here ...
     * @var	integer
     */
    private $FMysqliFlags = 0;

    /**
     *
     * Enter description here ...
     * @var	mysqli
     */
    private $FMysqli = null;

    /**
     *
     * Enter description here ...
     */
    protected function ConvertProperties() {
        if ($this->FProperties === null) {
            return;
        }

        if ($this->FProperties->ContainsKey('Socket')) {
            $this->FSocket = $this->FProperties['Socket'];
        }

        if ($this->FProperties->ContainsKey('ConnectTimeout')) {
            $this->FMysqliOptions[MYSQLI_OPT_CONNECT_TIMEOUT] = (integer) $this->FProperties['ConnectTimeout'];
        }
        if ($this->FProperties->ContainsKey('LocalInfileEnabled')) {
            $this->FMysqliOptions[MYSQLI_OPT_LOCAL_INFILE] = $this->FProperties['LocalInfileEnabled'] == 'True';
        }
        if ($this->FProperties->ContainsKey('ReadDefaultFile')) {
            $this->FMysqliOptions[MYSQLI_READ_DEFAULT_FILE] = $this->FProperties['ReadDefaultFile'];
        }
        if ($this->FProperties->ContainsKey('ReadDefaultGroup')) {
            $this->FMysqliOptions[MYSQLI_READ_DEFAULT_GROUP] = $this->FProperties['ReadDefaultGroup'];
        }

        if ($this->FProperties->ContainsKey('ClientCompress') && $this->FProperties['ClientCompress'] == 'True') {
            $this->FMysqliFlags += MYSQLI_CLIENT_COMPRESS;
        }
        if ($this->FProperties->ContainsKey('FoundRows') && $this->FProperties['FoundRows'] == 'True') {
            $this->FMysqliFlags += MYSQLI_CLIENT_FOUND_ROWS;
        }
        if ($this->FProperties->ContainsKey('IgnoreSpace') && $this->FProperties['IgnoreSpace'] == 'True') {
            $this->FMysqliFlags += MYSQLI_CLIENT_IGNORE_SPACE;
        }
        if ($this->FProperties->ContainsKey('Interactive') && $this->FProperties['Interactive'] == 'True') {
            $this->FMysqliFlags += MYSQLI_CLIENT_INTERACTIVE;
        }
        if ($this->FProperties->ContainsKey('Ssl') && $this->FProperties['Ssl'] == 'True') {
            $this->FMysqliFlags += MYSQLI_CLIENT_SSL;
        }

        //TODO: observe and add other PDO common available options.
        $this->FMysqliOptions[MYSQLI_INIT_COMMAND] = 'SET NAMES UTF8';
    }

    /**
     * descHere
     * @param	string	$Url
     * @param	IMap <K: string, V: string>	$Properties
     * @return	IConnection
     */
    public function Connect($Url, $Properties) {
        TType::String($Url);
        TType::Type($Properties, array (
            'IMap' => array ('K' => 'string', 'V' => 'string')));

        $this->FProperties = $Properties;
        if ($this->ValidateUrl($Url)) {
            $this->ConvertProperties();
            try {
                //$this->FMysqli = new mysqli(); TODO: --returns illegal object on php 5.3.1
                $this->FMysqli = mysqli_init();
                foreach ($this->FMysqliOptions as $mKey => &$mValue) {
                    if (!$this->FMysqli->options($mKey, $mValue)) {
                        throw new EFailedToConnectDb(EFailedToConnectDb::CMsg . $Url);
                    }
                }
                if (!$this->FMysqli->real_connect($this->FServer, $this->FProperties['Username'], $this->FProperties['Password'], $this->FDbName, $this->FPort, $this->FSocket, $this->FMysqliFlags)) {
                    throw new EFailedToConnectDb(EFailedToConnectDb::CMsg . $Url);
                }
            }
            catch (EIndexOutOfBounds $Ex) {
                throw new EInsufficientProperties(EInsufficientProperties::CMsg . 'Username, Password.');
            }

            return new TMysqlConnection($this);
        }
        throw new EFailedToConnectDb(EFailedToConnectDb::CMsg . $Url);
    }

    /**
     * descHere
     * @param	string	$Url
     * @param	IMap <K: string, V: string>	$Properties
     * @return	TDriverPropertyInfo[]
     */
    public function GetPropertyInfo($Url, $Properties) {
        TType::String($Url);
        TType::Type($Properties, array (
            'IMap' => array ('K' => 'string', 'V' => 'string')));

        if ($this->ValidateUrl($Url)) {
            $this->FProperties = $Properties;

            $mInfo = array ();

            $mInfo[0] = new TDriverPropertyInfo();
            $mInfo[0]->Choices = array ();
            $mInfo[0]->Description = 'Specify which user to connect the database.';
            $mInfo[0]->Name = 'Username';
            $mInfo[0]->Required = true;
            $mInfo[0]->Value = $this->FProperties['Username'];

            $mInfo[1] = new TDriverPropertyInfo();
            $mInfo[1]->Choices = array ();
            $mInfo[1]->Description = 'The password of the user. Use an empty string for empty password.';
            $mInfo[1]->Name = 'Password';
            $mInfo[1]->Required = true;
            $mInfo[1]->Value = $this->FProperties['Password'];

            $mInfo[2] = new TDriverPropertyInfo();
            $mInfo[2]->Choices = array ();
            $mInfo[2]->Description = 'Specifies the socket or named pipe that should be used.';
            $mInfo[2]->Name = 'Socket';
            $mInfo[2]->Required = false;
            if ($this->FProperties->ContainsKey('Socket')) {
                $mInfo[2]->Value = $this->FProperties['Socket'];
            }
            else {
                $mInfo[2]->Value = '';
            }

            $mInfo[3] = new TDriverPropertyInfo();
            $mInfo[3]->Choices = array ();
            $mInfo[3]->Description = 'Connection timeout in seconds. (supported on Windows with TCP/IP since PHP 5.3.1)';
            $mInfo[3]->Name = 'ConnectTimeout';
            $mInfo[3]->Required = false;
            if ($this->FProperties->ContainsKey('ConnectTimeout')) {
                $mInfo[3]->Value = $this->FProperties['ConnectTimeout'];
            }
            else {
                $mInfo[3]->Value = '0';
            }
            $mInfo[3]->Value = '0';

            $mInfo[4] = new TDriverPropertyInfo();
            $mInfo[4]->Choices = array ('True', 'False');
            $mInfo[4]->Description = 'Enable/disable use of LOAD LOCAL INFILE.';
            $mInfo[4]->Name = 'LocalInfileEnabled';
            $mInfo[4]->Required = false;
            if ($this->FProperties->ContainsKey('LocalInfileEnabled')) {
                $mInfo[4]->Value = $this->FProperties['LocalInfileEnabled'];
            }
            else {
                $mInfo[4]->Value = 'False';
            }

            $mInfo[5] = new TDriverPropertyInfo();
            $mInfo[5]->Choices = array ();
            $mInfo[5]->Description = 'Read options from named option file instead of my.cnf.';
            $mInfo[5]->Name = 'ReadDefaultFile';
            $mInfo[5]->Required = false;
            if ($this->FProperties->ContainsKey('ReadDefaultFile')) {
                $mInfo[5]->Value = $this->FProperties['ReadDefaultFile'];
            }
            else {
                $mInfo[5]->Value = '';
            }

            $mInfo[6] = new TDriverPropertyInfo();
            $mInfo[6]->Choices = array ();
            $mInfo[6]->Description = 'Read options from the named group from my.cnf or the file specified with ReadDefaultFile.';
            $mInfo[6]->Name = 'ReadDefaultGroup';
            $mInfo[6]->Required = false;
            if ($this->FProperties->ContainsKey('ReadDefaultGroup')) {
                $mInfo[6]->Value = $this->FProperties['ReadDefaultGroup'];
            }
            else {
                $mInfo[6]->Value = '';
            }

            $mInfo[7] = new TDriverPropertyInfo();
            $mInfo[7]->Choices = array ('True', 'False');
            $mInfo[7]->Description = 'Use compression protocol.';
            $mInfo[7]->Name = 'ClientCompress';
            $mInfo[7]->Required = false;
            if ($this->FProperties->ContainsKey('ClientCompress')) {
                $mInfo[7]->Value = $this->FProperties['ClientCompress'];
            }
            else {
                $mInfo[7]->Value = 'False';
            }

            $mInfo[8] = new TDriverPropertyInfo();
            $mInfo[8]->Choices = array ('True', 'False');
            $mInfo[8]->Description = 'Return number of matched rows, not the number of affected rows.';
            $mInfo[8]->Name = 'FoundRows';
            $mInfo[8]->Required = false;
            if ($this->FProperties->ContainsKey('FoundRows')) {
                $mInfo[8]->Value = $this->FProperties['FoundRows'];
            }
            else {
                $mInfo[8]->Value = 'False';
            }

            $mInfo[9] = new TDriverPropertyInfo();
            $mInfo[9]->Choices = array ('True', 'False');
            $mInfo[9]->Description = 'Allow spaces after function names. Makes all function names reserved words.';
            $mInfo[9]->Name = 'IgnoreSpace';
            $mInfo[9]->Required = false;
            if ($this->FProperties->ContainsKey('IgnoreSpace')) {
                $mInfo[9]->Value = $this->FProperties['IgnoreSpace'];
            }
            else {
                $mInfo[9]->Value = 'False';
            }

            $mInfo[10] = new TDriverPropertyInfo();
            $mInfo[10]->Choices = array ('True', 'False');
            $mInfo[10]->Description = 'Allow interactive_timeout seconds (instead of wait_timeout seconds) of inactivity before closing the connection.';
            $mInfo[10]->Name = 'Interactive';
            $mInfo[10]->Required = false;
            if ($this->FProperties->ContainsKey('Interactive')) {
                $mInfo[10]->Value = $this->FProperties['Interactive'];
            }
            else {
                $mInfo[10]->Value = 'False';
            }

            $mInfo[11] = new TDriverPropertyInfo();
            $mInfo[11]->Choices = array ('True', 'False');
            $mInfo[11]->Description = 'Use SSL (encryption).';
            $mInfo[11]->Name = 'Ssl';
            $mInfo[11]->Required = false;
            if ($this->FProperties->ContainsKey('Ssl')) {
                $mInfo[11]->Value = $this->FProperties['Ssl'];
            }
            else {
                $mInfo[11]->Value = 'False';
            }

            return $mInfo;
        }
        throw new EFailedToGetDbPropertyInfo(EFailedToGetDbPropertyInfo::CMsg);
    }

    /**
     * descHere
     * @return	TVersion
     */
    public function getVersion() {
        $mVer = new TVersion();
        $mDummy = '';
        sscanf(mysqli_get_client_info(), 'mysqlnd %d.%d.%d-dev - %s - $Revision: %d $', $mVer->MajorVersion, $mVer->MinorVersion, $mVer->Build, $mDummy, $mVer->Revision);
        return $mVer;
    }

    /**
     * descHere
     * @param	string	$Url
     * @return	boolean
     */
    public function ValidateUrl($Url) {
        TType::String($Url);

        $mTemp = explode('://', $Url, 2);
        if (count($mTemp) != 2) {
            return false;
        }
        list ($mProtocol, $mServer) = $mTemp;
        if (count($mTemp) != 2) {
            return false;
        }
        $mTemp = explode('/', $mServer, 2);
        if (count($mTemp) != 2) {
            return false;
        }
        list ($mServer, $mDbName) = $mTemp;
        $mTemp = explode(':', $mServer, 2);
        switch (count($mTemp)) {
            case 2 :
                list ($mServer, $mPort) = $mTemp;
                break;
            case 1 :
                list ($mServer) = $mTemp;
                $mPort = 3306;
                break;
            default :
                return false;
                break;
        }
        if ($mProtocol == 'MySQL' && $mServer != '' && $mDbName != '') {
            $this->FServer = $mServer;
            $this->FPort = (integer) $mPort;
            $this->FDbName = $mDbName;
            return true;
        }
        return false;
    }

    /**
     *
     * Enter description here ...
     * @param	TBaseMysqlObject	$Request
     * @return	mysqli
     */
    public function getMysqli($Request) {
        TType::Object($Request, 'TBaseMysqlObject');
        return $this->FMysqli;
    }
}

/**
 * TMysqlConnection
 * @author	许子健
 */
final class TMysqlConnection extends TBaseMysqlObject implements IConnection {
    /**
     * @var	string
     */
    const CCatalogUnsupported = 'Catalog is not supported by MySQL driver.';
    /**
     * @var	string
     */
    const CHoldabilityUnsupported = 'Holdability is not supported by MySQL driver.';
    /**
     * @var	string
     */
    const CNullDriverOrMysqliObj = 'The driver or/and the mysqli object given is null.';
    /**
     * @var	string
     */
    const CReadOnlyUnsupported = 'ReadOnly is not supported by MySQL driver.';

    /**
     *
     * Enter description here ...
     * @var	boolean
     */
    private $FIsConnected = false;
    /**
     *
     * Enter description here ...
     * @var	EDatabaseWarning
     */
    private $FWarnings = null;
    /**
     *
     * Enter description here ...
     * @var	TMysqlDatabaseMetaData
     */
    private $FMetaData = null;

    /**
     *
     * Enter description here ...
     */
    private function EnsureConnected() {
        if (!$this->FIsConnected) {
            throw new EDisconnected();
        }
    }

    /**
     *
     * Enter description here ...
     * @param	TMysqlConnection	$Connection
     * @param	mysqli	$Mysqli
     * @param	string	$QueryString
     * @param	string	$ExceptionType
     */
    public static function EnsureQuery($Connection, $QueryString, $ExceptionType) {
        if ($Connection->FMysqli->query($QueryString) === false) {
            self::PushWarning($ExceptionType, $Connection->FMysqli->sqlstate, $Connection->FMysqli->errno, $Connection->FMysqli->error, $Connection);
        }
    }

    /**
     *
     * Enter description here ...
     * @param	TMysqlDriver	$Driver
     */
    public function __construct($Driver) {
        TType::Object($Driver, 'TMysqlDriver');

        if ($Driver !== null) {
            parent::__construct($Driver);
            $this->FIsConnected = true;
        }
        else {
            $this->FIsConnected = false;
            throw new EIsNotNullable(self::CNullDriverOrMysqliObj);
        }
    }

    /**
     * (non-PHPdoc)
     * @see TObject::__destruct()
     */
    public function __destruct() {
        if ($this->FIsConnected) {
            $this->ClearWarnings();
            $this->Disconnect();
        }
    }

    /**
     *
     * Enter description here ...
     * @param	string	$WarningType
     * @param	string	$SqlState
     * @param	string	$ErrorCode
     * @param	string	$ErrorMessage
     * @param	TMysqlConnection	$Connection
     */
    public static function PushWarning($WarningType, $SqlState, $ErrorCode, $ErrorMessage, $Connection) {
        TType::String($WarningType);
        TType::String($SqlState);
        TType::String($ErrorCode);
        TType::String($ErrorMessage);
        TType::Object($Connection, 'TMysqlConnection');

        $mWarning = new $WarningType(new TMysqlWarningContext($SqlState, $ErrorCode, $ErrorMessage));
        $mWarning->setNextWarning($Connection->FWarnings);
        $Connection->FWarnings = $mWarning;
        throw $mWarning;
    }

    /**
     * descHere
     */
    public function ClearWarnings() {
        $this->EnsureConnected();
        while ($this->FWarnings !== null) {
            $mCurr = $this->FWarnings->getNextWarning();
            Framework::Free($this->FWarnings);
            $this->FWarnings = $mCurr;
        }
    }

    /**
     * descHere
     */
    public function Commit() {
        $this->EnsureConnected();
        if (!$this->FMysqli->commit()) {
            self::PushWarning(ECommitFailed::ClassType(), '', '', '', $this);
        }
    }

    /**
     * descHere
     * @param	string	$Name
     * @return	ISavepoint
     */
    public function CreateSavepoint($Name = '') {
        TType::String($Name);

        $this->EnsureConnected();
        $mSavepoint = new TSavepoint($Name);
        self::EnsureQuery($this, "SAVEPOINT {$mSavepoint->getProperName()}", ECreateSavepointFailed::ClassType());
        return $mSavepoint;
    }

    /**
     * descHere
     * @param	TResultSetType	$ResultSetType
     * @param	TConcurrencyType	$ConcurrencyType
     * @return	IStatement
     */
    public function CreateStatement($ResultSetType, $ConcurrencyType) {
        TType::Object($ResultSetType, 'TResultSetType');
        TType::Object($ConcurrencyType, 'TConcurrencyType');

        $this->EnsureConnected();

        return new TMysqlStatement($this, $ResultSetType, $ConcurrencyType);
    }

    /**
     * descHere
     */
    public function Disconnect() {
        $this->FMysqli->close();
        $this->FMysqli = null;
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

        self::EnsureQuery($this, $SqlStatement, EExecuteFailed::ClassType());
        return $this->FMysqli->affected_rows;
    }

    /**
     * descHere
     * @return	boolean
     */
    public function getAutoCommit() {
        $this->EnsureConnected();
        $mRaw = $this->FMysqli->query('SELECT @@autocommit');
        if (!$mRaw) {
            self::PushWarning(EExecuteFailed::ClassType(), $this->FMysqli->sqlstate, $this->FMysqli->errno, $this->FMysqli->error, $this);
        }
        $mRaw = $mRaw->fetch_row();
        return (boolean) $mRaw[0];
    }

    /**
     * descHere
     * @return	string
     */
    public function getCatalog() {
        return '';
    }

    /**
     * descHere
     * @return	THoldability
     */
    public function getHoldability() {
        $this->EnsureConnected();
        return THoldability::eCloseCursorsAtCommit(); //TODO: really?
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
     * @return	IDatabaseMetaData
     */
    public function getMetaData() {
        if ($this->FMetaData === null) {
            $this->FMetaData = new TMysqlDatabaseMetaData($this);
        }
        return $this->FMetaData;
    }

    /**
     * descHere
     * @return	boolean
     */
    public function getReadOnly() {
        return false;
    }

    /**
     * descHere
     * @return	TTransactionIsolationLevel
     */
    public function getTransactionIsolation() {
        $mLevel = $this->FMysqli->query('SELECT @@tx_isolation');
        if (!$mLevel) {
            self::PushWarning(EExecuteFailed::ClassType(), $this->FMysqli->sqlstate, $this->FMysqli->errno, $this->FMysqli->error, $this);
        }
        $mLevel = $mLevel->fetch_row();
        $mLevel = (string) $mLevel[0];

        switch ($mLevel) {
            case 'READ-UNCOMMITTED' :
                return TTransactionIsolationLevel::eReadUncommitted();
            case 'READ-COMMITTED' :
                return TTransactionIsolationLevel::eReadCommitted();
            case 'REPEATABLE-READ' :
                return TTransactionIsolationLevel::eRepeatableRead();
            case 'SERIALIZABLE' :
                return TTransactionIsolationLevel::eSerializable();
        }
    }

    /**
     * descHere
     * @return	EDatabaseWarning
     */
    public function getWarnings() {
        $this->EnsureConnected();
        return $this->FWarnings;
    }

    /**
     * descHere
     * @param	TResultSetType	$ResultSetType
     * @param	TConcurrencyType	$ConcurrencyType
     * @param	THoldability	$Holdability
     * @return	IPreparedStatement
     */
    public function PrepareStatement($ResultSetType, $ConcurrencyType) {
        TType::Object($ResultSetType, 'TResultSetType');
        TType::Object($ConcurrencyType, 'TConcurrencyType');

        $this->EnsureConnected();

        //TODO: check if the params are supported first.
        return new TMysqlPreparedStatement($this, $ResultSetType, $ConcurrencyType);
    }

    /**
     * descHere
     * @param	TResultSetType	$ResultSetType
     * @param	TConcurrencyType	$ConcurrencyType
     * @param	THoldability	$Holdability
     * @return	ICallableStatment
     */
    public function PrepareCall($ResultSetType, $ConcurrencyType) {
        TType::Object($ResultSetType, 'TResultSetType');
        TType::Object($ConcurrencyType, 'TConcurrencyType');

        $this->EnsureConnected();

        //TODO: check if the params are supported first.
        return new TMysqlCallableStatment($this, $ResultSetType, $ConcurrencyType);
    }

    /**
     * descHere
     * @param	ISavepoint	$Savepoint
     */
    public function RemoveSavepoint($Savepoint) {
        TType::Object($Savepoint, 'TSavepoint');
        $mName = $Savepoint->getProperName();
        self::EnsureQuery($this, "RELEASE SAVEPOINT {$mName}", EExecuteFailed::ClassType());
    }

    /**
     * descHere
     * @param	ISavepoint	$Savepoint
     */
    public function Rollback($Savepoint = null) {
        TType::Object($Savepoint, 'TSavepoint');
        $this->EnsureConnected();
        if ($Savepoint !== null) {
            $mName = $Savepoint->getProperName();
            $mQueryString = "ROLLBACK TO {$mName}";
        }
        else {
            $mQueryString = 'ROLLBACK';
        }
        self::EnsureQuery($this, $mQueryString, EExecuteFailed::ClassType());
    }

    /**
     * descHere
     * @param	boolean	$Value
     */
    public function setAutoCommit($Value) {
        TType::Bool($Value);
        $this->EnsureConnected();
        if (!$this->FMysqli->autocommit($Value)) {
            self::PushWarning(EExecuteFailed::ClassType(), $this->FMysqli->sqlstate, $this->FMysqli->errno, $this->FMysqli->error, $this);
        }
    }

    /**
     * descHere
     * @param	string	$Value
     */
    public function setCatalog($Value) {
        TType::String($Value);
        throw new EUnsupportedDbFeature(self::CCatalogUnsupported);
    }

    /**
     * descHere
     * @param	THoldability	$Value
     */
    public function setHoldability($Value) {
        TType::Object($Value, 'THoldability');
        if ($Value == THoldability::eHoldCursorsOverCommit()) {
            throw new EUnsupportedDbFeature(self::CHoldabilityUnsupported);
        }
    }

    /**
     * descHere
     * @param	boolean	$Value
     */
    public function setReadOnly($Value) {
        TType::Bool($Value);
        throw new EUnsupportedDbFeature(self::CReadOnlyUnsupported);
    }

    /**
     * descHere
     * @param	TTransactionIsolation	$Value
     */
    public function setTransactionIsolation($Value) {
        TType::Object($Value, 'TTransactionIsolationLevel');
        switch ($Value) {
            case TTransactionIsolationLevel::eReadCommitted() :
                $mSql = 'SET TRANSACTION ISOLATION LEVEL READ COMMITTED';
                break;
            case TTransactionIsolationLevel::eReadUncommitted() :
                $mSql = 'SET TRANSACTION ISOLATION LEVEL READ COMMITTED';
                break;
            case TTransactionIsolationLevel::eRepeatableRead() :
                $mSql = 'SET TRANSACTION ISOLATION LEVEL REPEATABLE READ';
                break;
            case TTransactionIsolationLevel::eSerializable() :
                $mSql = 'SET TRANSACTION ISOLATION LEVEL SERIALIZABLE';
                break;
            case TTransactionIsolationLevel::eNone() :
                throw new EUnsupportedDbFeature(TAbstractPdoConnection::CTransactionIsolationUnsupported);
        }
        self::EnsureQuery($this, $mSql, EExecuteFailed::ClassType());
    }
}

/**
 *
 * Enter description here ...
 * @author	许子健
 */
abstract class TAbstractMysqlStatement extends TBaseMysqlObject {
    /**
     * @var	TMysqlConnection
     */
    protected $FConnection = null;
    /**
     *
     * Enter description here ...
     * @var	TResultSetType
     */
    protected $FResultSetType = null;
    /**
     *
     * Enter description here ...
     * @var	TConcurrencyType
     */
    protected $FConcurrencyType = null;
    /**
     *
     * Enter description here ...
     * @var	string
     */
    protected $FCommand = '';
    /**
     *
     * Enter description here ...
     * @var	TList <T: string>
     */
    private $FCommands = null;
    /**
     *
     * Enter description here ...
     * @var	IResultSet
     */
    protected $FCurrentResultSet = null;

    /**
     *
     * Enter description here ...
     */
    abstract protected function DoSetCommand();

    /**
     *
     * Enter description here ...
     * @return	IResultSet
     */
    abstract protected function DoQuery();

    /**
     *
     * Enter description here ...
     * @param	mixed	$Value
     * @param	integer	$Type
     * @param	integer	$Length
     */
    protected abstract function DoFetchAsScalar(&$Value, &$Type, &$Length);

    /**
     *
     * Enter description here ...
     * @param	string	$Command
     * @return	integer
     */
    protected abstract function DoExecute($Command);

    /**
     *
     * Enter description here ...
     * @param	TMysqlConnection	$Connection
     * @param	TResultSetType		$ResultSetType
     * @param	TConcurrencyType	$ConcurrencyType
     */
    public function __construct($Connection, $ResultSetType, $ConcurrencyType) {
        parent::__construct($Connection->getDriver());

        TType::Object($Connection, 'TMysqlConnection');
        TType::Object($ResultSetType, 'TResultSetType');
        TType::Object($ConcurrencyType, 'TConcurrencyType');

        $this->FConnection = $Connection;
        $this->FResultSetType = $ResultSetType;
        $this->FConcurrencyType = $ConcurrencyType;
    }

    /**
     * (non-PHPdoc)
     * @see TObject::__destruct()
     */
    public function __destruct() {
        Framework::Free($this->FCommands);
    }

    /**
     * descHere
     * @param	string	$Command
     * @return	integer
     */
    public function Execute($Command = '') {
        TType::String($Command);

        if ($Command == '') {
            $Command = $this->FCommand;
        }

        return $this->DoExecute($Command);
    }

    /**
     * descHere
     * @return	integer[]
     */
    public function ExecuteCommands() {
        if ($this->FCommands === null || $this->FCommands->IsEmpty()) {
            throw new EEmptyCommand();
        }
        $mRows = array ();
        try {
            $this->FConnection->setAutoCommit(false);
            foreach ($this->FCommands as $mCmd) {
                $mRows[] = $this->DoExecute($mCmd);
            }
            $this->FConnection->Commit();
        }
        catch (EExecuteFailed $Ex) {
            $this->FConnection->Rollback();
            $mContext = $Ex->getWarningContext();
            TType::Object($mContext, 'TMysqlWarningContext');
            TMysqlConnection::PushWarning(EExecuteFailed::ClassType(), $Ex->getSqlState(), $Ex->getErrorCode(), $mContext->getErrorMessage(), $this->FConnection);
        }
        return $mRows;
    }

    /**
     * descHere
     * @return	IParam <T: ?>
     */
    public function FetchAsScalar() {
        $mRaw = null;
        $mType = -1;
        $mLength = -1;
        $this->DoFetchAsScalar($mRaw, $mType, $mLength);
        $mType = TMysqlValueTypeMapper::MapType($mType, $mLength);
        if ($mType == 'boolean') {
            $mRaw = ($mRaw == 1);
            $mGenericParam = array ('T' => 'boolean');
        }
        elseif (call_user_func('is_' . $mType, $mRaw)) {
            $mGenericParam = array ('T' => $mType);
        }
        elseif ($mRaw === null) {
            return null;
        }
        else {
            $mGenericParam = array ('T' => 'string');
        }
        TPrimativeParam::PrepareGeneric($mGenericParam);
        return new TPrimativeParam($mRaw);
    }

    /**
     * descHere
     * @return	IList <T: string>
     */
    public function getCommands() {
        if ($this->FCommands === null) {
            TList::PrepareGeneric(array ('T' => 'string'));
            $this->FCommands = new TList();
        }
        return $this->FCommands;
    }

    /**
     * descHere
     * @return	IConnection
     */
    public function getConnection() {
        return $this->FConnection;
    }

    /**
     * descHere
     * @return	IResultSet
     */
    public function GetCurrentResult() {
        return $this->FCurrentResultSet;
    }

    /**
     * descHere
     * @param	string	$Command
     * @return	IResultSet
     */
    public function Query($Command = '') {
        TType::String($Command);
        if ($Command != '') {
            $this->setCommand($Command);
        }

        $this->FCurrentResultSet = $this->DoQuery();
        return $this->FCurrentResultSet;
    }

    /**
     * descHere
     * @param	string	$Value
     */
    public function setCommand($Value) {
        TType::String($Value);
        $this->FCommand = $Value;
        $this->DoSetCommand();
    }
}

/**
 * TMysqlStatement
 * @author	许子健
 */
class TMysqlStatement extends TAbstractMysqlStatement implements IStatement {
    /**
     *
     * Enter description here ...
     * @var	mysqli_stmt
     */
    protected $FMysqliStmt = null;

    /**
     *
     * Enter description here ...
     */
    protected function EnsureMysqliStmt() {
        if ($this->FMysqliStmt === null) {
            throw new EEmptyCommand();
        }
    }

    /**
     *
     * Enter description here ...
     * @param	string	$WarningType
     */
    protected function ResetMysqliStmt($WarningType) {
        $mState = $this->FMysqliStmt->sqlstate;
        $mErrno = $this->FMysqliStmt->errno;
        $mError = $this->FMysqliStmt->error;
        $this->FMysqliStmt->close();
        TMysqlConnection::PushWarning(EExecuteFailed::ClassType(), $mState, $mErrno, $mError, $this->FConnection);
    }

    /**
     *
     * Enter description here ...
     */
    protected function DoSetCommand() {
        if (!$this->FMysqliStmt->prepare($this->FCommand)) {
            TMysqlConnection::PushWarning(ESetCommandFailed::ClassType(), $this->FMysqliStmt->sqlstate, $this->FMysqliStmt->errno, $this->FMysqliStmt->error, $this->FConnection);
        }
        switch ($this->FResultSetType) {
            case TResultSetType::eForwardOnly() :
                $this->FMysqliStmt->attr_set(MYSQLI_STMT_ATTR_CURSOR_TYPE, MYSQLI_CURSOR_TYPE_READ_ONLY);
                break;
            case TResultSetType::eScrollInsensitive() :
                $this->FMysqliStmt->attr_set(MYSQLI_STMT_ATTR_CURSOR_TYPE, MYSQLI_CURSOR_TYPE_NO_CURSOR);
                break;

            case TResultSetType::eScrollSensitive() :
                $this->FMysqliStmt->attr_set(MYSQLI_STMT_ATTR_CURSOR_TYPE, MYSQLI_CURSOR_TYPE_READ_ONLY);
                break;
        }
    }

    /**
     *
     * Enter description here ...
     * @return	IResultSet
     */
    protected function DoQuery() {
        return new TMysqlStmtResultSet($this, $this->FMysqliStmt, $this->FConcurrencyType, $this->FResultSetType);
    }

    /**
     *
     * Enter description here ...
     * @param	mixed	$Value
     * @param	integer	$Type
     * @param	integer	$Length
     */
    protected function DoFetchAsScalar(&$Value, &$Type, &$Length) {
        $this->EnsureMysqliStmt();
        $Value = null;
        if (!$this->FMysqliStmt->bind_result($Value)) {
            $this->ResetMysqliStmt(EExecuteFailed::ClassType());
        }
        if (!$this->FMysqliStmt->execute()) {
            $this->ResetMysqliStmt(EExecuteFailed::ClassType());
        }
        $mMeta = $this->FMysqliStmt->result_metadata();
        if ($mMeta === false) {
            $this->ResetMysqliStmt(EFetchAsScalarFailed::ClassType());
        }
        $mFieldMeta = $mMeta->fetch_field();
        if ($mFieldMeta === false) {
            $this->ResetMysqliStmt(EFetchAsScalarFailed::ClassType());
        }
        if ($this->FMysqliStmt->fetch() !== true) {
            $this->ResetMysqliStmt(EFetchAsScalarFailed::ClassType());
        }
        $Type = $mMeta->type;
        $Length = $mMeta->lengths;
        $mMeta->close();
    }

    /**
     *
     * Enter description here ...
     * @param	string	$Command
     * @return	integer
     */
    protected function DoExecute($Command) {
        TMysqlConnection::EnsureQuery($this->FConnection, $Command, EExecuteFailed::ClassType());
        return $this->FMysqli->affected_rows;
    }

    /**
     *
     * Enter description here ...
     * @param	TMysqlConnection	$Connection
     * @param	TResultSetType		$ResultSetType
     * @param	TConcurrencyType	$ConcurrencyType
     */
    public function __construct($Connection, $ResultSetType, $ConcurrencyType) {
        parent::__construct($Connection, $ResultSetType, $ConcurrencyType);
        $this->FMysqliStmt = $this->FMysqli->stmt_init();
    }

    /**
     * (non-PHPdoc)
     * @see TObject::__destruct()
     */
    public function __destruct() {
        if ($this->FMysqliStmt != null) {
            $this->FMysqliStmt->close();
            $this->FMysqliStmt = null;
        }
        //TODO actually it is no need to surround this with the if. for a wired php bug only in Running mode, not in debugging.
        //mysqlistmt::close() will run again after closed.
        parent::__destruct();
    }

    /**
     * descHere
     * @param	integer	$Index
     * @return	IResultSet
     */
    public function getResult($Index) {
        TType::Int($Index);
        if ($Index != 0 || $this->FCurrentResultSet == null) {
            throw new EIndexOutOfBounds();
        }
        return $this->FCurrentResultSet;
    }

    /**
     * descHere
     * @param	TCurrentResultOption	$Options
     */
    public function NextResult($Options) {
        TType::Object($Options, 'TCurrentResultOption');
        TMysqlConnection::PushWarning(ENoMoreResultSet::ClassType(), 0, 0, '', $this->FConnection); //TODO: provide more details about the error.
    }
}

/**
 *
 * Enter description here ...
 * @author	许子健
 */
class TMysqlPreparedStatement extends TMysqlStatement implements IPreparedStatement {
    /**
     *
     * Enter description here ...
     * @var	TMap <K: string, V: IParam<T: ?>>
     */
    private $FParams = null;
    /**
     *
     * Enter description here ...
     * @var	array
     */
    private $FRawParams = array ();
    /**
     *
     * Enter description here ...
     * @var	string
     */
    const CPattern = <<<'EOD'
/('.*(?<!\\)')|(".*(?<!\\)")|(:[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/
EOD;

    /**
     * (non-PHPdoc)
     * @see TMysqlStatement::DoSetCommand()
     */
    protected function DoSetCommand() {
        $mChunks = preg_split(self::CPattern, $this->FCommand, 0, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        foreach ($mChunks as &$mChunk) {
            if ($mChunk[0] == ':') {
                $this->FRawParams[] = $mChunk;
                $mChunk = '?';
            }
        }
        $mOriginalCommand = $this->FCommand;
        $this->FCommand = implode('', $mChunks);
        try {
            parent::DoSetCommand();
        }
        catch (ESetCommandFailed $Ex) {
            $this->FCommand = $mOriginalCommand;
            throw $Ex;
        }
        $this->FCommand = $mOriginalCommand;
    }

    /**
     * (non-PHPdoc)
     * @see TMysqlStatement::DoQuery()
     * @return	IResultSet
     */
    protected function DoQuery() {
        if ($this->FParams != null && $this->FParams->Size() > 0) {
            $mTypes = '';
            $mParamsRef = array ();
            foreach ($this->FRawParams as $mKey => &$mParam) {
                $mTypes .= 's';
                $mParam = $this->FParams[$mParam]->getValue();
                $mParamsRef[] = &$this->FRawParams[$mKey];
            }
            array_unshift($mParamsRef, $mTypes);
            if (!call_user_func_array(array ($this->FMysqliStmt, 'bind_param'), $mParamsRef)) {
                TMysqlConnection::PushWarning(EExecuteFailed::ClassType(), $this->FMysqliStmt->sqlstate, $this->FMysqliStmt->errno, $this->FMysqliStmt->error, $this->FConnection);
            }
        }
        return new TMysqlStmtResultSet($this, $this->FMysqliStmt, $this->FConcurrencyType, $this->FResultSetType);
    }

    /**
     * (non-PHPdoc)
     * @see TMysqlStatement::DoExecute()
     * @param	string	$Command
     * @return	integer
     */
    protected function DoExecute($Command) {
        $mChunks = preg_split(self::CPattern, $Command, 0, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        foreach ($mChunks as &$mChunk) {
            if ($mChunk[0] == ':') {
                $this->FRawParams[] = $mChunk;
                $mChunk = '?';
            }
        }

        if (!$this->FMysqliStmt->prepare(implode('', $mChunks)) || !$this->FMysqliStmt->execute()) {
            TMysqlConnection::PushWarning(EExecuteFailed::ClassType(), $this->FMysqliStmt->sqlstate, $this->FMysqliStmt->errno, $this->FMysqliStmt->error, $this->FConnection);
        }
        return $this->FMysqliStmt->affected_rows;
    }

    /**
     * (non-PHPdoc)
     * @see TMysqlStatement::__destruct()
     */
    public function __destruct() {
        Framework::Free($this->FParams);
        parent::__destruct();
    }

    /**
     * (non-PHPdoc)
     * @see IPreparedStatement::BindParam()
     * @param	string	$Name
     * @param	IParam	$Param <T: ?>
     */
    public function BindParam($Name, $Param) {
        TType::Object($Param, 'IParam');

        if ($this->FParams == null) {
            TMap::PrepareGeneric(array ('K' => 'string', 'V' => 'IParam',
                'T' => array ('TPair', array ('K' => 'string', 'V' => 'IParam'))));
            $this->FParams = new TMap(true);
        }

        $this->FParams->Put($Name, $Param);
    }

    /**
     * (non-PHPdoc)
     * @see IPreparedStatement::ClearParams()
     */
    public function ClearParams() {
        Framework::Free($this->FParams);
    }
}

/**
 *
 * Enter description here ...
 * @author	许子健
 */
class TMysqlCallableStatment extends TAbstractMysqlStatement implements ICallableStatement {
    /**
     *
     * Enter description here ...
     * @var	TMap <K: string, V: IParam<T: ?>>
     */
    private $FParams = null;
    /**
     *
     * Enter description here ...
     * @var	string[]
     */
    private $FCurrentSetParams = array ();

    /**
     *
     * Enter description here ...
     * @var	TLinkedList <T: TMysqlResultSet>
     */
    private $FResultSets = null;
    /**
     *
     * Enter description here ...
     * @var	integer
     */
    private $FCurrentResultSetIndex = -1;

    /**
     *
     * Enter description here ...
     * @return	mysqli_result
     */
    private function FetchRawResult() {
        foreach ($this->FCurrentSetParams as &$mParam) {
            $mParamValue = $this->FMysqli->real_escape_string($this->FParams[substr($mParam, 1)]->getValue());
            if ($this->FMysqli->query("SET @p{$mParam} = '{$mParamValue}'") === false) {
                TMysqlConnection::PushWarning(EExecuteFailed::ClassType(), $this->FMysqli->sqlstate, $this->FMysqli->errno, $this->FMysqli->error, $this->FConnection);
            }
        }

        if ($this->FMysqli->multi_query('EXECUTE sCall') && $this->FMysqli->more_results()) {
            $mRawResult = $this->FMysqli->store_result();
            if ($mRawResult === false) {
                TMysqlConnection::PushWarning(EExecuteFailed::ClassType(), $this->FMysqli->sqlstate, $this->FMysqli->errno, $this->FMysqli->error, $this->FConnection); //TODO: failed to fetch next result set.
            }

            return $mRawResult;
        }
        else {
            try {
                TMysqlConnection::PushWarning(EExecuteFailed::ClassType(), $this->FMysqli->sqlstate, $this->FMysqli->errno, $this->FMysqli->error, $this->FConnection);
            }
            catch (EExecuteFailed $Ex) {
                while ($this->FMysqli->more_results() && $this->FMysqli->next_result())
                    ;
                throw $Ex;
            }
        }
    }

    /**
     * (non-PHPdoc)
     * @see TMysqlStatement::DoSetCommand()
     */
    protected function DoSetCommand() {
        $this->FCurrentSetParams = array ();
        $mChunks = preg_split(TMysqlPreparedStatement::CPattern, $this->FCommand, 0, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        foreach ($mChunks as $mIndex => &$mChunk) {
            if ($mChunk[0] == ':') {
                $mChunk[0] = '_';
                $this->FCurrentSetParams[] = $mChunks[$mIndex];
                $mChunk = "@p{$mChunk}";
            }
        }
        $mCmd = $this->FMysqli->real_escape_string(implode('', $mChunks));
        if ($this->FMysqli->query("PREPARE sCall FROM '{$mCmd}'") === false) {
            TMysqlConnection::PushWarning(ESetCommandFailed::ClassType(), $this->FMysqli->sqlstate, $this->FMysqli->errno, $this->FMysqli->error, $this->FConnection);
        }
    }

    /**
     * (non-PHPdoc)
     * @see TMysqlStatement::DoQuery()
     * @return	IResultSet
     */
    protected function DoQuery() {
        if ($this->FResultSetType == TResultSetType::eScrollSensitive() || $this->FConcurrencyType == TConcurrencyType::eUpdatable()) {
            throw new EUnsupportedDbFeature();
        }

        if ($this->FResultSets == null) {
            TLinkedList::PrepareGeneric(array ('T' => 'TMysqlResultSet'));
            $this->FResultSets = new TLinkedList(true);
        }
        $this->FResultSets->Clear();
        $this->FCurrentResultSet = null;
        $this->FCurrentResultSetIndex = -1;

        $this->FResultSets->Add(new TMysqlResultSet($this, $this->FetchRawResult(), $this->FConcurrencyType, $this->FResultSetType));
        $this->FCurrentResultSetIndex = 0;
        $this->FCurrentResultSet = $this->FResultSets[0];
        return $this->FCurrentResultSet;
    }

    /**
     *
     * Enter description here ...
     * @param	mixed	$Value
     * @param	integer	$Type
     * @param	integer	$Length
     */
    protected function DoFetchAsScalar(&$Value, &$Type, &$Length) {
        $mRawResult = $this->FetchRawResult();

        $mMeta = $mRawResult->fetch_field();
        $Type = $mMeta->type;
        $Length = $mMeta->length;
        $mMeta = null;
        $mRawRow = $mRawResult->fetch_row();
        $mRawResult->close();

        while ($this->FMysqli->more_results() && $this->FMysqli->next_result())
            ;
        if ($mRawRow === null) {
            TMysqlConnection::PushWarning(EExecuteFailed::ClassType(), $this->FMysqli->sqlstate, $this->FMysqli->errno, $this->FMysqli->error, $this->FConnection);
        }
        $Value = $mRawRow[0];
    }

    /**
     * (non-PHPdoc)
     * @see TMysqlStatement::DoExecute()
     * @param	string	$Command
     * @return	integer
     */
    protected function DoExecute($Command) {
        $mRawResult = $this->FetchRawResult();

        $mResult = $mRawResult->num_rows;
        $mRawResult->close();
        while ($this->FMysqli->more_results() && $this->FMysqli->next_result())
            ;
        return $mResult;
    }

    /**
     * (non-PHPdoc)
     * @see TAbstractMysqlStatement::__destruct()
     */
    public function __destruct() {
        Framework::Free($this->FResultSets);
        parent::__destruct();
    }

    /**
     *
     * @param	string			$Name
     * @return	IParam <T: ?>
     */
    public function GetParam($Name) {
        TType::String($Name);

        $mRawResult = $this->FMysqli->query("SELECT @p_{$Name}");
        if (!$mRawResult instanceof mysqli_result) {
            TMysqlConnection::PushWarning(EExecuteFailed::ClassType(), $this->FMysqli->sqlstate, $this->FMysqli->errno, $this->FMysqli->error, $this->FConnection);
        }
        $mRawRow = $mRawResult->fetch_row();
        if ($mRawRow[0] === null) {
            $this->FParams[$Name] = null;
        }
        else {
            $this->FParams[$Name]->setValue($mRawRow[0]);
        }
        $mRawResult->close();

        return $this->FParams[$Name];
    }

    /**
     * (non-PHPdoc)
     * @see IPreparedStatement::BindParam()
     * @param	string	$Name
     * @param	IParam	$Param <T: ?>
     */
    public function BindParam($Name, $Param) {
        TType::Object($Param, 'IParam');

        if ($this->FParams == null) {
            TMap::PrepareGeneric(array ('K' => 'string', 'V' => 'IParam',
                'T' => array ('TPair', array ('K' => 'string', 'V' => 'IParam'))));
            $this->FParams = new TMap(true);
        }

        $this->FParams->Put($Name, $Param);
    }

    /**
     * (non-PHPdoc)
     * @see IPreparedStatement::ClearParams()
     */
    public function ClearParams() {
        Framework::Free($this->FParams);
    }

    /**
     * descHere
     * @param	integer	$Index
     * @return	IResultSet
     */
    public function getResult($Index) {
        TType::Int($Index);

        if ($this->FCurrentResultSetIndex == -1) {
            throw new EIndexOutOfBounds(); //TODO: no result set.
        }

        if ($Index < 0 || $Index > $this->FResultSets->Size()) {
            throw new EIndexOutOfBounds();
        }

        $this->FCurrentResultSet = $this->FResultSets[$Index];
        $this->FCurrentResultSetIndex = $Index;
        return $this->FCurrentResultSet;
    }

    /**
     * descHere
     * @param	TCurrentResultOption	$Options
     */
    public function NextResult($Options) {
        TType::Object($Options, 'TCurrentResultOption');
        if (!$this->FMysqli->more_results()) {
            throw new EException(); //TODO: no more new result sets.
        }
        switch ($Options) {
            case TCurrentResultOption::eCloseAllResults() :
                $this->FResultSets->Clear();
                break;
            case TCurrentResultOption::eCloseCurrentResult() :
                $this->FResultSets->RemoveAt($this->FCurrentResultSetIndex);
                break;
        }

        if (!$this->FMysqli->next_result()) {
            TMysqlConnection::PushWarning(ENoMoreResultSet::ClassType(), $this->FMysqli->sqlstate, $this->FMysqli->errno, $this->FMysqli->error, $this->FConnection);
        }

        $mRawResult = $this->FMysqli->store_result();
        if ($mRawResult === false) {
            TMysqlConnection::PushWarning(EFetchNextResultSetFailed::ClassType(), $this->FMysqli->sqlstate, $this->FMysqli->errno, $this->FMysqli->error, $this->FConnection);
        }

        $this->FCurrentResultSetIndex = $this->FResultSets->Size();
        $this->FCurrentResultSet = new TMysqlResultSet($this, $mRawResult, $this->FConcurrencyType, $this->FResultSetType);
        $this->FResultSets->Add($this->FCurrentResultSet);
    }

}

/**
 * TAbstractMysqlResultSet
 * @author	许子健
 */
abstract class TAbstractMysqlResultSet extends TBaseMysqlObject {

    /**
     *
     * Enter description here ...
     * @var	TAbstractMysqlStatement
     */
    protected $FStatement = null;
    /**
     *
     * Enter description here ...
     * @var TResultSetType
     */
    protected $FResultSetType = null;
    /**
     *
     * Enter description here ...
     * @var	array
     */
    protected $FMeta = array ();
    /**
     *
     * Enter description here ...
     * data structure:
     * colName => value, ...
     * @var	array
     */
    protected $FCurrentRow = array ();
    /**
     *
     * Enter description here ...
     * @var	integer
     */
    private $FCurrentRowId = -1;
    /**
     *
     * Enter description here ...
     * @var	TFetchDirection
     */
    private $FFetchDirection = null;
    /**
     *
     * Enter description here ...
     * @var	TMysqlRow
     */
    private $FRow = null;
    /**
     *
     * Enter description here ...
     * @var	TMysqlInsertRow
     */
    private $FInsertRow = null;
    /**
     *
     * Enter description here ...
     * @var	boolean
     */
    private $FValid = false;
    /**
     *
     * Enter description here ...
     * @var	boolean
     */
    private $FWasDeleted = false;
    /**
     *
     * Enter description here ...
     * @var	boolean
     */
    private $FWasUpdated = false;

    /**
     *
     * Enter description here ...
     */
    abstract protected function FetchMeta();

    /**
     *
     * Enter description here ...
     * @return	integer
     */
    abstract protected function DoGetCount();

    /**
     *
     * Enter description here ...
     * @param	integer	$Value
     */
    abstract protected function DoSetFetchSize($Value);

    /**
     *
     * Enter description here ...
     * @param	integer	$RowId
     */
    abstract protected function DoSeekAndFetch($RowId);

    /**
     *
     * Enter description here ...
     * @return	boolean
     */
    abstract protected function DoFetch();

    /**
     *
     * Enter description here ...
     */
    abstract protected function DoReset();

    /**
     *
     * Enter description here ...
     * @param	integer	$RowId
     */
    private function EnsureRowId($RowId) {
        if ($RowId < -1) {
            throw new EInvalidRowId();
        }
    }

    /**
     *
     * Enter description here ...
     * @param	integer	$RowId
     */
    private function FetchForward($RowId) {
        $mFetchFlag = null;
        $mCurrRowId = $this->FCurrentRowId;
        while ($mCurrRowId < $RowId) {
            $mFetchFlag = $this->DoFetch();
            if ($mFetchFlag === null) {
                break;
            }
            if ($mFetchFlag === false) {
                TMysqlConnection::PushWarning(EExecuteFailed::ClassType(), $this->FMysqliStmt->sqlstate, $this->FMysqliStmt->errno, $this->FMysqliStmt->error, $this->FStatement->getConnection());
            }
            ++$mCurrRowId;
        }
        if ($mCurrRowId !== $RowId) {
            throw new EInvalidRowId();
        }
        $this->FCurrentRowId = $RowId;
    }

    /**
     *
     * Enter description here ...
     * @param	integer	$RowId
     */
    private function FetchTo($RowId) {
        if ($RowId == -1) {
            $this->Refresh();
        }
        else {
            switch ($this->FResultSetType) {
                case TResultSetType::eForwardOnly() :
                    if ($RowId < $this->FCurrentRowId) {
                        throw new EInvalidRowId();
                    }
                    if ($RowId > $this->FCurrentRowId) {
                        $this->FetchForward($RowId);
                    }
                    break;
                case TResultSetType::eScrollInsensitive() :
                    $mCount = $this->DoGetCount();
                    if ($RowId >= $mCount) {
                        throw new EInvalidRowId();
                    }
                    if ($this->FFetchDirection == TFetchDirection::eReverse()) {
                        $RowId = $mCount - $RowId - 1;
                    }
                    $this->DoSeekAndFetch($RowId);
                    $this->FCurrentRowId = $RowId;
                    break;
                case TResultSetType::eScrollSensitive() :
                    if ($RowId <= $this->FCurrentRowId) {
                        $this->Refresh();
                    }
                    $this->FetchForward($RowId);
                    break;
            }
            $this->FWasDeleted = false;
            $this->FWasUpdated = false;
        }
    }

    /**
     * Enter description here ...
     * @param	TAbstractMysqlStatement	$Statement
     * @param	TConcurrencyType	$ConcurrencyType
     * @param	TResultSetType	$ResultSetType
     */
    public function __construct($Statement, $ConcurrencyType, $ResultSetType) {
        $this->PrepareGeneric(array ('K' => 'integer', 'V' => 'IRow',
            'T' => 'IRow'));
        parent::__construct($Statement->getConnection()->getDriver());
        TType::Object($Statement, 'TAbstractMysqlStatement');
        TType::Object($ConcurrencyType, 'TConcurrencyType');
        TType::Object($ResultSetType, 'TResultSetType');

        $this->FStatement = $Statement;
        $this->FResultSetType = $ResultSetType;
        $this->FFetchDirection = TFetchDirection::eForward();

        $this->FetchMeta();

        $this->FRow = new TMysqlRow($this, $ConcurrencyType, $this->FCurrentRow, $this->FMeta, $this->FWasDeleted, $this->FWasUpdated);
        if ($ConcurrencyType == TConcurrencyType::eUpdatable()) {
            $this->FInsertRow = new TMysqlInsertRow($this, $ConcurrencyType, $this->FMeta);
        }
    }

    /**
     * (non-PHPdoc)
     * @see TObject::__destruct()
     */
    public function __destruct() {
        Framework::Free($this->FRow);
        Framework::Free($this->FInsertRow);
    }

    /**
     * descHere
     * @return	IRow
     */
    public function current() {
        return $this->FRow;
    }

    /**
     * descHere
     * @param	integer	$RowId
     * @return	IRow
     */
    public function FetchAbsolute($RowId) {
        TType::Int($RowId);

        $this->EnsureRowId($RowId);
        $this->FetchTo($RowId);
        return $this->FRow;
    }

    /**
     * descHere
     * @param	integer	$Offset
     * @return	IRow
     */
    public function FetchRelative($Offset) {
        TType::Int($Offset);

        $Offset += $this->FCurrentRowId;
        $this->EnsureRowId($Offset);
        $this->FetchTo($Offset);
        return $this->FRow;
    }

    /**
     * descHere
     * @return	integer
     */
    public function getCount() {
        switch ($this->FResultSetType) {
            case TResultSetType::eForwardOnly() :
                return $this->FCurrentRowId + 1;
                break;
            case TResultSetType::eScrollInsensitive() :
                return $this->DoGetCount();
                break;
            case TResultSetType::eScrollSensitive() :
                return $this->FCurrentRowId + 1;
                break;
        }
    }

    /**
     * descHere
     * @return	string
     */
    public function getCursorName() {
        throw new EUnsupportedDbFeature();
    }

    /**
     * descHere
     * @return	TFetchDirection
     */
    public function getFetchDirection() {
        return $this->FFetchDirection;
    }

    /**
     * (non-PHPdoc)
     * @see IResultSet::getInsertRow()
     * @return IRow
     */
    public function getInsertRow() {
        return $this->FInsertRow;
    }

    /**
     * descHere
     * @return	boolean
     */
    public function getIsEmpty() {
        return $this->getCount() == 0;
    }

    /**
     * descHere
     * @return	IReusltMetaData
     */
    public function getMetaData() {
        //TODO todo
    }

    /**
     * descHere
     * @return	TResultSetType
     */
    public function getType() {
        return $this->FResultSetType;
    }

    /**
     * descHere
     * @return	IStatement
     */
    public function getStatement() {
        return $this->FStatement;
    }

    /**
     * descHere
     * @return	integer
     */
    public function key() {
        return $this->FCurrentRowId;
    }

    /**
     * descHere
     */
    public function next() {
        try {
            $this->FetchTo($this->FCurrentRowId + 1);
        }
        catch (EInvalidRowId $Ex) {
            $this->FValid = false;
        }
        catch (EExecuteFailed $Ex) {
            $this->FValid = false;
        }
    }

    /**
     * descHere
     * @param	integer	$offset
     * @return	boolean
     */
    public final function offsetExists($offset) {
        TType::Int($offset);

        try {
            $this->FetchTo($offset);
            return true;
        }
        catch (EInvalidRowId $Ex) {
            return false;
        }
    }

    /**
     * descHere
     * @param	integer	$offset
     * @return	IRow
     */
    public final function offsetGet($offset) {
        TType::Int($offset);
        return $this->FetchAbsolute($offset);
    }

    /**
     * descHere
     * @param	integer	$offset
     * @param	IRow	$value
     */
    public final function offsetSet($offset, $value) {
        TType::Int($offset);
        $this->FetchAbsolute($offset);
        foreach ($value as $mColumn => $mData) {
            if ($mData == null) {
                $this->FPendingUpdateRow["`{$this->FColumnNames[$offset]}`"] = null;
            }
            else {
                $this->FPendingUpdateRow["`{$this->FColumnNames[$offset]}`"] = $mData->getValue();
            }
        }
    }

    /**
     * descHere
     * @param	integer	$offset
     */
    public final function offsetUnset($offset) {
        TType::Int($offset);
        $this->FetchAbsolute($offset)->Delete();
    }

    /**
     * descHere
     */
    public function rewind() {
        try {
            $this->Refresh();
            $this->FValid = true;
        }
        catch (EExecuteFailed $Ex) {
            $this->FValid = false;
        }
        $this->FetchTo(0);
    }

    /**
     * descHere
     * @param	TFetchDirection	$Value
     */
    public function setFetchDirection($Value) {
        TType::Object($Value, 'TFetchDirection');
        if ($Value == TFetchDirection::eUnkown()) {
            throw new EInvalidParameter();
        }
        if ($this->FResultSetType != TResultSetType::eScrollInsensitive() && $Value == TFetchDirection::eReverse()) {
            throw new EUnsupportedDbFeature();
        }
        $this->FFetchDirection = $Value;
    }

    /**
     * descHere
     * @param	integer	$Value
     */
    public function setFetchSize($Value) {
        TType::Int($Value);
        if ($Value < 1) {
            throw new EInvalidParameter();
        }

        $this->DoSetFetchSize($Value);
    }

    /**
     * descHere
     * @return	boolean
     */
    public function valid() {
        return $this->FValid;
    }

    /**
     * descHere
     */
    public function Refresh() {
        $this->FCurrentRowId = -1;
        try {
            $this->DoReset();
        }
        catch (EExecuteFailed $Ex) {
            throw $Ex;
        }
    }

    /**
     *
     * Enter description here ...
     */
    public function Remove() {
        $this->FRow->Delete();
    }
}

/**
 * TMysqlResultSet
 * @author	许子健
 */
class TMysqlResultSet extends TAbstractMysqlResultSet implements IResultSet {
    /**
     *
     * Enter description here ...
     * @var	mysqli_result
     */
    private $FMysqliResult = null;

    /**
     * (non-PHPdoc)
     * @see TAbstractMysqlResultSet::FetchMeta()
     */
    protected function FetchMeta() {
        $this->FMeta = $this->FMysqliResult->fetch_fields();
    }

    /**
     * descHere
     * @return	boolean
     */
    protected function DoFetch() {
        $mRaw = $this->FMysqliResult->fetch_assoc();
        if ($mRaw === null) {
            return false;
        }
        else {
            $this->FCurrentRow = $mRaw;
            return true;
        }
    }

    /**
     * descHere
     * @return	integer
     */
    protected function DoGetCount() {
        return $this->FMysqliResult->num_rows;
    }

    /**
     * descHere
     */
    protected function DoReset() {
        $this->FMysqliResult->data_seek(0);
    }

    /**
     * descHere
     * @param	integer	$RowId
     */
    protected function DoSeekAndFetch($RowId) {
        $this->FMysqliResult->data_seek($RowId);
        $this->FCurrentRow = $this->FMysqliResult->fetch_assoc();
    }

    /**
     * descHere
     * @param	integer	$Value
     */
    protected function DoSetFetchSize($Value) {
        throw new EUnsupportedDbFeature(); //TODO: unsupported.
    }

    /**
     * (non-PHPdoc)
     * @see IResultSet::getFetchSize()
     * @return	integer
     */
    public function getFetchSize() {
        return 1;
    }

    /**
     *
     * @param	TMysqlCallableStatment		$Statement
     * @param	mysqli_result				$MysqliResult
     * @param	TConcurrencyType			$ConcurrencyType
     * @param	TResultSetType				$ResultSetType
     * Enter description here ...
     */
    public function __construct($Statement, $MysqliResult, $ConcurrencyType, $ResultSetType) {
        TType::Object($Statement, 'TMysqlCallableStatment');
        TType::Object($ConcurrencyType, 'TConcurrencyType');
        TType::Object($ResultSetType, 'TResultSetType');

        $this->FMysqliResult = $MysqliResult;
        parent::__construct($Statement, $ConcurrencyType, $ResultSetType);
    }

    /**
     * (non-PHPdoc)
     * @see TAbstractMysqlResultSet::__destruct()
     */
    public function __destruct() {
        if ($this->FMysqliResult != null) {
            $this->FMysqliResult->close();
            $this->FMysqliResult = null;
        }
        parent::__destruct();
    }
}

/**
 * TMysqlStmtResultSet
 * @author	许子健
 */
class TMysqlStmtResultSet extends TAbstractMysqlResultSet implements IResultSet {

    /**
     *
     * Enter description here ...
     * @var	mysqli_stmt
     */
    private $FMysqliStmt = null;

    /**
     * (non-PHPdoc)
     * @see TAbstractMysqlResultSet::FetchMeta()
     */
    protected function FetchMeta() {
        $mRawMeta = $this->FMysqliStmt->result_metadata();

        if ($mRawMeta instanceof mysqli_result) {
            $this->FMeta = $mRawMeta->fetch_fields();
            $mRawMeta->close();
        }
        else { //means this SQL does not yield resultsets.
            TMysqlConnection::PushWarning(EExecuteFailed::ClassType(), $this->FMysqliStmt->sqlstate, $this->FMysqliStmt->errno, $this->FMysqliStmt->error, $this->FStatement->getConnection());
        }
    }

    /**
     * (non-PHPdoc)
     * @see TAbstractMysqlResultSet::DoSetFetchSize()
     * @param	integer	$Value
     */
    protected function DoSetFetchSize($Value) {
        $this->FMysqliStmt->attr_set(MYSQLI_STMT_ATTR_PREFETCH_ROWS, $Value);
    }

    /**
     * (non-PHPdoc)
     * @see TAbstractMysqlResultSet::DoGetCount()
     * @return	integer
     */
    protected function DoGetCount() {
        $this->FMysqliStmt->store_result();
        return $this->FMysqliStmt->num_rows;
    }

    /**
     * (non-PHPdoc)
     * @see TAbstractMysqlResultSet::DoReset()
     */
    protected function DoReset() {
        if (!$this->FMysqliStmt->reset()) {
            TMysqlConnection::PushWarning(EFetchRowFailed::ClassType(), $this->FMysqliStmt->sqlstate, $this->FMysqliStmt->errno, $this->FMysqliStmt->error, $this->FStatement->getConnection());
        }
        foreach ($this->FMeta as $mItem) {
            $mParams[] = &$this->FCurrentRow[$mItem->name];
        }
        call_user_func_array(array ($this->FMysqliStmt, 'bind_result'), $mParams);
        if (!$this->FMysqliStmt->execute()) {
            TMysqlConnection::PushWarning(EExecuteFailed::ClassType(), $this->FMysqliStmt->sqlstate, $this->FMysqliStmt->errno, $this->FMysqliStmt->error, $this->FStatement->getConnection());
        }
    }

    /**
     * (non-PHPdoc)
     * @see TAbstractMysqlResultSet::DoSeekAndFetch()
     * @param	integer	$RowId
     */
    protected function DoSeekAndFetch($RowId) {
        $this->FMysqliStmt->data_seek($RowId);
        if ($this->FMysqliStmt->fetch() === false) {
            TMysqlConnection::PushWarning(EExecuteFailed::ClassType(), $this->FMysqliStmt->sqlstate, $this->FMysqliStmt->errno, $this->FMysqliStmt->error, $this->FStatement->getConnection());
        }
    }

    /**
     * (non-PHPdoc)
     * @see TAbstractMysqlResultSet::DoFetch()
     * @return	boolean
     */
    protected function DoFetch() {
        return $this->FMysqliStmt->fetch();
    }

    /**
     *
     * @param	TMysqlStatement	$Statement
     * @param	mysqli_stmt		$MysqliStmt
     * @param	TConcurrencyType	$ConcurrencyType
     * @param	TResultSetType	$ResultSetType
     * Enter description here ...
     */
    public function __construct($Statement, $MysqliStmt, $ConcurrencyType, $ResultSetType) {
        TType::Object($Statement, 'TMysqlStatement');
        TType::Object($ConcurrencyType, 'TConcurrencyType');
        TType::Object($ResultSetType, 'TResultSetType');

        $this->FMysqliStmt = $MysqliStmt;
        parent::__construct($Statement, $ConcurrencyType, $ResultSetType);

        foreach ($this->FMeta as $mItem) {
            $mParams[] = &$this->FCurrentRow[$mItem->name];
        }

        if (!$this->FMysqliStmt->execute()) {
            TMysqlConnection::PushWarning(EExecuteFailed::ClassType(), $this->FMysqliStmt->sqlstate, $this->FMysqliStmt->errno, $this->FMysqliStmt->error, $this->FStatement->getConnection());
        }
        if ($this->FMysqliStmt->attr_get(MYSQLI_STMT_ATTR_CURSOR_TYPE) === MYSQLI_CURSOR_TYPE_NO_CURSOR) { //TODO: php-cgi crashed here only in SnowLeopard.
            //TODO: php-cgi doesn't return NO_CURSOR in WINDOWS! MYSQLI_CURSOR_TYPE_READ_ONLY returned.
            $this->FMysqliStmt->store_result();
        }

        call_user_func_array(array ($this->FMysqliStmt, 'bind_result'), $mParams);
    }

    /**
     * (non-PHPdoc)
     * @see TObject::__destruct()
     */
    public function __destruct() {
        if ($this->FMysqliStmt != null) {
            if ($this->FMysqliStmt->attr_get(MYSQLI_STMT_ATTR_CURSOR_TYPE) === MYSQLI_CURSOR_TYPE_NO_CURSOR) {
                $this->FMysqliStmt->free_result();
            }
        }
        $this->FMysqliStmt = null;

        parent::__destruct();
    }

    /**
     * (non-PHPdoc)
     * @see IResultSet::getFetchSize()
     * @return	integer
     */
    public function getFetchSize() {
        $mRaw = $this->FMysqliStmt->attr_get(MYSQLI_STMT_ATTR_PREFETCH_ROWS);
        if ($mRaw === false) {
            throw new EFailedToGetFetchSize();
        }
        else {
            return $mRaw;
        }
    }
}

/**
 *
 * Enter description here ...
 * @author ExSystem
 */
abstract class TAbstractMysqlRow extends TBaseMysqlObject {
    /**
     *
     * Enter description here ...
     * @var	TAbstractMysqlResultSet
     */
    protected $FResultSet = null;
    /**
     *
     * Enter description here ...
     * @var	TConcurrencyType
     */
    private $FConcurrencyType = null;
    /**
     *
     * Enter description here ...
     * @var	array
     */
    protected $FRowMeta = array ();
    /**
     *
     * Enter description here ...
     * @var	array
     */
    private $FColumnNames = array ();
    /**
     *
     * Enter description here ...
     * @var	array
     */
    protected $FPendingUpdateRow = array ();
    /**
     *
     * Enter description here ...
     * @var	string
     */
    protected $FTableName = '';
    /**
     *
     * Enter description here ...
     * @var	string[]
     */
    protected $FPrimaryKeys = array ();
    /**
     *
     * Enter description here ...
     * @var	boolean
     */
    protected $FWasUpdated = false;

    /**
     *
     * Enter description here ...
     */
    protected function EnsureUpdatable() {
        if ($this->FTableName == '') {
            throw new EUnableToUpdateNonSingleTableResultSet();
        }
        if ($this->FResultSet->getType() != TResultSetType::eScrollSensitive() || $this->FConcurrencyType == TConcurrencyType::eReadOnly()) {
            throw new EResultSetIsNotUpdatable();
        }
    }

    /**
     *
     * Enter description here ...
     */
    abstract protected function DoUpdate();

    /**
     *
     * Enter description here ...
     * @param	TAbstractMysqlResultSet	$ResultSet
     * @param	TConcurrencyType		$ConcurrencyType
     * @param	array					$Meta
     * @param	boolean					$WasUpdated
     */
    public function __construct($ResultSet, $ConcurrencyType, &$Meta, &$WasUpdated) {
        TType::Object($ResultSet, 'TAbstractMysqlResultSet');
        TType::Object($ConcurrencyType, 'TConcurrencyType');
        parent::__construct($ResultSet->getStatement()->getDriver());

        $this->FResultSet = $ResultSet;
        $this->FConcurrencyType = $ConcurrencyType;
        $this->FWasUpdated = &$WasUpdated;
        $this->FTableName = "`{$Meta[0]->orgtable}`";
        foreach ($Meta as $mMetaObject) {
            //NOT_NULL_FLAG = 1
            //UNIQUE_KEY_FLAG = 4
            //PRI_KEY_FLAG = 2
            //BLOB_FLAG = 16
            //UNSIGNED_FLAG = 32
            //ZEROFILL_FLAG = 64
            //BINARY_FLAG = 128
            //ENUM_FLAG = 256
            //AUTO_INCREMENT_FLAG = 512
            //TIMESTAMP_FLAG = 1024
            //SET_FLAG = 2048
            //NUM_FLAG = 32768
            //PART_KEY_FLAG = 16384
            //GROUP_FLAG = 32768
            //UNIQUE_FLAG = 65536
            $mCurrTableName = "`{$mMetaObject->orgtable}`";
            if (($this->FTableName != '``') && ($this->FTableName == $mCurrTableName)) {
                if (($mMetaObject->flags & MYSQLI_PRI_KEY_FLAG) == MYSQLI_PRI_KEY_FLAG) {
                    $this->FTableName = $mCurrTableName;
                    $this->FPrimaryKeys[$mMetaObject->name] = "`{$mMetaObject->orgname}`=?";
                }
            }
            else {
                $this->FTableName = '';
                $this->FPrimaryKeys = array ();
                break;
            }
        }

        foreach ($Meta as $mMetaObject) {
            $this->FRowMeta[$mMetaObject->name] = TMysqlValueTypeMapper::MapType($mMetaObject->type, $mMetaObject->length);
            $this->FColumnNames[$mMetaObject->name] = $mMetaObject->orgname;
        }
    }

    /**
     * descHere
     * @return	TConcurrencyType
     */
    public function getConcurrencyType() {
        return $this->FConcurrencyType;
    }

    /**
     * descHere
     * @return	THoldability
     */
    public function getHoldability() {
        return $this->FResultSet->getStatement()->getConnection()->getHoldability();
    }

    /**
     * descHere
     * @return	IResultSet
     */
    public function getResultSet() {
        return $this->FResultSet;
    }

    /**
     * descHere
     * @param	string	$offset
     * @return	boolean
     */
    public final function offsetExists($offset) {
        TType::String($offset);

        return array_key_exists($offset, $this->FRowMeta);
    }

    /**
     * descHere
     * @param	string	$offset
     * @param	IParam	$value <T: ?>
     */
    public final function offsetSet($offset, $value) {
        //TType::String($offset);
        TType::Object($value, 'IParam');
        if (!$this->offsetExists($offset)) {
            throw new EInvalidColumnName();
        }

        $this->EnsureUpdatable();

        if ($value == null) {
            $this->FPendingUpdateRow["`{$this->FColumnNames[$offset]}`"] = null;
        }
        else {
            $this->FPendingUpdateRow["`{$this->FColumnNames[$offset]}`"] = $value->getValue();
        }
    }

    /**
     * descHere
     * @return	boolean
     */
    public function getWasUpdated() {
        $this->EnsureUpdatable();
        return $this->FWasUpdated;
    }

    /**
     * descHere
     * @param	string	$offset
     */
    public final function offsetUnset($offset) {
        //TType::String($offset);
        if (!$this->offsetExists($offset)) {
            throw new ENoSuchElement();
        }

        $this->offsetSet($offset, null);
    }

    /**
     * descHere
     */
    public function UndoUpdates() {
        $this->EnsureUpdatable();

        $this->FPendingUpdateRow = array ();
    }

    /**
     * descHere
     */
    public function Update() {
        $this->EnsureUpdatable();

        if (count($this->FPendingUpdateRow) == 0) {
            throw new ENothingToUpdate();
        }

        $this->DoUpdate();

        $this->FResultSet->FetchRelative(0);
        $this->FPendingUpdateRow = array ();
        $this->FWasUpdated = true;
    }
}

/**
 *
 * Enter description here ...
 * @author ExSystem
 */
final class TMysqlRow extends TAbstractMysqlRow implements IRow {
    /**
     *
     * Enter description here ...
     * @var	boolean
     */
    private $FWasDeleted = false;
    /**
     *
     * Enter description here ...
     * @var	array
     */
    private $FCurrentRow = array ();

    /**
     *
     * Enter description here ...
     */
    protected function EnsureUpdatable() {
        if ($this->FWasDeleted) {
            throw new ERowHasBeenDeleted();
        }
        parent::EnsureUpdatable();
    }

    /**
     *
     * Enter description here ...
     */
    protected function DoUpdate() {
        $mStatement = $this->FResultSet->getStatement();
        TType::Object($mStatement, 'TMysqlStatement');

        $mSetSpinnet = join('=?, ', array_keys($this->FPendingUpdateRow));
        $mSetSpinnet .= '=?';
        $mWhereSpinnet = join(' AND ', $this->FPrimaryKeys);
        $mStatement->Execute("PREPARE sUpd FROM 'UPDATE {$this->FTableName} SET {$mSetSpinnet} WHERE {$mWhereSpinnet}'");

        $mCount = -1;
        $mParams = array ();
        foreach ($this->FPendingUpdateRow as $mCol => &$mData) {
            ++$mCount;
            $mParams[] = "@q{$mCount}";
            $mValue = $this->FMysqli->real_escape_string($mData);
            $mStatement->Execute("SET @q{$mCount}='{$mValue}'");
        }

        $mCount = -1;
        foreach ($this->FPrimaryKeys as $mKeyName => &$mDummy) {
            ++$mCount;
            $mParams[] = "@p{$mCount}";

            $mValue = $this->FMysqli->real_escape_string($this->FCurrentRow[$mKeyName]);
            $mStatement->Execute("SET @p{$mCount}='{$mValue}'");
        }
        $mStatement->Execute("EXECUTE sUpd USING " . join(',', $mParams));
    }

    /**
     *
     * Enter description here ...
     * @param	TAbstractMysqlResultSet	$ResultSet
     * @param	TConcurrencyType		$ConcurrencyType
     * @param	array					$CurrentRow
     * @param	array					$Meta
     * @param	boolean					$WasDeleted
     * @param	boolean					$WasUpdated
     */
    public function __construct($ResultSet, $ConcurrencyType, &$CurrentRow, &$Meta, &$WasDeleted, &$WasUpdated) {
        TType::Object($ResultSet, 'TAbstractMysqlResultSet');
        TType::Object($ConcurrencyType, 'TConcurrencyType');

        $this->FCurrentRow = &$CurrentRow;
        $this->FWasDeleted = &$WasDeleted;

        parent::__construct($ResultSet, $ConcurrencyType, $Meta, $WasUpdated);
    }

    /**
     * descHere
     */
    public function Delete() {
        $this->EnsureUpdatable();

        $mStatement = $this->FResultSet->getStatement();
        TType::Object($mStatement, 'TMysqlStatement');

        $mSpinnet = join(' AND ', $this->FPrimaryKeys);
        $mStatement->Execute("PREPARE sDel FROM 'DELETE FROM {$this->FTableName} WHERE {$mSpinnet}'");
        $mCount = -1;
        $mParams = array ();
        foreach ($this->FPrimaryKeys as $mKeyName => &$mDummy) {
            ++$mCount;
            $mParams[] = "@p{$mCount}";
            $mValue = $this->FMysqli->real_escape_string($this->FCurrentRow[$mKeyName]);
            $mStatement->Execute("SET @p{$mCount}='{$mValue}'");
        }
        $mStatement->Execute("EXECUTE sDel USING " . join(',', $mParams));

        $this->FPendingUpdateRow = array ();
        $this->FResultSet->FetchRelative(-1);
        $this->FWasDeleted = true;
    }

    /**
     * descHere
     * @param	string	$offset
     * @return	IParam	<T: ?>
     */
    public final function offsetGet($offset) {
        //TType::String($offset);
        if (!$this->offsetExists($offset)) {
            throw new EInvalidColumnName();
        }

        $mRaw = $this->FCurrentRow[$offset];
        $mType = $this->FRowMeta[$offset];
        if ($mType == 'boolean') {
            $mRaw = ($mRaw == 1);
            $mGenericParam = array ('T' => 'boolean');
        }
        elseif (call_user_func('is_' . $mType, $mRaw)) {
            $mGenericParam = array ('T' => $mType);
        }
        elseif ($mRaw === null) {
            return null;
        }
        else {
            $mGenericParam = array ('T' => 'string');
        }

        TPrimativeParam::PrepareGeneric($mGenericParam);
        return new TPrimativeParam($mRaw);
    }

    /**
     * descHere
     * @return	boolean
     */
    public function getWasDeleted() {
        $this->EnsureUpdatable();
        return count($this->FCurrentRow) == 0;
    }
}

/**
 *
 * Enter description here ...
 * @author	许子健
 */
final class TMysqlInsertRow extends TAbstractMysqlRow implements IRow {
    /**
     *
     * Enter description here ...
     * @var	boolean
     */
    private $FWasInserted = false;

    /**
     * (non-PHPdoc)
     * @see TAbstractMysqlRow::DoExecutePrepareUpdate()
     */
    protected function DoUpdate() {
        $mStatement = $this->FResultSet->getStatement();
        TType::Object($mStatement, 'TMysqlStatement');

        $mColumnsSpinnet = join(', ', array_keys($this->FPendingUpdateRow));
        $mValuesSpinnet = str_repeat('?, ', count($this->FPendingUpdateRow) - 1) . '?';
        $mStatement->Execute("PREPARE sIns FROM 'INSERT INTO {$this->FTableName}({$mColumnsSpinnet}) VALUES({$mValuesSpinnet})'");

        $mCount = -1;
        $mParams = array ();
        foreach ($this->FPendingUpdateRow as $mCol => &$mData) {
            ++$mCount;
            $mParams[] = "@q{$mCount}";
            $mValue = $this->FMysqli->real_escape_string($mData);
            $mStatement->Execute("SET @q{$mCount}='{$mValue}'");
        }
        $mStatement->Execute("EXECUTE sIns USING " . join(',', $mParams));
    }

    /**
     *
     * Enter description here ...
     * @param	TAbstractMysqlResultSet	$ResultSet
     * @param	TConcurrencyType		$ConcurrencyType
     * @param	array					$Meta
     */
    public function __construct($ResultSet, $ConcurrencyType, &$Meta) {
        TType::Object($ResultSet, 'TAbstractMysqlResultSet');
        TType::Object($ConcurrencyType, 'TConcurrencyType');

        parent::__construct($ResultSet, $ConcurrencyType, $Meta, $this->FWasInserted);
    }

    /**
     * descHere
     * @param	string	$offset
     * @return	IParam	<T: ?>
     */
    public final function offsetGet($offset) {
        //TType::String($offset);
        if (!$this->offsetExists($offset)) {
            throw new EInvalidColumnName();
        }

        $mRaw = $this->FPendingUpdateRow["`{$this->FColumnNames[$offset]}`"];
        $mType = $this->FRowMeta[$offset];
        if ($mType == 'boolean') {
            $mRaw = ($mRaw == 1);
            $mGenericParam = array ('T' => 'boolean');
        }
        elseif (call_user_func('is_' . $mType, $mRaw)) {
            $mGenericParam = array ('T' => $mType);
        }
        elseif ($mRaw === null) {
            return null;
        }
        else {
            $mGenericParam = array ('T' => 'string');
        }

        TPrimativeParam::PrepareGeneric($mGenericParam);
        return new TPrimativeParam($mRaw);
    }

    /**
     * (non-PHPdoc)
     * @see		IRow::getWasDeleted()
     * @return	boolean
     */
    public function getWasDeleted() {
        throw new ECurrentRowIsInsertRow();
    }

    /**
     * (non-PHPdoc)
     * @see IRow::Delete()
     */
    public function Delete() {
        throw new ECurrentRowIsInsertRow();
    }
}

final class TMysqlDatabaseMetaData extends TObject implements IDatabaseMetaData { //TODO: pending...
    /**
     *
     * @var TMysqlConnection
     */
    private $FConnection = null;

    /**
     * @param	TMysqlConnection	$Connection
     */
    public function __construct($Connection) {
        parent::__construct();
        TType::Object($Connection, 'TMysqlConnection');

        $this->FConnection = $Connection;
    }

    /**
     * descHere
     * @return	boolean
     */
    public function AllProceduresAreCallable() {
        return false;
    }

    /**
     * descHere
     * @return	boolean
     */
    public function AllTablesAreSelectable() {
        return false;
    }

    /**
     * descHere
     * @return	boolean
     */
    public function DataDefinitionCausesTransactionCommit() {
        return true;
    }

    /**
     * descHere
     * @return	boolean
     */
    public function DataDefinitionIgnoredInTransactions() {
        return false;
    }

    /**
     * descHere
     * @param	TResultSetType	$Type
     * @return	boolean
     */
    public function DeletesAreDetected($Type) {
        TType::Object($Type, 'TResultSetType');
        return false;
    }

    /**
     * descHere
     * @return	boolean
     */
    public function DoesMaxRowSizeIncludeBlobs() {
        return true;
    }

    /**
     * descHere
     * @param	TPrimativeParam $Catalog <T: string>
     * @param	TPrimativeParam $SchemaPattern <T: string>
     * @param	string	$TypeNamePattern
     * @param	string	$AttributeNamePattern
     * @return	IResultSet
     */
    public function GetAttributes($Catalog, $SchemaPattern, $TypeNamePattern, $AttributeNamePattern) {
        throw new ENotImplemented(); //TODO: todo
    }

    /**
     * descHere
     * @param	TPrimativeParam	$Catalog <T: string>
     * @param	TPrimativeParam	$Schema <T: string>
     * @param	string	$Table
     * @param	TBestRowIdentifierScope	$Scope
     * @param	boolean	$Nullable
     * @return	IResultSet
     */
    public function GetBestRowIdentifier($Catalog, $Schema, $Table, $Scope, $Nullable) {
        throw new ENotImplemented(); //TODO: todo
    }

    /**
     * descHere
     * @return	IResultSet
     */
    public function getCatalogs() {
        throw new ENotImplemented(); //TODO: todo
    }

    /**
     * descHere
     * @return	string
     */
    public function getCatalogTerm() {
        throw new EUnsupportedDbFeature(TAbstractPdoConnection::CCatalogUnsupported);
    }

    /**
     * descHere
     * @param	TPrimativeParam	$Catalog <T: string>
     * @param	TPrimativeParam	$Schema <T: string>
     * @param	string	$Table
     * @param	string	$ColumnNamePattern
     * @return	IResultSet
     */
    public function GetColumnPrivileges($Catalog, $Schema, $Table, $ColumnNamePattern) {
    }

    /**
     * descHere
     * @param	TPrimativeParam	$Catalog <T: string>
     * @param	TPrimativeParam	$SchemaPattern <T: string>
     * @param	string	$TableNamePattern
     * @param	string	$ColumnNamePattern
     * @return	IResultSet
     */
    public function GetColumns($Catalog, $SchemaPattern, $TableNamePattern, $ColumnNamePattern) {
    }

    /**
     * descHere
     * @return	IConnection
     */
    public function getConnection() {
    }

    /**
     * descHere
     * @param	TPrimativeParam	$PrimaryCatalog <T: string>
     * @param	TPrimativeParam	$PrimarySchema <T: string>
     * @param	string	$PrimaryTable
     * @param	TPrimativeParam	$ForeignCatalog <T: string>
     * @param	TPrimativeParam	$ForeignSchema <T: string>
     * @param	string	$ForeignTable
     * @return	IResultSet
     */
    public function GetCrossReference($PrimaryCatalog, $PrimarySchema, $PrimaryTable, $ForeignCatalog, $ForeignSchema, $ForeignTable) {
    }

    /**
     * descHere
     * @return	TVersion
     */
    public function getDatabaseVersion() {
    }

    /**
     * descHere
     * @return	string[]
     */
    public function getDateTimeFunctions() {
    }

    /**
     * descHere
     * @return	string
     */
    public function getDbmsName() {
    }

    /**
     * descHere
     * @return	TVersion
     */
    public function getDbmsVersion() {
    }

    /**
     * descHere
     * @return	TTransactionIsolationLevel
     */
    public function getDefaultTransactionIsolation() {
    }

    /**
     * descHere
     * @return	string
     */
    public function getDriverName() {
    }

    /**
     * descHere
     * @return	TVersion
     */
    public function getDriverVersion() {
    }

    /**
     * descHere
     * @param	TPrimativeParam	$Catalog <T: string>
     * @param	TPrimativeParam	$Schema <T: string>
     * @param	string	$Table
     * @return	IResultSet
     */
    public function GetExportedKeys($Catalog, $Schema, $Table) {
    }

    /**
     * descHere
     * @return	string
     */
    public function getExtraNameCharacters() {
    }

    /**
     * descHere
     * @return	string
     */
    public function getIdentifierQuoteString() {
    }

    /**
     * descHere
     * @param	TPrimativeParam	$Catalog <T: string>
     * @param	TPrimativeParam	$Schema <T: string>
     * @param	string	$Table
     * @return	IResultSet
     */
    public function GetImportedKeys($Catalog, $Schema, $Table) {
    }

    /**
     * descHere
     * @param	TPrimativeParam	$Catalog <T: string>
     * @param	TPrimativeParam	$Schema <T: string>
     * @param	string	$Table
     * @param	boolean	$Unique
     * @param	boolean	$Approximate
     * @return	IResultSet
     */
    public function GetIndexInfo($Catalog, $Schema, $Table, $Unique, $Approximate) {
    }

    /**
     * descHere
     * @return	integer
     */
    public function getMaxBinaryLiteralLength() {
    }

    /**
     * descHere
     * @return	integer
     */
    public function getMaxCatalogNameLength() {
    }

    /**
     * descHere
     * @return	integer
     */
    public function getMaxCharLiteralLength() {
    }

    /**
     * descHere
     * @return	integer
     */
    public function getMaxColumnNameLength() {
    }

    /**
     * descHere
     * @return	integer
     */
    public function getMaxColumnsInGroupBy() {
    }

    /**
     * descHere
     * @return	integer
     */
    public function getMaxColumnsInIndex() {
    }

    /**
     * descHere
     * @return	integer
     */
    public function getMaxColumnsInOrderBy() {
    }

    /**
     * descHere
     * @return	integer
     */
    public function getMaxColumnsInSelect() {
    }

    /**
     * descHere
     * @return	integer
     */
    public function getMaxColumnsInTable() {
    }

    /**
     * descHere
     * @return	integer
     */
    public function getMaxConnections() {
    }

    /**
     * descHere
     * @return	integer
     */
    public function getMaxCursorNameLength() {
    }

    /**
     * descHere
     * @return	integer
     */
    public function getMaxIndexLength() {
    }

    /**
     * descHere
     * @return	integer
     */
    public function getMaxProcedureNameLength() {
    }

    /**
     * descHere
     * @return	integer
     */
    public function getMaxRowSize() {
    }

    /**
     * descHere
     * @return	integer
     */
    public function getMaxSchemaNameLength() {
    }

    /**
     * descHere
     * @return	integer
     */
    public function getMaxStatementLength() {
    }

    /**
     * descHere
     * @return	integer
     */
    public function getMaxStatements() {
    }

    /**
     * descHere
     * @return	integer
     */
    public function getMaxTableNameLength() {
    }

    /**
     * descHere
     * @return	integer
     */
    public function getMaxTablesInSelect() {
    }

    /**
     * descHere
     * @return	integer
     */
    public function getMaxUserNameLength() {
    }

    /**
     * descHere
     * @return	string[]
     */
    public function getNumericFunctions() {
    }

    /**
     * descHere
     * @param	TPrimativeParam	$Catalog <T: string>
     * @param	TPrimativeParam	$Schema <T: string>
     * @param	string	$Table
     * @return	IResultSet
     */
    public function GetPrimaryKeys($Catalog, $Schema, $Table) {
    }

    /**
     * descHere
     * @param	TPrimativeParam	$Catalog <T: string>
     * @param	TPrimativeParam	$SchemaPattern <T: string>
     * @param	string	$ProcedureNamePattern
     * @param	string	$ColumnNamePattern
     * @return	IResultSet
     */
    public function GetProcedureColumns($Catalog, $SchemaPattern, $ProcedureNamePattern, $ColumnNamePattern) {
    }

    /**
     * descHere
     * @param	TPrimativeParam	$Catalog <T: string>
     * @param	TPrimativeParam	$SchemaPattern <T: string>
     * @param	string	$ProcedureNamePattern
     * @return	IResultSet
     */
    public function GetProcedures($Catalog, $SchemaPattern, $ProcedureNamePattern) {
    }

    /**
     * descHere
     * @return	string
     */
    public function getProcedureTerm() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function getReadOnly() {
    }

    /**
     * descHere
     * @return	THoldability
     */
    public function getResultSetHoldability() {
    }

    /**
     * descHere
     * @return	IResultSet
     */
    public function getSchemas() {
    }

    /**
     * descHere
     * @return	string
     */
    public function getSchemaTerm() {
    }

    /**
     * descHere
     * @return	string
     */
    public function getSearchStringEscape() {
    }

    /**
     * descHere
     * @return	string[]
     */
    public function getSqlKeywords() {
    }

    /**
     * descHere
     * @return	TSqlStateType
     */
    public function getSqlStateType() {
    }

    /**
     * descHere
     * @return	string[]
     */
    public function getStringFunctions() {
    }

    /**
     * descHere
     * @param	TPrimativeParam	$Catalog <T: string>
     * @param	TPrimativeParam	$SchemaPattern <T: string>
     * @param	string	$TableNameSchema
     * @return	IResultSet
     */
    public function GetSuperTables($Catalog, $SchemaPattern, $TableNameSchema) {
    }

    /**
     * descHere
     * @param	TPrimativeParam	$Catalog <T: string>
     * @param	TPrimativeParam	$SchemaPattern <T: string>
     * @param	string	$TypeNamePattern
     * @return	IResultSet
     */
    public function GetSuperTypes($Catalog, $SchemaPattern, $TypeNamePattern) {
    }

    /**
     * descHere
     * @param	TPrimativeParam	$Catalog <T: string>
     * @param	TPrimativeParam	$SchemaPattern <T: string>
     * @param	string	$TableNamePatttern
     * @return	IResultSet
     */
    public function GetTablePrivileges($Catalog, $SchemaPattern, $TableNamePatttern) {
    }

    /**
     * descHere
     * @param	TPrimativeParam	$Catalog <T: string>
     * @param	TPrimativeParam	$SchemaPattern <T: string>
     * @param	string	$TableNamePattern
     * @param	string[]	$Types
     * @return	IResultSet
     */
    public function GetTables($Catalog, $SchemaPattern, $TableNamePattern, $Types) {
    }

    /**
     * descHere
     * @return	IResultSet
     */
    public function getTableTypes() {
    }

    /**
     * descHere
     * @return	IResultSet
     */
    public function getTypeInfo() {
    }

    /**
     * descHere
     * @param	TPrimativeParam	$Catalog <T: string>
     * @param	TPrimativeParam	$SchemaPattern <T: string>
     * @param	string	$TypeNamePattern
     * @return	IResultSet
     */
    public function GetUdts($Catalog, $SchemaPattern, $TypeNamePattern) {
    }

    /**
     * descHere
     * @return	string
     */
    public function getUrl() {
    }

    /**
     * descHere
     * @return	string
     */
    public function getUserName() {
    }

    /**
     * descHere
     * @param	TPrimativeParam	$Catalog <T: string>
     * @param	TPrimativeParam	$Schema <T: string>
     * @param	string	$Table
     * @return	IResultSet
     */
    public function GetVersionColumns($Catalog, $Schema, $Table) {
    }

    /**
     * descHere
     * @param	TResultSetType	$Type
     * @return	boolean
     */
    public function InsertsAreDetected($Type) {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function LocatorsUpdateCopy() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function NullPlusNonNullIsNull() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function NullsAreSortedAtEnd() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function NullsAreSortedAtStart() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function NullsAreSortedHigh() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function NullsAreSortedLow() {
    }

    /**
     * descHere
     * @param	TResultSetType	$Type
     * @return	boolean
     */
    public function OthersDeletesAreVisible($Type) {
    }

    /**
     * descHere
     * @param	TResultSetType	$Type
     * @return	boolean
     */
    public function OthersInsertsAreVisible($Type) {
    }

    /**
     * descHere
     * @param	TResultSetType	$Type
     * @return	boolean
     */
    public function OthersUpdatesAreVisible($Type) {
    }

    /**
     * descHere
     * @param	TResultSetType	$Type
     * @return	boolean
     */
    public function OwnDeletesAreVisible($Type) {
    }

    /**
     * descHere
     * @param	TResultSetType	$Type
     * @return	boolean
     */
    public function OwnInsertsAreVisible($Type) {
    }

    /**
     * descHere
     * @param	TResultSetType	$Type
     * @return	boolean
     */
    public function OwnUpdatesAreVisible($Type) {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function StoresLowerCaseIdentifiers() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function StoresLowerCaseQuotedIndentifiers() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function StoresMixedCaseIdentifiers() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function StoresMixedCaseQuotedIndentifiers() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function StoresUpperCaseIdentifiers() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function StoresUpperCaseQuotedIndentifiers() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsAlterTableWithAddColumn() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsAlterTableWithDropColumn() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsAnsi92EntryLevelSql() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsAnsi92FullSql() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsAnsi92IntermediateSql() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsBatchUpdates() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsCatalogsInDataManipulation() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsCatalogsInIndexDefinitions() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsCatalogsInPrivilegeDefinitions() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsCatalogsInProcedureCalls() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsCatalogsInTableDefinitions() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsColumnAliasing() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsCoreSqlGrammar() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsCorrelatedSubqueriers() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsDataDefinitionAndDataManipulationTransactions() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsDataManipulationTransactionsOnly() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsDifferentTableCorrelationName() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsExpressionsInOrderBy() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsExtendedSqlGrammar() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsFullOuterJoins() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsGetGeneratedKeys() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsGroupBy() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsGroupByBeyondSelect() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsGroupByUnrelated() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsIntegrityEnhancementFacility() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsLimitedOuterJoins() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsLinkEscapeClause() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsMinimumSqlGrammar() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsMixedCaseIdentifiers() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsMixedCaseQuotedIndentifiers() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsMultipleOpenResults() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsMultipleResultSets() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsMultipleTransaction() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsNamedParameters() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsNonNullableColumns() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsOpenCursorsAcrossCommit() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsOpenCursorsAcrossRollback() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsOpenStatementsAcrossCommit() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsOpenStatementsAcrossRollback() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsOrderByUnrelated() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsOuterJoins() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsPositionedDelete() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsPositionedUpdate() {
    }

    /**
     * descHere
     * @param	TConcurrencyType	$Concurrency
     * @param	TResultSetType	$Type
     * @return	boolean
     */
    public function SupportsResultSetConcurrency($Concurrency, $Type) {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsResultSetHoldability() {
    }

    /**
     * descHere
     * @param	TResultSetType	$Type
     * @return	boolean
     */
    public function SupportsResultSetType($Type) {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsSavepoints() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsSchemaInProcedureCalls() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsSchemasInDataManipulation() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsSchemasInIndexDefinitions() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsSchemasInPrivilegeDefinitions() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsSchemasInTableDefinitions() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsSelectForUpdate() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsStatementPooling() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsStoredProcedures() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsSubqueriersInQuantifieds() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsSubqueriesInComparisons() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsSubqueriesInExists() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsSubqueriesInIns() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsTableCorrelationNames() {
    }

    /**
     * descHere
     * @param	TTransactionIsolationLevel	$Level
     * @return	boolean
     */
    public function SupportsTransactionIsolationLevel($Level) {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsTransactions() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsUnion() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsUnionAll() {
    }

    /**
     * descHere
     * @param	TResultSetType	$Type
     * @return	boolean
     */
    public function UpdatesAreDetected($Type) {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function UsesLocalFiles() {
    }

    /**
     * descHere
     * @return	string
     */
    public function UsesLocalFilesPerTable() {
    }

}