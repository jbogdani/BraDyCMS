<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2013
 * @license			All rights reserved
 * @since			Apr 12, 2013
 */

namespace example;

class DB {
	const DSN = 'mysql:host=localhost;dbname=library';
	const USER = 'library';
	const PASS = 'library';

	static function getConnection() {
		static $conn;
		if ($conn == null) {
			$conn = new \PDO(self::DSN, self::USER, self::PASS);
		}
		return $conn;
	}

	static function fetchRow($sql) {
		foreach(self::query($sql) as $row) {
			$rows[] = $row;
		}
		return $rows[0];
	}

	static function query($sql) {
		$result = self::getConnection()->query($sql);
		if (!$result) {
			throw new \Exception(self::getConnection()->errorInfo());
		}
		return $result;
	}

	static function quote($val) {
		return self::getConnection()->quote($val);
	}
}