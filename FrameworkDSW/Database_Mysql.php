<?php
/**
 * Database_Mysql.php
 * @author	ExSystem
 * @version	$Id$
 * @since	separate file since reversion 17
 */

require_once 'FrameworkDSW/Database.php';
require_once 'FrameworkDSW/Containers.php';

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
            $this->$FMysqliOptions[MYSQLI_OPT_CONNECT_TIMEOUT] = (integer) $this->FProperties['ConnectTimeout'];
        }
        if ($this->FProperties->ContainsKey('LocalInfileEnabled')) {
            $this->$FMysqliOptions[MYSQLI_OPT_LOCAL_INFILE] = $this->FProperties['LocalInfileEnabled'] == 'True';
        }
        if ($this->FProperties->ContainsKey('ReadDefaultFile')) {
            $this->$FMysqliOptions[MYSQLI_READ_DEFAULT_FILE] = $this->FProperties['ReadDefaultFile'];
        }
        if ($this->FProperties->ContainsKey('ReadDefaultGroup')) {
            $this->$FMysqliOptions[MYSQLI_READ_DEFAULT_GROUP] = $this->FProperties['ReadDefaultGroup'];
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
                $this->FMysqli = new mysqli();
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
            
            return new TMysqlConnection($this, $this->FMysqli);
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
}

/**
 * TMysqlConnection
 * @author	许子健
 */
