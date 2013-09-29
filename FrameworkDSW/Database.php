<?php
/**
 * \FrameworkDSW\Database
 * @author	许子健
 * @version	$Id$
 * @since	separate file since reversion 1
 */
namespace FrameworkDSW\Database;

use FrameworkDSW\System\EException;
use FrameworkDSW\Utilities\TType;
use FrameworkDSW\System\TEnum;
use FrameworkDSW\System\TRecord;
use FrameworkDSW\System\IInterface;
use FrameworkDSW\Containers\IArrayAccess;
use FrameworkDSW\Containers\IIterator;
use FrameworkDSW\System\TObject;
use FrameworkDSW\System\TBoolean;
use FrameworkDSW\System\TInteger;
use FrameworkDSW\System\TFloat;
use FrameworkDSW\System\TString;
use FrameworkDSW\System\EIsNotNullable;
use FrameworkDSW\Framework\Framework;
use FrameworkDSW\Containers\TList;

/**
 * \FrameworkDSW\Database\EDatabaseException
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
 * @author 许子健
 */
class EInsufficientProperties extends EDatabaseException {
    /**
     * @var	string
     */
    const CMsg = 'The following fields are required: ';
}
/**
 *
 * @author 许子健
 */
class EDisconnected extends EDatabaseException {}
/**
 *
 * Enter description here ...
 * @author 许子健
 */
class EEmptyCommand extends EDatabaseException {}
/**
 * \FrameworkDSW\Database\EFailedToGetDbPropertyInfo
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
 *
 * Enter description here ...
 * @author	许子健
 */
class EIllegalSavepointIdentifier extends EDatabaseException {}
/**
 *
 * Enter description here ...
 * @author	许子健
 */
class EUnableToUpdateNonSingleTableResultSet extends EDatabaseException {}
/**
 *
 * Enter description here ...
 * @author	许子健
 */
class EResultSetIsNotUpdatable extends EDatabaseException {}
/**
 *
 * Enter description here ...
 * @author	许子健
 */
class ERowHasBeenDeleted extends EDatabaseException {}
/**
 *
 * Enter description here ...
 * @author	许子健
 */
class ENothingToUpdate extends EDatabaseException {}
/**
 *
 * Enter description here ...
 * @author	许子健
 */
class EInvalidRowId extends EDatabaseException {}
/**
 *
 * Enter description here ...
 * @author	许子健
 */
class EInvalidColumnName extends EDatabaseException {}
/**
 *
 * Enter description here ...
 * @author	许子健
 */
class ECurrentRowIsInsertRow extends EDatabaseException {}
/**
 *
 * Enter description here ...
 * @author	许子健
 */
class EFailedToGetFetchSize extends EDatabaseException {}

/**
 * \FrameworkDSW\Database\EDatabaseWarning
 * @author	许子健
 */
class EDatabaseWarning extends EDatabaseException {
    /**
     *
     * @var	\FrameworkDSW\Database\IDatabaseWarningContext
     */
    private $FContext = null;
    /**
     *
     * @var	\FrameworkDSW\Database\EDatabaseWarning
     */
    private $FNextWarning = null;

    /**
     *
     * @param	\FrameworkDSW\Database\IDatabaseWarningContext	$Context
     */
    public function __construct($Context) {
        parent::__construct();
        TType::Object($Context, 'IDatabaseWarningContext');

        $this->FContext = $Context;
    }

    /**
     * @return	string
     */
    public function getSqlState() {
        return $this->FContext->getSqlState();
    }

    /**
     * @return	string
     */
    public function getErrorCode() {
        return $this->FContext->getErrorCode();
    }

    /**
     * @return	\FrameworkDSW\Database\EDatabaseWarning
     */
    public function getNextWarning() {
        return $this->FNextWarning;
    }

    /**
     *
     * @param	\FrameworkDSW\Database\EDatabaseWarning	$Value
     */
    public function setNextWarning($Value) {
        TType::Object($Value, 'EDatabaseWarning');
        $this->FNextWarning = $Value;
    }

    /**
     *
     * Enter description here ...
     * @return	\FrameworkDSW\Database\IDatabaseWarningContext
     */
    public function getWarningContext() {
        return $this->FContext;
    }

}
/**
 *
 * @author	许子健
 */
class ECommitFailed extends EDatabaseWarning {}
/**
 *
 * @author	许子健
 */
class ECreateSavepointFailed extends EDatabaseWarning {}
/**
 *
 * @author	许子健
 */
class ERollbackFailed extends EDatabaseWarning {}
/**
 *
 * @author	许子健
 */
class EExecuteFailed extends EDatabaseWarning {}
/**
 *
 * Enter description here ...
 * @author	许子健
 */
class EFetchAsScalarFailed extends EDatabaseWarning {}
/**
 *
 * Enter description here ...
 * @author	许子健
 */
class ESetCommandFailed extends EDatabaseWarning {}
/**
 *
 * Enter description here ...
 * @author	许子健
 */
class EFetchRowFailed extends EDatabaseWarning {}
/**
 *
 * Enter description here ...
 * @author	许子健
 *
 */
class ENoMoreResultSet extends EDatabaseWarning {}
/**
 *
 * Enter description here ...
 * @author	许子健
 *
 */
class EFetchNextResultSetFailed extends EDatabaseWarning {}

/**
 * \FrameworkDSW\Database\TConcurrencyType
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
 * \FrameworkDSW\Database\TCurrentResultOption
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
 * \FrameworkDSW\Database\TDriverPropertyInfo
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
 * \FrameworkDSW\Database\TFetchDirection
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
 * \FrameworkDSW\Database\THoldability
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
 * \FrameworkDSW\Database\TResultSetType
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
 * \FrameworkDSW\Database\TTransactionIsolationLevel
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
 * \FrameworkDSW\Database\IDatabaseWarningContext
 * @author	许子健
 */
interface IDatabaseWarningContext extends IInterface {

    /**
     * descHere
     * @return	string
     */
    public function getErrorCode();

    /**
     * descHere
     * @return	string
     */
    public function getSqlState();

}

/**
 * \FrameworkDSW\Database\IParam
 * param	<T: ?>
 * @author	许子健
 */
interface IParam extends IInterface {

    /**
     * descHere
     * @return	\FrameworkDSW\System\TObject
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
     * @param	\FrameworkDSW\System\TObject	$Value
     */
    public function setObjectValue($Value);

    /**
     * descHere
     * @param	T	$Value
     */
    public function setValue($Value);
}

/**
 * \FrameworkDSW\Database\IDriver
 * @author	许子健
 */
interface IDriver extends IInterface {

    /**
     * descHere
     * @param	string	$Url
     * @param	\FrameworkDSW\Containers\IMap	$Properties <K: string, V: string>
     * @return	\FrameworkDSW\Database\IConnection
     */
    public function Connect($Url, $Properties);

    /**
     * descHere
     * @param	string					$Url
     * @param	\FrameworkDSW\Containers\IMap	$Properties <K: string, V: string>
     * @return	\FrameworkDSW\Database\TDriverPropertyInfo[]
     */
    public function GetPropertyInfo($Url, $Properties);

    /**
     * descHere
     * @return	\FrameworkDSW\Utilities\TVersion
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
 * \FrameworkDSW\Database\IConnection
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
     * @return	\FrameworkDSW\Database\ISavepoint
     */
    public function CreateSavepoint($Name = '');

    /**
     * descHere
     * @param	\FrameworkDSW\Database\TResultSetType	$ResultSetType
     * @param	\FrameworkDSW\Database\TConcurrencyType	$ConcurrencyType
     * @return	\FrameworkDSW\Database\IStatement
     */
    public function CreateStatement($ResultSetType, $ConcurrencyType);

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
     * @return	\FrameworkDSW\Database\THoldability
     */
    public function getHoldability();

    /**
     * @return	string
     */
    public function getCatalog();

    /**
     * descHere
     * @return	boolean
     */
    public function getIsConnected();

    /**
     * descHere
     * @return	\FrameworkDSW\Database\IDatabaseMetaData
     */
    public function getMetaData();

    /**
     * descHere
     * @return	boolean
     */
    public function getReadOnly();

    /**
     * descHere
     * @return	\FrameworkDSW\Database\TTransactionIsolationLevel
     */
    public function getTransactionIsolation();

