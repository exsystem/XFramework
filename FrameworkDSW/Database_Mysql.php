<?php
/**
 * \FrameworkDSW\Database\Mysql
 * @author 许子健
 * @version $Id$
 * @since    separate file since reversion 17
 */
namespace FrameworkDSW\Database\Mysql;

use FrameworkDSW\Containers\EIndexOutOfBounds;
use FrameworkDSW\Containers\ENoSuchKey;
use FrameworkDSW\Containers\IMap;
use FrameworkDSW\Containers\TLinkedList;
use FrameworkDSW\Containers\TList;
use FrameworkDSW\Containers\TMap;
use FrameworkDSW\Containers\TPair;
use FrameworkDSW\Database\ECommitFailed;
use FrameworkDSW\Database\ECreateSavepointFailed;
use FrameworkDSW\Database\ECurrentRowIsInsertRow;
use FrameworkDSW\Database\EDatabaseWarning;
use FrameworkDSW\Database\EDisconnected;
use FrameworkDSW\Database\EEmptyCommand;
use FrameworkDSW\Database\EExecuteFailed;
use FrameworkDSW\Database\EFailedToConnectDb;
use FrameworkDSW\Database\EFailedToGetDbPropertyInfo;
use FrameworkDSW\Database\EFailedToGetFetchSize;
use FrameworkDSW\Database\EFetchAsScalarFailed;
use FrameworkDSW\Database\EFetchRowFailed;
use FrameworkDSW\Database\EInsufficientProperties;
use FrameworkDSW\Database\EInvalidColumnName;
use FrameworkDSW\Database\EInvalidRowId;
use FrameworkDSW\Database\ENoMoreResultSet;
use FrameworkDSW\Database\ENothingToUpdate;
use FrameworkDSW\Database\EResultSetIsNotUpdatable;
use FrameworkDSW\Database\ERowHasBeenDeleted;
use FrameworkDSW\Database\ESetCommandFailed;
use FrameworkDSW\Database\EUnableToUpdateNonSingleTableResultSet;
use FrameworkDSW\Database\EUnsupportedDbFeature;
use FrameworkDSW\Database\ICallableStatement;
use FrameworkDSW\Database\IConnection;
use FrameworkDSW\Database\IDatabaseMetaData;
use FrameworkDSW\Database\IDatabaseWarningContext;
use FrameworkDSW\Database\IDriver;
use FrameworkDSW\Database\IPreparedStatement;
use FrameworkDSW\Database\IResultSet;
use FrameworkDSW\Database\IRow;
use FrameworkDSW\Database\IStatement;
use FrameworkDSW\Database\TBestRowIdentifierScope;
use FrameworkDSW\Database\TConcurrencyType;
use FrameworkDSW\Database\TCurrentResultOption;
use FrameworkDSW\Database\TDriverPropertyInfo;
use FrameworkDSW\Database\TFetchDirection;
use FrameworkDSW\Database\THoldability;
use FrameworkDSW\Database\TInMemoryResultSet;
use FrameworkDSW\Database\TResultSetType;
use FrameworkDSW\Database\TSavepoint;
use FrameworkDSW\Database\TTransactionIsolationLevel;
use FrameworkDSW\Framework\Framework;
use FrameworkDSW\Reflection\TClass;
use FrameworkDSW\System\EAccessViolation;
use FrameworkDSW\System\EInvalidParameter;
use FrameworkDSW\System\IInterface;
use FrameworkDSW\System\IPrimitive;
use FrameworkDSW\System\TBoolean;
use FrameworkDSW\System\TFloat;
use FrameworkDSW\System\TInteger;
use FrameworkDSW\System\TObject;
use FrameworkDSW\System\TString;
use FrameworkDSW\Utilities\TType;
use FrameworkDSW\Utilities\TVersion;

/**
 *
 * Enter description here ...
 * @author 许子健
 */
abstract class TBaseMysqlObject extends TObject {
    /**
     *
     * Enter description here ...
     * @var \FrameworkDSW\Database\Mysql\TMysqlDriver
     */
    protected $FDriver = null;
    /**
     *
     * Enter description here ...
     * @var \mysqli
     */
    protected $FMysqli = null;

    /**
     *
     * Enter description here ...
     * @param \FrameworkDSW\Database\Mysql\TMysqlDriver $Driver
     */
    public function __construct($Driver) {
        TType::Object($Driver, TMysqlDriver::class);
        parent::__construct();
        $this->FDriver = $Driver;
        $this->FMysqli = $Driver->getMysqli($this);
    }

    /**
     *
     * Enter description here ...
     * @return \FrameworkDSW\Database\Mysql\TMysqlDriver
     */
    protected function getDriver() {
        return $this->FDriver;
    }

}

/**
 * Class TMysqlDataTypeMapper
 * @package FrameworkDSW\Database\Mysql
 */
class TMysqlDataTypeMapper extends TObject {
    /**
     * @var mixed
     *
     */
    private static $FTypeMappingFromSqlTable = [];
    /**
     * @var mixed
     */
    private static $FCastMappingFromSqlTable = [];
    /**
     * @var mixed
     */
    private static $FCastMappingToSqlTable = [];

    /**
     * @param string $MysqlType
     * @return integer
     * @throws EInvalidParameter
     * @throws \FrameworkDSW\Utilities\EInvalidStringCasting
     */
    public static function MysqlTypeToMysqliType($MysqlType) {
        TType::String($MysqlType);

        switch (strtoupper($MysqlType)) {
            case 'BIT':
                return MYSQLI_TYPE_BIT;
            case 'BLOB':
                return MYSQLI_TYPE_BLOB;
            case 'CHAR':
                return MYSQLI_TYPE_CHAR;
            case 'DATE':
                return MYSQLI_TYPE_DATE;
            case 'DATETIME':
                return MYSQLI_TYPE_DATETIME;
            case 'DECIMAL':
                return MYSQLI_TYPE_DECIMAL;
            case 'DOUBLE':
                return MYSQLI_TYPE_DOUBLE;
            case 'ENUM':
                return MYSQLI_TYPE_ENUM;
            case 'FLOAT':
                return MYSQLI_TYPE_FLOAT;
            case 'GEOMETRY':
                return MYSQLI_TYPE_GEOMETRY;
            case 'MEDIUMINT':
                return MYSQLI_TYPE_INT24;
            case 'INTERVAL':
                return MYSQLI_TYPE_INTERVAL;
            case 'INT':
                return MYSQLI_TYPE_LONG;
            case 'LONGBLOB':
                return MYSQLI_TYPE_LONG_BLOB;
            case 'BIGINT':
                return MYSQLI_TYPE_LONGLONG;
            case 'MEDIUMBLOB':
                return MYSQLI_TYPE_MEDIUM_BLOB;
            case 'DATE':
                return MYSQLI_TYPE_NEWDATE;
            case 'DECIMAL':
                return MYSQLI_TYPE_NEWDECIMAL;
            case 'SET':
                return MYSQLI_TYPE_SET;
            case 'SMALLINT':
                return MYSQLI_TYPE_SHORT;
            case 'STRING':
                return MYSQLI_TYPE_STRING;
            case 'TIME':
                return MYSQLI_TYPE_TIME;
            case 'TIMESTAMP':
                return MYSQLI_TYPE_TIMESTAMP;
            case 'TINYINT':
                return MYSQLI_TYPE_TINY;
            case 'TINYBLOB':
                return MYSQLI_TYPE_TINY_BLOB;
            case 'VARCHAR':
                return MYSQLI_TYPE_VAR_STRING;
            case 'YEAR':
                return MYSQLI_TYPE_YEAR;
            default:
                throw new EInvalidParameter(sprintf('No such MySQL Type: %s.', $MysqlType));
        }
    }

    /**
     * @param integer $MysqliType
     * @param integer $FieldLength
     * @return \FrameworkDSW\Reflection\TClass <T: ?>
     */
    public static function MapFromSqlType($MysqliType, $FieldLength) {
        TType::Int($MysqliType);
        TType::Int($FieldLength);

        if ($FieldLength == 1 && ($MysqliType == MYSQLI_TYPE_BIT || $MysqliType == MYSQLI_TYPE_TINY)) {
            return Framework::Type(TBoolean::class);
        }

        if (count(self::$FTypeMappingFromSqlTable) == 0) {
            self::$FTypeMappingFromSqlTable = [
                MYSQLI_TYPE_BIT         => Framework::Type(TInteger::class),
                MYSQLI_TYPE_BLOB        => Framework::Type(TString::class),
                MYSQLI_TYPE_CHAR        => Framework::Type(TInteger::class),
                MYSQLI_TYPE_DATE        => Framework::Type(TString::class),
                MYSQLI_TYPE_DATETIME    => Framework::Type(TString::class), //TODO: date,datetime->string
                MYSQLI_TYPE_DECIMAL     => Framework::Type(TString::class),
                MYSQLI_TYPE_DOUBLE      => Framework::Type(TString::class),
                MYSQLI_TYPE_ENUM        => Framework::Type(TInteger::class),
                MYSQLI_TYPE_FLOAT       => Framework::Type(TFloat::class),
                MYSQLI_TYPE_GEOMETRY    => 'todo',
                MYSQLI_TYPE_INT24       => Framework::Type(TInteger::class),
                MYSQLI_TYPE_INTERVAL    => Framework::Type(TInteger::class),
                MYSQLI_TYPE_LONG        => Framework::Type(TInteger::class),
                MYSQLI_TYPE_LONG_BLOB   => Framework::Type(TInteger::class),
                MYSQLI_TYPE_LONGLONG    => Framework::Type(TInteger::class),
                MYSQLI_TYPE_MEDIUM_BLOB => Framework::Type(TString::class),
                MYSQLI_TYPE_NEWDATE     => Framework::Type(TString::class), //TODO: blob->string? newdate->string?
                MYSQLI_TYPE_NEWDECIMAL  => Framework::Type(TFloat::class),
                MYSQLI_TYPE_SET         => Framework::Type(TInteger::class),
                MYSQLI_TYPE_SHORT       => Framework::Type(TInteger::class),
                MYSQLI_TYPE_STRING      => Framework::Type(TString::class),
                MYSQLI_TYPE_TIME        => Framework::Type(TInteger::class),
                MYSQLI_TYPE_TIMESTAMP   => Framework::Type(TInteger::class), //TODO: time, timestamp->integer?
                MYSQLI_TYPE_TINY        => Framework::Type(TInteger::class),
                MYSQLI_TYPE_TINY_BLOB   => Framework::Type(TString::class), //TODO: blob->string?
                MYSQLI_TYPE_VAR_STRING  => Framework::Type(TString::class),
                MYSQLI_TYPE_YEAR        => Framework::Type(TInteger::class)
            ]; //TODO: year->integer?
        }

        //TODO: more mapping to do...
        return self::$FTypeMappingFromSqlTable[$MysqliType];
    }

    /**
     * @param \FrameworkDSW\Reflection\TClass $Type <T: ?>
     * @param mixed $Value
     * @return \FrameworkDSW\System\IInterface
     */
    public static function CastFromSqlValue($Type, $Value) {
        TType::Object($Type, [TClass::class => ['T' => null]]);

        if ($Value === null) {
            return null;
        }

        if (count(self::$FCastMappingFromSqlTable) == 0) {
            self::$FCastMappingFromSqlTable = [
                TBoolean::ClassType()->getSimpleName() => 'GetBoolean',
                TInteger::ClassType()->getSimpleName() => 'GetInteger',
                TFloat::ClassType()->getSimpleName()   => 'GetFloat',
                TString::ClassType()->getSimpleName()  => 'GetString'
            ];
        }
        $mMethod = self::$FCastMappingFromSqlTable[$Type->getSimpleName()];

        return self::$mMethod($Value);
    }

    /**
     * @param string $Type
     * @param \FrameworkDSW\System\IInterface $Value
     * @return mixed
     */
    public static function CastToSqlValue($Type, $Value) {
        TType::Object($Value, IInterface::class);

        if ($Value === null) {
            return null;
        }

        if (count(self::$FCastMappingToSqlTable) == 0) {
            self::$FCastMappingToSqlTable = [
                'string' => ['FromPrimitive', null]
            ];
        }

        $mMethod = self::$FCastMappingToSqlTable[$Type][0];
        self::PrepareMethodGeneric(self::$FCastMappingToSqlTable[$Type][1]);

        return self::$mMethod($Value);
    }

    /**
     * @param mixed $Value
     * @return \FrameworkDSW\System\TBoolean
     */
    public static function GetBoolean($Value) {
        return new TBoolean($Value == 1);
    }

    /**
     * @param mixed $Value
     * @return \FrameworkDSW\System\TInteger
     */
    public static function GetInteger($Value) {
        return new TInteger((integer)$Value);
    }

    /**
     * @param mixed $Value
     * @return \FrameworkDSW\System\TFloat
     */
    public static function GetFloat($Value) {
        return new TFloat((float)$Value);
    }

    /**
     * @param mixed $Value
     * @return \FrameworkDSW\System\TString
     */
    public static function GetString($Value) {
        return new TString((string)$Value);
    }

    /**
     * @param \FrameworkDSW\System\IPrimitive $Value <T: ?>
     * @return string
     */
    public static function FromPrimitive($Value) {
        TType::Object($Value, [IPrimitive::class => ['T' => null]]);

        return $Value->UnboxToString();
    }
}

/**
 * TMysqlWarningContext
 * @author    许子健
 */
class TMysqlWarningContext extends TObject implements IDatabaseWarningContext {

    /**
     * @var string
     */
    private $FErrorMessage = '';
    /**
     * @var string
     */
    private $FErrorCode = '';
    /**
     * @var string
     */
    private $FSqlState = '';

    /**
     * descHere
     * @param string $SqlState
     * @param string $ErrorCode
     * @param string $ErrorMessage
     */
    public function __construct($SqlState, $ErrorCode, $ErrorMessage) {
        parent::__construct();
        TType::String($SqlState);
        TType::String($ErrorCode);
        TType::String($ErrorMessage);

        $this->FSqlState     = $SqlState;
        $this->FErrorCode    = $ErrorCode;
        $this->FErrorMessage = $ErrorMessage;
    }

    /**
     * descHere
     * @return string
     */
    public function getErrorCode() {
        return $this->FErrorCode;
    }

    /**
     * descHere
     * @return string
     */
    public function getSqlState() {
        return $this->FSqlState;
    }

    /**
     *
     * Enter description here ...
     * @return string
     */
    public function getErrorMessage() {
        return $this->FErrorMessage;
    }
}

/**
 * \FrameworkDSW\Database\Mysql\TMysqlDriver
 * @author    许子健
 */
final class TMysqlDriver extends TObject implements IDriver {

    /**
     * @var string
     */
    const CInvalidServer = 'Bad form of the server string.';

    /**
     *
     * Enter description here ...
     * @var string
     */
    private $FServer = '';

    /**
     *
     * Enter description here ...
     * @var integer
     */
    private $FPort = 3306;

    /**
     *
     * Enter description here ...
     * @var string
     */
    private $FDbName = '';

    /**
     *
     * Enter description here ...
     * @var string
     */
    private $FSocket = '';

    /**
     * @var \FrameworkDSW\Containers\TMap <K: string, V: string>
     */
    private $FProperties = null;

    /**
     *
     * Enter description here ...
     * @var array
     */
    private $FMysqliOptions = [];