final class TMysqlConnection extends TObject implements IConnection {
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
     * @var	TMysqlDriver
     */
    private $FDriver = null;
    /**
     * 
     * Enter description here ...
     * @var	mysqli
     */
    private $FMysqli = null;
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
        if (!$Connection->FMysqli->query($QueryString)) {
            self::PushWarning($ExceptionType, $Connection->FMysqli->sqlstate, $Connection->FMysqli->errno, $Connection->FMysqli->error, $Connection);
        }
    }

    /**
     * 
     * Enter description here ...
     * @param	TMysqlDriver	$Driver
     * @param	mysqli			$Mysqli
     */
    public function __construct($Driver, $Mysqli) {
        TType::Object($Driver, 'TMysqlDriver');
        
        if ($Driver !== null && $Mysqli !== null) {
            $this->FDriver = $Driver;
            $this->FMysqli = $Mysqli;
            $this->FIsConnected = true;
        }
        else {
            $this->FIsConnected = false;
            throw new EIsNotNullable(self::CNullDriverOrMysqliObj);
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
    
     //TODO: check if params are supported first.
    //TODO: return a statement
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
    //TODO: return a statement
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
            $mQueryString = "ROLLBACK {$mName}";
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
 * TMysqlStatement
 * @author	许子健
 */
class TMysqlStatement extends TObject implements IStatement {
    /**
     * @var	TMysqlConnection
     */
    private $FConnection = null;
    /**
     * 
     * Enter description here ...
     * @var	mysqli
     */
    private $FMysqli = null;
    /**
     * 
     * Enter description here ...
     * @var	mysqli_stmt
     */
    private $FMysqliStmt = null;
    /**
     * 
     * Enter description here ...
     * @var	TResultSetType
     */
    private $FResultSetType = null;
    /**
     * 
     * Enter description here ...
     * @var	TConcurrencyType
     */
    private $FConcurrencyType = null;
    /**
     * 
     * Enter description here ...
     * @var	string
     */
    private $FCommand = '';
    /**
     * 
     * Enter description here ...
     * @var	TList <T: string>
     */
    private $FCommands = null;

    private function EnsureMysqliStmt() {
        if ($this->FMysqliStmt === null) {
            throw new EEmptyCommand();
        }
    }

    /**
     * 
     * Enter description here ...
     * @param	TMysqlConnection	$Connection
     * @param	mysqli				$Mysqli
     * @param	TResultSetType		$ResultSetType
     * @param	TConcurrencyType	$ConcurrencyType
     */
    public function __construct($Connection, $Mysqli, $ResultSetType, $ConcurrencyType) {
        TType::Object($Connection, 'TMysqlConnection');
        TType::Object($ResultSetType, 'TResultSetType');
        TType::Object($ConcurrencyType, 'TConcurrencyType');
        
        $this->FConnection = $Connection;
        if ($Mysqli instanceof mysqli) {
            throw new EInvalidParameter('$Mysqli' . EInvalidParameter::CMsg);
        }
        $this->FMysqli = $Mysqli;
        $this->FMysqliStmt = $this->FMysqli->stmt_init();
        $this->FResultSetType = $ResultSetType;
        $this->FConcurrencyType = $ConcurrencyType;
    }

    /**
     * descHere
     * @param	string	$Command
     * @return	integer
     */
    public function Execute($Command = '') {
        TType::String($Command);
        
        if ($Command != '') {
            $Command = $this->FCommand;
        }
        TMysqlConnection::EnsureQuery($this->FConnection, $Command, EExecuteFailed::ClassType());
        return $this->FMysqli->affected_rows;
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
                TMysqlConnection::EnsureQuery($this->FConnection, $mCmd, EExecuteFailed::ClassType());
                $mRows[] = $this->FMysqli->affected_rows;
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
        $this->EnsureMysqliStmt();
        $mRaw = null;
        if (!$this->FMysqliStmt->bind_result(&$mRaw)) {
            TMysqlConnection::PushWarning(EExecuteFailed::ClassType(), $this->FMysqliStmt->sqlstate, $this->FMysqliStmt->errno, $this->FMysqliStmt->error, $this->FConnection);
        }
        if (!$this->FMysqliStmt->execute()) {
            TMysqlConnection::PushWarning(EExecuteFailed::ClassType(), $this->FMysqliStmt->sqlstate, $this->FMysqliStmt->errno, $this->FMysqliStmt->error, $this->FConnection);
        }
        $mMeta = $this->FMysqliStmt->result_metadata();
        if (!$mMeta) {
            TMysqlConnection::PushWarning(EExecuteFailed::ClassType(), $this->FMysqliStmt->sqlstate, $this->FMysqliStmt->errno, $this->FMysqliStmt->error, $this->FConnection);
        }
        $mMeta = $mMeta->fetch_field();
        if (!$mMeta) {
            TMysqlConnection::PushWarning(EExecuteFailed::ClassType(), $this->FMysqliStmt->sqlstate, $this->FMysqliStmt->errno, $this->FMysqliStmt->error, $this->FConnection);
        }
        
        //Type mapping
        $mMap = array (MYSQLI_TYPE_BIT => 'integer', 
            MYSQLI_TYPE_BLOB => 'string', MYSQLI_TYPE_CHAR => 'string', 
            MYSQLI_TYPE_DATE => 'todo', MYSQLI_TYPE_DATETIME => 'todo', 
            MYSQLI_TYPE_DECIMAL => 'string', MYSQLI_TYPE_DOUBLE => 'string', 
            MYSQLI_TYPE_ENUM => 'integer', MYSQLI_TYPE_FLOAT => 'float', 
            MYSQLI_TYPE_GEOMETRY => 'todo', MYSQLI_TYPE_INT24 => 'integer', 
            MYSQLI_TYPE_INTERVAL => 'integer', MYSQLI_TYPE_LONG => 'integer', 
            MYSQLI_TYPE_LONG_BLOB => 'string', MYSQLI_TYPE_LONGLONG => 'float', 
            MYSQLI_TYPE_MEDIUM_BLOB => 'todo', MYSQLI_TYPE_NEWDATE => 'todo', 
            MYSQLI_TYPE_NEWDECIMAL => 'float', MYSQLI_TYPE_SET => 'integer', 
            MYSQLI_TYPE_SHORT => 'integer', MYSQLI_TYPE_STRING => 'string', 
            MYSQLI_TYPE_TIME => 'todo', MYSQLI_TYPE_TIMESTAMP => 'todo', 
            MYSQLI_TYPE_TINY => 'integer', MYSQLI_TYPE_TINY_BLOB => 'todo', 
            MYSQLI_TYPE_VAR_STRING => 'string', MYSQLI_TYPE_YEAR => 'todo');
        if ($mMeta->length == 1) {
            $mMap[MYSQLI_TYPE_BIT] = 'boolean';
            $mMap[MYSQLI_TYPE_TINY] = 'boolean';
        }
        //End - Type mapping
        //TODO: more mapping to do...
        

        if (call_user_func('is_' . $mMap[$mMeta->type], $mRaw) === true) {
            $mGenericParam = array ('T' => $mMap[$mMeta->type]);
        }
        elseif ($mRaw === null) {
            return null;
        }
        else {
            $mGenericParam = array ('T' => 'string');
        }
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
    }

    /**
     * descHere
     * @param	integer	$Index
     * @return	IResultSet
     */
    public function getResult($Index) {
    }

    /**
     * descHere
     * @param	TCurrentResultOption	$Options
     */
    public function NextResult($Options) {
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
    
     //TODO: ...
    }

    /**
     * descHere
     * @param	string	$Value
     */
    public function setCommand($Value) {
        TType::String($Value);
        $this->FCommand = $Value;
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

}

final class TMysqlDatabaseMetaData implements IDatabaseMetaData { //TODO: pending...
    /**
     * 
     * @var TMysqlConnection
     */
    private $FConnection = null;

    /**
     * @param	TMysqlConnection	$Connection
     */
    public function __construct($Connection) {
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