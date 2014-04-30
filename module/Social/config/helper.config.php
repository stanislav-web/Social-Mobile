<?php
/**
 * Конфигуратор помошников вида и виджетов
 */

return array(
    'invokables'    =>  array(
        'getLang'           =>  '\Social\Helper\LangHelper',        // локлизация в шаблонах
        'getInfo'           =>  '\Social\Helper\InfoHelper',        // информация с конфигуратора
        'setString'         =>  '\Social\Helper\StringHelper',      // операции со строками
        'setForm'           =>  '\Social\Helper\FormHelper',        // мини формы или формы с GET в шаблонах
    ),
);