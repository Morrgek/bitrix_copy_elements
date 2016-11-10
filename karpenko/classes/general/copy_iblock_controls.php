<?php
/**
 * Created by PhpStorm.
 * User: Юля
 * Date: 20.04.2015
 * Time: 21:38
 * Класс для добавления своих элементов управления
 */

class karpenkoIblocControls
{
    //Событие OnAdminContextMenuShow вызывается в функции CAdminContextMenu::Show() при выводе в административном разделе панели кнопок.
    public static function OnAdminListDisplayHandlerKarp(&$list)
    {
        //Если мы на странице со списком элементов
        if(  strstr( $_SERVER['REQUEST_URI'], '/bitrix/admin/iblock_list_admin.php' ) )
        {
            //Подключим нашу библиотеку
            CJSCore::Init(array('iblock_karpenko_tools'));
            if (!empty($list->arActions)) {
                //Добавим кнопки на панель кнопки на панель
                $strSomeScripts = '<script type="text/javascript">BX.ready(function(){addButtonsToAdminList()});</script>';
                $list->arActions['asd_checkbox_manager'] = array('type' => 'html', 'value' => $strSomeScripts);

                //Добавим кнопки в выпадающее меню, при клике на шестерёнку
                $list->context->additional_items[] = array(
                    'TEXT' => "Копировать в другой инфоблок",
                    'TITLE' => "Копировать в другой инфоблок",
                    'GLOBAL_ICON' => 'adm-menu-setting',
                    'ONCLICK' => 'showIbockSelect("copy")'
                );
                $list->context->additional_items[] = array(
                    'TEXT' => "Переместить в другой инфоблок",
                    'TITLE' => "Переместить в другой инфоблок",
                    'GLOBAL_ICON' => 'adm-menu-setting',
                    'ONCLICK' => 'showIbockSelect("remove")'
                );

            }
        }
    }
    //Событие OnAdminContextMenuShow вызывается в функции CAdminContextMenu::Show() при выводе в административном разделе панели кнопок.
    public static function OnAdminContextMenuShowHandlerKarp(&$items) {

        //Кнопку на панели можно создать таким способом
        //Для рабочей реализации, названия кнопок выносим в ланговый файл
        /* $items[] = array('ICON' => 'asd_iblock_show_element',
            'TEXT' =>"Копировать в другой инфоблок",
            'LINK' => "#",
            'ONCLICK' => 'showIbockSelect("copy")'
        );

         */

        //Если мы на странице редактирования элемента
        if(  strstr( $_SERVER['REQUEST_URI'], '/bitrix/admin/iblock_element_edit.php' ) )
        {
            CJSCore::Init(array('iblock_karpenko_tools'));
            //Таким: кнопка с выпадающими подпунктами
            $items[] = array(
                'TEXT' => "В другой ИБ",
                'TITLE' => "В другой ИБ",
                'LINK' => '#',
                'ICON' => 'btn_settings',
                'MENU' => array(
                    array(
                        'TEXT' => "Копировать в другой инфоблок",
                        'ACTION' => 'showIbockSelect("copy")',
                    ),
                    array(
                        'TEXT' => "Переместить в другой инфоблок",
                        'ACTION' => 'showIbockSelect("remove")'
                    ),
                ),
            );
        }
    }

    //Событие OnAdminTabControlBegin вызывается в функции CAdminTabControl::Begin() при выводе в административном интерфейсе формы редактирования.
    public static function OnAdminTabControlBeginHandlerKarp(&$form) {

    }

}