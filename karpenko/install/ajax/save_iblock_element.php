<?php
/**
 * Created by PhpStorm.
 * User: Юля
 * Date: 18.04.2015
 * Time: 0:36
 */
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
header('Content-Type: application/json');
if (CModule::IncludeModule("karpenko")) ;
{
	if (isset($_POST['ITEMS_ID']))
		$_POST['ITEMS_ID'] = json_decode($_POST['ITEMS_ID'], TRUE);             //Приведём строку json к виду массива


	//Получаем все свойства инфоблока, в который будет произведена вставка элемента
	$arResult["END_IBLOCK_PROPERTY"] = karpenkoCopyElements::GetIblockProperty($_POST["END_IBLOCK_ID"]);

	//Чтобы понять нужно ли создавать дополнительное свойство, найдём все заполненные свойства элемента и сравним их
	// со свойствами инфоблока, в который будет произведена вставка. Если копируется несколько элементов, то нужно найти заполненные свойства,
	// которых нет в инфоблоке, для каждого элемента
	$arResult["START_NOT_NULL_PROPERTY"] = karpenkoCopyElements::GetNonNullProperties($_POST["START_IBLOCK_ID"], $_POST["ITEMS_ID"]);
	$arResult["UNIC_PROPS"] = karpenkoCopyElements::GetUnicProperties($arResult["START_NOT_NULL_PROPERTY"], $arResult["END_IBLOCK_PROPERTY"]);


	foreach ($arResult["UNIC_PROPS"] as $prop)
		karpenkoCopyElements::CreateNewProperty($prop,$_POST["END_IBLOCK_ID"]);

	if($_POST["ACTION"]=="copy") karpenkoCopyElements::CopyIblockElement($_POST["ITEMS_ID"],$_POST["END_IBLOCK_ID"],$_POST["IBLOCK_SECTION"]);
	else karpenkoCopyElements::ReplaceIblockElement($_POST["ITEMS_ID"],$_POST["END_IBLOCK_ID"],$_POST["IBLOCK_SECTION"]);
	echo $_POST["ACTION"];

}
$result["OK"] = "ok";
echo json_encode($result);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");