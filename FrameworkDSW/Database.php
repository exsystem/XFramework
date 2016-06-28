<?php
/**
 * \FrameworkDSW\Database
 * @author 许子健
 * @version Id: Database.php 64 2013-09-29 11:23:39Z exsystemchina@gmail.com $
 * @since separate file since reversion 1
 */
namespace FrameworkDSW\Database;

use FrameworkDSW\Containers\ENoSuchElement;
use FrameworkDSW\Containers\IArrayAccess;
use FrameworkDSW\Containers\IIterator;
use FrameworkDSW\Containers\IMap;
use FrameworkDSW\Containers\TLinkedList;
use FrameworkDSW\Containers\TMap;
use FrameworkDSW\Framework\Framework;
use FrameworkDSW\Reflection\TClass;
use FrameworkDSW\System\EException;
use FrameworkDSW\System\EInvalidParameter;
use FrameworkDSW\System\IInterface;
use FrameworkDSW\System\TEnum;
use FrameworkDSW\System\TObject;
use FrameworkDSW\System\TRecord;
use FrameworkDSW\Utilities\TType;

/**
 * \FrameworkDSW\Database\IDatabaseWarningContext
 * @author 许子健
 */
interface IDatabaseWarningContext extends IInterface {

    /**
     * descHere
     * @return string
     */
    public function getErrorCode();

    /**
     * descHere
     * @return string
     */
    public function getSqlState();

    /**
     *
     * Enter description here ...
     * @return string
     */
    public function getErrorMessage();
}

/**
 * \FrameworkDSW\Database\IDriver
 * @author 许子健
 */
interface IDriver extends IInterface {

    /**
     * descHere
     * @param string $Url
     * @param \FrameworkDSW\Containers\IMap $Properties <K: string, V: string>
     * @return \FrameworkDSW\Database\IConnection
     */
    public function Connect($Url, $Properties);

    /**
     * descHere
     * @param string $Url
     * @param \FrameworkDSW\Containers\IMap $Properties <K: string, V: string>
     * @return \FrameworkDSW\Database\TDriverPropertyInfo[]
     */
    public function GetPropertyInfo($Url, $Properties);

    /**
     * descHere
     * @return \FrameworkDSW\Utilities\TVersion
     */
    public function getVersion();

    /**
     * descHere
     * @param string $Url
     * @return boolean
     */
    public function ValidateUrl($Url);
}

/**
 * \FrameworkDSW\Database\IConnection
 * @author 许子健
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
     * @param string $Name
     * @return \FrameworkDSW\Database\ISavepoint
     */
    public function CreateSavepoint($Name = '');

    /**
     * descHere
     * @param \FrameworkDSW\Database\TResultSetType $ResultSetType
     * @param \FrameworkDSW\Database\TConcurrencyType $ConcurrencyType
     * @return \FrameworkDSW\Database\IStatement
     */
    public function CreateStatement($ResultSetType, $ConcurrencyType);

    /**
     * descHere
     */
    public function Disconnect();

    /**
     * descHere
     * @param string $SqlStatement
     * @return integer
     */
    public function Execute($SqlStatement);

    /**
     * descHere
     * @return boolean
     */
    public function getAutoCommit();

    /**
     * descHere
     * @return \FrameworkDSW\Database\THoldability
     */
    public function getHoldability();

    /**
     * @return string
     */
    public function getCatalog();

    /**
     * descHere
     * @return boolean
     */
    public function getIsConnected();

    /**
     * descHere
     * @return \FrameworkDSW\Database\IDatabaseMetaData
     */
    public function getMetaData();

    /**
     * descHere
     * @return boolean
     */
    public function getReadOnly();

    /**
     * descHere
     * @return \FrameworkDSW\Database\TTransactionIsolationLevel
     */
    public function getTransactionIsolation();

    /**
     * descHere
     * @return \FrameworkDSW\Database\EDatabaseWarning
     */
    public function getWarnings();

    /**
     * descHere
     * @param \FrameworkDSW\Database\TResultSetType $ResultSetType
     * @param \FrameworkDSW\Database\TConcurrencyType $ConcurrencyType
     * @return \FrameworkDSW\Database\IPreparedStatement
     */
    public function PrepareStatement($ResultSetType, $ConcurrencyType);

    /**
     *
     * Enter description here ...
     * @param \FrameworkDSW\Database\TResultSetType $ResultSetType
     * @param \FrameworkDSW\Database\TConcurrencyType $ConcurrencyType
     * @return \FrameworkDSW\Database\ICallableStatement
     */
    public function PrepareCall($ResultSetType, $ConcurrencyType);

    /**
     * descHere
     * @param \FrameworkDSW\Database\ISavepoint $Savepoint
     */
    public function RemoveSavepoint($Savepoint);

    /**
     * descHere
     * @param \FrameworkDSW\Database\ISavepoint $Savepoint
     */
    public function Rollback($Savepoint = null);

    /**
     * descHere
     * @param boolean $Value
     */
    public function setAutoCommit($Value);

    /**
     * descHere
     * @param string $Value
     */
    public function setCatalog($Value);

    /**
     * descHere
     * @param \FrameworkDSW\Database\THoldability $Value
     */
    public function setHoldability($Value);

    /**
     * descHere
     * @param boolean $Value
     */
    public function setReadOnly($Value);

    /**
     * descHere
     * @param \FrameworkDSW\Database\TTransactionIsolationLevel $Value
     */
    public function setTransactionIsolation($Value);

    /**
     * @param string $Identifier
     * @return string
     */
    public function QuoteIdentifier($Identifier);
}

/**
 * \FrameworkDSW\Database\ISavepoint
 * @author 许子健
 */
interface ISavepoint extends IInterface {

    /**
     * descHere
     * @return integer
     */
    public function getId();

    /**
     * descHere
     * @return string
     */
    public function getName();
}

/**
 * \FrameworkDSW\Database\IStatement
 * @author 许子健
 */
interface IStatement extends IInterface {

    /**
     * descHere
     * @param string $Command
     * @return integer
     */
    public function Execute($Command = '');

    /**
     *
     * @return integer[]
     */
    public function ExecuteCommands();

    /**
     * @return \FrameworkDSW\System\IInterface
     */
    public function FetchAsScalar();

