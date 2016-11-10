<?php
/**
 * Created by PhpStorm.
 * User: Юля
 * Date: 12.04.15
 * Time: 23:11
 */
if (!check_bitrix_sessid()) return;
echo CAdminMessage::ShowNote("Модуль karpenko (копирование элементов инфоблока) успешно удален из системы");
