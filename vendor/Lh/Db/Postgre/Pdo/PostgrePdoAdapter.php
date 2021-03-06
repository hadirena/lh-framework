<?php
/**
 * LH Framework
 *
 * @author Hadi Susanto (hd.susanto@yahoo.com)
 * @copyright 2014
 */

namespace Lh\Db\Postgre\Pdo;

use Exception;
use Lh\Db\Pdo\PdoAdapterBase;
use Lh\Db\Postgre\Builders\PostgreFactory;

/**
 * Class PostgrePdoAdapter
 *
 * @package Lh\Db\Postgre\Pdo
 */
class PostgrePdoAdapter extends PdoAdapterBase {
	/** @var int Postgre SQL default port */
	protected $port = 5432;
	/** @var PostgrePdoPlatform Platform tools for generate server specific issues */
	private $platform;
	/** @var PostgreFactory Singleton factory object */
	private $factory;

	/**
	 * Get adapter name
	 *
	 * @return string
	 */
	public function getName() {
		return "PDO Postgre SQL";
	}

	/**
	 * Used to detect whether current PHP Installation support this adapter or not. If current server don't support requested driver then AdapterManager will throw
	 * an exception. This is a safety function to ensure that your web application will work with given adapter(s) configuration.
	 *
	 * @return bool
	 */
	public function isDriverAvailable() {
		return extension_loaded("pdo_pgsql");
	}


	/**
	 * Get specific Factory builder for each driver
	 *
	 * Builder factory used to generate SQL for each driver or database. For maximum portability any query should build based on object style.
	 * This factory will able to create SELECT, INSERT, UPDATE, DELETE statement easily and will protect against query injection.
	 *
	 * @return \Lh\Db\Builders\IFactory
	 */
	public function getBuilderFactory() {
		if ($this->factory === null) {
			$this->factory = new PostgreFactory($this);
		}

		return $this->factory;
	}

	/**
	 * Get Platform object for each driver
	 *
	 * Platform object used in conjunction with Builder Factory to provide portability between database engine. Platform object is responsible for
	 * escaping any value and quoting it.
	 *
	 * @return PostgrePdoPlatform
	 */
	public function getPlatform() {
		if ($this->platform === null) {
			$this->platform = new PostgrePdoPlatform($this);
		}

		return $this->platform;
	}

	/**
	 * Get dsn prefix used in PDO connection
	 *
	 * @return string
	 */
	public function getDsnPrefix() {
		return "pgsql:";
	}

	/**
	 * Prepare additional options for specific driver
	 *
	 * @param array $options
	 *
	 * @return void
	 */
	protected function prepareOptions($options) {
		if (isset($options["port"]) && is_numeric($options["port"])) {
			$this->port = (int)$options["port"];
		}
	}

	/**
	 * Generate DSN string for specific driver. Each driver maybe have their specific key which can be achieved by using options from application config
	 *
	 * @return string
	 */
	protected function generateDsn() {
		$tokens = array();
		$tokens[] = "host=" . $this->server;
		$tokens[] = "port=" . $this->port;
		if ($this->dbName != null) {
			$tokens[] = "dbname=" . $this->dbName;
		}

		return "pgsql:" . implode(";", $tokens);
	}

	/**
	 * Create appropriate exception for specific driver
	 *
	 * @param string    $message
	 * @param int       $code
	 * @param Exception $previousException
	 *
	 * @return PostgrePdoException
	 */
	protected function createException($message, $code = 0, Exception $previousException = null) {
		return new PostgrePdoException($message, $code, $previousException);
	}

	/**
	 * Create specialized PdoQuery object
	 *
	 * @param \PDOStatement $statement
	 * @param int           $fetchMode
	 *
	 * @return PostgrePdoQuery
	 */
	protected function createQuery(\PDOStatement &$statement, &$fetchMode) {
		return new PostgrePdoQuery($statement, $fetchMode);
	}

	/**
	 * Create specialized LhPdoStatement object
	 *
	 * @param \PDOStatement $statement
	 *
	 * @return PostgrePdoStatement
	 */
	protected function createStatement(\PDOStatement &$statement) {
		return new PostgrePdoStatement($statement);
	}

	/**
	 * Get column(s) name from given table
	 *
	 * This will retrieve all column(s) name from a table in a database.
	 * NOTE: This method will be obsoleted when a metadata feature added into LH Framework since it's only retrieve column name instead of column definition
	 *
	 * @param string $tableName
	 *
	 * @throws PostgrePdoException
	 *
	 * @return string[]
	 */
	public function getColumnNames($tableName) {
		$tableName = $this->getPlatform()->quoteValue($tableName);
		$query = $this->query("SELECT * FROM information_schema.columns WHERE table_schema = 'public' AND table_name = $tableName ORDER BY ordinal_position");
		if ($query == null) {
			if ($this->getErrorMessage() != null) {
				$innerException = $this->createException($this->getErrorMessage(), $this->getErrorCode());
			} else {
				$innerException = null;
			}

			throw $this->createException("Failed to retrieve column(s) detail using information_schema.columns table. Please check your connection", 0, $innerException);
		}

		$columns = array();
		foreach ($query->fetchAll() as $row) {
			$columns[] = $row["column_name"];
		}

		return $columns;
	}
}

// End of File: PostgrePdoAdapter.php 