    /**
     * descHere
     * @return \FrameworkDSW\Containers\IList <T: string>
     */
    public function getCommands();

    /**
     * descHere
     * @return \FrameworkDSW\Database\IConnection
     */
    public function getConnection();

    /**
     * descHere
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function GetCurrentResult();

    /**
     * descHere
     * @param integer $Index
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function getResult($Index);

    /**
     * descHere
     * @param \FrameworkDSW\Database\TCurrentResultOption $Options
     */
    public function NextResult($Options);

    /**
     * descHere
     * @param string $Command
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function Query($Command = '');

    /**
     * descHere
     * @param string $Value
     */
    public function setCommand($Value);
}

/**
 * \FrameworkDSW\Database\IPreparedStatement
 * @author 许子健
 */
interface IPreparedStatement extends IStatement {

    /**
     * descHere
     * @param string $Name
     * @param \FrameworkDSW\System\IInterface $Param
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
     * @param string $Name
     * @return \FrameworkDSW\System\IInterface
     */
    public function GetParam($Name);
}

/**
 * \FrameworkDSW\Database\IResultSet
 * extends \FrameworkDSW\Containers\IArrayAccess<K: integer, V: \FrameworkDSW\Database\IRow>, \FrameworkDSW\Containers\IIterator<T: \FrameworkDSW\Database\IRow>
 * @author 许子健
 */
interface IResultSet extends IArrayAccess, IIterator {

    /**
     * descHere
     * @param integer $RowId
     * @return \FrameworkDSW\Database\IRow
     */
    public function FetchAbsolute($RowId);

    /**
     * descHere
     * @param integer $Offset
     * @return \FrameworkDSW\Database\IRow
     */
    public function FetchRelative($Offset);

    /**
     * descHere
     * @return integer
     */
    public function getCount();

    /**
     * descHere
     * @return string
     */
    public function getCursorName();

    /**
     * descHere
     * @return \FrameworkDSW\Database\TFetchDirection
     */
    public function getFetchDirection();

    /**
     * descHere
     * @return integer
     */
    public function getFetchSize();

    /**
     * descHere
     * @return \FrameworkDSW\Database\IRow
     */
    public function getInsertRow();

    /**
     * descHere
     * @return boolean
     */
    public function getIsEmpty();

    /**
     * descHere
     * @return \FrameworkDSW\Database\IResultMetaData
     */
    public function getMetaData();

    /**
     *
     * Enter description here ...
     * @return \FrameworkDSW\Database\TResultSetType
     */
    public function getType();

    /**
     * descHere
     * @return \FrameworkDSW\Database\IStatement
     */
    public function getStatement();

    /**
     * descHere
     * @param \FrameworkDSW\Database\TFetchDirection $Value
     */
    public function setFetchDirection($Value);

    /**
     * descHere
     * @param integer $Value
     */
    public function setFetchSize($Value);

    /**
     * descHere
     */
    public function Refresh();
}

/**
 * \FrameworkDSW\Database\IRow
 * extends \FrameworkDSW\Containers\IArrayAccess <K: string, V: \FrameworkDSW\System\IInterface>
 * @author 许子健
 */
interface IRow extends IArrayAccess {

    /**
     * descHere
     */
    public function Delete();

    /**
     * descHere
     * @return \FrameworkDSW\Database\TConcurrencyType
     */
    public function getConcurrencyType();

    /**
     * descHere
     * @return \FrameworkDSW\Database\THoldability
     */
    public function getHoldability();

    /**
     * descHere
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function getResultSet();

    /**
     * descHere
     * @return boolean
     */
    public function getWasDeleted();

    /**
     * descHere
     * @return boolean
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
 * @author 许子健
 */
interface IDatabaseMetaData extends IInterface {

    /**
     * descHere
     * @return boolean
     */
    public function AllProceduresAreCallable();

    /**
     * descHere
     * @return boolean
     */
    public function AllTablesAreSelectable();

    /**
     * descHere
     * @return boolean
     */
    public function DataDefinitionCausesTransactionCommit();

    /**
     * descHere
     * @return boolean
     */
    public function DataDefinitionIgnoredInTransactions();

    /**
     * descHere
     * @param \FrameworkDSW\Database\TResultSetType $Type
     * @return boolean
     */
    public function DeletesAreDetected($Type);

    /**
     * descHere
     * @return boolean
     */
    public function DoesMaxRowSizeIncludeBlobs();

    /**
     * descHere
     * @param \FrameworkDSW\Database\\FrameworkDSW\Database\TPrimitiveParam <T: string> $Catalog
     * @param \FrameworkDSW\Database\\FrameworkDSW\Database\TPrimitiveParam <T: string> $SchemaPattern
     * @param string $TypeNamePattern
     * @param string $AttributeNamePattern
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function GetAttributes($Catalog, $SchemaPattern, $TypeNamePattern, $AttributeNamePattern);

    /**
     * descHere
     * @param \FrameworkDSW\Database\\FrameworkDSW\Database\TPrimitiveParam <T: string> $Catalog
     * @param \FrameworkDSW\Database\\FrameworkDSW\Database\TPrimitiveParam <T: string> $Schema
     * @param string $Table
     * @param \FrameworkDSW\Database\TBestRowIdentifierScope $Scope
     * @param boolean $Nullable
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function GetBestRowIdentifier($Catalog, $Schema, $Table, $Scope, $Nullable);

    /**
     * descHere
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function getCatalogs();

    /**
     * descHere
     * @return string
     */
    public function getCatalogSeparator();

    /**
     * descHere
     * @return string
     */
    public function getCatalogTerm();

    /**
     * descHere
     * @param \FrameworkDSW\Database\\FrameworkDSW\Database\TPrimitiveParam <T: string> $Catalog
     * @param \FrameworkDSW\Database\\FrameworkDSW\Database\TPrimitiveParam <T: string> $Schema
     * @param string $Table
     * @param string $ColumnNamePattern
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function GetColumnPrivileges($Catalog, $Schema, $Table, $ColumnNamePattern);

    /**
     * descHere
     * @param \FrameworkDSW\Database\TPrimitiveParam <T: string> $Catalog
     * @param \FrameworkDSW\Database\TPrimitiveParam <T: string> $SchemaPattern
     * @param string $TableNamePattern
     * @param string $ColumnNamePattern
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function GetColumns($Catalog, $SchemaPattern, $TableNamePattern, $ColumnNamePattern);

    /**
     * descHere
     * @return IConnection
     */
    public function getConnection();

