<?php

/**
 * Настройки адаптеров социальных служб
 */
$adapterConfigs = array(
    'swebsocialauth' => array(
        'vk' => array(
            'client_id'     => '4341268',
            'client_secret' => 'XomOc6paAsX7IB3jr9oP',
            'redirect_uri'  => 'http://'.$_SERVER['HTTP_HOST'].'/authservice/?provider=vk'
        ),
        'odnoklassniki' => array(
            'client_id' => '',
            'client_secret' => '',
            'redirect_uri' => 'http://'.$_SERVER['HTTP_HOST'].'/authservice/?provider=odnoklassniki',
            'public_key' => 'CBADCBMKABABABABA'
        ),
        'mailru' => array(
            'client_id' => '',
            'client_secret' => '',
            'redirect_uri' => 'http://'.$_SERVER['HTTP_HOST'].'/authservice/?provider=mailru'
        ),
        'yandex' => array(
            'client_id' => '',
            'client_secret' => '',
            'redirect_uri' => 'http://'.$_SERVER['HTTP_HOST'].'/authservice/?provider=yandex'
        ),
        'google' => array(
            'client_id' => 'stellar-button-569',
            'client_secret' => 'sdsdsdsdsdsdsds',
            'redirect_uri' => 'http://'.$_SERVER['HTTP_HOST'].'/authservice/?provider=google'
        ),
        'facebook' => array(
            'client_id' => '1490958114452442',
            'client_secret' => '61a92861365852f872f4dd9da662ad2b',
            'redirect_uri' => 'http://'.$_SERVER['HTTP_HOST'].'/authservice/?provider=facebook'
        )
    )
);

return $adapterConfigs;