    /**
     *
     * Enter description here ...
     * @var integer
     */
    private $FMysqliFlags = 0;

    /**
     *
     * Enter description here ...
     * @var \mysqli
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
            $this->FMysqliOptions[MYSQLI_OPT_CONNECT_TIMEOUT] = (integer)$this->FProperties['ConnectTimeout'];
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
    }

    /**
     * descHere
     * @param string $Url
     * @param \FrameworkDSW\Containers\IMap $Properties <K: string, V: string>
     * @param string $Url
     * @throws \FrameworkDSW\Database\EFailedToConnectDb
     * @throws \FrameworkDSW\Database\EInsufficientProperties
     * @return \FrameworkDSW\Database\IConnection
     */
    public function Connect($Url, $Properties) {
        TType::String($Url);
        TType::Object($Properties, [IMap::class => ['K' => Framework::String, 'V' => Framework::String]]);

        $this->FProperties = $Properties;
        if ($this->ValidateUrl($Url)) {
            $this->ConvertProperties();
            try {
                $this->FMysqli = new \mysqli();
                mysqli_report(MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ERROR);
                foreach ($this->FMysqliOptions as $mKey => &$mValue) {
                    $this->FMysqli->options($mKey, $mValue);
                }
                $this->FMysqli->real_connect($this->FServer, $this->FProperties['Username'], $this->FProperties['Password'], $this->FDbName, $this->FPort, $this->FSocket, $this->FMysqliFlags);
                $this->FMysqli->set_charset('utf8');
            }
            catch (ENoSuchKey $Ex) {
                throw new EInsufficientProperties(sprintf('Insufficient properties: Username and Password are required properties.'), $Ex);
            }
            catch (\mysqli_sql_exception $Ex) {
                throw new EFailedToConnectDb(sprintf('Connect database failed: failed to connect "%s" for internal mysqli exception "%s" with code "%s".', $Url, $Ex->getMessage(), $Ex->getCode()), null, $Url);
            }

            return new TMysqlConnection($this);
        }
        throw new EFailedToConnectDb(sprintf('Connect database failed: "%s" is an invalid connection.', $Url), null, $Url);
    }

    /**
     * descHere
     * @param string $Url
     * @param \FrameworkDSW\Containers\IMap $Properties <K: string, V: string>
     * @throws \FrameworkDSW\Database\EFailedToGetDbPropertyInfo
     * @return \FrameworkDSW\Database\TDriverPropertyInfo[]
     */
    public function GetPropertyInfo($Url, $Properties) {
        TType::String($Url);
        TType::Object($Properties, [IMap::class => ['K' => Framework::String, 'V' => Framework::String]]);

        if ($this->ValidateUrl($Url)) {
            $this->FProperties = $Properties;

            $mInfo = [];

            $mInfo[0]              = new TDriverPropertyInfo();
            $mInfo[0]->Choices     = [];
            $mInfo[0]->Description = 'Specify which user to connect the database.';
            $mInfo[0]->Name        = 'Username';
            $mInfo[0]->Required    = true;
            $mInfo[0]->Value       = $this->FProperties['Username'];

            $mInfo[1]              = new TDriverPropertyInfo();
            $mInfo[1]->Choices     = [];
            $mInfo[1]->Description = 'The password of the user. Use an empty string for empty password.';
            $mInfo[1]->Name        = 'Password';
            $mInfo[1]->Required    = true;
            $mInfo[1]->Value       = $this->FProperties['Password'];

            $mInfo[2]              = new TDriverPropertyInfo();
            $mInfo[2]->Choices     = [];
            $mInfo[2]->Description = 'Specifies the socket or named pipe that should be used.';
            $mInfo[2]->Name        = 'Socket';
            $mInfo[2]->Required    = false;
            if ($this->FProperties->ContainsKey('Socket')) {
                $mInfo[2]->Value = $this->FProperties['Socket'];
            }
            else {
                $mInfo[2]->Value = '';
            }

            $mInfo[3]              = new TDriverPropertyInfo();
            $mInfo[3]->Choices     = [];
            $mInfo[3]->Description = 'Connection timeout in seconds. (supported on Windows with TCP/IP since PHP 5.3.1)';
            $mInfo[3]->Name        = 'ConnectTimeout';
            $mInfo[3]->Required    = false;
            if ($this->FProperties->ContainsKey('ConnectTimeout')) {
                $mInfo[3]->Value = $this->FProperties['ConnectTimeout'];
            }
            else {
                $mInfo[3]->Value = '0';
            }
            $mInfo[3]->Value = '0';

            $mInfo[4]              = new TDriverPropertyInfo();
            $mInfo[4]->Choices     = array('True', 'False');
            $mInfo[4]->Description = 'Enable/disable use of LOAD LOCAL INFILE.';
            $mInfo[4]->Name        = 'LocalInfileEnabled';
            $mInfo[4]->Required    = false;
            if ($this->FProperties->ContainsKey('LocalInfileEnabled')) {
                $mInfo[4]->Value = $this->FProperties['LocalInfileEnabled'];
            }
            else {
                $mInfo[4]->Value = 'False';
            }

            $mInfo[5]              = new TDriverPropertyInfo();
            $mInfo[5]->Choices     = [];
            $mInfo[5]->Description = 'Read options from named option file instead of my.cnf.';
            $mInfo[5]->Name        = 'ReadDefaultFile';
            $mInfo[5]->Required    = false;
            if ($this->FProperties->ContainsKey('ReadDefaultFile')) {
                $mInfo[5]->Value = $this->FProperties['ReadDefaultFile'];
            }
            else {
                $mInfo[5]->Value = '';
            }

            $mInfo[6]              = new TDriverPropertyInfo();
            $mInfo[6]->Choices     = [];
            $mInfo[6]->Description = 'Read options from the named group from my.cnf or the file specified with ReadDefaultFile.';
            $mInfo[6]->Name        = 'ReadDefaultGroup';
            $mInfo[6]->Required    = false;
            if ($this->FProperties->ContainsKey('ReadDefaultGroup')) {
                $mInfo[6]->Value = $this->FProperties['ReadDefaultGroup'];
            }
            else {
                $mInfo[6]->Value = '';
            }

            $mInfo[7]              = new TDriverPropertyInfo();
            $mInfo[7]->Choices     = array('True', 'False');
            $mInfo[7]->Description = 'Use compression protocol.';
            $mInfo[7]->Name        = 'ClientCompress';
            $mInfo[7]->Required    = false;
            if ($this->FProperties->ContainsKey('ClientCompress')) {
                $mInfo[7]->Value = $this->FProperties['ClientCompress'];
            }
            else {
                $mInfo[7]->Value = 'False';
            }

            $mInfo[8]              = new TDriverPropertyInfo();
            $mInfo[8]->Choices     = array('True', 'False');
            $mInfo[8]->Description = 'Return number of matched rows, not the number of affected rows.';
            $mInfo[8]->Name        = 'FoundRows';
            $mInfo[8]->Required    = false;
            if ($this->FProperties->ContainsKey('FoundRows')) {
                $mInfo[8]->Value = $this->FProperties['FoundRows'];
            }
            else {
                $mInfo[8]->Value = 'False';
            }

            $mInfo[9]              = new TDriverPropertyInfo();
            $mInfo[9]->Choices     = array('True', 'False');
            $mInfo[9]->Description = 'Allow spaces after function names. Makes all function names reserved words.';
            $mInfo[9]->Name        = 'IgnoreSpace';
            $mInfo[9]->Required    = false;
            if ($this->FProperties->ContainsKey('IgnoreSpace')) {
                $mInfo[9]->Value = $this->FProperties['IgnoreSpace'];
            }
            else {
                $mInfo[9]->Value = 'False';
            }

            $mInfo[10]              = new TDriverPropertyInfo();
            $mInfo[10]->Choices     = array('True', 'False');
            $mInfo[10]->Description = 'Allow interactive_timeout seconds (instead of wait_timeout seconds) of inactivity before closing the connection.';
            $mInfo[10]->Name        = 'Interactive';
            $mInfo[10]->Required    = false;
            if ($this->FProperties->ContainsKey('Interactive')) {
                $mInfo[10]->Value = $this->FProperties['Interactive'];
            }
            else {
                $mInfo[10]->Value = 'False';
            }

            $mInfo[11]              = new TDriverPropertyInfo();
            $mInfo[11]->Choices     = array('True', 'False');
            $mInfo[11]->Description = 'Use SSL (encryption).';
            $mInfo[11]->Name        = 'Ssl';
            $mInfo[11]->Required    = false;
            if ($this->FProperties->ContainsKey('Ssl')) {
                $mInfo[11]->Value = $this->FProperties['Ssl'];
            }
            else {
                $mInfo[11]->Value = 'False';
            }

            return $mInfo;
        }
        throw new EFailedToGetDbPropertyInfo(sprintf('Get database property information failed: "%s" in an invalid URL.', $Url), null, $Url);
    }

    /**
     * descHere
     * @return \FrameworkDSW\Utilities\TVersion
     */
    public function getVersion() {
        $mVer   = new TVersion();
        $mDummy = '';
        sscanf(mysqli_get_client_info(), 'mysqlnd %d.%d.%d-dev - %s - $Revision: %d $', $mVer->MajorVersion, $mVer->MinorVersion, $mVer->Build, $mDummy, $mVer->Revision);

        return $mVer;
    }

    /**
     * descHere
     * @param string $Url
     * @return boolean
     */
    public function ValidateUrl($Url) {
        TType::String($Url);

        $mTemp = explode('://', $Url, 2);
        if (count($mTemp) != 2) {
            return false;
        }
        list($mProtocol, $mServer) = $mTemp;
        if (count($mTemp) != 2) {
            return false;
        }
        $mTemp = explode('/', $mServer, 2);
        if (count($mTemp) != 2) {
            return false;
        }
        list($mServer, $mDbName) = $mTemp;
        $mTemp = explode(':', $mServer, 2);
        switch (count($mTemp)) {
            case 2:
                list($mServer, $mPort) = $mTemp;
                break;
            case 1:
                list($mServer) = $mTemp;
                $mPort = 3306;
                break;
            default:
                return false;
                break;
        }
        if ($mProtocol == 'MySQL' && $mServer != '' && $mDbName != '') {
            $this->FServer = $mServer;
            $this->FPort   = (integer)$mPort;
            $this->FDbName = $mDbName;

            return true;
        }

        return false;
    }

    /**
     *
     * Enter description here ...
     * @param \FrameworkDSW\Database\Mysql\TBaseMysqlObject $Request
     * @return \mysqli
     */
    public function getMysqli($Request) {
        TType::Object($Request, TBaseMysqlObject::class);

        return $this->FMysqli;
    }
}

/**
 * \FrameworkDSW\Database\Mysql\TMysqlConnection
 * @author    许子健
 */
final class TMysqlConnection extends TBaseMysqlObject implements IConnection {
    /**
     * @var string
     */
    const CCatalogUnsupported = 'Catalog is not supported by MySQL driver.';
    /**
     * @var string
     */
    const CHoldabilityUnsupported = 'Holdability is not supported by MySQL driver.';
    /**
     * @var string
     */
    const CNullDriverOrMysqliObj = 'The driver or/and the mysqli object given is null.';
    /**
     * @var string
     */
    const CReadOnlyUnsupported = 'ReadOnly is not supported by MySQL driver.';

    /**
     *
     * Enter description here ...
     * @var boolean
     */
    private $FIsConnected = false;
    /**
     *
     * Enter description here ...
     * @var \FrameworkDSW\Database\EDatabaseWarning
     */
    private $FWarnings = null;
    /**
     *
     * Enter description here ...
     * @var \FrameworkDSW\Database\Mysql\TMysqlDatabaseMetaData
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
     * @param \FrameworkDSW\Database\Mysql\TMysqlConnection $Connection
     * @param string $QueryString
     * @param \FrameworkDSW\Reflection\TClass $ExceptionType <T: \FrameworkDSW\Database\EDatabaseWarning>
     */
    public static function EnsureQuery($Connection, $QueryString, $ExceptionType) {
        try {
            $Connection->FMysqli->query($QueryString);
        }
        catch (\mysqli_sql_exception $Ex) {
            self::PushMysqliExceptionWarning($ExceptionType, $Ex, $Connection);
        }
    }

    /**
     *
     * Enter description here ...
     * @param \FrameworkDSW\Database\Mysql\TMysqlDriver $Driver
     * @throws \FrameworkDSW\System\EAccessViolation
     */
    public function __construct($Driver) {
        TType::Object($Driver, TMysqlDriver::class);

        if ($Driver !== null) {
            parent::__construct($Driver);
            $this->FIsConnected = true;
        }
        else {
            $this->FIsConnected = false;
            throw new EAccessViolation(self::CNullDriverOrMysqliObj);
        }
    }

    /**
     * (non-PHPdoc)
     * @see \FrameworkDSW\System\TObject::Destroy()
     */
    public function Destroy() {
        if ($this->FIsConnected) {
            $this->ClearWarnings();
            $this->Disconnect();
        }
    }

    /**
     *
     * Enter description here ...
     * @param \FrameworkDSW\Reflection\TClass $WarningType <T: ?>
     * @param string $SqlState
     * @param string $ErrorCode
     * @param string $ErrorMessage
     * @param \FrameworkDSW\Database\Mysql\TMysqlConnection $Connection
     * @throws \FrameworkDSW\Database\EDatabaseWarning
     */
    public static function PushWarning($WarningType, $SqlState, $ErrorCode, $ErrorMessage, $Connection) {
        TType::Object($WarningType, [TClass::class => ['T' => null]]);
        TType::String($SqlState);
        TType::String($ErrorCode);
        TType::String($ErrorMessage);
        TType::Object($Connection, TMysqlConnection::class);

        /**@var EDatabaseWarning $mWarning */
        $mWarning = $WarningType->NewInstance([new TMysqlWarningContext($SqlState, $ErrorCode, $ErrorMessage)]);
        $mWarning->setNextWarning($Connection->FWarnings);
        $Connection->FWarnings = $mWarning;
        throw $mWarning;
    }

    /**
     *
     * @param \FrameworkDSW\Reflection\TClass $WarningType <T: ?>
     * @param \mysqli_sql_exception $Exception
     * @param \FrameworkDSW\Database\Mysql\TMysqlConnection $Connection
     */
    public static function PushMysqliExceptionWarning($WarningType, $Exception, $Connection) {
        TType::Object($WarningType, [TClass::class => ['T' => null]]);
        TType::Object($Connection, TMysqlConnection::class);

        $mExClassReflection = new \ReflectionProperty('mysqli_sql_exception', 'sqlstate');
        $mExClassReflection->setAccessible(true);
        self::PushWarning($WarningType, $mExClassReflection->getValue($Exception), $Exception->getCode(), $Exception->getMessage(), $Connection);
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
        try {
            $this->FMysqli->commit();
        }
        catch (\mysqli_sql_exception $Ex) {
            TClass::PrepareGeneric(['T' => ECommitFailed::class]);
            self::PushMysqliExceptionWarning(new TClass(), $Ex, $this);
        }
    }

    /**
     * descHere
     * @param string $Name
     * @return \FrameworkDSW\Database\ISavepoint
     */
    public function CreateSavepoint($Name = '') {
        TType::String($Name);

        $this->EnsureConnected();
        $mSavepoint = new TSavepoint($Name);
        TClass::PrepareGeneric(['T' => ECreateSavepointFailed::class]);
        self::EnsureQuery($this, "SAVEPOINT {$mSavepoint->getProperName()}", new TClass());

        return $mSavepoint;
    }

