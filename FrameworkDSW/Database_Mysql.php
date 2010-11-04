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
 * TMysqlDriver
 * @author	许子健
 */
final class TMysqlDriver extends TAbstractPdoDriver implements IDriver {
    /**
     * 
     * Enter description here ...
     * @var	string
     */
    const CInvalidServer = 'Bad form of the server string.';
    
    /**
     * 
     * Enter description here ...
     * @var	PDO
     */
    private $FPdo = null;

    /**
     * (non-PHPdoc)
     * @see TAbstractPdoDriver::ConvertProperties()
     */
    protected function ConvertProperties() {
        parent::ConvertProperties();
        
        if ($this->FProperties->ContainsKey('MaxBufferSize')) {
            $this->FPdoOptions[PDO::MYSQL_ATTR_MAX_BUFFER_SIZE] = (integer) $this->FProperties['MaxBufferSize'];
        }
        //TODO: observe and add other MySQL specific available options. 
        $this->FPdoOptions[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES UTF8';
    }

    /**
     * descHere
     * @return	IConnection
     */
    protected function DoConnect() {
        $mTemp = explode(':', $this->FServer, 2);
        if (count($mTemp) != 2) {
            $mHost = $this->FServer;
        }
        else {
            list ($mHost, $mPort) = $mTemp;
            $mPort = (string) $mPort;
            if ("{$mHost}:{$mPort}" != $this->FServer) {
                throw new EFailedToConnectDb(self::CInvalidServer);
            }
        }
        $this->ConvertProperties();
        try {
            $this->FPdo = new PDO("mysql:dbname={$this->FDbName};host={$this->FServer}", $this->FProperties['Username'], $this->FProperties['Password'], $this->FPdoOptions);
        }
        catch (PDOException $Ex) {
            throw new EFailedToConnectDb(self::CInvalidServer);
        }
        catch (EIndexOutOfBounds $Ex) {
            throw new EInsufficientProperties(EInsufficientProperties::CMsg . 'Username, Password.');
        }
        return new TMysqlConnection($this, $this->FPdo);
    }

    /**
     * descHere
     * @return	TDriverPropertyInfo[]
     */
    protected function DoGetPropertyInfo() {
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
        $mInfo[2]->Choices = array ('True', 'False');
        $mInfo[2]->Description = 'If this value is false, the MySQL connection attempts to disable autocommit so that the connection begins a transaction.';
        $mInfo[2]->Name = 'AutoCommit';
        $mInfo[2]->Required = false;
        if ($this->FProperties->ContainsKey('AutoCommit')) {
            $mInfo[2]->Value = $this->FProperties['AutoCommit'];
        }
        else {
            $mInfo[2]->Value = 'True';
        }
        
        $mInfo[3] = new TDriverPropertyInfo();
        $mInfo[3]->Choices = array ();
        $mInfo[3]->Description = 'Sets the timeout value in seconds for communications with the database.';
        $mInfo[3]->Name = 'Timeout';
        $mInfo[3]->Required = false;
        if ($this->FProperties->ContainsKey('Timeout')) {
            $mInfo[3]->Value = $this->FProperties['Timeout'];
        }
        else {
            $mInfo[3]->Value = '0';
        }
        $mInfo[3]->Value = '0';
        
        $mInfo[4] = new TDriverPropertyInfo();
        $mInfo[4]->Choices = array ();
        $mInfo[4]->Description = 'Setting the prefetch size allows you to balance speed against memory usage for your application. Not all database/driver combinations support setting of the prefetch size. A larger prefetch size results in increased performance at the cost of higher memory usage.';
        $mInfo[4]->Name = 'Prefetch';
        $mInfo[4]->Required = false;
        if ($this->FProperties->ContainsKey('Prefetch')) {
            $mInfo[4]->Value = $this->FProperties['Prefetch'];
        }
        else {
            $mInfo[4]->Value = '0';
        }
        
        $mInfo[5] = new TDriverPropertyInfo();
        $mInfo[5]->Choices = array ('Natrual', 'Upper', 'Lower');
        $mInfo[5]->Description = 'Force column names to a specific case specified.';
        $mInfo[5]->Name = 'Case';
        $mInfo[5]->Required = false;
        if ($this->FProperties->ContainsKey('Case')) {
            $mInfo[5]->Value = $this->FProperties['Case'];
        }
        else {
            $mInfo[5]->Value = 'Natrual';
        }
        
        $mInfo[6] = new TDriverPropertyInfo();
        $mInfo[6]->Choices = array ();
        $mInfo[6]->Description = 'Maximum buffer size, in bytes. Defaults to 1 MiB.';
        $mInfo[6]->Name = 'MaxBufferSize';
        $mInfo[6]->Required = false;
        if ($this->FProperties->ContainsKey('MaxBufferSize')) {
            $mInfo[6]->Value = $this->FProperties['MaxBufferSize'];
        }
        else {
            $mInfo[6]->Value = '1048576';
        }
        
        return $mInfo;
    }

    /**
     * descHere
     * @return	TVersion
     */
    protected function DoGetVersion() {
        if ($this->FPdo === null) {
            throw new EDisconnected();
        }
        $mVer = new TVersion();
        $mDummy = '';
        sscanf($this->FPdo->getAttribute(PDO::ATTR_CLIENT_VERSION), 'mysqlnd %d.%d.%d-dev - %s - $Revision: %d $', $mVer->MajorVersion, $mVer->MinorVersion, $mVer->Build, $mDummy, $mVer->Revision);
        return $mVer;
    }

    /**
     * descHere
     * @return	boolean
     */
    protected function DoValidateUrl() {
        if ($this->FProtocol != 'MySQL') {
            return false;
        }
        return true;
    }
}

/**
 * TMysqlConnection
 * @author	许子健
 */
final class TMysqlConnection extends TAbstractPdoConnection implements IConnection {
    /**
     * 
     * Enter description here ...
     * @var	TMysqlDatabaseMetaData
     */
    private $FMetaData = null;