    /**
     * descHere
     * @return	\FrameworkDSW\Database\EDatabaseWarning
     */
    public function getWarnings();

    /**
     * descHere
     * @param	\FrameworkDSW\Database\TResultSetType	$ResultSetType
     * @param	\FrameworkDSW\Database\TConcurrencyType	$ConcurrencyType
     * @return	\FrameworkDSW\Database\IPreparedStatement
     */
    public function PrepareStatement($ResultSetType, $ConcurrencyType);

    /**
     *
     * Enter description here ...
     * @param	\FrameworkDSW\Database\TResultSetType		$ResultSetType
     * @param	\FrameworkDSW\Database\TConcurrencyType	$ConcurrencyType
     * @return	\FrameworkDSW\Database\ICallableStatement
     */
    public function PrepareCall($ResultSetType, $ConcurrencyType);

    /**
     * descHere
     * @param	\FrameworkDSW\Database\ISavepoint	$Savepoint
     */
    public function RemoveSavepoint($Savepoint);

    /**
     * descHere
     * @param	\FrameworkDSW\Database\ISavepoint	$Savepoint
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
     * @param	\FrameworkDSW\Database\THoldability	$Value
     */
    public function setHoldability($Value);

    /**
     * descHere
     * @param	boolean	$Value
     */
    public function setReadOnly($Value);

    /**
     * descHere
     * @param	\FrameworkDSW\Database\TTransactionIsolation	$Value
     */
    public function setTransactionIsolation($Value);
}

/**
 * \FrameworkDSW\Database\ISavepoint
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
 * \FrameworkDSW\Database\IStatement
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
     *
     * @return	integer[]
     */
    public function ExecuteCommands();

    /**
     * @return	\FrameworkDSW\Database\IParam <T: ?>
     */
    public function FetchAsScalar();

    /**
     * descHere
     * @return	\FrameworkDSW\Database\IList <T: string>
     */
    public function getCommands();

    /**
     * descHere
     * @return	\FrameworkDSW\Database\IConnection
     */
    public function getConnection();

    /**
     * descHere
     * @return	\FrameworkDSW\Database\IResultSet
     */
    public function GetCurrentResult();

    /**
     * descHere
     * @param	integer	$Index
     * @return	\FrameworkDSW\Database\IResultSet
     */
    public function getResult($Index);

    /**
     * descHere
     * @param	\FrameworkDSW\Database\TCurrentResultOption	$Options
     */
    public function NextResult($Options);

    /**
     * descHere
     * @param	string	$Command
     * @return	\FrameworkDSW\Database\IResultSet
     */
    public function Query($Command = '');

    /**
     * descHere
     * @param	string	$Value
     */
    public function setCommand($Value);
}

/**
 * \FrameworkDSW\Database\IPreparedStatement
 * @author	许子健
 */
interface IPreparedStatement extends IStatement {

    /**
     * descHere
     * @param	string	$Name
     * @param	\FrameworkDSW\Database\IParam	$Param <T: ?>
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
     * @return	\FrameworkDSW\Database\IParam <T: ?>
     */
    public function GetParam($Name);
}

/**
 * \FrameworkDSW\Database\IResultSet
 * extends IArrayAccess<K: integer, V: IRow>, IIterator<T: IRow>
 * @author	许子健
 */
interface IResultSet extends IArrayAccess, IIterator {

    /**
     * descHere
     * @param	integer	$RowId
     * @return	\FrameworkDSW\Database\IRow
     */
    public function FetchAbsolute($RowId);

    /**
     * descHere
     * @param	integer	$Offset
     * @return	\FrameworkDSW\Database\IRow
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
     * @return	\FrameworkDSW\Database\TFetchDirection
     */
    public function getFetchDirection();

    /**
     * descHere
     * @return	integer
     */
    public function getFetchSize();

    /**
     * descHere
     * @return	\FrameworkDSW\Database\IRow
     */
    public function getInsertRow();

    /**
     * descHere
     * @return	boolean
     */
    public function getIsEmpty();

    /**
     * descHere
     * @return	\FrameworkDSW\Database\IResultMetaData
     */
    public function getMetaData();

    /**
     *
     * Enter description here ...
     * @return	\FrameworkDSW\Database\TResultSetType
     */
    public function getType();

    /**
     * descHere
     * @return	\FrameworkDSW\Database\IStatement
     */
    public function getStatement();

    /**
     * descHere
     * @param	\FrameworkDSW\Database\TFetchDirection	$Value
     */
    public function setFetchDirection($Value);

    /**
     * descHere
     * @param	integer	$Value
     */
    public function setFetchSize($Value);

    /**
     * descHere
     */
    public function Refresh();
}

/**
 * \FrameworkDSW\Database\IRow
 * extends IArrayAccess <K: string, V: IParam<T: ?>>
 * @author	许子健
 */
interface IRow extends IArrayAccess {

    /**
     * descHere
     */
    public function Delete();

    /**
     * descHere
     * @return	\FrameworkDSW\Database\TConcurrencyType
     */
    public function getConcurrencyType();

    /**
     * descHere
     * @return	\FrameworkDSW\Database\THoldability
     */
    public function getHoldability();

    /**
     * descHere
     * @return	\FrameworkDSW\Database\IResultSet
     */
    public function getResultSet();

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
    public function UndoUpdates();

    /**
     * descHere
     */
    public function Update();
}

/**
 * \FrameworkDSW\Database\IDatabaseMetaData
 * @author	许子健
 */
interface IDatabaseMetaData extends IInterface {

    /**
     * descHere
     * @return	boolean
     */
    public function AllProceduresAreCallable();

    /**
     * descHere
     * @return	boolean
     */
    public function AllTablesAreSelectable();

    /**
     * descHere
     * @return	boolean
     */
    public function DataDefinitionCausesTransactionCommit();

    /**
     * descHere
     * @return	boolean
     */
    public function DataDefinitionIgnoredInTransactions();

    /**
     * descHere
     * @param	\FrameworkDSW\Database\TResultSetType	$Type
     * @return	boolean
     */
    public function DeletesAreDetected($Type);

    /**
     * descHere
     * @return	boolean
     */
    public function DoesMaxRowSizeIncludeBlobs();

    /**
     * descHere
     * @param	\FrameworkDSW\Database\\FrameworkDSW\Database\TPrimitiveParam <T: string>	$Catalog
     * @param	\FrameworkDSW\Database\\FrameworkDSW\Database\TPrimitiveParam <T: string>	$SchemaPattern
     * @param	string	$TypeNamePattern
     * @param	string	$AttributeNamePattern
     * @return	\FrameworkDSW\Database\IResultSet
     */
    public function GetAttributes($Catalog, $SchemaPattern, $TypeNamePattern, $AttributeNamePattern);

    /**
     * descHere
     * @param	\FrameworkDSW\Database\\FrameworkDSW\Database\TPrimitiveParam <T: string>	$Catalog
     * @param	\FrameworkDSW\Database\\FrameworkDSW\Database\TPrimitiveParam <T: string>	$Schema
     * @param	string	$Table
     * @param	\FrameworkDSW\Database\TBestRowIdentifierScope	$Scope
     * @param	boolean	$Nullable
     * @return	\FrameworkDSW\Database\IResultSet
     */
    public function GetBestRowIdentifier($Catalog, $Schema, $Table, $Scope, $Nullable);

    /**
     * descHere
     * @return	\FrameworkDSW\Database\IResultSet
     */
    public function getCatalogs();

    /**
     * descHere
     * @return	string
     */
    public function getCatalogTerm();

    /**
     * descHere
     * @param	\FrameworkDSW\Database\\FrameworkDSW\Database\TPrimitiveParam <T: string>	$Catalog
     * @param	\FrameworkDSW\Database\\FrameworkDSW\Database\TPrimitiveParam <T: string>	$Schema
     * @param	string	$Table
     * @param	string	$ColumnNamePattern
     * @return	\FrameworkDSW\Database\IResultSet
     */
    public function GetColumnPrivileges($Catalog, $Schema, $Table, $ColumnNamePattern);

