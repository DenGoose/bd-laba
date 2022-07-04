<?php

namespace App\DataBase;

use PDO;

class DB
{
	private static ?DB $instance = null;

	private ?PDO $link;

	private string $hostName = '';
	private string $userName = '';
	private string $password = '';
	private string $database = '';

	private function __construct()
	{
		$this->parseFileDataForDb();

		$this->connect();
	}

	/**
	 * Вытаскивает информацию о доступах к БД
	 *
	 * @return void
	 */
	private function parseFileDataForDb(): void
	{
		$arEnv = explode("\n", file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/.env'));

		$arEnv = array_filter($arEnv);

		$env = [];

		foreach ($arEnv as $item)
		{
			$temp = explode('=', $item);

			if ($temp[1])
			{
				$env[trim($temp[0])] = trim($temp[1]);
			}
		}

		$this->hostName = $env['db-host'];
		$this->userName = $env['db-user'];
		$this->password = $env['db-password'];
		$this->database = $env['db-name'];
	}

	/**
	 * Создаёт подключение к БД
	 *
	 * @return void
	 */
	private function connect()
	{
		try
		{
			$this->link = new PDO(
				'mysql:dbname=' . $this->database . ';host=' . $this->hostName,
				$this->userName,
				$this->password
			);
			$this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (\PDOException $e)
		{
			echo 'Подключение не удалось: ' . $e->getMessage();
			die();
		}
	}

	/**
	 * Вызываем класс синглтон
	 *
	 * @return DB|null
	 */
	public static function getInstance(): ?DB
	{
		if (!self::$instance)
		{
			self::$instance = new DB();
		}

		return self::$instance;
	}

	/**
	 * Вызываем объект PDO
	 *
	 * @return PDO|null
	 */
	public function getConnection(): ?PDO
	{
		return $this->link;
	}

	public function ifTableExists($tableName): bool
	{
		$sql = "SELECT IF(COUNT(*)>0, 1, 0) AS 'existence' ";
		$sql .= "FROM `information_schema`.`TABLES` ";
		$sql .= "WHERE 1 AND `TABLE_SCHEMA`='" . $this->database . "' AND `TABLE_NAME`='" . $tableName . "'";

		return $this->getConnection()->query($sql)->fetch()["existence"];
	}
}