    /**
     * descHere
     * @param \FrameworkDSW\Database\TResultSetType $ResultSetType
     * @param \FrameworkDSW\Database\TConcurrencyType $ConcurrencyType
     * @return \FrameworkDSW\Database\IStatement
     */
    public function CreateStatement($ResultSetType, $ConcurrencyType) {
        TType::Object($ResultSetType, TResultSetType::class);
        TType::Object($ConcurrencyType, TConcurrencyType::class);

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
     * @param string $SqlStatement
     * @return integer
     */
    public function Execute($SqlStatement) {
        TType::String($SqlStatement);
        $this->EnsureConnected();

        TClass::PrepareGeneric(['T' => EExecuteFailed::class]);
        self::EnsureQuery($this, $SqlStatement, new TClass());

        return $this->FMysqli->affected_rows;
    }

    /**
     * descHere
     * @return boolean
     */
    public function getAutoCommit() {
        $this->EnsureConnected();
        try {
            $mRaw = $this->FMysqli->query('SELECT @@autocommit');
        }
        catch (\mysqli_sql_exception $Ex) {
            TClass::PrepareGeneric(['T' => EExecuteFailed::class]);
            self::PushMysqliExceptionWarning(new TClass(), $Ex, $this);
        }
        /** @noinspection PhpUndefinedVariableInspection */
        $mRaw = $mRaw->fetch_row();

        return (boolean)$mRaw[0];
    }

    /**
     * descHere
     * @return string
     */
    public function getCatalog() {
        return '';
    }

    /**
     * descHere
     * @return \FrameworkDSW\Database\THoldability
     */
    public function getHoldability() {
        $this->EnsureConnected();

        return THoldability::eCloseCursorsAtCommit(); //TODO: really?
    }

    /**
     * descHere
     * @return boolean
     */
    public function getIsConnected() {
        return $this->FIsConnected;
    }

    /**
     * descHere
     * @return \FrameworkDSW\Database\IDatabaseMetaData
     */
    public function getMetaData() {
        if ($this->FMetaData === null) {
            $this->FMetaData = new TMysqlDatabaseMetaData($this);
        }

        return $this->FMetaData;
    }

    /**
     * descHere
     * @return boolean
     */
    public function getReadOnly() {
        return false;
    }

    /**
     * descHere
     * @return \FrameworkDSW\Database\TTransactionIsolationLevel
     */
    /** @noinspection PhpInconsistentReturnPointsInspection */
    public function getTransactionIsolation() {
        try {
            $mLevel = $this->FMysqli->query('SELECT @@tx_isolation');
            $mLevel = $mLevel->fetch_row();
        }
        catch (\mysqli_sql_exception $Ex) {
            TClass::PrepareGeneric(['T' => EExecuteFailed::class]);
            self::PushMysqliExceptionWarning(new TClass(), $Ex, $this);
        }

        /** @noinspection PhpUndefinedVariableInspection */
        $mLevel = (string)$mLevel[0];

        switch ($mLevel) {
            case 'READ-UNCOMMITTED':
                return TTransactionIsolationLevel::eReadUncommitted();
            case 'READ-COMMITTED':
                return TTransactionIsolationLevel::eReadCommitted();
            case 'REPEATABLE-READ':
                return TTransactionIsolationLevel::eRepeatableRead();
            case 'SERIALIZABLE':
                return TTransactionIsolationLevel::eSerializable();
        }
    }

    /**
     * descHere
     * @return \FrameworkDSW\Database\EDatabaseWarning
     */
    public function getWarnings() {
        $this->EnsureConnected();

        return $this->FWarnings;
    }

    /**
     * descHere
     * @param \FrameworkDSW\Database\TResultSetType $ResultSetType
     * @param \FrameworkDSW\Database\TConcurrencyType $ConcurrencyType
     * @return \FrameworkDSW\Database\IPreparedStatement
     */
    public function PrepareStatement($ResultSetType, $ConcurrencyType) {
        TType::Object($ResultSetType, TResultSetType::class);
        TType::Object($ConcurrencyType, TConcurrencyType::class);

        $this->EnsureConnected();

        //TODO: check if the params are supported first.
        return new TMysqlPreparedStatement($this, $ResultSetType, $ConcurrencyType);
    }

    /**
     * descHere
     * @param \FrameworkDSW\Database\TResultSetType $ResultSetType
     * @param \FrameworkDSW\Database\TConcurrencyType $ConcurrencyType
     * @return \FrameworkDSW\Database\ICallableStatement
     */
    public function PrepareCall($ResultSetType, $ConcurrencyType) {
        TType::Object($ResultSetType, TResultSetType::class);
        TType::Object($ConcurrencyType, TConcurrencyType::class);

        $this->EnsureConnected();

        //TODO: check if the params are supported first.
        return new TMysqlCallableStatement($this, $ResultSetType, $ConcurrencyType);
    }

    /**
     * descHere
     * @param \FrameworkDSW\Database\ISavepoint $Savepoint
     */
    public function RemoveSavepoint($Savepoint) {
        /**@var $Savepoint \FrameworkDSW\Database\TSavepoint* */
        TType::Object($Savepoint, TSavepoint::class);
        $mName = $Savepoint->getProperName();
        TClass::PrepareGeneric(['T' => EExecuteFailed::class]);
        self::EnsureQuery($this, "RELEASE SAVEPOINT {$mName}", new TClass());
    }

    /**
     * descHere
     * @param \FrameworkDSW\Database\ISavepoint $Savepoint
     */
    public function Rollback($Savepoint = null) {
        /**@var $Savepoint \FrameworkDSW\Database\TSavepoint* */
        TType::Object($Savepoint, TSavepoint::class);
        $this->EnsureConnected();
        if ($Savepoint !== null) {
            $mName        = $Savepoint->getProperName();
            $mQueryString = "ROLLBACK TO {$mName}";
        }
        else {
            $mQueryString = 'ROLLBACK';
        }
        TClass::PrepareGeneric(['T' => EExecuteFailed::class]);
        self::EnsureQuery($this, $mQueryString, new TClass());
    }

    /**
     * descHere
     * @param boolean $Value
     */
    public function setAutoCommit($Value) {
        TType::Bool($Value);
        $this->EnsureConnected();
        try {
            $this->FMysqli->autocommit($Value);
        }
        catch (\mysqli_sql_exception $Ex) {
            TClass::PrepareGeneric(['T' => EExecuteFailed::class]);
            self::PushMysqliExceptionWarning(new TClass(), $Ex, $this);
        }
    }

    /**
     * descHere
     * @param string $Value
     * @throws \FrameworkDSW\Database\EUnsupportedDbFeature
     */
    public function setCatalog($Value) {
        TType::String($Value);
        throw new EUnsupportedDbFeature(self::CCatalogUnsupported);
    }

    /**
     * descHere
     * @param \FrameworkDSW\Database\THoldability $Value
     * @throws \FrameworkDSW\Database\EUnsupportedDbFeature
     */
    public function setHoldability($Value) {
        TType::Object($Value, THoldability::class);
        if ($Value == THoldability::eHoldCursorsOverCommit()) {
            throw new EUnsupportedDbFeature(self::CHoldabilityUnsupported);
        }
    }

    /**
     * descHere
     * @param boolean $Value
     * @throws \FrameworkDSW\Database\EUnsupportedDbFeature
     */
    public function setReadOnly($Value) {
        TType::Bool($Value);
        throw new EUnsupportedDbFeature(self::CReadOnlyUnsupported);
    }

    /**
     * descHere
     * @param \FrameworkDSW\Database\TTransactionIsolationLevel $Value
     * @throws \FrameworkDSW\Database\EUnsupportedDbFeature
     */
    public function setTransactionIsolation($Value) {
        TType::Object($Value, TTransactionIsolationLevel::class);
        /**@var $mSql string* */
        switch ($Value) {
            case TTransactionIsolationLevel::eReadCommitted():
                $mSql = 'SET TRANSACTION ISOLATION LEVEL READ COMMITTED';
                break;
            case TTransactionIsolationLevel::eReadUncommitted():
                $mSql = 'SET TRANSACTION ISOLATION LEVEL READ COMMITTED';
                break;
            case TTransactionIsolationLevel::eRepeatableRead():
                $mSql = 'SET TRANSACTION ISOLATION LEVEL REPEATABLE READ';
                break;
            case TTransactionIsolationLevel::eSerializable():
                $mSql = 'SET TRANSACTION ISOLATION LEVEL SERIALIZABLE';
                break;
            case TTransactionIsolationLevel::eNone():
                throw new EUnsupportedDbFeature(sprintf('Unsupported database feature: transaction isolation is not supported by this driver.'));
        }
        TClass::PrepareGeneric(['T' => EExecuteFailed::class]);
        self::EnsureQuery($this, $mSql, new TClass());
    }

    /**
     * @param string $Identifier
     * @return string
     */
    public function QuoteIdentifier($Identifier) {
        // TODO: Implement QuoteIdentifier() method.
        TType::String($Identifier);
        return $Identifier;
    }
}

/**
 *
 * Enter description here ...
 * @author    许子健
 */
abstract class TAbstractMysqlStatement extends TBaseMysqlObject {
    /**
     * @var \FrameworkDSW\Database\Mysql\TMysqlConnection
     */
    protected $FConnection = null;
    /**
     *
     * Enter description here ...
     * @var \FrameworkDSW\Database\TResultSetType
     */
    protected $FResultSetType = null;
    /**
     *
     * Enter description here ...
     * @var \FrameworkDSW\Database\TConcurrencyType
     */
    protected $FConcurrencyType = null;
    /**
     *
     * Enter description here ...
     * @var string
     */
    protected $FCommand = '';
    /**
     *
     * Enter description here ...
     * @var \FrameworkDSW\Containers\TList <T: string>
     */
    private $FCommands = null;
    /**
     *
     * Enter description here ...
     * @var \FrameworkDSW\Database\IResultSet
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
     * @return \FrameworkDSW\Database\IResultSet
     */
    abstract protected function DoQuery();

    /**
     *
     * Enter description here ...
     * @param mixed $Value
     * @param integer $Type
     * @param integer $Length
     */
    protected abstract function DoFetchAsScalar(&$Value, &$Type, &$Length);

    /**
     *
     * Enter description here ...
     * @param string $Command
     * @return integer
     */
    protected abstract function DoExecute($Command);

    /**
     *
     * Enter description here ...
     * @param \FrameworkDSW\Database\Mysql\TMysqlConnection $Connection
     * @param \FrameworkDSW\Database\TResultSetType $ResultSetType
     * @param \FrameworkDSW\Database\TConcurrencyType $ConcurrencyType
     */
    public function __construct($Connection, $ResultSetType, $ConcurrencyType) {
        parent::__construct($Connection->getDriver());

        TType::Object($Connection, TMysqlConnection::class);
        TType::Object($ResultSetType, TResultSetType::class);
        TType::Object($ConcurrencyType, TConcurrencyType::class);

        $this->FConnection      = $Connection;
        $this->FResultSetType   = $ResultSetType;
        $this->FConcurrencyType = $ConcurrencyType;
    }

    /**
     * (non-PHPdoc)
     * @see \FrameworkDSW\System\TObject::Destroy()
     */
    public function Destroy() {
        Framework::Free($this->FCommands);
    }

    /**
     * descHere
     * @param string $Command
     * @return integer
     */
    public function Execute($Command = '') {
        TType::String($Command);

        if ($Command == '') {
            $Command = $this->FCommand;
        }
        if ($Command != $this->FCommand) {
            $this->setCommand($Command);
        }

        return $this->DoExecute($Command);
    }

    /**
     * descHere
     * @throws \FrameworkDSW\Database\EEmptyCommand
     * @return integer[]
     */
    public function ExecuteCommands() {
        if ($this->FCommands === null || $this->FCommands->IsEmpty()) {
            throw new EEmptyCommand('Execute commands failed: Empty command.');
        }
        $mRows = [];
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
            TType::Object($mContext, TMysqlWarningContext::class);
            TClass::PrepareGeneric(['T' => EExecuteFailed::class]);
            /**@var TMysqlWarningContext $mContext */
            TMysqlConnection::PushWarning(new TClass(), $Ex->getSqlState(), $Ex->getErrorCode(), $mContext->getErrorMessage(), $this->FConnection);
        }

        return $mRows;
    }

    /**
     * descHere
     * @return \FrameworkDSW\System\IInterface
     */
    public function FetchAsScalar() {
        $mRaw    = null;
        $mType   = -1;
        $mLength = -1;
        $this->DoFetchAsScalar($mRaw, $mType, $mLength);

        return TMysqlDataTypeMapper::CastFromSqlValue(TMysqlDataTypeMapper::MapFromSqlType($mType, $mLength), $mRaw);
    }

    /**
     * descHere
     * @return \FrameworkDSW\Containers\IList <T: string>
     */
    public function getCommands() {
        if ($this->FCommands === null) {
            TList::PrepareGeneric(['T' => Framework::String]);
            $this->FCommands = new TList();
        }

        return $this->FCommands;
    }

    /**
     * descHere
     * @return \FrameworkDSW\Database\IConnection
     */
    public function getConnection() {
        return $this->FConnection;
    }

    /**
     * descHere
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function GetCurrentResult() {
        return $this->FCurrentResultSet;
    }

    /**
     * descHere
     * @param string $Command
     * @return \FrameworkDSW\Database\IResultSet
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
     * @param string $Value
     */
    public function setCommand($Value) {
        TType::String($Value);
        $this->FCommand = $Value;
        $this->DoSetCommand();
    }
}

/**
 * \FrameworkDSW\Database\Mysql\TMysqlStatement
 * @author    许子健
 */
class TMysqlStatement extends TAbstractMysqlStatement implements IStatement {
    /**
     *
     * Enter description here ...
     * @var \mysqli_stmt
     */
    protected $FMysqliStmt = null;

    /**
     *
     * Enter description here ...
     */
    protected function EnsureMysqliStmt() {
        if ($this->FMysqliStmt === null) {
            throw new EEmptyCommand('Empty command: command is not set yet.');
        }
    }

    /**
     *
     * Enter description here ...
     * @param \FrameworkDSW\Reflection\TClass $WarningType <T: ?>
     * @param \mysqli_sql_exception $Exception
     */
    protected function ResetMysqliStmt($WarningType, $Exception) {
        $this->FMysqliStmt->close();
        $this->FMysqliStmt = null;
        TMysqlConnection::PushMysqliExceptionWarning($WarningType, $Exception, $this->FConnection);
    }

    /**
     *
     * Enter description here ...
     */
    protected function DoSetCommand() {
        try {
            if ($this->FMysqliStmt === null) {
                $this->FMysqliStmt = $this->FMysqli->prepare($this->FCommand);
            }
            else {
                $this->FMysqliStmt->prepare($this->FCommand);
            }
        }
        catch (\mysqli_sql_exception $Ex) {
            TMysqlConnection::PushMysqliExceptionWarning(Framework::Type(ESetCommandFailed::class), $Ex, $this->FConnection);
        }
        switch ($this->FResultSetType) {
            case TResultSetType::eForwardOnly():
                $this->FMysqliStmt->attr_set(MYSQLI_STMT_ATTR_CURSOR_TYPE, MYSQLI_CURSOR_TYPE_READ_ONLY);
                break;
            case TResultSetType::eScrollInsensitive():
                $this->FMysqliStmt->attr_set(MYSQLI_STMT_ATTR_CURSOR_TYPE, MYSQLI_CURSOR_TYPE_NO_CURSOR);
                break;
            case TResultSetType::eScrollSensitive():
                $this->FMysqliStmt->attr_set(MYSQLI_STMT_ATTR_CURSOR_TYPE, MYSQLI_CURSOR_TYPE_READ_ONLY);
                break;
        }
    }

