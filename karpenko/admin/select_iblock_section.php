<?php
/**
 * Created by PhpStorm.
 * User: Юля
 * Date: 19.04.2015
 * Time: 14:31
 */
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
CModule::IncludeModule("iblock");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/prolog.php");
IncludeModuleLangFile(__FILE__);?>
    <option value="0">(<?=GetMessage("SELECT_IBLOCK_SECTION")?>)</option>
<?//Получаем список разделов выбранного инфоблока?>
<?$obSection    = CIBlockSection::GetTreeList(array('IBLOCK_ID'=> $_POST["ID"]));?>
<?while($arResult = $obSection->GetNext()):?>
        <option value="<?=$arResult['ID']?>">
            <?for($i=0;$i<=($arResult['DEPTH_LEVEL']-2);$i++):?>
                <?echo "..";?>
            <?endfor?>
            <?=$arResult['NAME']?>
        </option>
<?endwhile?>