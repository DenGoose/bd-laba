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
		ViewManager::show('header', ['title' => 'Лаба 6 БД']);

		$pages = [
			[
				'url' => '/order/',
				'name' => 'Заказы'
			],
			[
				'url' => '/pick-point/',
				'name' => 'Пункты выдачи'
			],
			[
				'url' => '/product-section/',
				'name' => 'Категории товаров'
			],
			[
				'url' => '/product/',
				'name' => 'Товары'
			],
			[
				'url' => '/stock/',
				'name' => 'Склады'
			],
			[
				'url' => '/user/',
				'name' => 'Покупатели'
			]
		];

		ViewManager::show('index', ['result' => $pages]);

		ViewManager::show('footer');
	}
}