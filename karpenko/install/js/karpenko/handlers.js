/**
 * Created by Юля on 17.04.2015.
 */
BX.ready(function () { //Это аналог джейкверивской функции: $(ready.function(){ //Функция проверяющая полностью ли загрузилась страница});

    /**
     * Навешиваем на кнопки обработчики
     */
    $('.adm_btn_copy_elem').live('click', function () {
        showIbockSelect("copy");
        return false;
    });

    $('.adm_btn_replace_elem').live('click', function () {
        showIbockSelect("replace");
        return false;
    });
    //Обработчик для того, чтобы при выборе типа инфоблока, в другой выпадающий список подгружались
    //только те названия инфоблоков, которые относятся к выбранному типу
    $('#filter_iblock_type').live('change', function () {
        console.log(1);
        var iblock_type = $(this).val();
        isEmptyValue(iblock_type, $(this));
        $("#filter_iblock_id").load("/bitrix/admin/karpenko/select_iblock_id.php", {"TYPE": iblock_type})
    });

    $('#filter_iblock_id').live('change', function () {
        var iblock_id = $(this).val();
        isEmptyValue(iblock_id, $(this));
        $("#filter_iblock_section_id").load("/bitrix/admin/karpenko/select_iblock_section.php", {"ID": iblock_id})
    });
});

//добавляет красную обводку элементу
function isEmptyValue(value, object) {
    (value == 0) ?
        $(object).css("border", "1px solid red") :
        $(object).css("border", "none");
}
//получает список гет параметров из строки и их значения
function parseGetParams() {
    var $_GET = {};
    var __GET = window.location.search.substring(1).split("&");
    for (var i = 0; i < __GET.length; i++) {
        var getVar = __GET[i].split("=");
        $_GET[getVar[0]] = typeof(getVar[1]) == "undefined" ? "" : getVar[1];
    }
    return $_GET;
}
//показывает модальное окошко с выбором инфоблока для копирования или перемещения в него элемента
function showIbockSelect(action) {
    var get = parseGetParams();
    var selectSect=0;
    $('.adm-list-table input:checkbox:checked').each(function (i, elem) {
        if($(elem).val()[0]=="S") selectSect++;
    });

    if($('.adm-list-table input:checkbox:checked').length==0 && get['ID'] === undefined)
        showMessage("Не выбрано ни одного элемента");
    else if(selectSect>0)
        showMessage("Копируйте только элементы инфоблока");
    else if($('.adm-list-table input:checkbox:checked').length>20)
        showMessage("Копируйте не более 20 элементов за раз");

    else
    {

        var elemIDs = 0;
        if (get["ID"] !== undefined) elemID = get["ID"];
        var Dialog = new BX.CDialog({
            title: "Выберите инфоблок",
            head: '',
            content_url: "/bitrix/admin/karpenko/select_iblock.php",
            icon: 'head-block',
            resizable: true,
            draggable: true,
            height: '60',
            width: '600',
            content_post: action
        });
        Dialog.SetButtons([
            {
                'title': 'OK',
                'id': 'action_send',
                'name': 'action_send',
                'action': function () {
                    //document.getElementById('searchform').submit();

                    console.log(1);
                    var iblock_type = $('#filter_iblock_type').val();                 //ID выбранного типа
                    var iblock_id = $("#filter_iblock_id").val();                     //ID выбранного инфоблока
                    var iblock_section = $("#filter_iblock_section_id").val();        //ID выбранного раздела

                    isEmptyValue(iblock_type, $('#filter_iblock_type'));
                    isEmptyValue(iblock_id, $('#filter_iblock_id'));


                    if (iblock_type == 0 || iblock_id == 0) return false;
                    else
                    {
                        var itemID = new Array();
                        if (get['ID'] === undefined) {
                            $('.adm-list-table input:checkbox:checked').each(function (i, elem) {
                                itemID.push($(elem).val().substr(1));
                            })
                        }
                        else {
                            itemID.push(get['ID']);
                        }
                        console.log(itemID);
                        $.ajax({
                            url: '/ajax/karpenko/save_iblock_element.php',
                            type: "POST",
                            async: true,                               //Сделаем синхронный запрос к серверу. Пока запрос будет выполняться, страница не будет реагировать на действия пользователя
                            data: {
                                "ACTION": action,
                                "START_IBLOCK_ID": get['IBLOCK_ID'],
                                "END_IBLOCK_ID": iblock_id,
                                "IBLOCK_SECTION": iblock_section,
                                "ITEMS_ID": JSON.stringify(itemID)
                            },
                            dataType: "json",
                            success: function (result) {
                            }
                        });
                        //return false;
                        this.parentWindow.Close();
                    }
                }
            },
            {
                'title': 'Отмена',
                'id': 'cancel',
                'name': 'cancel',
                'action': function () {

                    this.parentWindow.Close();
                }
            }
        ]);
        Dialog.Show();
    }
}
function addButtonsToAdminList() {
    // Добавляем кнопки
    $(".adm-list-table-footer").append('<a href="" class="adm-btn adm_btn_copy_elem" title="Копировать в другой инфоблок">Копировать в другой инфоблок</a>');
    $(".adm-list-table-footer").append('<a href="" class="adm-btn adm_btn_replace_elem" title="Копировать в другой инфоблок">Переместить в другой инфоблок</a>');

}
function addButtonsToAdminElemEdit() {
    $('.adm-btn.adm-btn-copy').before('<a title="" id="adm_btn_copy_elem" class="adm-btn adm-btn-desktop-gadgets adm-btn-test-btn adm_btn_copy_elem" hidefocus="true" href="#">Копировать в другой инфоблок</a>');
    $('.adm-btn.adm-btn-copy').before('<a title="" id="adm_btn_remove_elem" class="adm-btn adm-btn-desktop-gadgets adm-btn-test-btn adm_btn_replace_elem" hidefocus="true" href="#">Переместить в другой инфоблок</a>');

}
function showMessage(text)
{
    var Dialog = new BX.CDialog({
        title: "Сообщение",
        head: '',
        content: text,
        icon: 'head-block',
        resizable: true,
        draggable: true,
        height: '60',
        width: '600',
        buttons: [ BX.CDialog.prototype.btnClose]
    });
    Dialog.Show();
}