    /**
     * descHere
     * @param \FrameworkDSW\Database\TPrimitiveParam <T: string> $PrimaryCatalog
     * @param \FrameworkDSW\Database\TPrimitiveParam <T: string> $PrimarySchema
     * @param string $PrimaryTable
     * @param \FrameworkDSW\Database\TPrimitiveParam <T: string> $ForeignCatalog
     * @param \FrameworkDSW\Database\TPrimitiveParam <T: string> $ForeignSchema
     * @param string $ForeignTable
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function GetCrossReference($PrimaryCatalog, $PrimarySchema, $PrimaryTable, $ForeignCatalog, $ForeignSchema, $ForeignTable);

    /**
     * descHere
     * @return string[]
     */
    public function getDateTimeFunctions();

    /**
     * descHere
     * @return string
     */
    public function getDbmsName();

    /**
     * descHere
     * @return \FrameworkDSW\Utilities\TVersion
     */
    public function getDbmsVersion();

    /**
     * descHere
     * @return \FrameworkDSW\Database\TTransactionIsolationLevel
     */
    public function getDefaultTransactionIsolation();

    /**
     * descHere
     * @return string
     */
    public function getDriverName();

    /**
     * descHere
     * @return \FrameworkDSW\Utilities\TVersion
     */
    public function getDriverVersion();

    /**
     * descHere
     * @param string $Catalog
     * @param string $Schema
     * @param string $Table
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function GetExportedKeys($Catalog, $Schema, $Table);

    /**
     * descHere
     * @return string
     */
    public function getExtraNameCharacters();

    /**
     * descHere
     * @return string
     */
    public function getIdentifierQuoteString();

    /**
     * descHere
     * @param string $Catalog
     * @param string $Schema
     * @param string $Table
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function GetImportedKeys($Catalog, $Schema, $Table);

    /**
     * descHere
     * @param string $Catalog
     * @param string $Schema
     * @param string $Table
     * @param boolean $Unique
     * @param boolean $Approximate
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function GetIndexInfo($Catalog, $Schema, $Table, $Unique, $Approximate);

    /**
     * descHere
     * @return integer
     */
    public function getMaxBinaryLiteralLength();

    /**
     * descHere
     * @return integer
     */
    public function getMaxCatalogNameLength();

    /**
     * descHere
     * @return integer
     */
    public function getMaxCharLiteralLength();

    /**
     * descHere
     * @return integer
     */
    public function getMaxColumnNameLength();

    /**
     * descHere
     * @return integer
     */
    public function getMaxColumnsInGroupBy();

    /**
     * descHere
     * @return integer
     */
    public function getMaxColumnsInIndex();

    /**
     * descHere
     * @return integer
     */
    public function getMaxColumnsInOrderBy();

    /**
     * descHere
     * @return integer
     */
    public function getMaxColumnsInSelect();

    /**
     * descHere
     * @return integer
     */
    public function getMaxColumnsInTable();

    /**
     * descHere
     * @return integer
     */
    public function getMaxConnections();

    /**
     * descHere
     * @return integer
     */
    public function getMaxCursorNameLength();

    /**
     * descHere
     * @return integer
     */
    public function getMaxIndexLength();

    /**
     * descHere
     * @return integer
     */
    public function getMaxProcedureNameLength();

    /**
     * descHere
     * @return integer
     */
    public function getMaxRowSize();

    /**
     * descHere
     * @return integer
     */
    public function getMaxSchemaNameLength();

    /**
     * descHere
     * @return integer
     */
    public function getMaxStatementLength();

    /**
     * descHere
     * @return integer
     */
    public function getMaxStatements();

    /**
     * descHere
     * @return integer
     */
    public function getMaxTableNameLength();

    /**
     * descHere
     * @return integer
     */
    public function getMaxTablesInSelect();

    /**
     * descHere
     * @return integer
     */
    public function getMaxUserNameLength();

    /**
     * descHere
     * @return string[]
     */
    public function getNumericFunctions();

    /**
     * descHere
     * @param string $Catalog
     * @param string $Schema
     * @param string $Table
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function GetPrimaryKeys($Catalog, $Schema, $Table);

    /**
     * descHere
     * @param string $Catalog
     * @param string $SchemaPattern
     * @param string $ProcedureNamePattern
     * @param string $ColumnNamePattern
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function GetProcedureColumns($Catalog, $SchemaPattern, $ProcedureNamePattern, $ColumnNamePattern);

    /**
     * descHere
     * @param string $Catalog
     * @param string $SchemaPattern
     * @param string $ProcedureNamePattern
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function GetProcedures($Catalog, $SchemaPattern, $ProcedureNamePattern);

    /**
     * descHere
     * @return string
     */
    public function getProcedureTerm();

    /**
     * descHere
     * @return boolean
     */
    public function getReadOnly();

    /**
     * descHere
     * @return THoldability
     */
    public function getResultSetHoldability();

    /**
     * descHere
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function getSchemas();

    /**
     * descHere
     * @return string
     */
    public function getSchemaTerm();

    /**
     * descHere
     * @return string
     */
    public function getSearchStringEscape();

    /**
     * descHere
     * @return string[]
     */
    public function getSqlKeywords();

    /**
     * descHere
     * @return \FrameworkDSW\Database\TSqlStateType
     */
    public function getSqlStateType();

    /**
     * descHere
     * @return string[]
     */
    public function getStringFunctions();

