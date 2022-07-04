<?php

namespace App\Controller;

use App\Controller;
use App\Tables\ProductTable;
use App\Tables\SectionTable;
use App\ViewManager;

class ProductController extends Controller
{
	public static function show()
	{
		ViewManager::show('header', ['title' => 'Товары']);

		$result['currentUrl'] = $_SERVER['REQUEST_URI'];
		$result['result'] = [
			'columns' => [
				'ID', 'Название', 'Категория', 'Цена'
			]
		];

		$ob = ProductTable::query()
			->registerRuntimeField('SECTION', [
				'data_class' => SectionTable::class,
				'reference' => [
					'this' => 'SECTION_ID',
					'ref' => 'ID',
				],
				'join_type' => 'inner'
			])
			->addSelect('product.ID', 'PRODUCT_ID')
			->addSelect('product.NAME', 'PRODUCT_NAME')
			->addSelect('SECTION.NAME', 'SECTION_NAME')
			->addSelect('product.PRICE', 'PRODUCT_PRICE');

		$query = $ob->getQuery();
		$users = $ob->exec();

		while ($itm = $users->fetch())
		{
			$result['result']['items'][] = [
				'PRODUCT_ID' => $itm['PRODUCT_ID'],
				'PRODUCT_NAME' => $itm['PRODUCT_NAME'],
				'SECTION_NAME' => $itm['SECTION_NAME'],
				'PRODUCT_PRICE' => $itm['PRODUCT_PRICE']
			];
		}

		$arQuery = [];
		if (isset($_SESSION['dbQuery']) && $_SESSION['dbQuery'])
		{
			$arQuery = $_SESSION['dbQuery'];
			unset($_SESSION['dbQuery']);
		}
		$arQuery[] = $query;
		ViewManager::show('query', ['query' => $arQuery]);
		ViewManager::show('table', $result);
		ViewManager::show('footer');
		return '';
	}

	public static function add()
	{
		ViewManager::show('header', ['title' => 'Добавление товара']);

		$ob = SectionTable::query()
			->addSelect('ID', 'SECTION_ID')
			->addSelect('NAME', 'SECTION_NAME');

		$query = $ob->getQuery();
		$sectionObj = $ob->exec();
		$sections = [];

		while ($itm = $sectionObj->fetch())
		{
			$sections[] = [
				'id' => $itm['SECTION_ID'],
				'name' => $itm['SECTION_NAME']
			];
		}

		$result['result'] = [
			'action' => '/product/add/',
			'items' => [
				[
					'name' => 'Название',
					'code' => 'NAME',
					'type' => 'text',
					'value' => '',
					'list_values' => []
				],
				[
					'name' => 'Категория',
					'code' => 'SECTION',
					'type' => 'list',
					'value' => '',
					'list_values' => $sections
				],
				[
					'name' => 'Цена',
					'code' => 'PRICE',
					'type' => 'text',
					'value' => '',
					'list_values' => []
				],
			],
		];
		ViewManager::show('query', ['query' => [$query]]);
		ViewManager::show('record', $result);

		ViewManager::show('footer');
		return '';
	}

	public static function addAction()
	{

	}

	public static function update()
	{
		ViewManager::show('header', ['title' => 'Заказы']);
		ViewManager::show('footer');
		return '';
	}

	public static function updateAction()
	{

	}

	public static function deleteAction()
	{

	}
}