    /**
     * descHere
     * @param	\FrameworkDSW\Database\TPrimitiveParam <T: string>	$Catalog
     * @param	\FrameworkDSW\Database\TPrimitiveParam <T: string>	$SchemaPattern
     * @param	string	$TableNamePattern
     * @param	string	$ColumnNamePattern
     * @return	\FrameworkDSW\Database\IResultSet
     */
    public function GetColumns($Catalog, $SchemaPattern, $TableNamePattern, $ColumnNamePattern);

    /**
     * descHere
     * @return	IConnection
     */
    public function getConnection();

    /**
     * descHere
     * @param	\FrameworkDSW\Database\TPrimitiveParam <T: string>	$PrimaryCatalog
     * @param	\FrameworkDSW\Database\TPrimitiveParam <T: string>	$PrimarySchema
     * @param	string	$PrimaryTable
     * @param	\FrameworkDSW\Database\TPrimitiveParam <T: string>	$ForeignCatalog
     * @param	\FrameworkDSW\Database\TPrimitiveParam <T: string>	$ForeignSchema
     * @param	string	$ForeignTable
     * @return	\FrameworkDSW\Database\IResultSet
     */
    public function GetCrossReference($PrimaryCatalog, $PrimarySchema, $PrimaryTable, $ForeignCatalog, $ForeignSchema, $ForeignTable);

    /**
     * descHere
     * @return	\FrameworkDSW\Utilities\TVersion
     */
    public function getDatabaseVersion();

    /**
     * descHere
     * @return	string[]
     */
    public function getDateTimeFunctions();

    /**
     * descHere
     * @return	string
     */
    public function getDbmsName();

    /**
     * descHere
     * @return	\FrameworkDSW\Utilities\TVersion
     */
    public function getDbmsVersion();

    /**
     * descHere
     * @return	TTransactionIsolationLevel
     */
    public function getDefaultTransactionIsolation();

    /**
     * descHere
     * @return	string
     */
    public function getDriverName();

    /**
     * descHere
     * @return	\FrameworkDSW\Utilities\TVersion
     */
    public function getDriverVersion();

    /**
     * descHere
     * @param	\FrameworkDSW\Database\TPrimitiveParam <T: string>	$Catalog
     * @param	\FrameworkDSW\Database\TPrimitiveParam <T: string>	$Schema
     * @param	string	$Table
     * @return	\FrameworkDSW\Database\IResultSet
     */
    public function GetExportedKeys($Catalog, $Schema, $Table);

    /**
     * descHere
     * @return	string
     */
    public function getExtraNameCharacters();

    /**
     * descHere
     * @return	string
     */
    public function getIdentifierQuoteString();

    /**
     * descHere
     * @param	\FrameworkDSW\Database\TPrimitiveParam <T: string>	$Catalog
     * @param	\FrameworkDSW\Database\TPrimitiveParam <T: string>	$Schema
     * @param	string	$Table
     * @return	\FrameworkDSW\Database\IResultSet
     */
    public function GetImportedKeys($Catalog, $Schema, $Table);

    /**
     * descHere
     * @param	\FrameworkDSW\Database\TPrimitiveParam <T: string>	$Catalog
     * @param	\FrameworkDSW\Database\TPrimitiveParam <T: string>	$Schema
     * @param	string	$Table
     * @param	boolean	$Unique
     * @param	boolean	$Approximate
     * @return	\FrameworkDSW\Database\IResultSet
     */
    public function GetIndexInfo($Catalog, $Schema, $Table, $Unique, $Approximate);

    /**
     * descHere
     * @return	integer
     */
    public function getMaxBinaryLiteralLength();

    /**
     * descHere
     * @return	integer
     */
    public function getMaxCatalogNameLength();

    /**
     * descHere
     * @return	integer
     */
    public function getMaxCharLiteralLength();

    /**
     * descHere
     * @return	integer
     */
    public function getMaxColumnNameLength();

    /**
     * descHere
     * @return	integer
     */
    public function getMaxColumnsInGroupBy();

    /**
     * descHere
     * @return	integer
     */
    public function getMaxColumnsInIndex();

    /**
     * descHere
     * @return	integer
     */
    public function getMaxColumnsInOrderBy();

    /**
     * descHere
     * @return	integer
     */
    public function getMaxColumnsInSelect();

    /**
     * descHere
     * @return	integer
     */
    public function getMaxColumnsInTable();

    /**
     * descHere
     * @return	integer
     */
    public function getMaxConnections();

    /**
     * descHere
     * @return	integer
     */
    public function getMaxCursorNameLength();

    /**
     * descHere
     * @return	integer
     */
    public function getMaxIndexLength();

    /**
     * descHere
     * @return	integer
     */
    public function getMaxProcedureNameLength();

    /**
     * descHere
     * @return	integer
     */
    public function getMaxRowSize();

    /**
     * descHere
     * @return	integer
     */
    public function getMaxSchemaNameLength();

    /**
     * descHere
     * @return	integer
     */
    public function getMaxStatementLength();

    /**
     * descHere
     * @return	integer
     */
    public function getMaxStatements();

    /**
     * descHere
     * @return	integer
     */
    public function getMaxTableNameLength();

    /**
     * descHere
     * @return	integer
     */
    public function getMaxTablesInSelect();

    /**
     * descHere
     * @return	integer
     */
    public function getMaxUserNameLength();

    /**
     * descHere
     * @return	string[]
     */
    public function getNumericFunctions();

    /**
     * descHere
     * @param	\FrameworkDSW\Database\TPrimitiveParam <T: string>	$Catalog
     * @param	\FrameworkDSW\Database\TPrimitiveParam <T: string>	$Schema
     * @param	string	$Table
     * @return	\FrameworkDSW\Database\IResultSet
     */
    public function GetPrimaryKeys($Catalog, $Schema, $Table);

    /**
     * descHere
     * @param	\FrameworkDSW\Database\TPrimitiveParam <T: string>	$Catalog
     * @param	\FrameworkDSW\Database\TPrimitiveParam <T: string>	$SchemaPattern
     * @param	string	$ProcedureNamePattern
     * @param	string	$ColumnNamePattern
     * @return	\FrameworkDSW\Database\IResultSet
     */
    public function GetProcedureColumns($Catalog, $SchemaPattern, $ProcedureNamePattern, $ColumnNamePattern);

    /**
     * descHere
     * @param	\FrameworkDSW\Database\TPrimitiveParam <T: string>	$Catalog
     * @param	\FrameworkDSW\Database\TPrimitiveParam <T: string>	$SchemaPattern
     * @param	string	$ProcedureNamePattern
     * @return	\FrameworkDSW\Database\IResultSet
     */
    public function GetProcedures($Catalog, $SchemaPattern, $ProcedureNamePattern);

    /**
     * descHere
     * @return	string
     */
    public function getProcedureTerm();

    /**
     * descHere
     * @return	boolean
     */
    public function getReadOnly();

    /**
     * descHere
     * @return	THoldability
     */
    public function getResultSetHoldability();

    /**
     * descHere
     * @return	\FrameworkDSW\Database\IResultSet
     */
    public function getSchemas();

    /**
     * descHere
     * @return	string
     */
    public function getSchemaTerm();

    /**
     * descHere
     * @return	string
     */
    public function getSearchStringEscape();

    /**
     * descHere
     * @return	string[]
     */
    public function getSqlKeywords();

    /**
     * descHere
     * @return	\FrameworkDSW\Database\TSqlStateType
     */
    public function getSqlStateType();

    /**
     * descHere
     * @return	string[]
     */
    public function getStringFunctions();

    /**
     * descHere
     * @param	\FrameworkDSW\Database\TPrimitiveParam <T: string>	$Catalog
     * @param	\FrameworkDSW\Database\TPrimitiveParam <T: string>	$SchemaPattern
     * @param	string	$TableNameSchema
     * @return	\FrameworkDSW\Database\IResultSet
     */
    public function GetSuperTables($Catalog, $SchemaPattern, $TableNameSchema);

    /**
     * descHere
     * @param	\FrameworkDSW\Database\TPrimitiveParam <T: string>	$Catalog
     * @param	\FrameworkDSW\Database\TPrimitiveParam <T: string>	$SchemaPattern
     * @param	string	$TypeNamePattern
     * @return	\FrameworkDSW\Database\IResultSet
     */
    public function GetSuperTypes($Catalog, $SchemaPattern, $TypeNamePattern);