    /**
     * descHere
     * @param string $Catalog
     * @param string $SchemaPattern
     * @param string $TableNameSchema
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function GetSuperTables($Catalog, $SchemaPattern, $TableNameSchema);

    /**
     * descHere
     * @param string $Catalog
     * @param string $SchemaPattern
     * @param string $TypeNamePattern
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function GetSuperTypes($Catalog, $SchemaPattern, $TypeNamePattern);

    /**
     * descHere
     * @param string $Catalog
     * @param string $SchemaPattern
     * @param string $TableNamePattern
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function GetTablePrivileges($Catalog, $SchemaPattern, $TableNamePattern);

    /**
     * descHere
     * @param string $Catalog
     * @param string $SchemaPattern
     * @param string $TableNamePattern
     * @param string[] Types
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function GetTables($Catalog, $SchemaPattern, $TableNamePattern, $Types);

    /**
     * descHere
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function getTableTypes();

    /**
     * descHere
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function getTypeInfo();

    /**
     * descHere
     * @param string $Catalog
     * @param string $SchemaPattern
     * @param string $TypeNamePattern
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function GetUdts($Catalog, $SchemaPattern, $TypeNamePattern);

    /**
     * descHere
     * @return string
     */
    public function getUrl();

    /**
     * descHere
     * @return string
     */
    public function getUserName();

    /**
     * descHere
     * @param string $Catalog
     * @param string $Schema
     * @param string $Table
     * @return \FrameworkDSW\Database\IResultSet
     */
    public function GetVersionColumns($Catalog, $Schema, $Table);

    /**
     * descHere
     * @param \FrameworkDSW\Database\TResultSetType $Type
     * @return boolean
     */
    public function InsertsAreDetected($Type);

    /**
     * descHere
     * @return boolean
     */
    public function LocatorsUpdateCopy();

    /**
     * descHere
     * @return boolean
     */
    public function NullPlusNonNullIsNull();

    /**
     * descHere
     * @return boolean
     */
    public function NullsAreSortedAtEnd();

    /**
     * descHere
     * @return boolean
     */
    public function NullsAreSortedAtStart();

    /**
     * descHere
     * @return boolean
     */
    public function NullsAreSortedHigh();

    /**
     * descHere
     * @return boolean
     */
    public function NullsAreSortedLow();

    /**
     * descHere
     * @param \FrameworkDSW\Database\TResultSetType $Type
     * @return boolean
     */
    public function OthersDeletesAreVisible($Type);

    /**
     * descHere
     * @param \FrameworkDSW\Database\TResultSetType $Type
     * @return boolean
     */
    public function OthersInsertsAreVisible($Type);

    /**
     * descHere
     * @param \FrameworkDSW\Database\TResultSetType $Type
     * @return boolean
     */
    public function OthersUpdatesAreVisible($Type);

    /**
     * descHere
     * @param \FrameworkDSW\Database\TResultSetType $Type
     * @return boolean
     */
    public function OwnDeletesAreVisible($Type);

    /**
     * descHere
     * @param \FrameworkDSW\Database\TResultSetType $Type
     * @return boolean
     */
    public function OwnInsertsAreVisible($Type);

    /**
     * descHere
     * @param \FrameworkDSW\Database\TResultSetType $Type
     * @return boolean
     */
    public function OwnUpdatesAreVisible($Type);

    /**
     * descHere
     * @return boolean
     */
    public function StoresLowerCaseIdentifiers();

    /**
     * descHere
     * @return boolean
     */
    public function StoresLowerCaseQuotedIdentifiers();

    /**
     * descHere
     * @return boolean
     */
    public function StoresMixedCaseIdentifiers();

    /**
     * descHere
     * @return boolean
     */
    public function StoresMixedCaseQuotedIdentifiers();

    /**
     * descHere
     * @return boolean
     */
    public function StoresUpperCaseIdentifiers();

    /**
     * descHere
     * @return boolean
     */
    public function StoresUpperCaseQuotedIdentifies();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsAlterTableWithAddColumn();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsAlterTableWithDropColumn();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsAnsi92EntryLevelSql();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsAnsi92FullSql();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsAnsi92IntermediateSql();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsBatchUpdates();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsCatalogsInDataManipulation();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsCatalogsInIndexDefinitions();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsCatalogsInPrivilegeDefinitions();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsCatalogsInProcedureCalls();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsCatalogsInTableDefinitions();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsColumnAliasing();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsCoreSqlGrammar();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsCorrelatedSubqueriers();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsDataDefinitionAndDataManipulationTransactions();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsDataManipulationTransactionsOnly();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsDifferentTableCorrelationName();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsExpressionsInOrderBy();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsExtendedSqlGrammar();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsFullOuterJoins();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsGetGeneratedKeys();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsGroupBy();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsGroupByBeyondSelect();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsGroupByUnrelated();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsIntegrityEnhancementFacility();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsLimitedOuterJoins();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsLinkEscapeClause();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsMinimumSqlGrammar();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsMixedCaseIdentifiers();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsMixedCaseQuotedIndentifiers();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsMultipleOpenResults();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsMultipleResultSets();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsMultipleTransaction();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsNamedParameters();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsNonNullableColumns();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsOpenCursorsAcrossCommit();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsOpenCursorsAcrossRollback();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsOpenStatementsAcrossCommit();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsOpenStatementsAcrossRollback();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsOrderByUnrelated();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsOuterJoins();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsPositionedDelete();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsPositionedUpdate();

    /**
     * descHere
     * @param \FrameworkDSW\Database\TConcurrencyType $Concurrency
     * @param \FrameworkDSW\Database\TResultSetType $Type
     * @return boolean
     */
    public function SupportsResultSetConcurrency($Concurrency, $Type);

    /**
     * descHere
     * @return boolean
     */
    public function SupportsResultSetHoldability();

    /**
     * descHere
     * @param \FrameworkDSW\Database\TResultSetType $Type
     * @return boolean
     */
    public function SupportsResultSetType($Type);

    /**
     * descHere
     * @return boolean
     */
    public function SupportsSavepoints();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsSchemaInProcedureCalls();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsSchemasInDataManipulation();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsSchemasInIndexDefinitions();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsSchemasInPrivilegeDefinitions();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsSchemasInTableDefinitions();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsSelectForUpdate();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsStatementPooling();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsStoredProcedures();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsSubqueriersInQuantifieds();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsSubqueriesInComparisons();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsSubqueriesInExists();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsSubqueriesInIns();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsTableCorrelationNames();

    /**
     * descHere
     * @param \FrameworkDSW\Database\TTransactionIsolationLevel $Level
     * @return boolean
     */
    public function SupportsTransactionIsolationLevel($Level);

    /**
     * descHere
     * @return boolean
     */
    public function SupportsTransactions();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsUnion();

