<?php
/**
 * Created by PhpStorm.
 * User: Юля
 * Date: 17.04.2015
 * Time: 20:26
 * Класс для работы с элементами и инфоблоками
 */
CModule::IncludeModule("iblock");

class karpenkoCopyElements
{

	//Функция возвращает список всех свойств инфоблока
	public static function GetIblockProperty($iblock_id)
	{
		$rsProperty = CIBlockProperty::GetList(
			array(),
			array("IBLOCK_ID" => intval($iblock_id))
		);
		$properties = array();
		while ($property = $rsProperty->Fetch())
			$properties[$property["ID"]] = $property;                                //Запомним все свойтсва в удобном виде
		return $properties;
	}

	//Функция получает все свойства копируемого элемента, значения которых не пустые
	public static function GetNonNullProperties($iblock_id, $elem_ids)
	{
		$arSelect = Array();
		$properties = array();
		$arFilter = Array("IBLOCK_ID" => IntVal($iblock_id), "ID" => $elem_ids);
		$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
		while ($ob = $res->GetNextElement())
		{
			$arProps = $ob->GetProperties();
			//Собираем значения свойств
			foreach ($arProps as $ID => $prop)
				if (!empty($prop["VALUE"]))
					$properties[$prop["ID"]] = $prop;
		}
		//Получаем варианты значений свойства (список)
		foreach ($properties as $ID => $prop) {
			$db_enum_list = CIBlockProperty::GetPropertyEnum($ID, Array(), Array("IBLOCK_ID" => $iblock_id));
			while ($ar_enum_list = $db_enum_list->GetNext())
				$properties[$ID]["ENUM"][] = $ar_enum_list;
		}
		return $properties;
	}

	//Функция для поиска свойств, которые нужно создать
	public static function GetUnicProperties($startProps, $endProps)
	{
		$arProps = array();
		$temp=array();
		if(!empty($endProps)) {
			foreach ($startProps as $startProp)
				foreach ($endProps as $endProp)
					if ($startProp["CODE"] == $endProp["CODE"])
						$temp[] =$startProp["ID"];

			foreach ($startProps as $Prop)
				if(!in_array($Prop["ID"],$temp))
					$arProps[$Prop['ID']]=$Prop;

		}
		else $arProps=$startProps;
		return $arProps;
	}

