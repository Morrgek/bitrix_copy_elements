<?php
/**
 * Created by PhpStorm.
 * User: Юля
 * Date: 12.04.15
 * Time: 23:11
 */
//ласс нашего модуля karpenko как потомок CModule
Class karpenko extends CModule
{
	var $MODULE_ID = "karpenko";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;

	//конструктор
	function karpenko()
	{
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path . "/version.php");

		// определение переменных для вывода информации о модуле в списке модулей Bitrix Framework.
		if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		}

		$this->MODULE_NAME = "Копирование элементов инфоблока";
		$this->MODULE_DESCRIPTION = "Элемент можно переместить или копировать между разными типами инфоблоков.Если у элемента есть созданные свойства, они создаются в инфоблоке, в который переносится элемент и тоже копируются.";
	}

	function InstallFiles($arParams = array())
	{
		if(!is_dir($_SERVER['DOCUMENT_ROOT'].'/ajax/karpenko')) mkdir($_SERVER['DOCUMENT_ROOT'].'/ajax/karpenko') ;
		if(!is_dir($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/karpenko')) mkdir($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/karpenko') ;
		if(!is_dir($_SERVER['DOCUMENT_ROOT'].'/bitrix/js/karpenko')) mkdir($_SERVER['DOCUMENT_ROOT'].'/bitrix/js/karpenko') ;
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/karpenko/install/ajax',
			$_SERVER['DOCUMENT_ROOT'].'/ajax/karpenko', true, true);
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/karpenko/install/admin/karpenko',
			$_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/karpenko', true, true);
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/karpenko/install/js/karpenko',
			$_SERVER['DOCUMENT_ROOT'].'/bitrix/js/karpenko', true, true);

	}

	function UnInstallFile()
	{

		  DeleteDirFilesEx("/ajax/karpenko");
		  DeleteDirFilesEx("/bitrix/admin/karpenko");
		  DeleteDirFilesEx("/bitrix/js/karpenko");
		return true;
	}

	//вызывается при установке модуля из Панели управления
	function DoInstall()
	{
		global $DOCUMENT_ROOT, $APPLICATION;
		//Копируем все необходимые файлы
		$this->InstallFiles();
		//Регистрируем модуль
		RegisterModule("karpenko");
		//Регистрируем события
		RegisterModuleDependences('main', 'OnAdminListDisplay', 'karpenko', 'karpenkoIblocControls', 'OnAdminListDisplayHandlerKarp');
		RegisterModuleDependences('main', 'OnAdminContextMenuShow', 'karpenko', 'karpenkoIblocControls', 'OnAdminContextMenuShowHandlerKarp');
		RegisterModuleDependences('main', 'OnAdminTabControlBegin', 'karpenko', 'karpenkoIblocControls', 'OnAdminTabControlBeginHandlerKarp');
		//Переход на страницу в админке с сообщением об успешной установке
		$APPLICATION->IncludeAdminFile("Установка модуля karpenko (копирование элементов инфоблока)", $DOCUMENT_ROOT . "/bitrix/modules/karpenko/install/step.php");


	}

	//вызывается при деинсталяции модуля из Панели управления
	function DoUninstall()
	{
		global $DOCUMENT_ROOT, $APPLICATION;
		//Удаляем регистрационную запись обработчика событий.
		UnRegisterModuleDependences('main', 'OnAdminListDisplay', 'karpenko', 'karpenkoIblocControls', 'OnAdminListDisplayHandlerKarp');
		UnRegisterModuleDependences('main', 'OnAdminContextMenuShow', 'karpenko', 'karpenkoIblocControls', 'OnAdminContextMenuShowHandlerKarp');
		UnRegisterModuleDependences('main', 'OnAdminTabControlBegin', 'karpenko', 'karpenkoIblocControls', 'OnAdminTabControlBeginHandlerKarp');

		//Удаляем регистрационную записьмодуля.
		UnRegisterModule("karpenko");
		//Удаляем файлы, скопированные при установке
		$this->UnInstallFiles();
		//Переход на страницу в админке с сообщением об успешном удалении
		$APPLICATION->IncludeAdminFile("Деинсталляция модуля  karpenko (копирование элементов инфоблока)", $DOCUMENT_ROOT . "/bitrix/modules/karpenko/install/unstep.php");
	}


}