    /**
     * descHere
     * @param	TResultSetType	$ResultSetType
     * @param	TConcurrencyType	$ConcurrencyType
     * @return	IStatement
     */
    protected function DoCreateStatement($ResultSetType, $ConcurrencyType) { //add extra mysqllink here.
        return new TPdoStatement($this, $this->FPdo, $ResultSetType, $ConcurrencyType);
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
            self::PushWarning(EExecuteFailed::ClassType(), $Ex, $this);
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
            self::PushWarning(ERollbackFailed::ClassType(), $Ex, $this);
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
/**
 * TPdoStatement
 * @author	许子健
 */
final class TPdoStatement implements IStatement {
    /**
     * 
     * Enter description here ...
     * @var	TAbstractPdoConnection
     */
    private $FConnection = null;
    /**
     * 
     * Enter description here ...
     * @var PDO
     */
    private $FPdo = null;
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
     * @var	PDOStatement
     */
    private $FPdoStatement = null;
    /**
     * 
     * Enter description here ...
     * @var string
     */
    private $FCommand = '';
    /**
     * 
     * Enter description here ...
     * @var	IList <T: string>
     */
    private $FCommands = null;

    /**
     * 
     * Enter description here ...
     */
    private function EnsurePdoStatement() {
        if ($this->FPdoStatement === null) {
            throw new EEmptyCommand();
        }
    }

    /**
     * 
     * Enter description here ...
     * @param TAbstractPdoConnection	$Connection
     * @param PDO						$Pdo
     * @param TResultSetType			$ResultSetType
     * @param TConcurrencyType			$ConcurrencyType
     */
    public function __construct($Connection, $Pdo, $ResultSetType, $ConcurrencyType) {
        TType::Object($Connection, 'TAbstractPdoConnection');
        TType::Object($Pdo, 'PDO');
        TType::Object($ResultSetType, 'TResultSetType');
        TType::Object($ConcurrencyType, 'TConcurrencyType');
        
        $this->FConnection = $Connection;
        $this->FPdo = $Pdo;
        $this->FResultSetType = $ResultSetType;
        //eForwardOnly 只能向前滚动row
        //eScrollInsensitive 对其他对象作出的数据修改不敏感
        //eScrollSensitive   ----------------------敏感
        $this->FConcurrencyType = $ConcurrencyType;
    
     //eReadOnly 不能修改数据
    //eUpdatable 可以修改数据 
    }

    /**
     * descHere
     * @param	string	$Command
     * @return	integer
     */
    public function Execute($Command = '') {
        TType::String($Command);
        
        if ($Command != '') {
            $this->FCommand = $Command;
        }
        try {
            $mStmt = $this->FPdo->query($this->FCommand);
            return $mStmt->rowCount();
        }
        catch (PDOException $Ex) {
            TAbstractPdoConnection::PushWarning(EExecuteFailed::ClassType(), $Ex, $this->FConnection);
        }
    }

    /**
     * (non-PHPdoc)
     * @see IStatement::ExecuteCommands()
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
                $mStmt = $this->FPdo->query($mCmd);
                $mRows[] = $mStmt->rowCount();
            }
            $this->FConnection->Commit();
        }
        catch (PDOException $Ex) {
            $this->FConnection->Rollback();
            TAbstractPdoConnection::PushWarning(EExecuteFailed::ClassType(), $Ex, $this->FConnection);
        }
        return $mRows;
    }

    /**
     * descHere
     * @return	IParam <T: ?>
     */
    public function FetchAsScalar() {
        try {
            $mData = $this->FPdoStatement->fetch(PDO::FETCH_COLUMN, PDO::FETCH_ORI_ABS, 0);
            if ($mData === false) {
                return null;
            }
            // $this->FPdoStatement->getColumnMeta(0); is not used because of:
            // http://bugs.php.net/bug.php?id=46508
            TPrimativeParam::PrepareGeneric(array ('T' => 'string'));
            return new TPrimativeParam($mData);
        }
        catch (PDOException $Ex) {
            TAbstractPdoConnection::PushWarning(EFetchAsScalarFailed::ClassType(), $Ex, $this->FConnection);
        }
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
        //TODO: to deal with insensitive. maybe to write back to db after updating result sets.
        $mAttr = array (PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL);
        if ($this->FResultSetType == TResultSetType::eForwardOnly()) {
            $mAttr[PDO::ATTR_CURSOR] = PDO::CURSOR_FWDONLY;
        }
        try {
            $this->FPdoStatement = $this->FPdo->prepare($this->FCommand, $mAttr);
        }
        catch (PDOException $Ex) {
            TAbstractPdoConnection::PushWarning(ESetCommandFailed::ClassType(), $Ex, $this->FConnection);
        }
    }

}

/**
 * TPdoPreparedStatement
 * @author	许子健
 */
class TPdoPreparedStatement extends TPdoStatement implements IPreparedStatement {

    /**
     * descHere
     * @param	string	$Name
     * @param	IParam	$Param <T: ?>
     */
    public function BindParam($Name, $Param) {
    }

    /**
     * descHere
     */
    public function ClearParams() {
    }

}

/**
 * TPdoCallableStatement
 * @author	许子健
 */
class TPdoCallableStatement extends TPdoPreparedStatement implements ICallableStatement {

    /**
     * descHere
     * @param	string	$Name
     * @return	IParam <T: ?>
     */
    public function GetParam($Name) {
    }

}