    /**
     * descHere
     * @return boolean
     */
    public function SupportsUnionAll();

    /**
     * descHere
     * @param \FrameworkDSW\Database\TResultSetType $Type
     * @return boolean
     */
    public function UpdatesAreDetected($Type);

    /**
     * descHere
     * @return boolean
     */
    public function UsesLocalFiles();

    /**
     * descHere
     * @return string
     */
    public function UsesLocalFilesPerTable();

}

/**
 * \FrameworkDSW\Database\EDatabaseException
 * @author 许子健
 */
class EDatabaseException extends EException {
}

/**
 *
 * @author 许子健
 */
class EFailedToConnectDb extends EDatabaseException {
    /**
     * @var string
     */
    private $FUrl = '';

    /**
     * @param string $Message
     * @param \FrameworkDSW\System\EException $Previous
     * @param string $Url
     */
    public function __construct($Message, $Previous = null, $Url) {
        parent::__construct($Message, $Previous);

        TType::String($Message);
        TType::Object($Previous, EException::class);
        TType::String($Url);

        $this->FUrl = $Url;
    }

    /**
     * @return string
     */
    public function getUrl() {
        return $this->FUrl;
    }
}

/**
 * @author 许子健
 */
class EInsufficientProperties extends EDatabaseException {
    /**
     * @var string
     */
    const CMsg = 'The following fields are required: ';
}

/**
 *
 * @author 许子健
 */
class EDisconnected extends EDatabaseException {
}

/**
 *
 * Enter description here ...
 * @author 许子健
 */
class EEmptyCommand extends EDatabaseException {
}

/**
 * \FrameworkDSW\Database\EFailedToGetDbPropertyInfo
 * @author 许子健
 */
class EFailedToGetDbPropertyInfo extends EDatabaseException {
    /**
     * @var string
     */
    private $FUrl = '';

    /**
     * descHere
     * @param string $Message
     * @param \FrameworkDSW\System\EException $Previous
     * @param string $Url
     */
    public function __construct($Message, $Previous = null, $Url) {
        parent::__construct($Message, $Previous);
        TType::String($Message);
        TType::Object($Previous, EException::class);
        TType::String($Url);

        $this->FUrl = $Url;
    }

    /**
     * descHere
     * @return string
     */
    public function getUrl() {
        return $this->FUrl;
    }
}

/**
 *
 * @author 许子健
 */
class EUnsupportedDbFeature extends EDatabaseException {
}

/**
 *
 * Enter description here ...
 * @author 许子健
 */
class EIllegalSavepointIdentifier extends EDatabaseException {
}

/**
 *
 * Enter description here ...
 * @author 许子健
 */
class EUnableToUpdateNonSingleTableResultSet extends EDatabaseException {
}

/**
 *
 * Enter description here ...
 * @author 许子健
 */
class EResultSetIsNotUpdatable extends EDatabaseException {
}

/**
 *
 * Enter description here ...
 * @author 许子健
 */
class ERowHasBeenDeleted extends EDatabaseException {
}

/**
 *
 * Enter description here ...
 * @author 许子健
 */
class ENothingToUpdate extends EDatabaseException {
}

/**
 *
 * Enter description here ...
 * @author 许子健
 */
class EInvalidRowId extends EDatabaseException {
}

/**
 *
 * Enter description here ...
 * @author 许子健
 */
class EInvalidColumnName extends EDatabaseException {
}

/**
 *
 * Enter description here ...
 * @author 许子健
 */
class ECurrentRowIsInsertRow extends EDatabaseException {
}

/**
 *
 * Enter description here ...
 * @author 许子健
 */
class EFailedToGetFetchSize extends EDatabaseException {
}

/**
 * \FrameworkDSW\Database\EDatabaseWarning
 * @author 许子健
 */
class EDatabaseWarning extends EDatabaseException {
    /**
     *
     * @var \FrameworkDSW\Database\IDatabaseWarningContext
     */
    private $FContext = null;
    /**
     *
     * @var \FrameworkDSW\Database\EDatabaseWarning
     */
    private $FNextWarning = null;

    /**
     *
     * @param \FrameworkDSW\Database\IDatabaseWarningContext $Context
     */
    public function __construct($Context) {
        parent::__construct();
        TType::Object($Context, IDatabaseWarningContext::class);

        $this->FContext = $Context;
    }

    /**
     * @return string
     */
    public function getSqlState() {
        return $this->FContext->getSqlState();
    }

    /**
     * @return string
     */
    public function getErrorCode() {
        return $this->FContext->getErrorCode();
    }

    /**
     * @return \FrameworkDSW\Database\EDatabaseWarning
     */
    public function getNextWarning() {
        return $this->FNextWarning;
    }

    /**
     *
     * @param \FrameworkDSW\Database\EDatabaseWarning $Value
     */
    public function setNextWarning($Value) {
        TType::Object($Value, EDatabaseWarning::class);
        $this->FNextWarning = $Value;
    }