    /**
     * descHere
     * @param	\FrameworkDSW\Database\TPrimitiveParam <T: string>	$Catalog
     * @param	\FrameworkDSW\Database\TPrimitiveParam <T: string>	$SchemaPattern
     * @param	string	$TableNamePattern
     * @return	\FrameworkDSW\Database\IResultSet
     */
    public function GetTablePrivileges($Catalog, $SchemaPattern, $TableNamePattern);

    /**
     * descHere
     * @param	\FrameworkDSW\Database\TPrimitiveParam <T: string>	$Catalog
     * @param	\FrameworkDSW\Database\TPrimitiveParam <T: string>	$SchemaPattern
     * @param	string	$TableNamePattern
     * @param	string[]	$Types
     * @return	\FrameworkDSW\Database\IResultSet
     */
    public function GetTables($Catalog, $SchemaPattern, $TableNamePattern, $Types);

    /**
     * descHere
     * @return	\FrameworkDSW\Database\IResultSet
     */
    public function getTableTypes();

    /**
     * descHere
     * @return	\FrameworkDSW\Database\IResultSet
     */
    public function getTypeInfo();

    /**
     * descHere
     * @param	\FrameworkDSW\Database\TPrimitiveParam <T: string>	$Catalog
     * @param	\FrameworkDSW\Database\TPrimitiveParam <T: string>	$SchemaPattern
     * @param	string	$TypeNamePattern
     * @return	\FrameworkDSW\Database\IResultSet
     */
    public function GetUdts($Catalog, $SchemaPattern, $TypeNamePattern);

    /**
     * descHere
     * @return	string
     */
    public function getUrl();

    /**
     * descHere
     * @return	string
     */
    public function getUserName();

    /**
     * descHere
     * @param	\FrameworkDSW\Database\TPrimitiveParam <T: string>	$Catalog
     * @param	\FrameworkDSW\Database\TPrimitiveParam <T: string>	$Schema
     * @param	string	$Table
     * @return	\FrameworkDSW\Database\IResultSet
     */
    public function GetVersionColumns($Catalog, $Schema, $Table);

    /**
     * descHere
     * @param	\FrameworkDSW\Database\TResultSetType	$Type
     * @return	boolean
     */
    public function InsertsAreDetected($Type);

    /**
     * descHere
     * @return	boolean
     */
    public function LocatorsUpdateCopy();

    /**
     * descHere
     * @return	boolean
     */
    public function NullPlusNonNullIsNull();

    /**
     * descHere
     * @return	boolean
     */
    public function NullsAreSortedAtEnd();

    /**
     * descHere
     * @return	boolean
     */
    public function NullsAreSortedAtStart();

    /**
     * descHere
     * @return	boolean
     */
    public function NullsAreSortedHigh();

    /**
     * descHere
     * @return	boolean
     */
    public function NullsAreSortedLow();

    /**
     * descHere
     * @param	\FrameworkDSW\Database\TResultSetType	$Type
     * @return	boolean
     */
    public function OthersDeletesAreVisible($Type);

    /**
     * descHere
     * @param	\FrameworkDSW\Database\TResultSetType	$Type
     * @return	boolean
     */
    public function OthersInsertsAreVisible($Type);

    /**
     * descHere
     * @param	\FrameworkDSW\Database\TResultSetType	$Type
     * @return	boolean
     */
    public function OthersUpdatesAreVisible($Type);

    /**
     * descHere
     * @param	\FrameworkDSW\Database\TResultSetType	$Type
     * @return	boolean
     */
    public function OwnDeletesAreVisible($Type);

    /**
     * descHere
     * @param	\FrameworkDSW\Database\TResultSetType	$Type
     * @return	boolean
     */
    public function OwnInsertsAreVisible($Type);

    /**
     * descHere
     * @param	\FrameworkDSW\Database\TResultSetType	$Type
     * @return	boolean
     */
    public function OwnUpdatesAreVisible($Type);

    /**
     * descHere
     * @return	boolean
     */
    public function StoresLowerCaseIdentifiers();

    /**
     * descHere
     * @return	boolean
     */
    public function StoresLowerCaseQuotedIdentifiers();

    /**
     * descHere
     * @return	boolean
     */
    public function StoresMixedCaseIdentifiers();

    /**
     * descHere
     * @return	boolean
     */
    public function StoresMixedCaseQuotedIdentifiers();

    /**
     * descHere
     * @return	boolean
     */
    public function StoresUpperCaseIdentifiers();

    /**
     * descHere
     * @return	boolean
     */
    public function StoresUpperCaseQuotedIdentifies();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsAlterTableWithAddColumn();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsAlterTableWithDropColumn();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsAnsi92EntryLevelSql();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsAnsi92FullSql();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsAnsi92IntermediateSql();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsBatchUpdates();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsCatalogsInDataManipulation();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsCatalogsInIndexDefinitions();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsCatalogsInPrivilegeDefinitions();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsCatalogsInProcedureCalls();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsCatalogsInTableDefinitions();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsColumnAliasing();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsCoreSqlGrammar();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsCorrelatedSubqueriers();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsDataDefinitionAndDataManipulationTransactions();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsDataManipulationTransactionsOnly();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsDifferentTableCorrelationName();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsExpressionsInOrderBy();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsExtendedSqlGrammar();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsFullOuterJoins();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsGetGeneratedKeys();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsGroupBy();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsGroupByBeyondSelect();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsGroupByUnrelated();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsIntegrityEnhancementFacility();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsLimitedOuterJoins();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsLinkEscapeClause();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsMinimumSqlGrammar();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsMixedCaseIdentifiers();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsMixedCaseQuotedIndentifiers();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsMultipleOpenResults();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsMultipleResultSets();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsMultipleTransaction();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsNamedParameters();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsNonNullableColumns();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsOpenCursorsAcrossCommit();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsOpenCursorsAcrossRollback();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsOpenStatementsAcrossCommit();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsOpenStatementsAcrossRollback();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsOrderByUnrelated();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsOuterJoins();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsPositionedDelete();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsPositionedUpdate();

    /**
     * descHere
     * @param	\FrameworkDSW\Database\TConcurrencyType	$Concurrency
     * @param	\FrameworkDSW\Database\TResultSetType	$Type
     * @return	boolean
     */
    public function SupportsResultSetConcurrency($Concurrency, $Type);

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsResultSetHoldability();

    /**
     * descHere
     * @param	\FrameworkDSW\Database\TResultSetType	$Type
     * @return	boolean
     */
    public function SupportsResultSetType($Type);

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsSavepoints();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsSchemaInProcedureCalls();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsSchemasInDataManipulation();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsSchemasInIndexDefinitions();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsSchemasInPrivilegeDefinitions();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsSchemasInTableDefinitions();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsSelectForUpdate();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsStatementPooling();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsStoredProcedures();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsSubqueriersInQuantifieds();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsSubqueriesInComparisons();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsSubqueriesInExists();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsSubqueriesInIns();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsTableCorrelationNames();

    /**
     * descHere
     * @param	\FrameworkDSW\Database\TTransactionIsolationLevel	$Level
     * @return	boolean
     */
    public function SupportsTransactionIsolationLevel($Level);

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsTransactions();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsUnion();

    /**
     * descHere
     * @return	boolean
     */
    public function SupportsUnionAll();

    /**
     * descHere
     * @param	\FrameworkDSW\Database\TResultSetType	$Type
     * @return	boolean
     */
    public function UpdatesAreDetected($Type);

    /**
     * descHere
     * @return	boolean
     */
    public function UsesLocalFiles();

    /**
     * descHere
     * @return	string
     */
    public function UsesLocalFilesPerTable();

}

/**
 * \FrameworkDSW\Database\TPdoWarningContext
 * @author	许子健
 */
class TPdoWarningContext extends TObject implements IDatabaseWarningContext {

    /**
     * @var	\PDOException
     */
    private $FPdoException;

    /**
     * descHere
     * @param	\PDOException	$PdoException
     */
    public function __construct($PdoException) {
        parent::__construct();

        $this->FPdoException = $PdoException;
    }

    /**
     * (non-PHPdoc)
     * @see \FrameworkDSW\System\TObject::Destroy()
     */
    public function Destroy() {
        $this->FPdoException = null;
        parent::Destroy();
    }

    /**
     * @return	string
     */
    public function getSqlState() {
        return $this->FPdoException->errorInfo[0];
    }