	//Функция для создания (копирования) свойства
	public static function CreateNewProperty($arProp,$iblock_id)
	{
		/**
		 * Добавление свойства
		 */
		$arFields = Array(
			"NAME" => $arProp["NAME"],                                 //Название свойства
			"ACTIVE" => $arProp["ACTIVE"],                             //Активность
			"SORT" => $arProp["SORT"],                                 //Индекс сортировки
			"CODE" => $arProp["CODE"],                                 //Символьный код
			"PROPERTY_TYPE" => $arProp["PROPERTY_TYPE"],               //Тип
			"IBLOCK_ID" => intval($iblock_id),                         //Инфоблок, к которому добавляется
			"ROW_COUNT" => $arProp["ROW_COUNT"],                      //Количество строк в ячейке ввода значения свойства.
			"COL_COUNT" => $arProp["COL_COUNT"],                      //Количество столбцов в ячейке ввода значения свойства.
			"LIST_TYPE" => $arProp["LIST_TYPE"],                      //Тип для свойства список (L). Может быть "L" - выпадающий список или "C" - флажки.
			"MULTIPLE" => $arProp["MULTIPLE"],                        //Множественное
			"FILE_TYPE" => $arProp["FILE_TYPE"],                      //Список допустимых расширений для свойств файл "F"
			"MULTIPLE_CNT" => $arProp["MULTIPLE_CNT"],                //Количество строк в выпадающем списке для свойств типа "список".
			"LINK_IBLOCK_ID" => $arProp["LINK_IBLOCK_ID"],            //Для свойств типа привязки к элементам и группам задает код информационного блока с элементами/группами которого и будут связано значение.
			"WITH_DESCRIPTION" => $arProp["WITH_DESCRIPTION"],        //Признак наличия у значения свойства дополнительного поля описания
			"SEARCHABLE" => $arProp["SEARCHABLE"],                    //Индексировать значения данного свойства
			"FILTRABLE" => $arProp["FILTRABLE"],                      //Выводить поля для фильтрации по данному свойству на странице списка элементов в административном разделе
			"IS_REQUIRED" => $arProp["IS_REQUIRED"],                  //Обязательное
			"USER_TYPE" => $arProp["USER_TYPE"],                      //Идентификатор пользовательского типа свойства
			"USER_TYPE_SETTINGS" => $arProp["USER_TYPE_SETTINGS"]     //
		);
		if (!empty($arProp["ENUM"])) {
			foreach ($arProp["ENUM"] as $enum) {
				$arFields["VALUES"][] = Array(
					"VALUE" => $enum["VALUE"],
					"DEF" => $enum["DEF"],
					"SORT" => $enum["SORT"]
				);
			}
		}
		$ibp = new CIBlockProperty;
		$PropID = $ibp->Add($arFields);
		return $PropID;
	}
	public static function CopyIblockElement($elem_ids,$iblock_id,$sect_id)
	{
		$el = new CIBlockElement;
		$arSelect = Array();
		$properties = array();
		$arFilter = Array( "ID" => $elem_ids);
		$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
		while ($ob = $res->GetNextElement())
		{
			$arFields = $ob->GetFields();
			$properties = array();
			$arProps = $ob->GetProperties();
			foreach ($arProps as $ID => $prop)
				if (!empty($prop["VALUE"]))
					$properties[$prop["ID"]] = $prop;
			$PROP = array();


			$arLoadProductArray = Array(
				'IBLOCK_SECTION_ID' => intval($sect_id),
				'IBLOCK_ID' => $iblock_id,
				'NAME' => $arFields["~NAME"],
				'CODE' => $arFields["CODE"],
				'XML_ID' => $arFields["XML_ID"],
				'ACTIVE' => $arFields["ACTIVE"],
				'DATE_ACTIVE_FROM' => $arFields["DATE_ACTIVE_FROM"],
				'DATE_ACTIVE_TO' => $arFields["DATE_ACTIVE_TO"],
				'SORT' => $arFields["SORT"],
				'PREVIEW_TEXT' => $arFields["~PREVIEW_TEXT"],
				'PREVIEW_PICTURE' => CFile::MakeFileArray(CFile::GetPath($arFields["PREVIEW_PICTURE"])) ,
				'PREVIEW_TEXT_TYPE' => $arFields["PREVIEW_TEXT_TYPE"],
				'DETAIL_TEXT' => $arFields["~DETAIL_TEXT"],
				'DETAIL_PICTURE' =>CFile::MakeFileArray(CFile::GetPath($arFields["DETAIL_PICTURE"])),
				'DATE_CREATE' => $arFields["DATE_CREATE"]
			);
			if($ELEMENT_ID = $el->Add($arLoadProductArray))
			{
				foreach($properties as $elemProp)
				{
					if($elemProp["USER_TYPE"]=="HTML")
					{
						if($elemProp["MULTIPLE"]=="Y")
							foreach ($elemProp["~VALUE"] as $id => $v)
								$PROP[$elemProp["CODE"]][] = array('VALUE'=>array("TYPE"=>"HTML","TEXT"=>$v["TEXT"]),
																				"DESCRIPTION" => $elemProp["DESCRIPTION"][$id]);
						else
						$PROP[$elemProp["CODE"]] = array('VALUE'=>array("TYPE"=>"HTML","TEXT"=>$elemProp["~VALUE"]["TEXT"]),
																	"DESCRIPTION" => $elemProp["DESCRIPTION"]);
					}
					elseif($elemProp["PROPERTY_TYPE"]=="L")
					{
						$enumID=array();
						$property_enums= $property_enums = CIBlockPropertyEnum::GetList(array(),Array("IBLOCK_ID"=>$iblock_id, "CODE"=>$elemProp["CODE"]));
						if($elemProp["MULTIPLE"]=="N")
						{
							while($enum_fields = $property_enums->GetNext()) {
								if(in_array($enum_fields["VALUE"],$elemProp["VALUE"]))
									$enumID[]= $enum_fields["ID"];
							}
							$PROP[$elemProp["CODE"]]=array('VALUE' => $enumID);
						}
						else
						{
							while($enum_fields = $property_enums->GetNext()) {
								if(in_array($enum_fields["VALUE"],$elemProp["VALUE"]))
									$enumID[]= $enum_fields["ID"];
							}
							$PROP[$elemProp["CODE"]]=$enumID;
					}
				}
				elseif($elemProp["USER_TYPE"]=="video")
				{
					if($elemProp["MULTIPLE"]=="Y")
					{
						foreach ($elemProp["VALUE"] as $id=>$v)
						{
							$PROP[$elemProp["CODE"]][] =array("n0"=>array('VALUE'=>Array (  "PATH" => $v["path"],
								"WIDTH" => $v["width"],
								"HEIGHT" => $v["height"],
								"TITLE" => $v["title"],
								"DURATION" => $v["duration"],
								"AUTHOR" => $v["author"],
								"DATE" => $v["date"],
								"DESC" => $v["desc"]),
								"DESCRIPTION" => $v[$id]));
						}
					}
					else{
						$PROP[$elemProp["CODE"]] = array("n0"=>array('VALUE'=>Array (  "PATH" => $elemProp["VALUE"]["path"],
							"WIDTH" => $elemProp["VALUE"]["width"],
							"HEIGHT" => $elemProp["VALUE"]["height"],
							"TITLE" => $elemProp["VALUE"]["title"],
							"DURATION" => $elemProp["VALUE"]["duration"],
							"AUTHOR" => $elemProp["VALUE"]["author"],
							"DATE" => $elemProp["VALUE"]["date"],
							"DESC" => $elemProp["VALUE"]["desc"]),
							"DESCRIPTION" => $elemProp["DESCRIPTION"]));
					}
				}
					elseif($elemProp["PROPERTY_TYPE"]=="F")
					{
						if($elemProp["MULTIPLE"]=="Y")
						{
							foreach ($elemProp["VALUE"] as $id=>$v)
							{
								$arFile = CFile::MakeFileArray(CFile::GetPath($v));
								$PROP[$elemProp["CODE"]][] = array('VALUE'=>$arFile,"DESCRIPTION"=>$elemProp["DESCRIPTION"][$id]);
							}
						}
						else
						{
							$arFile = CFile::MakeFileArray(CFile::GetPath($elemProp["VALUE"]));
							$PROP[$elemProp["CODE"]] = array('VALUE'=>$arFile,"DESCRIPTION"=>$elemProp["DESCRIPTION"]);
						}
					}
					else
					{
						if($elemProp["MULTIPLE"]=="Y")
							foreach ($elemProp["VALUE"] as $id=>$v)
								$PROP[$elemProp["CODE"]][] = array('VALUE'=>$v,"DESCRIPTION"=>$elemProp["DESCRIPTION"][$id]);
						else $PROP[$elemProp["CODE"]] = array('VALUE'=>$elemProp["VALUE"],"DESCRIPTION"=>$elemProp["DESCRIPTION"]);
					}
					$el->SetPropertyValuesEx($ELEMENT_ID, false, $PROP);
				}

			}
		}

	}
	public static function ReplaceIblockElement($elem_ids,$iblock_id,$sect_id)
	{
		karpenkoCopyElements::CopyIblockElement($elem_ids,$iblock_id,$sect_id);
		GLOBAL $DB;
		foreach($elem_ids as $ID)
		{
			$DB->StartTransaction();
			if(!CIBlockElement::Delete($ID))
			{
				$DB->Rollback();
			}
			else
				$DB->Commit();
		}
	}

}