    /**
     *
     * Enter description here ...
     * @return \FrameworkDSW\Database\IDatabaseWarningContext
     */
    public function getWarningContext() {
        return $this->FContext;
    }

}

/**
 *
 * @author  许子健
 */
class ECommitFailed extends EDatabaseWarning {
}

/**
 *
 * @author  许子健
 */
class ECreateSavepointFailed extends EDatabaseWarning {
}

/**
 *
 * @author  许子健
 */
class ERollbackFailed extends EDatabaseWarning {
}

/**
 *
 * @author  许子健
 */
class EExecuteFailed extends EDatabaseWarning {
}

/**
 *
 * Enter description here ...
 * @author  许子健
 */
class EFetchAsScalarFailed extends EDatabaseWarning {
}

/**
 *
 * Enter description here ...
 * @author  许子健
 */
class ESetCommandFailed extends EDatabaseWarning {
}

/**
 *
 * Enter description here ...
 * @author  许子健
 */
class EFetchRowFailed extends EDatabaseWarning {
}

/**
 *
 * Enter description here ...
 * @author  许子健
 *
 */
class ENoMoreResultSet extends EDatabaseWarning {
}

/**
 *
 * Enter description here ...
 * @author  许子健
 *
 */
class EFetchNextResultSetFailed extends EDatabaseWarning {
}

/**
 * \FrameworkDSW\Database\TConcurrencyType
 * @author 许子健
 */
final class TConcurrencyType extends TEnum {
    /**
     * @var integer
     */
    const eReadOnly = 0;
    /**
     * @var integer
     */
    const eUpdatable = 1;
}

/**
 * \FrameworkDSW\Database\TCurrentResultOption
 * @author 许子健
 */
final class TCurrentResultOption extends TEnum {
    /**
     * @var integer
     */
    const eCloseCurrentResult = 0;
    /**
     * @var integer
     */
    const eKeepCurrentResult = 1;
    /**
     * @var integer
     */
    const eCloseAllResults = 2;
}

/**
 * \FrameworkDSW\Database\TDriverPropertyInfo
 * @author 许子健
 */
final class TDriverPropertyInfo extends TRecord {
    /**
     * @var string[]
     */
    public $Choices = [];
    /**
     * @var string
     */
    public $Description;
    /**
     * @var string
     */
    public $Name;
    /**
     * @var boolean
     */
    public $Required;
    /**
     * @var string
     */
    public $Value;
}

/**
 * \FrameworkDSW\Database\TFetchDirection
 * @author 许子健
 */
final class TFetchDirection extends TEnum {
    /**
     * @var integer
     */
    const eReverse = 1;
    /**
     * @var integer
     */
    const eForward = 0;
    /**
     * @var integer
     */
    const eUnknown = 2;
}

/**
 * \FrameworkDSW\Database\THoldability
 * @author 许子健
 */
final class THoldability extends TEnum {
    /**
     * @var integer
     */
    const eHoldCursorsOverCommit = 0;
    /**
     * @var integer
     */
    const eCloseCursorsAtCommit = 1;
}

/**
 * \FrameworkDSW\Database\TResultSetType
 * @author 许子健
 */
final class TResultSetType extends TEnum {
    /**
     * @var integer
     */
    const eForwardOnly = 0;
    /**
     * @var integer
     */
    const eScrollInsensitive = 1;
    /**
     * @var integer
     */
    const eScrollSensitive = 2;
}

/**
 * \FrameworkDSW\Database\TTransactionIsolationLevel
 * @author 许子健
 */
final class TTransactionIsolationLevel extends TEnum {
    /**
     * @var integer
     */
    const eNone = 0;
    /**
     * @var integer
     */
    const eReadUncommitted = 1;
    /**
     * @var integer
     */
    const eReadCommitted = 2;
    /**
     * @var integer
     */
    const eRepeatableRead = 3;
    /**
     * @var integer
     */
    const eSerializable = 4;
}

/**
 * @package FrameworkDSW\Database
 * @author 许子健
 */
class TBestRowIdentifierScope extends TEnum {
    /**
     * @var integer
     */
    const eTemporary = 0;
    /**
     * @var integer
     */
    const eTransaction = 1;
    /**
     * @var integer
     */
    const eSession = 2;
}

/**
 * \FrameworkDSW\Database\TSavepoint
 * @author 许子健
 */
class TSavepoint extends TObject implements ISavepoint {
    /**
     * @var integer
     */
    private static $FNextId = 0;
    /**
     * @var integer
     */
    private $FId = -1;
    /**
     * @var string
     */
    private $FName = '';

    /**
     * descHere
     * @param string $Name
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
     * @return integer
     */
    public function getId() {
        if ($this->FId != -1) {
            return $this->FId;
        }
        else {
            throw new EIllegalSavepointIdentifier(sprintf('No such savepoint ID: use name "%s" instead.', $this->FName));
        }
    }

    /**
     * descHere
     * @throws EIllegalSavepointIdentifier
     * @return   string
     */
    public function getName() {
        if ($this->FId == -1) {
            return $this->FName;
        }
        else {
            throw new EIllegalSavepointIdentifier(sprintf('No such savepoint name: use ID "%s" instead.', $this->FId));
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
            return 'Svpt' . (string)$this->FId;
        }
    }
}

/**
 * Class TDriverManager
 * @package FrameworkDSW\Database
 */
class TDriverManager extends TObject {
    /**
     * @var \FrameworkDSW\Containers\TLinkedList <T: \FrameworkDSW\Database\IDriver>
     */
    private static $FDrivers = null;

    /**
     * descHere
     * @param string $Url
     * @param \FrameworkDSW\Containers\IMap $Properties <K: string, V: string>
     * @return \FrameworkDSW\Database\IConnection
     */
    public static function Connect($Url, $Properties) {
        TType::String($Url);
        TType::Object($Properties, [IMap::class => ['K' => Framework::String, 'V' => Framework::String]]);
        return TDriverManager::GetDriver($Url)->Connect($Url, $Properties);
    }

    /**
     * @param string $Url
     * @throws \FrameworkDSW\System\EException
     * @return \FrameworkDSW\Database\IDriver
     */
    public static function GetDriver($Url) {
        TType::String($Url);
        TDriverManager::EnsureDriversList();
        foreach (TDriverManager::$FDrivers as $mDriver) {
            /** @var IDriver $mDriver */
            if ($mDriver->ValidateUrl($Url)) {
                return $mDriver;
            }
        }
        throw new EDatabaseException(sprintf('No such driver: for URL "%s".', $Url));
    }

    /**
     *
     */
    private static function EnsureDriversList() {
        if (TDriverManager::$FDrivers === null) {
            TLinkedList::PrepareGeneric(['T' => IDriver::class]);
            TDriverManager::$FDrivers = new TLinkedList(true);
        }
    }

    /**
     * @param \FrameworkDSW\Database\IDriver $Driver
     */
    public static function RegisterDriver($Driver) {
        TType::Object($Driver, IDriver::class);
        TDriverManager::EnsureDriversList();
        if ($Driver !== null && !TDriverManager::$FDrivers->Contains($Driver)) {
            TDriverManager::$FDrivers->Add($Driver);
        }
    }

