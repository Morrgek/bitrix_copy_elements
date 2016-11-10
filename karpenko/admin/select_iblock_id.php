<?
/**
 * Created by PhpStorm.
 * User: Юленька
 * Date: 17.04.2015
 * Time: 23:33
 */
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
CModule::IncludeModule("iblock");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/prolog.php");
IncludeModuleLangFile(__FILE__);?>

    <?$iblocks =  CIBlock::GetList( Array("SORT"=>"ASC"),array("TYPE"=>$_POST["TYPE"]));?>
    <option value="0">(<?=GetMessage("SELECT_IBLOCK_ID")?>)</option>
    <?while($ar_res = $iblocks->Fetch()):?>
        <option value="<?=$ar_res['ID']?>"><?=$ar_res['NAME']?></option>
    <?endwhile?>


