<?php
/**
 * LH Framework
 *
 * @author Hadi Susanto (hd.susanto@yahoo.com)
 * @copyright 2014
 */

namespace Lh\Db\MySql\Builders;

use Lh\Db\Builders\IFactory;
use Lh\Db\IAdapter;

/**
 * Class MySqlFactory
 *
 * This factory class should return any class represent SQL statement supported by their native driver.
 *
 * @package Lh\Db\MySql\Builders
 */
class MySqlFactory implements IFactory {
	/** @var IAdapter This adapter will passed to ISql instance */
	private $adapter;

	/**
	 * Create new instance of MySqlFactory
	 *
	 * @param IAdapter $adapter
	 */
	public function __construct(IAdapter $adapter) {
		$this->adapter = $adapter;
	}

	/**
	 * Create object represent SELECT statement
	 *
	 * @param string|string[]|\Lh\Db\Builders\ILiteral|\Lh\Db\Builders\ILiteral[] $columns
	 *
	 * @return MySqlSelect
	 */
	public function select($columns = null) {
		return new MySqlSelect($columns, $this->adapter);
	}

	/**
	 * Create object represent INSERT INTO statement
	 *
	 * @param string $tableName
	 *
	 * @return MySqlInsert
	 */
	public function insert($tableName = null) {
		return new MySqlInsert($tableName, $this->adapter);
	}

	/**
	 * Create object represent UPDATE statement
	 *
	 * @param string $tableName
	 *
	 * @return MySqlUpdate
	 */
	public function update($tableName = null) {
		return new MySqlUpdate($tableName, $this->adapter);
	}

	/**
	 * Create object represent DELETE statement
	 *
	 * @param string $tableName
	 *
	 * @return MySqlDelete
	 */
	public function delete($tableName = null) {
		return new MySqlDelete($tableName, $this->adapter);
	}
}

// End of File: MySqlFactory.php 