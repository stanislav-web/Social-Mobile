<?php
/**
 * Массив сохраняет мои пользовательские библиотеки
 * затем передает их в Module.php где можно их просто вызвать
 * или например можно забабахать из них сервис
 * внутри любого метода обращаясть по пространству имен,
 * указаному сдесь как ключик.
 */

return array(

    // Подборка моих кассов и библиотек

    'SW\Http\Header\MobileDetect'           =>  'vendor/SW/library/Http/Header/MobileDetect.php',  // класс определения платформы уст-ва
    'SW\FileSystem\FileSys'                 =>  'xvendor/SW/library/FileSystem/FileSys.php',        // класс для работы с фс
    'SW\String\Translit'                    =>  'vendor/SW/library/String/Translit.php',           // класс транслита
    'SW\String\Format'                      =>  'vendor/SW/library/String/Format.php',             // форматирование текста

 );
