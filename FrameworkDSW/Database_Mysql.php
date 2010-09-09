<?php
/**
 * Database_Mysql.php
 * @author	ExSystem
 * @version	$Id$
 * @since	separate file since reversion 17
 */

require_once 'FrameworkDSW/Database.php';

/**
 * TPdoConnection
 * @author	许子健
 */
class TMysqlConnection extends TAbstractPdoConnection implements IConnection {

    /**
     * descHere
     * @param	TResultSetType	$ResultSetType
     * @param	TConcurrencyType	$ConcurrencyType
     * @return	IStatement
     */
    protected function DoCreateStatement($ResultSetType, $ConcurrencyType) {
    }

    /**
     * descHere
     * @return	THoldability
     */
    protected function DoGetHoldability() {
        return THoldability::eCloseCursorsAtCommit();
    }

    /**
     * descHere
     * @param	TResultSetType	$ResultSetType
     * @param	TConcurrencyType	$ConcurrencyType
     * @return	IStatement
     */
    protected function DoPrepareStatement($ResultSetType, $ConcurrencyType) {
    }

    /**
     * descHere
     * @param	ISavepoint	$Savepoint
     */
    protected function DoRemoveSavepoint($Savepoint) {
        $mId = $Savepoint->getId();
        try {
            $this->FPdo->exec("RELEASE SAVEPOINT {$mId}");
        }
        catch (PDOException $Ex) {
            $this->PushWarning(EUnableToExecute::ClassType(), $Ex);
        }
    }

    /**
     * descHere
     * @param	ISavepoint	$Savepoint
     */
    protected function DoRollback($Savepoint = null) {
        try {
            if ($Savepoint !== null) {
                $mId = $Savepoint->getId();
                $this->FPdo->exec("ROLLBACK {$mId}");
            }
            else {
                $this->FPdo->exec('ROLLBACK');
            }
        }
        catch (PDOException $Ex) {
            $this->PushWarning(EUnableToRollback::ClassType(), $Ex);
        }
    }

    /**
     * descHere
     * @param	THoldability	$Value
     */
    protected function DoSetHoldability($Value) {
        TType::Object($Value, 'THoldability');
        if ($Value == THoldability::eHoldCursorsOverCommit()) {
            throw new EUnsupportedDbFeature(TAbstractPdoConnection::CHoldabilityUnsupported);
        }
    }

    /**
     * descHere
     * @param	TTransactionIsolation	$Value
     */
    protected function DoSetTransactionIsolation($Value) {
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
        $this->FPdo->exec($mSql);
    }

    /**
     * descHere
     * @return	TDatabaseMetaData
     */
    public function getMetaData() {
    }

    /**
     * descHere
     * @return	TTransactionIsolationLevel
     */
    public function getTransactionIsolation() {
        $mLevel = (string) $this->FPdo->query('SELECT @@tx_isolation')->fetchColumn(0);
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

}

/**
 * TMysqlDatabaseMetaData
 * @author	许子健
 */
final class TMysqlDatabaseMetaData implements IDatabaseMetaData {
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