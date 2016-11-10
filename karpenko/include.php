<?php
//Регистрируем свои классы
CModule::AddAutoloadClasses(
	'karpenko',
	array(
		'karpenkoCopyElements' => 'classes/general/copy_iblock_element.php',
		'karpenkoIblocControls' => 'classes/general/copy_iblock_controls.php'
	)
);
//Регистрируем свою библиотеку
CJSCore::RegisterExt("iblock_karpenko_tools", Array(
	"js" => "/bitrix/js/karpenko/handlers.js",
	'rel' => array('jquery', 'window', 'popup', 'ajax', 'date'),
));
CJSCore::Init(array("iblock_karpenko_tools"));


