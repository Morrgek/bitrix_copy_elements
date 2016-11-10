<?php
/**
 * Created by PhpStorm.
 * User: Юля
 * Date: 17.04.2015
 * Time: 23:12
 */
?>
<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
CModule::IncludeModule("iblock");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/prolog.php");
IncludeModuleLangFile(__FILE__);
?>
<?$arResult=array();?>
<?//Составляем список типов инфоблоков
$db_iblock_type = CIBlockType::GetList();
while($ar_iblock_type = $db_iblock_type->Fetch())
{
    $arResult["IBLOCK_TYPES"][$ar_iblock_type["ID"]]=$ar_iblock_type;
    if($arIBType = CIBlockType::GetByIDLang($ar_iblock_type["ID"], LANG))
        $arResult["IBLOCK_TYPES"][$ar_iblock_type["ID"]]["NAME"]=$arIBType["NAME"];
}
?>
<form   action=""  name="form_select_iblock" id="form_select_iblock"  method="POST">
    <input name="action"  type="hidden" value="">
    <input name="elem_id"  type="hidden" value="">
    <input name="start_iblock_id"  type="hidden" value="">
    <div class="settings-form">
        <div class="adm-filter-item-center">
            <div class="adm-filter-alignment">
                <div class="adm-filter-box-sizing">
                    <span class="adm-select-wrap">
                        <?//Выпадающий список типов инфоблоков?>
                        <select name="filter_type" id="filter_iblock_type"  class="adm-select">
                            <option value="0">(<?=GetMessage("SELECT_IBLOCK_TYPE")?>)</option>
                            <?foreach($arResult["IBLOCK_TYPES"] as $type):?>
                                <option value="<?=$type["ID"]?>"><?=$type["NAME"]?> [<?=$type["ID"]?>]</option>
                            <?endforeach?>
                        </select>
                    </span>
                    <span class="adm-select-wrap">
                        <?//Выпадающий список инфоблоков выбранного типа?>
                        <select name="filter_iblock_id" id="filter_iblock_id" class="adm-select">
                            <option value="0">(<?=GetMessage("SELECT_IBLOCK")?>)</option>
                        </select>
                    </span>
                     <span class="adm-select-wrap">
                         <?//Выпадающий список разделов всех уровней выбранного инфоблока?>
                          <select name="filter_iblock_section_id" id="filter_iblock_section_id" class="adm-select">
                              <option value="0">(<?=GetMessage("SELECT_IBLOCK_SECTION")?>)</option>
                          </select>
                     </span>
                </div>
            </div>
        </div>
    </div>
</form>