    /**
     *
     * Enter description here ...
     * @return \FrameworkDSW\Database\IResultSet
     */
    protected function DoQuery() {
        return new TMysqlStmtResultSet($this, $this->FMysqliStmt, $this->FConcurrencyType, $this->FResultSetType);
    }

    /**
     *
     * Enter description here ...
     * @param mixed $Value
     * @param integer $Type
     * @param integer $Length
     */
    protected function DoFetchAsScalar(&$Value, &$Type, &$Length) {
        $this->EnsureMysqliStmt();
        $Value = null;
        try {
            $this->FMysqliStmt->execute();
            $this->FMysqliStmt->bind_result($Value);
            if ($this->FMysqliStmt->fetch() == null) {
                $this->FMysqliStmt->reset(); //Prevent 'Command out of async' error for next mysqli_stmt_prepare() calling including those of other TMysqlStatement objects.
                $this->FMysqliStmt->close();
                $this->FMysqliStmt = null;
                TMysqlConnection::PushWarning(Framework::Type(EFetchAsScalarFailed::class), '', '', '', $this->FConnection);
            }
            $mMeta  = $this->FMysqliStmt->result_metadata();
            $mField = $mMeta->fetch_field();
            $Type   = $mField->type;
            $Length = $mField->length;
            $mMeta->close();
            $this->FMysqliStmt->reset(); //Prevent 'Command out of async' error for next mysqli_stmt_prepare() calling including those of other TMysqlStatement objects.
        }
        catch (\mysqli_sql_exception $Ex) {
            $this->ResetMysqliStmt(Framework::Type(EFetchAsScalarFailed::class), $Ex);
        }
    }

    /**
     *
     * Enter description here ...
     * @param string $Command
     * @return integer
     */
    protected function DoExecute($Command) {
        TClass::PrepareGeneric(['T' => EExecuteFailed::class]);
        TMysqlConnection::EnsureQuery($this->FConnection, $Command, new TClass());

        return $this->FMysqli->affected_rows;
    }

    /**
     *
     * Enter description here ...
     * @param \FrameworkDSW\Database\Mysql\TMysqlConnection $Connection
     * @param \FrameworkDSW\Database\TResultSetType $ResultSetType
     * @param \FrameworkDSW\Database\TConcurrencyType $ConcurrencyType
     */
    public function __construct($Connection, $ResultSetType, $ConcurrencyType) {
        parent::__construct($Connection, $ResultSetType, $ConcurrencyType);
        $this->FMysqliStmt = $this->FMysqli->stmt_init();
    }

    /**
     * (non-PHPdoc)
     * @see \FrameworkDSW\System\TObject::Destroy()
     */
    public function Destroy() {
        if ($this->FMysqliStmt != null) {
            $this->FMysqliStmt->close();
            $this->FMysqliStmt = null;
        }
        //TODO actually it is no need to surround this with the if. for a wired php bug only in Running mode, not in debugging.
        //mysqlistmt::close() will run again after closed.
        parent::Destroy();
    }

    /**
     * descHere
     * @param integer $Index
     * @throws \FrameworkDSW\Containers\EIndexOutOfBounds
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function getResult($Index) {
        TType::Int($Index);
        if ($Index != 0 || $this->FCurrentResultSet === null) {
            throw new EIndexOutOfBounds('Index out of bounds: Multiple result set for single statement is not supported.');
        }

        return $this->FCurrentResultSet;
    }

    /**
     * descHere
     * @param \FrameworkDSW\Database\TCurrentResultOption $Options
     */
    public function NextResult($Options) {
        TType::Object($Options, TCurrentResultOption::class);
        TClass::PrepareGeneric(['T' => ENoMoreResultSet::class]);
        $mWarningType = new TClass();
        TMysqlConnection::PushWarning($mWarningType, 0, 0, '', $this->FConnection); //TODO: provide more details about the error.
    }
}

/**
 *
 * Enter description here ...
 * @author 许子健
 */