    /**
     * @param \FrameworkDSW\Database\IDriver $Driver
     * @throws EDatabaseException
     */
    public static function UnregisterDriver($Driver) {
        TType::Object($Driver, IDriver::class);
        TDriverManager::EnsureDriversList();
        try {
            TDriverManager::$FDrivers->Remove($Driver);
        }
        catch (ENoSuchElement $Ex) {
            throw new EDatabaseException(sprintf('No such driver.'));
        }
        finally {
            if (TDriverManager::$FDrivers->IsEmpty()) {
                Framework::Free(TDriverManager::$FDrivers);
            }
        }
    }

    /**
     * @return \FrameworkDSW\Containers\IList <T: \FrameworkDSW\Database\IDriver>
     */
    public static function getDrivers() {
        return TDriverManager::$FDrivers;
    }
}

/**
 * Class TInMemoryResultSet
 * @package FrameworkDSW\Database
 */
class TInMemoryResultSet extends TObject implements IResultSet {

    /**
     * @var \FrameworkDSW\Containers\TMap[] <K: string, V: mixed>
     */
    private $FData = [];

    /**
     * @var integer
     */
    private $FCurrentRowId = 0;
    /**
     * @var \FrameworkDSW\Containers\TMap <K: string, V: mixed>
     */
    private $FCurrentRow = null;
    /**
     * @var boolean
     */
    private $FValid = false;
    /**
     * @var \FrameworkDSW\Database\IRow
     */
    private $FRow = null;

    /**
     * @var \FrameworkDSW\Database\IStatement
     */
    private $FStatement = null;

    /**
     * @param $RowId
     * @throws \FrameworkDSW\Database\EInvalidRowId
     * @throws \FrameworkDSW\Utilities\EInvalidIntCasting
     */
    private function EnsureRowId($RowId) {
        if ($RowId < -1 || $RowId >= count($this->FData)) {
            throw new EInvalidRowId(sprintf('No such row: at index %s.', $RowId));
        }
    }

    /**
     *
     * @param \FrameworkDSW\Database\IStatement $Statement
     * @param \FrameworkDSW\Containers\TMap[] $Data <K: string, V: \FrameworkDSW\System\IInterface>
     * @param \FrameworkDSW\Containers\TMap $Meta <K: string, V: \FrameworkDSW\Reflection\TClass<T: ?>>
     * @throws EInvalidParameter
     */
    public function __construct($Statement, $Data, $Meta) {
        parent::__construct();
        TType::Object($Statement, IStatement::class);
        TType::Type($Data, [TMap::class . '[]' => ['K' => Framework::String, 'V' => IInterface::class]]);
        TType::Type($Meta, [TMap::class => ['K' => Framework::String, 'V' => [TClass::class => ['T' => null]]]]);

        $this->FStatement = $Statement;
        $this->FData      = $Data;
        if (count($Data) > 0) {
            $this->FCurrentRow = $this->FData[$this->FCurrentRowId];
        }
        $this->FRow = new TInMemoryRow($this, TConcurrencyType::eUpdatable(), $this->FCurrentRow, $Meta);
    }

    /**
     *
     */
    public function Destroy() {
        Framework::Free($this->FRow);
        foreach ($this->FData as $mRow) {
            Framework::Free($mRow);
        }
        parent::Destroy();
    }