    /**
     * @return	string
     */
    public function getErrorCode() {
        return $this->FPdoException->errorInfo[1];
    }

}

/**
 * \FrameworkDSW\Database\TSavepoint
 * @author	许子健
 */
class TSavepoint extends TObject implements ISavepoint {

    /**
     * @var	integer
     */
    private $FId = -1;
    /**
     * @var	string
     */
    private $FName = '';
    /**
     * @var	integer
     */
    private static $FNextId = 0;

    /**
     * descHere
     * @param	string	$Name
     */
    public function __construct($Name = '') {
        parent::__construct();
        TType::String($Name);

        if ($Name != '') {
            $this->FName = $Name;
        }
        else {
            $this->FId = self::$FNextId++;
        }
    }

    /**
     * descHere
     * @throws EIllegalSavepointIdentifier
     * @return    integer
     */
    public function getId() {
        if ($this->FId != -1) {
            return $this->FId;
        }
        else {
            throw new EIllegalSavepointIdentifier();
        }
    }

    /**
     * descHere
     * @throws EIllegalSavepointIdentifier
     * @return    string
     */
    public function getName() {
        if ($this->FId == -1) {
            return $this->FName;
        }
        else {
            throw new EIllegalSavepointIdentifier();
        }
    }

    /**
     *
     * @return string
     */
    public function getProperName() {
        if ($this->FId == -1) {
            return $this->FName;
        }
        else {
            return 'Svpt' . (string) $this->FId;
        }
    }
}

/**
 * \FrameworkDSW\Database\TAbstractParam
 * param	<T: ?>
 * @author	许子健
 */
abstract class TAbstractParam extends TObject {
    /**
     * @var	T
     */
    protected $FValue;

    //public abstract function getType(); //returns a string.
    //TODO: SEE http://bugs.php.net/bug.php?id=51826 to know why this method was commented.


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
 * \FrameworkDSW\Database\TPrimitiveParam
 * extends	\FrameworkDSW\Database\IParam<T: T>
 * param	<T: ?>
 * @author	许子健
 */
final class TPrimitiveParam extends TAbstractParam implements IParam {
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
        parent::__construct();
        TType::Type($Value, $this->GenericArg('T'));

        $mMapping = array ('boolean' => 'BIT', 'integer' => 'INTEGER',
            'float' => 'FLOAT', 'string' => 'VARCHAR', 'TBoolean' => 'BIT',
            'TInteger' => 'INTEGER', 'TFloat' => 'FLOAT',
            'TString' => 'VARCHAR');
        $this->FType = $mMapping[$this->GenericArg('T')];
        $this->FValue = $Value;
    }

