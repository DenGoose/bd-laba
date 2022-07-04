<?php

namespace App\Controller;

use App\Controller;
use App\DataBase\ORM\Query;
use App\Tables\UserTable;
use App\ViewManager;
use Silex\Application;

class IndexController extends Controller
{
	public static function init()
	{
		ViewManager::show('header', ['title' => 'Поиск маски для разбиения на подсети']);
		try
		{
			$ob = UserTable::query();
			echo '<pre>' . __FILE__ . ':' . __LINE__ . ':<br>' . print_r($ob->addSelect('ID')->exec()->fetchAll(), true) . '</pre>';
		}catch (\Throwable $e)
		{
			die('<pre>' . __FILE__ . ':' . __LINE__ . ':<br>' . print_r($e->getMessage(), true) . '</pre>');
		}
		ViewManager::show('footer');
	}

	public static function calc()
	{
		try
		{
			$_SESSION['result'] = (new \App\Network\SubNet(
				$_POST['class'],
				$_POST['subnet-count'],
				$_POST['subnet-devices'])
			)->calculate();
			header('Location: /result/');
			die();
		}catch (\Throwable $e)
		{
			$_SESSION['form'] = $_POST;
			$_SESSION['msg'] = $e->getMessage();
			header('Location: /');
			die();
		}
	}
}