    /**
     * Remove
     */
    public function Remove() {
        Framework::Free($this->FData[$this->FCurrentRowId]);
        unset($this->FData[$this->FCurrentRowId]);
        $this->FData = array_values($this->FData);
        $this->Refresh();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return \FrameworkDSW\Database\IRow
     */
    public function current() {
        $this->FCurrentRow = $this->FData[$this->FCurrentRowId];
        return $this->FRow;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     */
    public function next() {
        ++$this->FCurrentRowId;
        if ($this->FCurrentRowId >= count($this->FData)) {
            $this->FValid = false;
        }
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return integer
     */
    public function key() {
        return $this->FCurrentRowId;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid() {
        return $this->FValid;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     */
    public function rewind() {
        $this->FValid        = (count($this->FData) > 0);
        $this->FCurrentRowId = 0;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param integer $offset
     * @return boolean
     */
    public function offsetExists($offset) {
        TType::Int($offset);
        return $offset >= 0 && $offset < count($this->FData);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param integer $offset
     * @return \FrameworkDSW\Database\IRow Can return all value types.
     * @throws \FrameworkDSW\Database\EInvalidRowId
     * @throws \FrameworkDSW\Utilities\EInvalidIntCasting
     */
    public function offsetGet($offset) {
        TType::Int($offset);

        $this->FetchTo($offset);
        return $this->FRow;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param integer $offset
     * @param \FrameworkDSW\Database\IRow $value
     */
    public function offsetSet($offset, $value) {
        TType::Int($offset);
        TType::Object($value, IRow::class);
        $this->FetchTo($offset);
        foreach ($value as $mColumn => $mValue) {
            $this->FData[$offset][$mColumn] = $mValue;
        }
    }

    /**
     * @param integer $offset
     */
    public function offsetUnset($offset) {
        TType::Int($offset);
        $this->FetchTo($offset);
        $this->FRow->Delete();
    }

    /**
     * descHere
     * @param integer $RowId
     * @return \FrameworkDSW\Database\IRow
     */
    public function FetchAbsolute($RowId) {
        TType::Int($RowId);
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
        $this->FetchTo($this->FCurrentRowId + $Offset);
        return $this->FRow;
    }

    /**
     * descHere
     * @return integer
     */
    public function getCount() {
        return count($this->FData);
    }

    /**
     * descHere
     * @return string
     * @throws EUnsupportedDbFeature
     */
    public function getCursorName() {
        throw new EUnsupportedDbFeature(sprintf('Get cursor name is not supported.'));
    }

    /**
     * descHere
     * @return \FrameworkDSW\Database\TFetchDirection
     * @throws EUnsupportedDbFeature
     */
    public function getFetchDirection() {
        throw new EUnsupportedDbFeature(sprintf('Fetch direction is not supported.'));
    }

    /**
     * descHere
     * @return integer
     * @throws EUnsupportedDbFeature
     */
    public function getFetchSize() {
        throw new EUnsupportedDbFeature(sprintf('Fetch direction is not supported.'));
    }

    /**
     * descHere
     * @return \FrameworkDSW\Database\IRow
     * @throws EUnsupportedDbFeature
     */
    public function getInsertRow() {
        throw new EUnsupportedDbFeature(sprintf('Insert row is not supported.'));
    }

    /**
     * descHere
     * @return boolean
     */
    public function getIsEmpty() {
        return count($this->FData) === 0;
    }

    /**
     * descHere
     * @return \FrameworkDSW\Database\IResultMetaData
     */
    public function getMetaData() {
        // TODO: Implement getMetaData() method.
    }

    /**
     *
     * Enter description here ...
     * @return \FrameworkDSW\Database\TResultSetType
     */
    public function getType() {
        return TResultSetType::eScrollInsensitive();
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
     * @param \FrameworkDSW\Database\TFetchDirection $Value
     * @throws EUnsupportedDbFeature
     */
    public function setFetchDirection($Value) {
        throw new EUnsupportedDbFeature(sprintf('Fetch direction is not supported.'));
    }

    /**
     * descHere
     * @param integer $Value
     * @throws EUnsupportedDbFeature
     */
    public function setFetchSize($Value) {
        throw new EUnsupportedDbFeature(sprintf('Fetch size is not supported.'));
    }

    /**
     * descHere
     */
    public function Refresh() {
        $this->FCurrentRowId = -1;
    }

    /**
     * @param integer $offset
     * @throws EInvalidRowId
     */
    private function FetchTo($offset) {
        $this->EnsureRowId($offset);
        $this->FCurrentRowId = $offset;
        $this->FCurrentRow   = $this->FData[$offset];
    }
}

/**
 * Class TInMemoryRow
 * @package FrameworkDSW\Database
 */
class TInMemoryRow extends TObject implements IRow {
    /**
     * @var \FrameworkDSW\Database\TInMemoryResultSet
     */
    private $FResultSet = null;
    /**
     * @var \FrameworkDSW\Database\TConcurrencyType
     */
    private $FConcurrencyType = null;
    /**
     * @var \FrameworkDSW\Containers\TMap <K: string, V: \FrameworkDSW\System\IInterface>
     */
    private $FCurrentRow = null;
    /**
     * @var \FrameworkDSW\Containers\TMap <K: string, V: \FrameworkDSW\Reflection\TClass<T: ?>>
     */
    private $FMeta = [];
    /**
     * @var boolean
     */
    private $FWasDeleted = false;
    /**
     * @var boolean
     */
    private $FWasUpdated = false;
    /**
     * @var \FrameworkDSW\Containers\TMap <K: string, V: \FrameworkDSW\System\IInterface>
     */
    private $FPendingUpdateRow = null;

    /**
     * @param \FrameworkDSW\Database\IResultSet $ResultSet
     * @param \FrameworkDSW\Database\TConcurrencyType $ConcurrencyType
     * @param \FrameworkDSW\Containers\TMap $CurrentRow <K: string, V: \FrameworkDSW\System\IInterface>
     * @param \FrameworkDSW\Containers\TMap $Meta <K: string, V: \FrameworkDSW\Reflection\TClass<T: ?>>
     */
    public function __construct($ResultSet, $ConcurrencyType, &$CurrentRow, $Meta) {
        parent::__construct();
        TType::Object($ResultSet, IResultSet::class);
        TType::Object($ConcurrencyType, TConcurrencyType::class);
        TType::Type($CurrentRow, [TMap::class => ['K' => Framework::String, 'V' => IInterface::class]]);
        TType::Type($Meta, [TMap::class => ['K' => Framework::String, 'V' => [TClass::class => ['T' => null]]]]);

        $this->FResultSet       = $ResultSet;
        $this->FConcurrencyType = $ConcurrencyType;
        $this->FCurrentRow      = &$CurrentRow;
        $this->FMeta            = $Meta;
    }

    /**
     *
     */
    public function Destroy() {
        Framework::Free($this->FPendingUpdateRow);
        Framework::Free($this->FMeta);
        parent::Destroy();
    }

    /**
     * @param string $offset
     * @return boolean
     */
    public function offsetExists($offset) {
        TType::String($offset);
        return $this->FMeta->ContainsKey($offset);
    }

    /**
     * @param string $offset
     * @return \FrameworkDSW\System\IInterface
     * @throws EInvalidColumnName
     * @throws \FrameworkDSW\Utilities\EInvalidStringCasting
     */
    public function offsetGet($offset) {
        TType::String($offset);
        if (!$this->offsetExists($offset)) {
            throw new EInvalidColumnName(sprintf('No such column: %s when getting value.', $offset));
        }
        return $this->FCurrentRow[$offset];
    }

    /**
     * @param string $offset
     * @param \FrameworkDSW\System\IInterface $value
     * @throws EInvalidColumnName
     * @throws \FrameworkDSW\Utilities\EInvalidStringCasting
     */
    public function offsetSet($offset, $value) {
        TType::String($offset);
        if (!$this->offsetExists($offset)) {
            throw new EInvalidColumnName(sprintf('No such column: %s when setting value.', $offset));
        }
        if ($this->FPendingUpdateRow === null) {
            TMap::PrepareGeneric(['K' => Framework::String, 'V' => IInterface::class]);
            $this->FPendingUpdateRow = new TMap();
        }
        $this->FPendingUpdateRow[$offset] = $value;
    }

    /**
     * @param string $offset
     */
    public function offsetUnset($offset) {
        TType::String($offset);
        $this->offsetSet($offset, null);
    }

    /**
     * descHere
     */
    public function Delete() {
        $this->FResultSet->Remove();
        $this->FWasDeleted = true;
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
        return THoldability::eHoldCursorsOverCommit();
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
     * @return boolean
     */
    public function getWasDeleted() {
        return $this->FWasDeleted;
    }

    /**
     * descHere
     * @return boolean
     */
    public function getWasUpdated() {
        return $this->FWasUpdated;
    }

    /**
     * descHere
     */
    public function UndoUpdates() {
        Framework::Free($this->FPendingUpdateRow);
    }

    /**
     * descHere
     */
    public function Update() {
        if ($this->FPendingUpdateRow === null) {
            throw new ENothingToUpdate(sprintf('Nothing changed.'));
        }
        $this->FCurrentRow->PutAll($this->FPendingUpdateRow);
        Framework::Free($this->FPendingUpdateRow);
        $this->FWasUpdated = true;
    }
}