class TMysqlPreparedStatement extends TMysqlStatement implements IPreparedStatement {
    /**
     *
     * Enter description here ...
     * @var \FrameworkDSW\Containers\TMap <K: string, V: \FrameworkDSW\System\IInterface>>
     */
    private $FParams = null;
    /**
     *
     * Enter description here ...
     * @var array
     */
    private $FRawParams = [];
    /**
     *
     * Enter description here ...
     * @var string
     */
    const CPattern = <<<'EOD'
/('.*?(?<!\\)')|(".*?(?<!\\)")|(:[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/
EOD;

    /**
     * (non-PHPdoc)
     * @see \FrameworkDSW\Database\TMysqlStatement::DoSetCommand()
     */
    protected function DoSetCommand() {
        $mChunks          = preg_split(self::CPattern, $this->FCommand, 0, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $this->FRawParams = [];
        foreach ($mChunks as &$mChunk) {
            if ($mChunk[0] == ':') {
                $this->FRawParams[] = $mChunk;
                $mChunk             = '?';
            }
        }
        $mOriginalCommand = $this->FCommand;
        $this->FCommand   = implode('', $mChunks);
        try {
            parent::DoSetCommand();
        }
        finally {
            $this->FCommand = $mOriginalCommand;
        }
    }

    /**
     *
     */
    protected function BindRawParameters() {
        if ($this->FParams !== null && $this->FParams->Size() > 0) {
            $mTypes     = '';
            $mParams    = [];
            $mParamsRef = [];
            foreach ($this->FRawParams as $mParam) {
                $mTypes .= 's';
                $mParams[] = TMysqlDataTypeMapper::CastToSqlValue('string', $this->FParams[$mParam]);
            }
            foreach ($mParams as $mIndex => &$mValue) {
                $mParamsRef[] = &$mParams[$mIndex];
            }
            array_unshift($mParamsRef, $mTypes);
            try {
                call_user_func_array([$this->FMysqliStmt, 'bind_param'], $mParamsRef);
            }
            catch (\mysqli_sql_exception $Ex) {
                TMysqlConnection::PushMysqliExceptionWarning(Framework::Type(EExecuteFailed::class), $Ex, $this->FConnection);
            }
        }
    }

    /**
     * (non-PHPdoc)
     * @see \FrameworkDSW\Database\Mysql\TMysqlStatement::DoQuery()
     * @return \FrameworkDSW\Database\IResultSet
     */
    protected function DoQuery() {
        $this->BindRawParameters();

        return new TMysqlStmtResultSet($this, $this->FMysqliStmt, $this->FConcurrencyType, $this->FResultSetType);
    }

    /**
     *
     * Enter description here ...
     * @param mixed $Value
     * @param integer $Type
     * @param integer $Length
     */
    protected function DoFetchAsScalar(&$Value, &$Type, &$Length) {
        $this->BindRawParameters();
        parent::DoFetchAsScalar($Value, $Type, $Length);
    }

    /**
     * (non-PHPdoc)
     * @see \FrameworkDSW\Database\Mysql\TMysqlStatement::DoExecute()
     * @param string $Command
     * @return integer
     */
    protected function DoExecute($Command) {
        $this->BindRawParameters();
        try {
            $this->FMysqliStmt->execute();
        }
        catch (\mysqli_sql_exception $Ex) {
            TMysqlConnection::PushMysqliExceptionWarning(Framework::Type(EExecuteFailed::class), $Ex, $this->FConnection);
        }

        return $this->FMysqliStmt->affected_rows;
    }

    /**
     * (non-PHPdoc)
     * @see \FrameworkDSW\Database\Mysql\TMysqlStatement::Destroy()
     */
    public function Destroy() {
        Framework::Free($this->FParams);
        parent::Destroy();
    }

    /**
     * (non-PHPdoc)
     * @see \FrameworkDSW\Database\IPreparedStatement::BindParam()
     * @param string $Name
     * @param \FrameworkDSW\System\IInterface $Param
     */
    public function BindParam($Name, $Param) {
        TType::Object($Param, IInterface::class);

        if ($this->FParams === null) {
            TMap::PrepareGeneric(['K' => Framework::String, 'V' => IInterface::class, 'T' => [TPair::class => ['K' => Framework::String, 'V' => IInterface::class]]]);
            $this->FParams = new TMap(true);
        }

        $this->FParams->Put($Name, $Param);
    }

    /**
     * (non-PHPdoc)
     * @see \FrameworkDSW\Database\IPreparedStatement::ClearParams()
     */
    public function ClearParams() {
        Framework::Free($this->FParams);
        $this->FRawParams = [];
    }
}

/**
 *
 * Enter description here ...
 * @author 许子健
 */
class TMysqlCallableStatement extends TAbstractMysqlStatement implements ICallableStatement {
    /**
     *
     * Enter description here ...
     * @var \FrameworkDSW\Containers\TMap <K: string, V: \FrameworkDSW\System\IInterface<T: ?>>
     */
    private $FParams = null;
    /**
     *
     * Enter description here ...
     * @var string[]
     */
    private $FCurrentSetParams = [];

    /**
     *
     * Enter description here ...
     * @var \FrameworkDSW\Containers\TLinkedList <T: \FrameworkDSW\Database\Mysql\TMysqlResultSet>
     */
    private $FResultSets = null;
    /**
     *
     * Enter description here ...
     * @var integer
     */
    private $FCurrentResultSetIndex = -1;

    /**
     *
     * Enter description here ...
     * @throws \Exception|\FrameworkDSW\Database\EExecuteFailed
     * @return \mysqli_result
     */
    /** @noinspection PhpInconsistentReturnPointsInspection */
    private function FetchRawResult() {
        foreach ($this->FCurrentSetParams as &$mParam) {
            $mParamValue = $this->FMysqli->real_escape_string(TMysqlDataTypeMapper::CastToSqlValue('string', $this->FParams[substr($mParam, 1)]));
            try {
                $this->FMysqli->query("SET @p{$mParam} = '{$mParamValue}'");
            }
            catch (\mysqli_sql_exception $Ex) {
                TMysqlConnection::PushMysqliExceptionWarning(Framework::Type(EExecuteFailed::class), $Ex, $this->FConnection);
            }
        }

        try {
            $this->FMysqli->multi_query('EXECUTE sCall');
            try {
                return $this->FMysqli->store_result();
            }
            catch (\mysqli_sql_exception $Ex) {
                TMysqlConnection::PushMysqliExceptionWarning(Framework::Type(EExecuteFailed::class), $Ex, $this->FConnection);
            }
        }
        catch (\mysqli_sql_exception $Ex) {
            try {
                TMysqlConnection::PushMysqliExceptionWarning(Framework::Type(EExecuteFailed::class), $Ex, $this->FConnection);
            }
            catch (EExecuteFailed $Ex) {
                while ($this->FMysqli->more_results() && $this->FMysqli->next_result()) {
                    ;
                }
                throw $Ex;
            }
        }
    }

    /**
     * (non-PHPdoc)
     * @see \FrameworkDSW\Database\Mysql\TMysqlStatement::DoSetCommand()
     */
    protected function DoSetCommand() {
        $this->FCurrentSetParams = [];
        $mChunks                 = preg_split(TMysqlPreparedStatement::CPattern, $this->FCommand, 0, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        foreach ($mChunks as $mIndex => &$mChunk) {
            if ($mChunk[0] == ':') {
                $mChunk[0]                 = '_';
                $this->FCurrentSetParams[] = $mChunks[$mIndex];
                $mChunk                    = "@p{$mChunk}";
            }
        }
        $mCmd = $this->FMysqli->real_escape_string(implode('', $mChunks));
        try {
            $this->FMysqli->query("PREPARE sCall FROM '{$mCmd}'");
        }
        catch (\mysqli_sql_exception $Ex) {
            TClass::PrepareGeneric(['T' => ESetCommandFailed::class]);
            TMysqlConnection::PushMysqliExceptionWarning(new TClass(), $Ex, $this->FConnection);
        }
    }

    /**
     * (non-PHPdoc)
     * @see \FrameworkDSW\Database\Mysql\TMysqlStatement::DoQuery()
     * @throws \FrameworkDSW\Database\EUnsupportedDbFeature
     * @return \FrameworkDSW\Database\IResultSet
     */
    protected function DoQuery() {
        if ($this->FResultSetType == TResultSetType::eScrollSensitive() || $this->FConcurrencyType == TConcurrencyType::eUpdatable()) {
            throw new EUnsupportedDbFeature(sprintf('Calling failed: can not scroll or update result set produced by stored procedure.'));
        }

        if ($this->FResultSets === null) {
            TLinkedList::PrepareGeneric(['T' => TMysqlResultSet::class]);
            $this->FResultSets = new TLinkedList(true);
        }
        $this->FResultSets->Clear();
        $this->FCurrentResultSet      = null;
        $this->FCurrentResultSetIndex = -1;

        $this->FResultSets->Add(new TMysqlResultSet($this, $this->FetchRawResult(), $this->FConcurrencyType, $this->FResultSetType));
        $this->FCurrentResultSetIndex = 0;
        $this->FCurrentResultSet      = $this->FResultSets[0];

        return $this->FCurrentResultSet;
    }

    /**
     *
     * Enter description here ...
     * @param mixed $Value
     * @param integer $Type
     * @param integer $Length
     */
    protected function DoFetchAsScalar(&$Value, &$Type, &$Length) {
        $mRawResult = $this->FetchRawResult();

        $mMeta   = $mRawResult->fetch_field();
        $Type    = $mMeta->type;
        $Length  = $mMeta->length;
        $mMeta   = null;
        $mRawRow = $mRawResult->fetch_row();
        $mRawResult->close();

        while ($this->FMysqli->more_results() && $this->FMysqli->next_result()) {
            ;
        }
        if ($mRawRow === null) {
            TMysqlConnection::PushWarning(Framework::Type(EFetchAsScalarFailed::class), $this->FMysqli->sqlstate, $this->FMysqli->errno, $this->FMysqli->error, $this->FConnection);
        }
        $Value = $mRawRow[0];
    }

    /**
     * (non-PHPdoc)
     * @see \FrameworkDSW\Database\Mysql\TMysqlStatement::DoExecute()
     * @param string $Command
     * @return integer
     */
    protected function DoExecute($Command) {
        $mRawResult = $this->FetchRawResult();

        $mResult = $mRawResult->num_rows;
        $mRawResult->close();
        while ($this->FMysqli->more_results() && $this->FMysqli->next_result()) {
            ;
        }

        return $mResult;
    }

    /**
     * (non-PHPdoc)
     * @see \FrameworkDSW\Database\Mysql\TAbstractMysqlStatement::Destroy()
     */
    public function Destroy() {
        Framework::Free($this->FResultSets);
        parent::Destroy();
    }

    /**
     *
     * @param string $Name
     * @return \FrameworkDSW\System\IInterface
     */
    public function GetParam($Name) {
        TType::String($Name);

        try {
            $mRawResult = $this->FMysqli->query("SELECT @p_{$Name}");
        }
        catch (\mysqli_sql_exception $Ex) {
            TClass::PrepareGeneric(['T' => EExecuteFailed::class]);
            TMysqlConnection::PushMysqliExceptionWarning(new TClass(), $Ex, $this->FConnection);
        }
        /** @noinspection PhpUndefinedVariableInspection */
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
     * @see \FrameworkDSW\Database\IPreparedStatement::BindParam()
     * @param string $Name
     * @param \FrameworkDSW\System\IInterface $Param
     */
    public function BindParam($Name, $Param) {
        TType::Object($Param, IInterface::class);

        if ($this->FParams === null) {
            TMap::PrepareGeneric(['K' => Framework::String, 'V' => IInterface::class, 'T' => [TPair::class => ['K' => Framework::String, 'V' => IInterface::class]]]);
            $this->FParams = new TMap(true);
        }

        $this->FParams->Put($Name, $Param);
    }

    /**
     * (non-PHPdoc)
     * @see \FrameworkDSW\Database\IPreparedStatement::ClearParams()
     */
    public function ClearParams() {
        Framework::Free($this->FParams);
    }

    /**
     * descHere
     * @param integer $Index
     * @throws \FrameworkDSW\Containers\EIndexOutOfBounds
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function getResult($Index) {
        TType::Int($Index);

        if ($this->FCurrentResultSetIndex == -1) {
            throw new EIndexOutOfBounds(sprintf('No result set produced.'));
        }

        if ($Index < 0 || $Index > $this->FResultSets->Size()) {
            throw new EIndexOutOfBounds(sprintf('No such result set: only %s result sets found.', $this->FResultSets->Size()));
        }

        $this->FCurrentResultSet      = $this->FResultSets[$Index];
        $this->FCurrentResultSetIndex = $Index;

        return $this->FCurrentResultSet;
    }

    /**
     * descHere
     * @param \FrameworkDSW\Database\TCurrentResultOption $Options
     * @throws \FrameworkDSW\System\EException
     */
    public function NextResult($Options) {
        TType::Object($Options, TCurrentResultOption::class);
        if (!$this->FMysqli->more_results()) {
            throw new ENoMoreResultSet(sprintf('No more result sets.'));
        }
        switch ($Options) {
            case TCurrentResultOption::eCloseAllResults():
                $this->FResultSets->Clear();
                break;
            case TCurrentResultOption::eCloseCurrentResult():
                $this->FResultSets->RemoveAt($this->FCurrentResultSetIndex);
                break;
        }

        try {
            $this->FMysqli->next_result();
        }
        catch (\mysqli_sql_exception $Ex) {
            TClass::PrepareGeneric(['T' => ENoMoreResultSet::class]);
            TMysqlConnection::PushMysqliExceptionWarning(new TClass(), $Ex, $this->FConnection);
        }

        try {
            $mRawResult = $this->FMysqli->store_result();
        }
        catch (\mysqli_sql_exception $Ex) {
            TClass::PrepareGeneric(['T' => EFetchAsScalarFailed::class]);
            TMysqlConnection::PushMysqliExceptionWarning(new TClass(), $Ex, $this->FConnection);
        }

        $this->FCurrentResultSetIndex = $this->FResultSets->Size();
        /** @noinspection PhpUndefinedVariableInspection */
        $this->FCurrentResultSet = new TMysqlResultSet($this, $mRawResult, $this->FConcurrencyType, $this->FResultSetType);
        $this->FResultSets->Add($this->FCurrentResultSet);
    }

}

/**
 * \FrameworkDSW\Database\Mysql\TAbstractMysqlResultSet
 * @author 许子健
 */
abstract class TAbstractMysqlResultSet extends TBaseMysqlObject {

    /**
     *
     * Enter description here ...
     * @var \FrameworkDSW\Database\Mysql\TAbstractMysqlStatement
     */
    protected $FStatement = null;
    /**
     *
     * Enter description here ...
     * @var \FrameworkDSW\Database\TResultSetType
     */
    protected $FResultSetType = null;
    /**
     *
     * Enter description here ...
     * @var mixed
     */
    protected $FMeta = [];
    /**
     *
     * Enter description here ...
     * data structure:
     * colName => value, ...
     * @var mixed
     */
    protected $FCurrentRow = [];
    /**
     *
     * Enter description here ...
     * @var integer
     */
    private $FCurrentRowId = -1;
    /**
     *
     * Enter description here ...
     * @var \FrameworkDSW\Database\TFetchDirection
     */
    private $FFetchDirection = null;
    /**
     *
     * Enter description here ...
     * @var \FrameworkDSW\Database\Mysql\TMysqlRow
     */
    private $FRow = null;
    /**
     *
     * Enter description here ...
     * @var \FrameworkDSW\Database\Mysql\TMysqlInsertRow
     */
    private $FInsertRow = null;
    /**
     *
     * Enter description here ...
     * @var boolean
     */
    private $FValid = false;
    /**
     *
     * Enter description here ...
     * @var boolean
     */
    private $FWasDeleted = false;
    /**
     *
     * Enter description here ...
     * @var boolean
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
     * @return integer
     */
    abstract protected function DoGetCount();

    /**
     *
     * Enter description here ...
     * @param integer $Value
     */
    abstract protected function DoSetFetchSize($Value);

    /**
     *
     * Enter description here ...
     * @param integer $RowId
     */
    abstract protected function DoSeekAndFetch($RowId);

    /**
     *
     * Enter description here ...
     * @return boolean
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
     * @param integer $RowId
     * @throws \FrameworkDSW\Database\EInvalidRowId
     */
    private function EnsureRowId($RowId) {
        if ($RowId < -1) {
            throw new EInvalidRowId(sprintf('No such row: at index %s.', $RowId));
        }
    }

    /**
     *
     * Enter description here ...
     * @param integer $RowId
     * @throws \FrameworkDSW\Database\EInvalidRowId
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
                TClass::PrepareGeneric(['T' => EExecuteFailed::class]);
                TMysqlConnection::PushWarning(new TClass(), $this->FMysqli->sqlstate, $this->FMysqli->errno, $this->FMysqli->error, $this->FStatement->getConnection());
            }
            ++$mCurrRowId;
        }
        if ($mCurrRowId !== $RowId) {
            throw new EInvalidRowId(sprintf('No such row: at index %s.', $RowId));
        }
        $this->FCurrentRowId = $RowId;
    }

    /**
     *
     * Enter description here ...
     * @param integer $RowId
     * @throws \FrameworkDSW\Database\EInvalidRowId
     */
    private function FetchTo($RowId) {
        if ($RowId == -1) {
            $this->Refresh();
        }
        else {
            switch ($this->FResultSetType) {
                case TResultSetType::eForwardOnly():
                    if ($RowId < $this->FCurrentRowId) {
                        throw new EInvalidRowId(sprintf('Row not reachable: you can not doing row scrolling back for a forward only result set.'));
                    }
                    if ($RowId > $this->FCurrentRowId) {
                        $this->FetchForward($RowId);
                    }
                    break;
                case TResultSetType::eScrollInsensitive():
                    $mCount = $this->DoGetCount();
                    if ($RowId >= $mCount) {
                        throw new EInvalidRowId(sprintf('No such row: there are only %s rows.', $mCount));
                    }
                    if ($this->FFetchDirection == TFetchDirection::eReverse()) {
                        $RowId = $mCount - $RowId - 1;
                    }
                    $this->DoSeekAndFetch($RowId);
                    $this->FCurrentRowId = $RowId;
                    break;
                case TResultSetType::eScrollSensitive():
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
     * @param \FrameworkDSW\Database\Mysql\TAbstractMysqlStatement $Statement
     * @param \FrameworkDSW\Database\TConcurrencyType $ConcurrencyType
     * @param \FrameworkDSW\Database\TResultSetType $ResultSetType
     */
    public function __construct($Statement, $ConcurrencyType, $ResultSetType) {
        $this->PrepareGeneric(['K' => Framework::Integer, 'V' => IRow::class, 'T' => IRow::class]);
        TType::Object($Statement, TAbstractMysqlStatement::class);
        TType::Object($ConcurrencyType, TConcurrencyType::class);
        TType::Object($ResultSetType, TResultSetType::class);

        parent::__construct($Statement->getConnection()->getDriver());
        $this->FStatement      = $Statement;
        $this->FResultSetType  = $ResultSetType;
        $this->FFetchDirection = TFetchDirection::eForward();

        $this->FetchMeta();

        $this->FRow = new TMysqlRow($this, $ConcurrencyType, $this->FCurrentRow, $this->FMeta, $this->FWasDeleted, $this->FWasUpdated);
        if ($ConcurrencyType == TConcurrencyType::eUpdatable()) {
            $this->FInsertRow = new TMysqlInsertRow($this, $ConcurrencyType, $this->FMeta);
        }
    }

    /**
     * (non-PHPdoc)
     * @see \FrameworkDSW\System\TObject::Destroy()
     */
    public function Destroy() {
        Framework::Free($this->FRow);
        Framework::Free($this->FInsertRow);
    }

    /**
     * descHere
     * @return \FrameworkDSW\Database\IRow
     */
    public function current() {
        return $this->FRow;
    }

    /**
     * descHere
     * @param integer $RowId
     * @return \FrameworkDSW\Database\IRow
     */
    public function FetchAbsolute($RowId) {
        TType::Int($RowId);

        $this->EnsureRowId($RowId);
        $this->FetchTo($RowId);

        return $this->FRow;
    }

    /**
     * descHere
     * @param integer $Offset
     * @return \FrameworkDSW\Database\IRow
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
     * @return integer
     */
    /** @noinspection PhpInconsistentReturnPointsInspection */
    public function getCount() {
        switch ($this->FResultSetType) {
            case TResultSetType::eForwardOnly():
                return $this->FCurrentRowId + 1;
                break;
            case TResultSetType::eScrollInsensitive():
                return $this->DoGetCount();
                break;
            case TResultSetType::eScrollSensitive():
                return $this->FCurrentRowId + 1;
                break;
        }
    }

    /**
     * descHere
     * @throws \FrameworkDSW\Database\EUnsupportedDbFeature
     * @return string
     */
    public function getCursorName() {
        throw new EUnsupportedDbFeature(sprintf('Get cursor name is not supported.'));
    }

    /**
     * descHere
     * @return \FrameworkDSW\Database\TFetchDirection
     */
    public function getFetchDirection() {
        return $this->FFetchDirection;
    }

    /**
     * (non-PHPdoc)
     * @see \FrameworkDSW\Database\IResultSet::getInsertRow()
     * @return \FrameworkDSW\Database\IRow
     */
    public function getInsertRow() {
        return $this->FInsertRow;
    }

    /**
     * descHere
     * @return boolean
     */
    public function getIsEmpty() {
        return $this->getCount() == 0;
    }

    /**
     * descHere
     * @return \FrameworkDSW\Database\IResultMetaData
     */
    public function getMetaData() {
        //TODO todo
    }

    /**
     * descHere
     * @return \FrameworkDSW\Database\TResultSetType
     */
    public function getType() {
        return $this->FResultSetType;
    }

    /**
     * descHere
     * @return \FrameworkDSW\Database\IStatement
     */
    public function getStatement() {
        return $this->FStatement;
    }

    /**
     * descHere
     * @return integer
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
     * @param integer $offset
     * @return boolean
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
     * @param integer $offset
     * @return \FrameworkDSW\Database\IRow
     */
    public final function offsetGet($offset) {
        TType::Int($offset);

        return $this->FetchAbsolute($offset);
    }

    /**
     * descHere
     * @param integer $offset
     * @param \FrameworkDSW\Database\IRow $value
     */
    public final function offsetSet($offset, $value) { //TODO problematic!!
        TType::Int($offset);
        TType::Object($value, IRow::class);
        $this->FetchAbsolute($offset);
        foreach ($value as $mColumn => $mData) {
            $this->FRow[$mColumn] = $mData;
        }
    }

    /**
     * descHere
     * @param integer $offset
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
        try {
            $this->FetchTo(0);
        }
        catch (EInvalidRowId $Ex) {
            $this->FValid = false;
        }
    }

    /**
     * descHere
     * @param \FrameworkDSW\Database\TFetchDirection $Value
     * @throws \FrameworkDSW\Database\EUnsupportedDbFeature
     * @throws \FrameworkDSW\System\EInvalidParameter
     */
    public function setFetchDirection($Value) {
        TType::Object($Value, TFetchDirection::class);
        if ($Value == TFetchDirection::eUnknown()) {
            throw new EInvalidParameter('Set fetch direction failed: Use a fetch direction except Unknown.');
        }
        if ($this->FResultSetType != TResultSetType::eScrollInsensitive() && $Value == TFetchDirection::eReverse()) {
            throw new EUnsupportedDbFeature('Set fetch direction failed: can not use reverse fetch direction for non-scroll-insensitive result set.');
        }
        $this->FFetchDirection = $Value;
    }

    /**
     * descHere
     * @param integer $Value
     * @throws \FrameworkDSW\System\EInvalidParameter
     */
    public function setFetchSize($Value) {
        TType::Int($Value);
        if ($Value < 1) {
            throw new EInvalidParameter(sprintf('Invalid fetch size: must be greater than 0, but %s received.', $Value));
        }

        $this->DoSetFetchSize($Value);
    }

    /**
     * descHere
     * @return boolean
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
 * \FrameworkDSW\Database\Mysql\TMysqlResultSet
 * @author 许子健
 */
class TMysqlResultSet extends TAbstractMysqlResultSet implements IResultSet {
    /**
     *
     * Enter description here ...
     * @var \mysqli_result
     */
    private $FMysqliResult = null;

    /**
     * (non-PHPdoc)
     * @see \FrameworkDSW\Database\Mysql\TAbstractMysqlResultSet::FetchMeta()
     */
    protected function FetchMeta() {
        $this->FMeta = $this->FMysqliResult->fetch_fields();
    }

    /**
     * descHere
     * @return boolean
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
     * @return integer
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
     * @param integer $RowId
     */
    protected function DoSeekAndFetch($RowId) {
        $this->FMysqliResult->data_seek($RowId);
        $this->FCurrentRow = $this->FMysqliResult->fetch_assoc();
    }

    /**
     * descHere
     * @param integer $Value
     * @throws \FrameworkDSW\Database\EUnsupportedDbFeature
     */
    protected function DoSetFetchSize($Value) {
        throw new EUnsupportedDbFeature('Unsupported database feature: setting result set fetch size is unsupported by MySQL.');
    }

    /**
     * (non-PHPdoc)
     * @see \FrameworkDSW\Database\IResultSet::getFetchSize()
     * @return integer
     */
    public function getFetchSize() {
        return 1;
    }

    /**
     *
     * @param \FrameworkDSW\Database\Mysql\TMysqlCallableStatement $Statement
     * @param \mysqli_result $MysqliResult
     * @param \FrameworkDSW\Database\TConcurrencyType $ConcurrencyType
     * @param \FrameworkDSW\Database\TResultSetType $ResultSetType
     * Enter description here ...
     */
    public function __construct($Statement, $MysqliResult, $ConcurrencyType, $ResultSetType) {
        TType::Object($Statement, TMysqlCallableStatement::class);
        TType::Object($ConcurrencyType, TConcurrencyType::class);
        TType::Object($ResultSetType, TResultSetType::class);

        $this->FMysqliResult = $MysqliResult;
        parent::__construct($Statement, $ConcurrencyType, $ResultSetType);
    }

    /**
     * (non-PHPdoc)
     * @see \FrameworkDSW\Database\Mysql\TAbstractMysqlResultSet::Destroy()
     */
    public function Destroy() {
        if ($this->FMysqliResult != null) {
            $this->FMysqliResult->close();
            $this->FMysqliResult = null;
        }
        parent::Destroy();
    }
}

/**
 * \FrameworkDSW\Database\Mysql\TMysqlStmtResultSet
 * @author 许子健
 */
class TMysqlStmtResultSet extends TAbstractMysqlResultSet implements IResultSet {
    /**
     *
     * @var integer
     */
    private $FInsensitiveResultSetCount = -1;
    /**
     *
     * Enter description here ...
     * @var \mysqli_stmt
     */
    private $FMysqliStmt = null;

    /**
     * (non-PHPdoc)
     * @see \FrameworkDSW\Database\Mysql\TAbstractMysqlResultSet::FetchMeta()
     */
    protected function FetchMeta() {
        try {
            $mRawMeta    = $this->FMysqliStmt->result_metadata();
            $this->FMeta = $mRawMeta->fetch_fields();
            $mRawMeta->close();
        }
        catch (\mysqli_sql_exception $Ex) {
            TClass::PrepareGeneric(['T' => EExecuteFailed::class]);
            TMysqlConnection::PushMysqliExceptionWarning(new TClass(), $Ex, $this->FStatement->getConnection());
        }
    }

    /**
     * (non-PHPdoc)
     * @see \FrameworkDSW\Database\Mysql\TAbstractMysqlResultSet::DoSetFetchSize()
     * @param integer $Value
     */
    protected function DoSetFetchSize($Value) {
        $this->FMysqliStmt->attr_set(MYSQLI_STMT_ATTR_PREFETCH_ROWS, $Value);
    }

    /**
     * (non-PHPdoc)
     * @see \FrameworkDSW\Database\Mysql\TAbstractMysqlResultSet::DoGetCount()
     * @return integer
     */
    protected function DoGetCount() {
        if ($this->FInsensitiveResultSetCount == -1) {
            $this->FInsensitiveResultSetCount = $this->FMysqliStmt->num_rows;
        }

        return $this->FInsensitiveResultSetCount;
    }

    /**
     * (non-PHPdoc)
     * @see \FrameworkDSW\Database\Mysql\TAbstractMysqlResultSet::DoReset()
     */
    protected function DoReset() {
        try {
            $this->FMysqliStmt->reset();
        }
        catch (\mysqli_sql_exception $Ex) {
            TClass::PrepareGeneric(['T' => EFetchRowFailed::class]);
            TMysqlConnection::PushMysqliExceptionWarning(new TClass(), $Ex, $this->FStatement->getConnection());
        }
        foreach ($this->FMeta as $mItem) {
            $mParams[] = &$this->FCurrentRow[$mItem->name];
        }
        try {
            $this->FMysqliStmt->execute();
            if ($this->FMysqliStmt->attr_get(MYSQLI_STMT_ATTR_CURSOR_TYPE) === MYSQLI_CURSOR_TYPE_NO_CURSOR) {
                $this->FMysqliStmt->store_result();
            }
        }
        catch (\mysqli_sql_exception $Ex) {
            TMysqlConnection::PushMysqliExceptionWarning(Framework::Type(EFetchRowFailed::class), $Ex, $this->FStatement->getConnection());
        }
    }

    /**
     * (non-PHPdoc)
     * @see \FrameworkDSW\Database\Mysql\TAbstractMysqlResultSet::DoSeekAndFetch()
     * @param integer $RowId
     */
    protected function DoSeekAndFetch($RowId) {
        $this->FMysqliStmt->data_seek($RowId);
        try {
            $this->FMysqliStmt->fetch();
        }
        catch (\mysqli_sql_exception $Ex) {
            TMysqlConnection::PushMysqliExceptionWarning(Framework::Type(EExecuteFailed::class), $Ex, $this->FStatement->getConnection());
        }
    }

    /**
     * (non-PHPdoc)
     * @see \FrameworkDSW\Database\Mysql\TAbstractMysqlResultSet::DoFetch()
     * @return boolean
     */
    protected function DoFetch() {
        return $this->FMysqliStmt->fetch();
    }

    /**
     *
     * @param \FrameworkDSW\Database\Mysql\TMysqlStatement $Statement
     * @param \mysqli_stmt $MysqliStmt
     * @param \FrameworkDSW\Database\TConcurrencyType $ConcurrencyType
     * @param \FrameworkDSW\Database\TResultSetType $ResultSetType
     * Enter description here ...
     */
    public function __construct($Statement, $MysqliStmt, $ConcurrencyType, $ResultSetType) {
        TType::Object($Statement, TMysqlStatement::class);
        TType::Object($ConcurrencyType, TConcurrencyType::class);
        TType::Object($ResultSetType, TResultSetType::class);

        $this->FMysqliStmt = $MysqliStmt;
        parent::__construct($Statement, $ConcurrencyType, $ResultSetType);

        foreach ($this->FMeta as $mItem) {
            $mParams[] = &$this->FCurrentRow[$mItem->name];
        }

        try {
            $this->FMysqliStmt->execute();
        }
        catch (\mysqli_sql_exception $Ex) {
            TMysqlConnection::PushMysqliExceptionWarning(Framework::Type(EExecuteFailed::class), $Ex, $this->FStatement->getConnection());
        }
        if ($this->FMysqliStmt->attr_get(MYSQLI_STMT_ATTR_CURSOR_TYPE) === MYSQLI_CURSOR_TYPE_NO_CURSOR) {
            //TODO: php-cgi crashed here only in SnowLeopard.
            //see https://bugs.php.net/bug.php?id=55104
            //TODO: php-cgi doesn't return NO_CURSOR in WINDOWS! MYSQLI_CURSOR_TYPE_READ_ONLY returned.
            $this->FMysqliStmt->store_result();
        }
        /** @noinspection PhpUndefinedVariableInspection */
        call_user_func_array(array($this->FMysqliStmt, 'bind_result'), $mParams);
    }

    /**
     * (non-PHPdoc)
     * @see TObject::Destroy()
     */
    public function Destroy() {
        if ($this->FMysqliStmt != null) {
            if ($this->FMysqliStmt->attr_get(MYSQLI_STMT_ATTR_CURSOR_TYPE) === MYSQLI_CURSOR_TYPE_NO_CURSOR) {
                $this->FMysqliStmt->free_result();
            }
        }
        $this->FMysqliStmt = null;

        parent::Destroy();
    }

    /**
     * (non-PHPdoc)
     * @see \FrameworkDSW\Database\IResultSet::getFetchSize()
     * @throws \FrameworkDSW\Database\EFailedToGetFetchSize
     * @return integer
     */
    public function getFetchSize() {
        try {
            return $this->FMysqliStmt->attr_get(MYSQLI_STMT_ATTR_PREFETCH_ROWS);
        }
        catch (\mysqli_sql_exception $Ex) {
            throw new EFailedToGetFetchSize(sprintf('Get fetch size failed: %s.', $Ex->getMessage()));
        }
    }
}

/**
 *
 * Enter description here ...
 * @author 许子健
 */
abstract class TAbstractMysqlRow extends TBaseMysqlObject {
    /**
     *
     * Enter description here ...
     * @var \FrameworkDSW\Database\Mysql\TAbstractMysqlResultSet
     */
    protected $FResultSet = null;
    /**
     *
     * Enter description here ...
     * @var \FrameworkDSW\Database\TConcurrencyType
     */
    private $FConcurrencyType = null;
    /**
     *
     * Enter description here ...
     * @var mixed
     */
    protected $FRowMeta = [];
    /**
     *
     * Enter description here ...
     * @var string[]
     */
    protected $FColumnNames = [];
    /**
     *
     * Enter description here ...
     * @var mixed
     */
    protected $FPendingUpdateRow = [];
    /**
     *
     * Enter description here ...
     * @var string
     */
    protected $FTableName = '';
    /**
     *
     * Enter description here ...
     * @var string[]
     */
    protected $FPrimaryKeys = [];
    /**
     *
     * Enter description here ...
     * @var boolean
     */
    protected $FWasUpdated = false;

    /**
     *
     * Enter description here ...
     */
    protected function EnsureUpdatable() {
        if ($this->FTableName == '') {
            throw new EUnableToUpdateNonSingleTableResultSet(sprintf('UPDATE result SET failed: the ROW can NOT be updated since it comes FROM a result SET which IS NOT a part of a single TABLE. CHECK IF the result SET IS a joined result SET.'));
        }
        if (/*$this->FResultSet->getType() != TResultSetType::eScrollSensitive() || */
            $this->FConcurrencyType == TConcurrencyType::eReadOnly()
        ) {
            throw new EResultSetIsNotUpdatable(sprintf('UPDATE result SET failed: scroll INSENSITIVE AND READ-only result sets are NOT updatable.'));
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
     * @param \FrameworkDSW\Database\Mysql\TAbstractMysqlResultSet $ResultSet
     * @param \FrameworkDSW\Database\TConcurrencyType $ConcurrencyType
     * @param mixed $Meta
     * @param boolean $WasUpdated
     */
    public function __construct($ResultSet, $ConcurrencyType, &$Meta, &$WasUpdated) {
        TType::Object($ResultSet, TAbstractMysqlResultSet::class);
        TType::Object($ConcurrencyType, TConcurrencyType::class);
        parent::__construct($ResultSet->getStatement()->getDriver());

        $this->FResultSet       = $ResultSet;
        $this->FConcurrencyType = $ConcurrencyType;
        $this->FWasUpdated      = &$WasUpdated;
        $this->FTableName       = "`{$Meta[0]->orgtable}`";
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
                    $this->FTableName                       = $mCurrTableName;
                    $this->FPrimaryKeys[$mMetaObject->name] = "`{$mMetaObject->orgname}`=?";
                }
            }
            else {
                $this->FTableName   = '';
                $this->FPrimaryKeys = [];
                break;
            }
        }

        foreach ($Meta as $mMetaObject) {
            $this->FRowMeta[$mMetaObject->name]     = TMysqlDataTypeMapper::MapFromSqlType($mMetaObject->type, $mMetaObject->length);
            $this->FColumnNames[$mMetaObject->name] = $mMetaObject->orgname;
        }
    }

    /**
     * descHere
     * @return \FrameworkDSW\Database\TConcurrencyType
     */
    public function getConcurrencyType() {
        return $this->FConcurrencyType;
    }

    /**
     * descHere
     * @return \FrameworkDSW\Database\THoldability
     */
    public function getHoldability() {
        return $this->FResultSet->getStatement()->getConnection()->getHoldability();
    }

    /**
     * descHere
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function getResultSet() {
        return $this->FResultSet;
    }

    /**
     * descHere
     * @param string $offset
     * @return boolean
     */
    public final function offsetExists($offset) {
        TType::String($offset);

        return array_key_exists($offset, $this->FRowMeta);
    }

    /**
     * descHere
     * @param string $offset
     * @param \FrameworkDSW\System\IInterface $value
     * @throws \FrameworkDSW\Database\EInvalidColumnName
     */
    public final function offsetSet($offset, $value) {
        //TType::String($offset);
        TType::Object($value, IInterface::class);
        if (!$this->offsetExists($offset)) {
            throw new EInvalidColumnName(sprintf('No such column: %s when setting value.', $offset));
        }

        $this->EnsureUpdatable();

        if ($value === null) {
            $this->FPendingUpdateRow["`{$this->FColumnNames[$offset]}`"] = null;
        }
        else {
            $this->FPendingUpdateRow["`{$this->FColumnNames[$offset]}`"] = $value;
        }
    }

    /**
     * descHere
     * @return boolean
     */
    public function getWasUpdated() {
        $this->EnsureUpdatable();

        return $this->FWasUpdated;
    }

    /**
     * descHere
     * @param string $offset
     * @throws \FrameworkDSW\Database\EInvalidColumnName
     */
    public final function offsetUnset($offset) {
        //TType::String($offset);
        if (!$this->offsetExists($offset)) {
            throw new EInvalidColumnName(sprintf('No such column: %s when setting value NULL.', $offset));
        }

        $this->offsetSet($offset, null);
    }

    /**
     * descHere
     */
    public function UndoUpdates() {
        $this->EnsureUpdatable();

        $this->FPendingUpdateRow = [];
    }

    /**
     * descHere
     */
    public function Update() {
        $this->EnsureUpdatable();

        if (count($this->FPendingUpdateRow) == 0) {
            throw new ENothingToUpdate(sprintf('Nothing changed.'));
        }

        $this->DoUpdate();

        $this->FResultSet->FetchRelative(0);
        $this->FPendingUpdateRow = [];
        $this->FWasUpdated       = true;
    }
}

/**
 *
 * Enter description here ...
 * @author 许子健
 */
final class TMysqlRow extends TAbstractMysqlRow implements IRow {
    /**
     *
     * Enter description here ...
     * @var boolean
     */
    private $FWasDeleted = false;
    /**
     *
     * Enter description here ...
     * @var mixed
     */
    private $FCurrentRow = [];

    /**
     *
     * Enter description here ...
     */
    protected function EnsureUpdatable() {
        if ($this->FWasDeleted) {
            throw new ERowHasBeenDeleted(sprintf('No such row: updating a deleted row.'));
        }
        parent::EnsureUpdatable();
    }

    /**
     *
     * Enter description here ...
     */
    protected function DoUpdate() {
        $mSetSpinet = join('=?, ', array_keys($this->FPendingUpdateRow));
        $mSetSpinet .= '=?';
        $mWhereSpinet = join(' AND ', $this->FPrimaryKeys);
        $this->FMysqli->query("PREPARE sUpd FROM 'UPDATE {$this->FTableName} SET {$mSetSpinet} WHERE {$mWhereSpinet}'");

        $mCount  = -1;
        $mParams = [];
        foreach ($this->FPendingUpdateRow as &$mData) {
            ++$mCount;
            $mParams[] = "@q{$mCount}";
            $mValue    = $this->FMysqli->real_escape_string(TMysqlDataTypeMapper::CastToSqlValue('string', $mData));
            $this->FMysqli->query("SET @q{$mCount}='{$mValue}'");
        }

        $mCount = -1;
        /** @noinspection PhpUnusedLocalVariableInspection */
        foreach ($this->FPrimaryKeys as $mKeyName => &$mDummy) {
            ++$mCount;
            $mParams[] = "@p{$mCount}";

            $mValue = $this->FMysqli->real_escape_string($this->FCurrentRow[$mKeyName]);
            $this->FMysqli->query("SET @p{$mCount}='{$mValue}'");
        }
        $this->FMysqli->query("EXECUTE sUpd USING " . join(',', $mParams));
    }

    /**
     *
     * Enter description here ...
     * @param \FrameworkDSW\Database\Mysql\TAbstractMysqlResultSet $ResultSet
     * @param \FrameworkDSW\Database\TConcurrencyType $ConcurrencyType
     * @param mixed $CurrentRow
     * @param mixed $Meta
     * @param boolean $WasDeleted
     * @param boolean $WasUpdated
     */
    public function __construct($ResultSet, $ConcurrencyType, &$CurrentRow, &$Meta, &$WasDeleted, &$WasUpdated) {
        TType::Object($ResultSet, TAbstractMysqlResultSet::class);
        TType::Object($ConcurrencyType, TConcurrencyType::class);

        $this->FCurrentRow = &$CurrentRow;
        $this->FWasDeleted = &$WasDeleted;

        parent::__construct($ResultSet, $ConcurrencyType, $Meta, $WasUpdated);
    }

    /**
     * descHere
     */
    public function Delete() {
        $this->EnsureUpdatable();

        $mSpinet = join(' AND ', $this->FPrimaryKeys);
        $this->FMysqli->query("PREPARE sDel FROM 'DELETE FROM {$this->FTableName} WHERE {$mSpinet}'");
        $mCount  = -1;
        $mParams = [];
        /** @noinspection PhpUnusedLocalVariableInspection */
        foreach ($this->FPrimaryKeys as $mKeyName => &$mDummy) {
            ++$mCount;
            $mParams[] = "@p{$mCount}";
            $mValue    = $this->FMysqli->real_escape_string($this->FCurrentRow[$mKeyName]);
            $this->FMysqli->query("SET @p{$mCount}='{$mValue}'");
        }
        $this->FMysqli->query("EXECUTE sDel USING " . join(',', $mParams));

        $this->FPendingUpdateRow = [];
        $this->FResultSet->FetchRelative(-1);
        $this->FWasDeleted = true;
    }

    /**
     * descHere
     * @param string $offset
     * @throws \FrameworkDSW\Database\EInvalidColumnName
     * @return \FrameworkDSW\System\IInterface
     */
    public final function offsetGet($offset) {
        //TType::String($offset);
        if (!$this->offsetExists($offset)) {
            throw new EInvalidColumnName(sprintf('No such column: %s when getting value.', $offset));
        }

        return TMysqlDataTypeMapper::CastFromSqlValue($this->FRowMeta[$offset], $this->FCurrentRow[$offset]);
    }

    /**
     * descHere
     * @return boolean
     */
    public function getWasDeleted() {
        $this->EnsureUpdatable();

        return $this->FWasDeleted; //count($this->FCurrentRow) == 0;
    }
}

/**
 *
 * Enter description here ...
 * @author  许子健
 */
final class TMysqlInsertRow extends TAbstractMysqlRow implements IRow {
    /**
     *
     * Enter description here ...
     * @var boolean
     */
    private $FWasInserted = false;

    /**
     * (non-PHPdoc)
     * @see \FrameworkDSW\Database\Mysql\TAbstractMysqlRow::DoExecutePrepareUpdate()
     */
    protected function DoUpdate() {
        $mColumnsSpinet = join(', ', array_keys($this->FPendingUpdateRow));
        $mValuesSpinet  = str_repeat('?, ', count($this->FPendingUpdateRow) - 1) . '?';
        $this->FMysqli->query("PREPARE sIns FROM 'INSERT INTO {$this->FTableName}({$mColumnsSpinet}) VALUES({$mValuesSpinet})'");
        //if using $mStatement->Execute('PREPARE ...'), you would got exception thrown as this is equivalent doing PREPARE x FROM 'PREPARE y ...' in mysql prompt tool, which is not allowed, and mysqli will not give you error description.

        $mCount  = -1;
        $mParams = [];
        foreach ($this->FPendingUpdateRow as &$mData) {
            ++$mCount;
            $mParams[] = "@q{$mCount}";
            $mValue    = $this->FMysqli->real_escape_string($mData);
            $this->FMysqli->query("SET @q{$mCount}='{$mValue}'");
        }
        $this->FMysqli->query("EXECUTE sIns USING " . join(',', $mParams));
    }

    /**
     *
     * Enter description here ...
     * @param \FrameworkDSW\Database\Mysql\TAbstractMysqlResultSet $ResultSet
     * @param \FrameworkDSW\Database\TConcurrencyType $ConcurrencyType
     * @param array $Meta
     */
    public function __construct($ResultSet, $ConcurrencyType, &$Meta) {
        TType::Object($ResultSet, TAbstractMysqlResultSet::class);
        TType::Object($ConcurrencyType, TConcurrencyType::class);

        parent::__construct($ResultSet, $ConcurrencyType, $Meta, $this->FWasInserted);
    }

    /**
     * descHere
     * @param string $offset
     * @throws \FrameworkDSW\Database\EInvalidColumnName
     * @return \FrameworkDSW\System\IInterface
     */
    public final function offsetGet($offset) {
        //TType::String($offset);
        if (!$this->offsetExists($offset)) {
            throw new EInvalidColumnName(sprintf('No such column: %s when getting value.', $offset));
        }

        return TMysqlDataTypeMapper::CastFromSqlValue($this->FRowMeta[$offset], $this->FPendingUpdateRow["`{$this->FColumnNames[$offset]}`"]);
    }

    /**
     * (non-PHPdoc)
     * @see \FrameworkDSW\Database\IRow::getWasDeleted()
     * @throws \FrameworkDSW\Database\ECurrentRowIsInsertRow
     * @return  boolean
     */
    public function getWasDeleted() {
        throw new ECurrentRowIsInsertRow(sprintf('Current row is insert row.'));
    }

    /**
     * (non-PHPdoc)
     * @see \FrameworkDSW\Database\IRow::Delete()
     */
    public function Delete() {
        throw new ECurrentRowIsInsertRow(sprintf('Current row is insert row.'));
    }
}

/**
 *
 * @author 许子健
 */
final class TMysqlDatabaseMetaData extends TObject implements IDatabaseMetaData { //TODO: pending
    /**
     * @var integer
     */
    private static $FMaxBufferSize = 65535; //TODO what is it?
    /**
     *
     * @var \FrameworkDSW\Database\Mysql\TMysqlConnection
     */
    private $FConnection = null;

    /**
     * @param \FrameworkDSW\Database\Mysql\TMysqlConnection $Connection
     */
    public function __construct($Connection) {
        parent::__construct();
        TType::Object($Connection, TMysqlConnection::class);

        $this->FConnection = $Connection;
    }

    /**
     * descHere
     * @return boolean
     */
    public function AllProceduresAreCallable() {
        return false;
    }

    /**
     * descHere
     * @return boolean
     */
    public function AllTablesAreSelectable() {
        return false;
    }

    /**
     * descHere
     * @return boolean
     */
    public function DataDefinitionCausesTransactionCommit() {
        return true;
    }

    /**
     * descHere
     * @return boolean
     */
    public function DataDefinitionIgnoredInTransactions() {
        return false;
    }

    /**
     * descHere
     * @param \FrameworkDSW\Database\TResultSetType $Type
     * @return boolean
     */
    public function DeletesAreDetected($Type) {
        TType::Object($Type, TResultSetType::class);

        return false;
    }

    /**
     * descHere
     * @return boolean
     */
    public function DoesMaxRowSizeIncludeBlobs() {
        return true;
    }

    /**
     * descHere
     * @param \FrameworkDSW\System\TString $Catalog
     * @param \FrameworkDSW\System\TString $SchemaPattern
     * @param string $TypeNamePattern
     * @param string $AttributeNamePattern
     * @throws \FrameworkDSW\System\ENotImplemented
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function GetAttributes($Catalog, $SchemaPattern, $TypeNamePattern, $AttributeNamePattern) {
        TType::Object($Catalog, TString::class);
        TType::Object($SchemaPattern, TString::class);
        TType::String($TypeNamePattern);
        TType::String($AttributeNamePattern);

        TMap::PrepareGeneric(['K' => Framework::String, 'V' => [TClass::class => ['T' => null]]]);
        $mMeta = new TMap(true);
        $mMeta->Put('TYPE_CAT', Framework::Type(TString::class));
        $mMeta->Put('TYPE_SCHEM', Framework::Type(TString::class));
        $mMeta->Put('TYPE_NAME', Framework::Type(TString::class));
        $mMeta->Put('ATTR_NAME', Framework::Type(TString::class));
        $mMeta->Put('DATA_TYPE', Framework::Type(TString::class));
        $mMeta->Put('ATTR_TYPE_NAME', Framework::Type(TString::class));
        $mMeta->Put('ATTR_SIZE', Framework::Type(TInteger::class));
        $mMeta->Put('DECIMAL_DIGITS', Framework::Type(TInteger::class));
        $mMeta->Put('NUM_PREC_RADIX', Framework::Type(TInteger::class));
        $mMeta->Put('NULLABLE', Framework::Type(TBoolean::class));
        $mMeta->Put('REMARKS', Framework::Type(TString::class));
        $mMeta->Put('ATTR_DEF', Framework::Type(TString::class));
        $mMeta->Put('SQL_DATA_TYPE', Framework::Type(TInteger::class));
        $mMeta->Put('SQL_DATETIME_SUB', Framework::Type(TInteger::class));
        $mMeta->Put('CHAR_OCTET_LENGTH', Framework::Type(TInteger::class));
        $mMeta->Put('ORDINAL_POSITION', Framework::Type(TInteger::class));
        $mMeta->Put('IS_NULLABLE', Framework::Type(TBoolean::class));
        $mMeta->Put('SCOPE_CATALOG', Framework::Type(TString::class));
        $mMeta->Put('SCOPE_SCHEMA', Framework::Type(TString::class));
        $mMeta->Put('SCOPE_TABLE', Framework::Type(TString::class));
        $mMeta->Put('SOURCE_DATA_TYPE', Framework::Type(TInteger::class));
        return new TInMemoryResultSet(null, [], $mMeta);
    }

    /**
     * descHere
     * @param \FrameworkDSW\System\TString $Catalog
     * @param \FrameworkDSW\System\TString $Schema
     * @param string $Table
     * @param \FrameworkDSW\Database\TBestRowIdentifierScope $Scope
     * @param boolean $Nullable
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function GetBestRowIdentifier($Catalog, $Schema, $Table, $Scope, $Nullable) {
        TType::String($Catalog);
        TType::String($Schema);
        TType::String($Table);
        TType::Object($Scope, TBestRowIdentifierScope::class);
        TType::Bool($Nullable);

        if ($Catalog == '') {
            $Database = '';
        }
        else {
            $Database = $this->FConnection->QuoteIdentifier($Catalog) . '.';
        }
        $Database = $this->FConnection->QuoteIdentifier($Schema);
        $Table    = $this->FConnection->QuoteIdentifier($Table);

        /** @var \FrameworkDSW\Containers\TMap[] $mData <K: string, V: \FrameworkDSW\System\IInterface> */
        $mData = [];

        TMap::PrepareGeneric(['K' => Framework::String, 'V' => [TClass::class => ['T' => null]]]);
        $mMeta = new TMap(true);
        $mMeta->Put('SCOPE', Framework::Type(TInteger::class));
        $mMeta->Put('COLUMN_NAME', Framework::Type(TString::class));
        $mMeta->Put('DATA_TYPE', Framework::Type([TClass::class => ['T' => null]]));
        $mMeta->Put('TYPE_NAME', Framework::Type(TString::class));
        $mMeta->Put('COLUMN_SIZE', Framework::Type(TInteger::class));
        $mMeta->Put('BUFFER_LENGTH', Framework::Type(TInteger::class));
        $mMeta->Put('DECIMAL_DIGITS', Framework::Type(TInteger::class));
        $mMeta->Put('PSEUDO_COLUMN', Framework::Type(TInteger::class));

        try {
            $mStatement = $this->FConnection->CreateStatement(TResultSetType::eScrollInsensitive(), TConcurrencyType::eReadOnly());
            $mResultSet = $mStatement->Query("SHOW COLUMNS FROM {$Table} FROM {$Database}");

            /** @var \FrameworkDSW\Database\IRow $mRow */
            foreach ($mResultSet as $mRow) {
                $mKeyTypeRaw = $mRow['Key'];
                if ($mKeyTypeRaw instanceof TString && $mKeyTypeRaw !== null) {
                    $mKeyType = $mKeyTypeRaw->Unbox();
                    if (stripos($mKeyType, 'PRI') === 0) {
                        TMap::PrepareGeneric(['K' => Framework::String, 'V' => IInterface::class]);
                        $mDataRow = new TMap(true);
                        $mDataRow->Put('SCOPE', TBestRowIdentifierScope::eSession());
                        $mDataRow->Put('COLUMN_NAME', $mRow['Field']);
                        /** @var TString[] $mRow */
                        $mType = $mRow['Type']->Unbox();

                        $mSize     = self::$FMaxBufferSize;
                        $mDecimals = 0;

                        if (strpos($mType, 'enum') !== false) {
                            $mToken     = strtok(strstr(strstr($mType, '('), ')', true), ',');
                            $mMaxLength = 0;

                            while ($mToken !== false) {
                                $mMaxLength = max($mMaxLength, strlen($mToken) - 2);
                                $mToken     = strtok(',');
                            }
                            $mSize     = $mMaxLength;
                            $mDecimals = 0;
                            $mType     = 'enum';
                        }
                        elseif (strpos($mType, '(') !== false) {
                            $mTemp = substr(strstr($mType, '('), 1);
                            if (strpos($mTemp, ',') !== false) {
                                list($mSize, $mDecimals) = explode(',', $mTemp, 2);
                                $mSize     = (integer)$mSize;
                                $mDecimals = (integer)$mDecimals;
                            }
                            else {
                                $mSize = (integer)strstr($mTemp, ')', true);
                            }
                            $mType = strstr($mType, '(', true);
                        }

                        $mDataRow->Put('DATA_TYPE', TMysqlDataTypeMapper::MapFromSqlType(TMysqlDataTypeMapper::MysqlTypeToMysqliType($mType), 1));
                        $mDataRow->Put('TYPE_NAME', new TString($mType));
                        $mDataRow->Put('COLUMN_SIZE', new TInteger($mSize + $mDecimals));
                        $mDataRow->Put('BUFFER_LENGTH', new TInteger($mSize + $mDecimals));
                        $mDataRow->Put('DECIMAL_DIGITS', new TInteger($mDecimals));
                        $mDataRow->Put('PSEUDO_COLUMN', new TBoolean(false));

                        $mData[] = $mDataRow;
                    }
                }
            }

            return new TInMemoryResultSet(null, $mData, $mMeta);
        }
        finally {
            Framework::Free($mStatement);
        }
    }

    /**
     * descHere
     * @throws \FrameworkDSW\System\ENotImplemented
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function getCatalogs() {
        try {
            $mStatement = $this->FConnection->CreateStatement(TResultSetType::eScrollInsensitive(), TConcurrencyType::eReadOnly());
            $mResultSet = $mStatement->Query('SHOW DATABASES');

            TMap::PrepareGeneric(['K' => Framework::String, 'V' => [TClass::class => ['T' => null]]]);
            $mMeta = new TMap(true);
            $mMeta->Put('TABLE_CAT', Framework::Type(TString::class));

            /** @var \FrameworkDSW\Containers\TMap[] $mData <K: string, V: \FrameworkDSW\System\IInterface> */
            $mData = [];

            /** @var \FrameworkDSW\Database\IRow $mRow */
            foreach ($mResultSet as $mRow) {
                TMap::PrepareGeneric(['K' => Framework::String, 'V' => IInterface::class]);
                $mItem = new TMap(true);
                $mItem->Put('TABLE_CAT', $mRow['Database']);
                $mData[] = $mItem;
            }
            return new TInMemoryResultSet(null, $mData, $mMeta);
        }
        finally {
            Framework::Free($mStatement);
        }
    }

    /**
     * descHere
     * @return string
     */
    public function getCatalogSeparator() {
        return '.';
    }

    /**
     * descHere
     * @return string
     */
    public function getCatalogTerm() {
        return 'database';
    }

    /**
     * descHere
     * @param \FrameworkDSW\System\TString $Catalog
     * @param \FrameworkDSW\System\TString $SchemaPattern
     * @param string $TableNamePattern
     * @param string $ColumnNamePattern
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function GetColumns($Catalog, $SchemaPattern, $TableNamePattern, $ColumnNamePattern) {
    }

    /**
     * descHere
     * @param \FrameworkDSW\System\TString $Catalog
     * @param \FrameworkDSW\System\TString $Schema
     * @param string $Table
     * @param string $ColumnNamePattern
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function GetColumnPrivileges($Catalog, $Schema, $Table, $ColumnNamePattern) {
    }

    /**
     * descHere
     * @return \FrameworkDSW\Database\IConnection
     */
    public function getConnection() {
        return $this->FConnection;
    }

    /**
     * descHere
     * @param \FrameworkDSW\System\TString $PrimaryCatalog
     * @param \FrameworkDSW\System\TString $PrimarySchema
     * @param string $PrimaryTable
     * @param \FrameworkDSW\System\TString $ForeignCatalog
     * @param \FrameworkDSW\System\TString $ForeignSchema
     * @param string $ForeignTable
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function GetCrossReference($PrimaryCatalog, $PrimarySchema, $PrimaryTable, $ForeignCatalog, $ForeignSchema, $ForeignTable) {
    }

    /**
     * descHere
     * @return string[]
     */
    public function getDateTimeFunctions() {
        return ['DAYOFWEEK', 'WEEKDAY', 'DAYOFMONTH', 'DAYOFYEAR', 'MONTH', 'DAYNAME', 'MONTHNAME', 'QUARTER', 'WEEK', 'YEAR', 'HOUR', 'MINUTE', 'SECOND', 'PERIOD_ADD', 'PERIOD_DIFF', 'TO_DAYS', 'FROM_DAYS', 'DATE_FORMAT', 'TIME_FORMAT', 'CURDATE', 'CURRENT_DATE', 'CURTIME', 'CURRENT_TIME', 'NOW', 'SYSDATE', 'CURRENT_TIMESTAMP', 'UNIX_TIMESTAMP', 'FROM_UNIXTIME', 'SEC_TO_TIME', 'TIME_TO_SEC'];
    }

    /**
     * descHere
     * @return string
     */
    public function getDbmsName() {
        return 'MySQL';
    }

    /**
     * descHere
     * @return \FrameworkDSW\Utilities\TVersion
     */
    public function getDbmsVersion() {
        try {
            $mStatement = $this->FConnection->CreateStatement(TResultSetType::eScrollInsensitive(), TConcurrencyType::eReadOnly());
            $mStatement->setCommand('SELECT VERSION()');
            $mRaw = $mStatement->FetchAsScalar();
            if ($mRaw instanceof TString) { // FIXME buggy for handling '5.2.0-standard', etc.
                $mRawVersion            = explode('.', $mRaw->Unbox(), 3);
                $mVersion               = new TVersion();
                $mVersion->MajorVersion = (integer)$mRawVersion[0];
                $mVersion->MinorVersion = (integer)$mRawVersion[1];
                $mVersion->Build        = (integer)$mRawVersion[2];
            }
            return $mVersion;
        }
        finally {
            Framework::Free($mStatement);
        }
    }

    /**
     * descHere
     * @return \FrameworkDSW\Database\TTransactionIsolationLevel
     */
    public function getDefaultTransactionIsolation() {
    }

    /**
     * descHere
     * @return string
     */
    public function getDriverName() {
    }

    /**
     * descHere
     * @return \FrameworkDSW\Utilities\TVersion
     */
    public function getDriverVersion() {
    }

    /**
     * descHere
     * @param \FrameworkDSW\System\TString $Catalog
     * @param \FrameworkDSW\System\TString $Schema
     * @param string $Table
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function GetExportedKeys($Catalog, $Schema, $Table) {
    }

    /**
     * descHere
     * @return string
     */
    public function getExtraNameCharacters() {
    }

    /**
     * descHere
     * @return string
     */
    public function getIdentifierQuoteString() {
    }

    /**
     * descHere
     * @param \FrameworkDSW\System\TString $Catalog
     * @param \FrameworkDSW\System\TString $Schema
     * @param string $Table
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function GetImportedKeys($Catalog, $Schema, $Table) {
    }

    /**
     * descHere
     * @param \FrameworkDSW\System\TString $Catalog
     * @param \FrameworkDSW\System\TString $Schema
     * @param string $Table
     * @param boolean $Unique
     * @param boolean $Approximate
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function GetIndexInfo($Catalog, $Schema, $Table, $Unique, $Approximate) {
    }

    /**
     * descHere
     * @return integer
     */
    public function getMaxBinaryLiteralLength() {
    }

    /**
     * descHere
     * @return integer
     */
    public function getMaxCatalogNameLength() {
    }

    /**
     * descHere
     * @return integer
     */
    public function getMaxCharLiteralLength() {
    }

    /**
     * descHere
     * @return integer
     */
    public function getMaxColumnNameLength() {
    }

    /**
     * descHere
     * @return integer
     */
    public function getMaxColumnsInGroupBy() {
    }

    /**
     * descHere
     * @return integer
     */
    public function getMaxColumnsInIndex() {
    }

    /**
     * descHere
     * @return integer
     */
    public function getMaxColumnsInOrderBy() {
    }

    /**
     * descHere
     * @return integer
     */
    public function getMaxColumnsInSelect() {
    }

    /**
     * descHere
     * @return integer
     */
    public function getMaxColumnsInTable() {
    }

    /**
     * descHere
     * @return integer
     */
    public function getMaxConnections() {
    }

    /**
     * descHere
     * @return integer
     */
    public function getMaxCursorNameLength() {
    }

    /**
     * descHere
     * @return integer
     */
    public function getMaxIndexLength() {
    }

    /**
     * descHere
     * @return integer
     */
    public function getMaxProcedureNameLength() {
    }

    /**
     * descHere
     * @return integer
     */
    public function getMaxRowSize() {
    }

    /**
     * descHere
     * @return integer
     */
    public function getMaxSchemaNameLength() {
    }

    /**
     * descHere
     * @return integer
     */
    public function getMaxStatementLength() {
    }

    /**
     * descHere
     * @return integer
     */
    public function getMaxStatements() {
    }

    /**
     * descHere
     * @return integer
     */
    public function getMaxTableNameLength() {
    }

    /**
     * descHere
     * @return integer
     */
    public function getMaxTablesInSelect() {
    }

    /**
     * descHere
     * @return integer
     */
    public function getMaxUserNameLength() {
    }

    /**
     * descHere
     * @return string[]
     */
    public function getNumericFunctions() {
    }

    /**
     * descHere
     * @param \FrameworkDSW\System\TString $Catalog
     * @param \FrameworkDSW\System\TString $Schema
     * @param string $Table
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function GetPrimaryKeys($Catalog, $Schema, $Table) {
    }

    /**
     * descHere
     * @param \FrameworkDSW\System\TString $Catalog
     * @param \FrameworkDSW\System\TString $SchemaPattern
     * @param string $ProcedureNamePattern
     * @param string $ColumnNamePattern
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function GetProcedureColumns($Catalog, $SchemaPattern, $ProcedureNamePattern, $ColumnNamePattern) {
    }

    /**
     * descHere
     * @param \FrameworkDSW\System\TString $Catalog
     * @param \FrameworkDSW\System\TString $SchemaPattern
     * @param string $ProcedureNamePattern
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function GetProcedures($Catalog, $SchemaPattern, $ProcedureNamePattern) {
    }

    /**
     * descHere
     * @return string
     */
    public function getProcedureTerm() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function getReadOnly() {
    }

    /**
     * descHere
     * @return \FrameworkDSW\Database\THoldability
     */
    public function getResultSetHoldability() {
    }

    /**
     * descHere
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function getSchemas() {
    }

    /**
     * descHere
     * @return string
     */
    public function getSchemaTerm() {
    }

    /**
     * descHere
     * @return string
     */
    public function getSearchStringEscape() {
    }

    /**
     * descHere
     * @return string[]
     */
    public function getSqlKeywords() {
    }

    /**
     * descHere
     * @return \FrameworkDSW\Database\TSqlStateType
     */
    public function getSqlStateType() {
    }

    /**
     * descHere
     * @return string[]
     */
    public function getStringFunctions() {
    }

    /**
     * descHere
     * @param \FrameworkDSW\System\TString $Catalog
     * @param \FrameworkDSW\System\TString $SchemaPattern
     * @param string $TableNameSchema
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function GetSuperTables($Catalog, $SchemaPattern, $TableNameSchema) {
    }

    /**
     * descHere
     * @param \FrameworkDSW\System\TString $Catalog
     * @param \FrameworkDSW\System\TString $SchemaPattern
     * @param string $TypeNamePattern
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function GetSuperTypes($Catalog, $SchemaPattern, $TypeNamePattern) {
    }

    /**
     * descHere
     * @param \FrameworkDSW\System\TString $Catalog
     * @param \FrameworkDSW\System\TString $SchemaPattern
     * @param string $TableNamePattern
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function GetTablePrivileges($Catalog, $SchemaPattern, $TableNamePattern) {
    }

    /**
     * descHere
     * @param \FrameworkDSW\System\TString $Catalog
     * @param \FrameworkDSW\System\TString $SchemaPattern
     * @param string $TableNamePattern
     * @param string[] $Types
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function GetTables($Catalog, $SchemaPattern, $TableNamePattern, $Types) {
    }

    /**
     * descHere
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function getTableTypes() {
    }

    /**
     * descHere
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function getTypeInfo() {
    }

    /**
     * descHere
     * @param \FrameworkDSW\System\TString $Catalog
     * @param \FrameworkDSW\System\TString $SchemaPattern
     * @param string $TypeNamePattern
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function GetUdts($Catalog, $SchemaPattern, $TypeNamePattern) {
    }

    /**
     * descHere
     * @return string
     */
    public function getUrl() {
    }

    /**
     * descHere
     * @return string
     */
    public function getUserName() {
    }

    /**
     * descHere
     * @param \FrameworkDSW\System\TString $Catalog
     * @param \FrameworkDSW\System\TString $Schema
     * @param string $Table
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function GetVersionColumns($Catalog, $Schema, $Table) {
    }

    /**
     * descHere
     * @param \FrameworkDSW\Database\TResultSetType $Type
     * @return boolean
     */
    public function InsertsAreDetected($Type) {
    }

    /**
     * descHere
     * @return boolean
     */
    public function LocatorsUpdateCopy() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function NullPlusNonNullIsNull() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function NullsAreSortedAtEnd() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function NullsAreSortedAtStart() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function NullsAreSortedHigh() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function NullsAreSortedLow() {
    }

    /**
     * descHere
     * @param \FrameworkDSW\Database\TResultSetType $Type
     * @return boolean
     */
    public function OthersDeletesAreVisible($Type) {
    }

    /**
     * descHere
     * @param \FrameworkDSW\Database\TResultSetType $Type
     * @return boolean
     */
    public function OthersInsertsAreVisible($Type) {
    }

    /**
     * descHere
     * @param \FrameworkDSW\Database\TResultSetType $Type
     * @return boolean
     */
    public function OthersUpdatesAreVisible($Type) {
    }

    /**
     * descHere
     * @param \FrameworkDSW\Database\TResultSetType $Type
     * @return boolean
     */
    public function OwnDeletesAreVisible($Type) {
    }

    /**
     * descHere
     * @param \FrameworkDSW\Database\TResultSetType $Type
     * @return boolean
     */
    public function OwnInsertsAreVisible($Type) {
    }

    /**
     * descHere
     * @param \FrameworkDSW\Database\TResultSetType $Type
     * @return boolean
     */
    public function OwnUpdatesAreVisible($Type) {
    }

    /**
     * descHere
     * @return boolean
     */
    public function StoresLowerCaseIdentifiers() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function StoresLowerCaseQuotedIdentifiers() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function StoresMixedCaseIdentifiers() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function StoresMixedCaseQuotedIdentifiers() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function StoresUpperCaseIdentifiers() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function StoresUpperCaseQuotedIdentifies() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsAlterTableWithAddColumn() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsAlterTableWithDropColumn() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsAnsi92EntryLevelSql() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsAnsi92FullSql() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsAnsi92IntermediateSql() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsBatchUpdates() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsCatalogsInDataManipulation() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsCatalogsInIndexDefinitions() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsCatalogsInPrivilegeDefinitions() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsCatalogsInProcedureCalls() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsCatalogsInTableDefinitions() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsColumnAliasing() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsCoreSqlGrammar() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsCorrelatedSubqueriers() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsDataDefinitionAndDataManipulationTransactions() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsDataManipulationTransactionsOnly() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsDifferentTableCorrelationName() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsExpressionsInOrderBy() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsExtendedSqlGrammar() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsFullOuterJoins() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsGetGeneratedKeys() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsGroupBy() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsGroupByBeyondSelect() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsGroupByUnrelated() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsIntegrityEnhancementFacility() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsLimitedOuterJoins() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsLinkEscapeClause() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsMinimumSqlGrammar() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsMixedCaseIdentifiers() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsMixedCaseQuotedIndentifiers() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsMultipleOpenResults() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsMultipleResultSets() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsMultipleTransaction() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsNamedParameters() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsNonNullableColumns() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsOpenCursorsAcrossCommit() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsOpenCursorsAcrossRollback() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsOpenStatementsAcrossCommit() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsOpenStatementsAcrossRollback() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsOrderByUnrelated() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsOuterJoins() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsPositionedDelete() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsPositionedUpdate() {
    }

    /**
     * descHere
     * @param \FrameworkDSW\Database\TConcurrencyType $Concurrency
     * @param \FrameworkDSW\Database\TResultSetType $Type
     * @return boolean
     */
    public function SupportsResultSetConcurrency($Concurrency, $Type) {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsResultSetHoldability() {
    }

    /**
     * descHere
     * @param \FrameworkDSW\Database\TResultSetType $Type
     * @return boolean
     */
    public function SupportsResultSetType($Type) {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsSavepoints() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsSchemaInProcedureCalls() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsSchemasInDataManipulation() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsSchemasInIndexDefinitions() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsSchemasInPrivilegeDefinitions() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsSchemasInTableDefinitions() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsSelectForUpdate() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsStatementPooling() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsStoredProcedures() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsSubqueriersInQuantifieds() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsSubqueriesInComparisons() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsSubqueriesInExists() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsSubqueriesInIns() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsTableCorrelationNames() {
    }

    /**
     * descHere
     * @param \FrameworkDSW\Database\TTransactionIsolationLevel $Level
     * @return boolean
     */
    public function SupportsTransactionIsolationLevel($Level) {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsTransactions() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsUnion() {
    }

    /**
     * descHere
     * @return boolean
     */
    public function SupportsUnionAll() {
    }

    /**
     * descHere
     * @param \FrameworkDSW\Database\TResultSetType $Type
     * @return boolean
     */
    public function UpdatesAreDetected($Type) {
    }

    /**
     * descHere
     * @return boolean
     */
    public function UsesLocalFiles() {
    }

    /**
     * descHere
     * @return string
     */
    public function UsesLocalFilesPerTable() {
    }

}