    /**
     * descHere
     * @return	\FrameworkDSW\System\TObject
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
     * @param	\FrameworkDSW\System\TObject	$Value
     */
    public function setObjectValue($Value) {
        TType::Object($Value, '\FrameworkDSW\System\IPrimitive');
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
 * \FrameworkDSW\System\TAbstractPdoDriver
 * @author	许子健
 */
abstract class TAbstractPdoDriver extends TObject {

    /**
     * @var	\FrameworkDSW\Containers\IMap <K: string, V: string>
     */
    protected $FProperties = null;
    /**
     *
     * Enter description here ...
     * @var	array
     */
    protected $FPdoOptions = null;
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
     *
     * Enter description here ...
     */
    protected function ConvertProperties() {
        if ($this->FProperties === null) {
            return;
        }
        if ($this->FProperties->ContainsKey('AutoCommit')) {
            $this->FPdoOptions[\PDO::ATTR_AUTOCOMMIT] = (boolean) $this->FProperties['AutoCommit'];
        }
        if ($this->FProperties->ContainsKey('Timeout')) {
            $this->FPdoOptions[\PDO::ATTR_TIMEOUT] = (integer) $this->FProperties['Timeout'];
        }
        if ($this->FProperties->ContainsKey('Prefetch')) {
            $this->FPdoOptions[\PDO::ATTR_PREFETCH] = (integer) $this->FProperties['Prefetch'];
        }
        if ($this->FProperties->ContainsKey('Case')) {
            switch ($this->FProperties['Case']) {
                case 'Natural' :
                    $this->FPdoOptions[\PDO::ATTR_CASE] = \PDO::CASE_NATURAL;
                    break;
                case 'Upper' :
                    $this->FPdoOptions[\PDO::ATTR_CASE] = \PDO::CASE_UPPER;
                    break;
                case 'Lower' :
                    $this->FPdoOptions[\PDO::ATTR_CASE] = \PDO::CASE_LOWER;
                    break;
                default :
                    break;
            }
        }

     //TODO: observe and add other PDO common available options.
    }

    /**
     * descHere
     * @param    string $Url
     * @param    \FrameworkDSW\Containers\IMap $Properties <K: string, V: string>
     * @throws EFailedToConnectDb
     * @return    \FrameworkDSW\Database\IConnection
     */
    public function Connect($Url, $Properties) {
        TType::String($Url);
        TType::Type($Properties, array (
            'IMap' => array ('K' => 'string', 'V' => 'string')));

        $this->FProperties = $Properties;
        if ($this->ValidateUrl($Url)) {
            return $this->DoConnect();
        }
        throw new EFailedToConnectDb(EFailedToConnectDb::CMsg . $Url);
    }

    /**
     * descHere
     * @return	\FrameworkDSW\Database\IConnection
     */
    protected abstract function DoConnect();

    /**
     * descHere
     * @return	\FrameworkDSW\Database\TDriverPropertyInfo
     */
    protected abstract function DoGetPropertyInfo();

    /**
     * descHere
     * @return	\FrameworkDSW\Utilities\TVersion
     */
    protected abstract function DoGetVersion();

    /**
     * descHere
     * @return	boolean
     */
    protected abstract function DoValidateUrl();

    /**
     * descHere
     * @param    string $Url
     * @param    \FrameworkDSW\Containers\IMap $Properties <K: string, V: string>
     * @throws EFailedToGetDbPropertyInfo
     * @return    \FrameworkDSW\System\TDriverPropertyInfo[]
     */
    public function GetPropertyInfo($Url, $Properties) {
        TType::String($Url);
        TType::Type($Properties, array (
            'IMap' => array ('K' => 'string', 'V' => 'string')));

        if ($this->ValidateUrl($Url)) {
            $this->FProperties = $Properties;
            return $this->DoGetPropertyInfo();
        }
        throw new EFailedToGetDbPropertyInfo(EFailedToGetDbPropertyInfo::CMsg);
    }

    /**
     * the format of the url: Protocol://Server/DbName
     * example: mysql://localhost/Test
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
        $mTemp = explode('/', $mServer, 2);
        if (count($mTemp) != 2) {
            return false;
        }
        list ($mServer, $mDbName) = $mTemp;
        if ($mProtocol != '' && $mServer != '' && $mDbName != '') {
            $this->FProtocol = $mProtocol;
            $this->FServer = $mServer;
            $this->FDbName = $mDbName;
            return $this->DoValidateUrl();
        }
        return false;
    }

    /**
     * descHere
     * @return	\FrameworkDSW\Utilities\TVersion
     */
    public function getVersion() {
        return $this->DoGetVersion();
    }
}

/**
 * \FrameworkDSW\Database\TAbstractPdoConnection
 * @author	许子健
 */
abstract class TAbstractPdoConnection extends TObject {
    /**
     * @var	string
     */
    const CCatalogUnsupported = 'Catalog is not supported by this driver.';
    /**
     * @var	string
     */
    const CHoldabilityUnsupported = 'Holdability is not supported by this driver.';
    /**
     * @var	string
     */
    const CNullDriverOrPdoObj = 'The driver or/and the PDO object given is null.';
    /**
     * @var	string
     */
    const CReadOnlyUnsupported = 'ReadOnly is not supported by this driver.';
    /**
     * @var	string
     */
    const CSavepointsUnsupported = 'Savepoints is not supported by this driver.';
    /**
     * @var	string
     */
    const CTransactionIsolationUnsupported = 'Transaction isolation is not supported by this driver.';

    /**
     *
     * @var	\FrameworkDSW\Database\IDriver
     */
    protected $FDriver = null;
    /**
     * @var	\PDO
     */
    protected $FPdo = null;
    /**
     *
     * @var	boolean
     */
    protected $FIsConnected = false;
    /**
     *
     * @var	\FrameworkDSW\Database\EDatabaseWarning
     */
    protected $FWarnings = null;

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
     */
    protected function DoCommit() {
        $this->FPdo->commit();
    }

    /**
     *
     * @param	\FrameworkDSW\Database\TResultSetType		$ResultSetType
     * @param 	\FrameworkDSW\Database\TConcurrencyType	$ConcurrencyType
     * @return	\FrameworkDSW\Database\IStatement
     */
    protected abstract function DoCreateStatement($ResultSetType, $ConcurrencyType);

    /**
     *
     * @param	\FrameworkDSW\Database\TResultSetType		$ResultSetType
     * @param	\FrameworkDSW\Database\TConcurrencyType	$ConcurrencyType
     * @return	\FrameworkDSW\Database\IPreparedStatement
     */
    protected abstract function DoPrepareStatement($ResultSetType, $ConcurrencyType);

    /**
     *
     * @param    string $Name
     * @throws EUnsupportedDbFeature
     * @return    \FrameworkDSW\Database\ISavepoint
     */
    protected function DoCreateSavepoint(/** @noinspection PhpUnusedParameterInspection */
        $Name) {
        throw new EUnsupportedDbFeature(self::CSavepointsUnsupported);
    }

    /**
     * @return	\FrameworkDSW\Database\THoldability
     */
    protected function DoGetHoldability() {
        return THoldability::eHoldCursorsOverCommit();
    }

    /**
     *
     * @param    \FrameworkDSW\Database\ISavepoint $Savepoint
     * @throws EUnsupportedDbFeature
     */
    protected function DoRemoveSavepoint(/** @noinspection PhpUnusedParameterInspection */
        $Savepoint) {
        throw new EUnsupportedDbFeature(self::CSavepointsUnsupported);
    }

    /**
     *
     * @param    \FrameworkDSW\Database\ISavepoint $Savepoint
     * @throws EUnsupportedDbFeature
     */
    protected function DoRollback($Savepoint = null) {
        if ($Savepoint !== null) {
            throw new EUnsupportedDbFeature(self::CSavepointsUnsupported);
        }

        try {
            $this->FPdo->rollBack();
        }
        catch (\PDOException $Ex) {
            self::PushWarning(ERollbackFailed::ClassType(), $Ex, $this);
        }
    }

    /**
     *
     * @param    \FrameworkDSW\Database\THoldability $Value
     * @throws EUnsupportedDbFeature
     */
    protected function DoSetHoldability(/** @noinspection PhpUnusedParameterInspection */
        $Value) {
        throw new EUnsupportedDbFeature(self::CHoldabilityUnsupported);
    }

    /**
     *
     * @param    boolean $Value
     * @throws EUnsupportedDbFeature
     */
    protected function DoSetReadOnly(/** @noinspection PhpUnusedParameterInspection */
        $Value) {
        throw new EUnsupportedDbFeature(self::CReadOnlyUnsupported);
    }

    /**
     *
     * @param    \FrameworkDSW\Database\TTransactionIsolation $Value
     * @throws EUnsupportedDbFeature
     */
    protected function DoSetTransactionIsolation(/** @noinspection PhpUnusedParameterInspection */
        $Value) {
        throw new EUnsupportedDbFeature(self::CTransactionIsolationUnsupported);
    }

    /**
     *
     * @param    \FrameworkDSW\Database\IDriver $Driver
     * @param    \PDO $Pdo
     * @throws \FrameworkDSW\System\EIsNotNullable
     */
    public function __construct($Driver, $Pdo) {
        parent::__construct();
        TType::Object($Driver, 'IDriver');

        if ($Driver !== null && $Pdo !== null) {
            $this->FDriver = $Driver;
            $this->FPdo = $Pdo;
            $this->FPdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->FIsConnected = true;
        }
        else {
            $this->FIsConnected = false;
            throw new EIsNotNullable(self::CNullDriverOrPdoObj);
        }
    }

    /**
     * @param    string $WarningType
     * @param    \PDOException $PdoException
     * @param    \FrameworkDSW\Database\TAbstractPdoConnection $Connection
     * @throws
     */
    public static function PushWarning($WarningType, $PdoException, $Connection) {
        TType::String($WarningType);
        TType::Object($PdoException, 'PDOException');
        TType::Object($Connection, 'TAbstractPdoConnection');

        //$WarningType::InheritsFrom('EDatabaseWarning');
        $mWarning = new $WarningType(new TPdoWarningContext($PdoException));
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
        try {
            $this->DoCommit();
        }
        catch (\PDOException $Ex) {
            self::PushWarning(ECommitFailed::ClassType(), $Ex, $this);
        }
    }

    /**
     * descHere
     * @param	string	$Name
     * @return	\FrameworkDSW\Database\ISavepoint
     */
    public function CreateSavepoint($Name = '') {
        TType::String($Name);
        return $this->DoCreateSavepoint($Name);
    }

    /**
     * descHere
     * @param	\FrameworkDSW\Database\TResultSetType		$ResultSetType
     * @param	\FrameworkDSW\Database\TConcurrencyType	$ConcurrencyType
     * @return	\FrameworkDSW\Database\IStatement
     */
    public function CreateStatement($ResultSetType, $ConcurrencyType) {
        TType::Object($ResultSetType, 'TResultSetType');
        TType::Object($ConcurrencyType, 'TConcurrencyType');

        $this->EnsureConnected();
        //TODO: check if params are supported first.
        return $this->DoCreateStatement($ResultSetType, $ConcurrencyType);
    }

    /**
     * descHere
     */
    public function Disconnect() {
        $this->FPdo = null;
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
        try {
            $mResult = $this->FPdo->exec($SqlStatement);
        }
        catch (\PDOException $Ex) {
            self::PushWarning(EExecuteFailed::ClassType(), $Ex, $this);
        }
        /** @noinspection PhpUndefinedVariableInspection */
        return $mResult;
    }

    /**
     * descHere
     * @return	boolean
     */
    public function getAutoCommit() {
        $this->EnsureConnected();
        return (boolean) $this->FPdo->getAttribute(\PDO::ATTR_AUTOCOMMIT);
    }

    /**
     * @return	string
     */
    public function getCatalog() {
        return '';

     //override this in subclasses if capable.
    }

    /**
     * descHere
     * @return	THoldability
     */
    public function getHoldability() {
        $this->EnsureConnected();
        return $this->DoGetHoldability();
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
     * @throws EUnsupportedDbFeature
     * @return    \FrameworkDSW\Database\IDatabaseMetaData
     */
    public function getMetaData() {
        throw new EUnsupportedDbFeature();

     //TODO: metadata
    }

    /**
     * descHere
     * @return	boolean
     */
    public function getReadOnly() {
        return false;

     //override this if capable.
    }

    /**
     * descHere
     * @return	\FrameworkDSW\Database\TTransactionIsolationLevel
     */
    public function getTransactionIsolation() {
        return TTransactionIsolationLevel::eNone();

     //override this if capable.
    }

    /**
     * descHere
     * @return	\FrameworkDSW\Database\EDatabaseWarning
     */
    public function getWarnings() {
        $this->EnsureConnected();
        return $this->FWarnings;
    }

    /**
     * descHere
     * @param	\FrameworkDSW\Database\TResultSetType	$ResultSetType
     * @param	\FrameworkDSW\Database\TConcurrencyType	$ConcurrencyType
     * @return	\FrameworkDSW\Database\IPreparedStatement
     */
    public function PrepareStatement($ResultSetType, $ConcurrencyType) {
        TType::Object($ResultSetType, 'TResultSetType');
        TType::Object($ConcurrencyType, 'TConcurrencyType');

        $this->EnsureConnected();
        //TODO: check if the params are supported first.
        return $this->DoPrepareStatement($ResultSetType, $ConcurrencyType);
    }

    /**
     * descHere
     * @param	\FrameworkDSW\Database\ISavepoint	$Savepoint
     */
    public function RemoveSavepoint($Savepoint) {
        TType::Object($Savepoint, 'ISavepoint');
        $this->DoRemoveSavepoint($Savepoint);
    }

    /**
     * descHere
     * @param	\FrameworkDSW\Database\ISavepoint	$Savepoint
     */
    public function Rollback($Savepoint = null) {
        TType::Object($Savepoint, 'ISavepoint');
        $this->EnsureConnected();
        $this->DoRollback($Savepoint);
    }

    /**
     * descHere
     * @param	boolean	$Value
     */
    public function setAutoCommit($Value) {
        TType::Bool($Value);
        $this->EnsureConnected();
        $this->FPdo->setAttribute(\PDO::ATTR_AUTOCOMMIT, $Value);
    }

    /**
     * descHere
     * @param    string $Value
     * @throws EUnsupportedDbFeature
     */
    public function setCatalog($Value) {
        TType::String($Value);
        throw new EUnsupportedDbFeature(self::CCatalogUnsupported);
    }

    /**
     * descHere
     * @param	\FrameworkDSW\Database\THoldability	$Value
     */
    public function setHoldability($Value) {
        TType::Object($Value, 'THoldability');
        $this->DoSetHoldability($Value);
    }

    /**
     * descHere
     * @param	boolean	$Value
     */
    public function setReadOnly($Value) {
        TType::Bool($Value);
        $this->DoSetReadOnly($Value);
    }

    /**
     * descHere
     * @param	\FrameworkDSW\Database\TTransactionIsolation	$Value
     */
    public function setTransactionIsolation($Value) {
        TType::Object($Value, 'TTransactionIsolationLevel');
        $this->DoSetTransactionIsolation($Value);
    }

}

/**
 * \FrameworkDSW\Database\TAbstractPdoStatement
 * @author	许子健
 */
abstract class TAbstractPdoStatement extends TObject implements IStatement {
    /**
     *
     * Enter description here ...
     * @var	\FrameworkDSW\Database\TAbstractPdoConnection
     */
    protected $FConnection = null;
    /**
     *
     * Enter description here ...
     * @var \PDO
     */
    protected $FPdo = null;
    /**
     *
     * Enter description here ...
     * @var	\FrameworkDSW\Database\TResultSetType
     */
    protected $FResultSetType = null;
    /**
     *
     * Enter description here ...
     * @var	\FrameworkDSW\Database\TConcurrencyType
     */
    protected $FConcurrencyType = null;
    /**
     *
     * Enter description here ...
     * @var	\PDOStatement
     */
    protected $FPdoStatement = null;
    /**
     *
     * Enter description here ...
     * @var string
     */
    protected $FCommand = '';
    /**
     *
     * Enter description here ...
     * @var	\FrameworkDSW\Containers\IList <T: string>
     */
    private $FCommands = null;

//    /**
//     *
//     * Enter description here ...
//     */
//    private function EnsurePdoStatement() {
//        if ($this->FPdoStatement === null) {
//            throw new EEmptyCommand();
//        }
//    }

    /**
     *
     * Enter description here ...
     * @return	\FrameworkDSW\Database\IParam <T: ?>
     */
    protected abstract function DoFetchAsScalar();

    /**
     *
     * Enter description here ...
     * @param \FrameworkDSW\Database\TAbstractPdoConnection	$Connection
     * @param \PDO						$Pdo
     * @param \FrameworkDSW\Database\TResultSetType			$ResultSetType
     * @param \FrameworkDSW\Database\TConcurrencyType			$ConcurrencyType
     */
    public function __construct($Connection, $Pdo, $ResultSetType, $ConcurrencyType) {
        parent::__construct();
        TType::Object($Connection, 'TAbstractPdoConnection');
        TType::Object($Pdo, 'PDO');
        TType::Object($ResultSetType, 'TResultSetType');
        TType::Object($ConcurrencyType, 'TConcurrencyType');

        $this->FConnection = $Connection;
        $this->FPdo = $Pdo;
        $this->FResultSetType = $ResultSetType;
        //eForwardOnly 只能向前滚动row
        //eScrollInsensitive 对其他对象作出的数据修改不敏感，直接装入全部数据到内存
        //eScrollSensitive   ----------------------敏感，每次调用数据库某一行
        $this->FConcurrencyType = $ConcurrencyType;

     //eReadOnly 不能修改数据
    //eUpdatable 可以修改数据
    }

    /**
     * descHere
     * @param	string	$Command
     * @return	integer
     */
    /** @noinspection PhpInconsistentReturnPointsInspection */
    public function Execute($Command = '') {
        TType::String($Command);

        if ($Command != '') {
            $this->FCommand = $Command;
        }
        try {
            $mStmt = $this->FPdo->query($this->FCommand);
            return $mStmt->rowCount();
        }
        catch (\PDOException $Ex) {
            TAbstractPdoConnection::PushWarning(EExecuteFailed::ClassType(), $Ex, $this->FConnection);
        }
    }

    /**
     * (non-PHPdoc)
     * @see \FrameworkDSW\Database\IStatement::ExecuteCommands()
     * @throws EEmptyCommand
     * @return    integer[]
     */
    public function ExecuteCommands() {
        if ($this->FCommands === null || $this->FCommands->IsEmpty()) {
            throw new EEmptyCommand();
        }
        $mRows = array ();
        try {
            $this->FConnection->setAutoCommit(false);
            foreach ($this->FCommands as $mCmd) {
                $mStmt = $this->FPdo->query($mCmd);
                $mRows[] = $mStmt->rowCount();
            }
            $this->FConnection->Commit();
        }
        catch (\PDOException $Ex) {
            $this->FConnection->Rollback();
            TAbstractPdoConnection::PushWarning(EExecuteFailed::ClassType(), $Ex, $this->FConnection);
        }
        return $mRows;
    }

    /**
     * descHere
     * @return	\FrameworkDSW\Database\IParam <T: ?>
     */
    /** @noinspection PhpInconsistentReturnPointsInspection */
    public function FetchAsScalar() {
        try {
            return $this->DoFetchAsScalar();
        }
        catch (\PDOException $Ex) {
            TAbstractPdoConnection::PushWarning(EFetchAsScalarFailed::ClassType(), $Ex, $this->FConnection);
        }
    }

    /**
     * descHere
     * @return	\FrameworkDSW\Containers\IList <T: string>
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
     * @return	\FrameworkDSW\Database\IConnection
     */
    public function getConnection() {
        return $this->FConnection;
    }

    /**
     * descHere
     * @return	\FrameworkDSW\Database\IResultSet
     */
    public function GetCurrentResult() {
    }

    /**
     * descHere
     * @param	integer	$Index
     * @return	\FrameworkDSW\Database\IResultSet
     */
    public function getResult($Index) {
    }

    /**
     * descHere
     * @param	\FrameworkDSW\Database\TCurrentResultOption	$Options
     */
    public function NextResult($Options) {
    }

    /**
     * descHere
     * @param	string	$Command
     * @return	\FrameworkDSW\Database\IResultSet
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
        //TODO: to deal with insensitive. maybe to write back to db after updating result sets.
        $mAttr = array (\PDO::ATTR_CURSOR => \PDO::CURSOR_SCROLL);
        if ($this->FResultSetType == TResultSetType::eForwardOnly()) {
            $mAttr[\PDO::ATTR_CURSOR] = \PDO::CURSOR_FWDONLY;
        }
        try {
            $this->FPdoStatement = $this->FPdo->prepare($this->FCommand, $mAttr);
        }
        catch (\PDOException $Ex) {
            TAbstractPdoConnection::PushWarning(ESetCommandFailed::ClassType(), $Ex, $this->FConnection);
        }
    }

}

/**
 * \FrameworkDSW\Database\TAbstractPdoResultSet
 * @author	许子健
 */
abstract class TAbstractPdoResultSet extends TObject {

    /**
     *
     * Enter description here ...
     * @var	\FrameworkDSW\Database\TAbstractPdoStatement
     */
    protected $FStatement = null;

    /**
     *
     * Enter description here ...
     * @var	\PDO
     */
    protected $FPdo = null;

    /**
     *
     * Enter description here ...
     * @var	\PDOStatement
     */
    protected $FPdoStatement = null;

    /**
     *
     * Enter description here ...
     * @var	\FrameworkDSW\Database\TResultSetType
     */
    protected $FResultSetType = null;

    /**
     *
     * Enter description here ...
     * @var	\FrameworkDSW\Database\TConcurrencyType
     */
    protected $FConcurrencyType = null;

    /**
     *
     * Enter description here ...
     * @var	\FrameworkDSW\Database\TFetchDirection
     */
    protected $FFetchDirection = null;

    /**
     *
     * Enter description here ...
     * @var	string
     */
    protected $FCommand = '';

    /**
     *
     * Enter description here ...
     * @var	boolean
     */
    protected $FIsClosed = false;

    /**
     * [0 .. n]:
     * Indicates current row number, scroll insensitive result sets only.
     * [-1]:
     * indicates the pending row to be inserted.
     * [-2]:
     * empty result set.
     * @var	integer
     */
    protected $FCurrentRow = -2;

    /**
     *
     * Enter description here ...
     * @var	mixed
     */
    protected $FRawData = null;

    /**
     *
     * Enter description here ...
     * @param	\FrameworkDSW\Database\TAbstractPdoStatement	$Statement
     * @param	string					$Command
     * @param	\PDO						$Pdo
     * @param	\PDOStatement			$PdoStatement
     * @param	\FrameworkDSW\Database\TResultSetType			$ResultSetType
     * @param	\FrameworkDSW\Database\TConcurrencyType		$ConcurrencyType
     */
    public function __construct($Statement, $Command, $Pdo, $PdoStatement, $ResultSetType, $ConcurrencyType) {
        TType::Object($Statement, 'TAbstractPdoStatement');
        TType::String($Command);
        TType::Object($Pdo, 'PDO');
        TType::Object($PdoStatement, 'PDOStatement');
        TType::Object($ResultSetType, 'TResultSetType');
        TType::Object($ConcurrencyType, 'TConcurrencyType');

        parent::__construct();

        $this->FStatement = $Statement;
        $this->FCommand = $Command;
        $this->FPdo = $Pdo;

        $PdoStatement->execute(); //TODO invoke prepare() with proper PDO::ATTR_CURSOR value.
        if ($this->FResultSetType == TResultSetType::eScrollInsensitive()) {
            $this->FRawData = $PdoStatement->fetchAll();
            $PdoStatement = null;
        }
        $this->FPdoStatement = $PdoStatement;

        $this->FResultSetType = $ResultSetType;
        $this->FConcurrencyType = $ConcurrencyType;

        $this->FFetchDirection = TFetchDirection::eUnkown();
    }

    /**
     * descHere
     */
    public function Close() {
        if ($this->FIsClosed) {
            return;
        }
        if ($this->FResultSetType == TResultSetType::eScrollInsensitive()) {
            $this->FRawData = null;
        }
        $this->FPdoStatement = null;
        $this->FIsClosed = true;
        $this->FCurrentRow = -2;
        $this->FFetchDirection = TFetchDirection::eUnkown();
    }

    /**
     * descHere
     * @return	\FrameworkDSW\Database\IRow
     */
    public function current() {
    }

    /**
     * descHere
     * @param	integer	$RowId
     * @return	\FrameworkDSW\Database\IRow
     */
    public function FetchAbsolute($RowId) {
        TType::Int($RowId);

        switch ($this->FResultSetType) {
            case TResultSetType::eScrollInsensitive() :
                //TODO return raw data.
                $this->FCurrentRow = $RowId;
                break;
            case TResultSetType::eScrollSensitive() :
                $mRawRow = $this->FPdoStatement->fetch(\PDO::FETCH_BOTH, \PDO::FETCH_ORI_ABS, $RowId);
                //TODO generate the IRow instance.
                break;
            default : //not supported. eg. forward only mode
                TAbstractPdoConnection::PushWarning(EFetchRowFailed::ClassType(), null, $this->FStatement->getConnection());
                break;
        }
    }

    /**
     * descHere
     * @param	integer	$Offset
     * @return	\FrameworkDSW\Database\IRow
     */
    public function FetchRelative($Offset) {
        TType::Int($Offset);
        if ($Offset == 0) {
            //TODO: throw an exception.
        }
        switch ($this->FResultSetType) {
            case TResultSetType::eScrollInsensitive() :
                //TODO generate the IRow instance based on the appropriate row inside raw data.
                $this->FCurrentRow += $Offset;
                break;
            case TResultSetType::eScrollSensitive() :
                switch ($this->FFetchDirection) {
                    case TFetchDirection::eForward() :
                        $mRawRow = $this->FPdoStatement->fetch(\PDO::FETCH_BOTH, \PDO::FETCH_ORI_REL, $Offset);
                        //TODO generate the IRow instance based on $mRawRow.
                        break;
                    case TFetchDirection::eReverse() :
                        if ($this->FCurrentRow == -2) {
                            $mRawRow = $this->FPdoStatement->fetch(\PDO::FETCH_BOTH, \PDO::FETCH_ORI_LAST);
                            $this->FCurrentRow = 0;
                            --$Offset;
                        }
                        if ($Offset > 0) {
                            while ($Offset > 1) {
                                $this->FPdoStatement->fetch(\PDO::FETCH_BOTH, \PDO::FETCH_ORI_PRIOR);
                            }
                            $mRawRow = $this->FPdoStatement->fetch(\PDO::FETCH_BOTH, \PDO::FETCH_ORI_PRIOR);
                        }
                        else {
                            while ($Offset < -1) {
                                $this->FPdoStatement->fetch(\PDO::FETCH_BOTH, \PDO::FETCH_ORI_NEXT);
                            }
                            $mRawRow = $this->FPdoStatement->fetch(\PDO::FETCH_BOTH, \PDO::FETCH_ORI_NEXT);
                        }
                        //TODO generate the IRow instance based on $mRawRow.
                        break;
                }
                break;
            default : //not supported. eg. forward only mode
                TAbstractPdoConnection::PushWarning(EFetchRowFailed::ClassType(), null, $this->FStatement->getConnection());
                break;
        }
    }

    /**
     * descHere
     * @throws EUnsupportedDbFeature
     * @return    integer
     */
    public function getCount() {
        if ($this->FResultSetType == TResultSetType::eScrollInsensitive()) {
            return count($this->FRawData);
        }

        throw new EUnsupportedDbFeature();
    }

    /**
     * descHere
     * @return	string
     */
    public function getCursorName() {
    }

    /**
     * descHere
     * @return	\FrameworkDSW\Database\TFetchDirection
     */
    public function getFetchDirection() {
        if ($this->FResultSetType == TResultSetType::eForwardOnly()) {
            return TFetchDirection::eForward();
        }
        return $this->FFetchDirection;
    }

    /**
     * descHere
     * @return	integer
     */
    public function getFetchSize() {
    }

    /**
     * descHere
     * @return	\FrameworkDSW\Database\IRow
     */
    public function getInsertRow() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function getIsClosed() {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function getIsEmpty() {
    }

    /**
     * descHere
     * @return	\FrameworkDSW\Database\IResultMetaData
     */
    public function getMetaData() {
    }

    /**
     * descHere
     * @return	\FrameworkDSW\Database\IStatement
     */
    public function getStatement() {
    }

    /**
     * descHere
     * @return	string
     */
    public function key() {
    }

    /**
     * descHere
     */
    public function next() {
    }

    /**
     * descHere
     * @param	string	$offset
     * @return	boolean
     */
    public function offsetExists($offset) {
    }

    /**
     * descHere
     * @param	string	$offset
     * @return	\FrameworkDSW\Database\IRow
     */
    public function offsetGet($offset) {
    }

    /**
     * descHere
     * @param	string	$offset
     * @param	\FrameworkDSW\Database\IRow	$value
     */
    public function offsetSet($offset, $value) {
    }

    /**
     * descHere
     * @param	string	$offset
     */
    public function offsetUnset($offset) {
    }

    /**
     * descHere
     */
    public function rewind() {
    }

    /**
     * descHere
     * @param	\FrameworkDSW\Database\TFetchDirection	$Value
     */
    public function setFetchDirection($Value) {
        TType::Object($Value, 'TFetchDirection');

        if ($Value == TFetchDirection::eUnkown()) {
            //TODO: throw new exception. can not set as unknown.
        }
        if ($this->FResultSetType == TResultSetType::eForwardOnly()) {
            //TODO: unsupported. it must be forward, no need to set.
        }

        $this->FFetchDirection = $Value;
    }

    /**
     * descHere
     * @param	integer	$Value
     */
    public function setFetchSize($Value) {
    }

    /**
     * descHere
     * @return	boolean
     */
    public function valid() {
    }

}