<?php
/**
 * Database_Mysql.php
 * @author	ExSystem
 * @version	$Id$
 * @since	separate file since reversion 16
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