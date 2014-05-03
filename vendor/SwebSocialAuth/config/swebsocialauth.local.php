<?php

/**
 * Настройки адаптеров социальных служб
 */
$adapterConfigs = array(
    'swebsocialauth' => array(
        'vk' => array(
            'client_id' => '',
            'client_secret' => '',
            'redirect_uri' => '/authservice/?provider=vk'
        ),
        'odnoklassniki' => array(
            'client_id' => '',
            'client_secret' => '',
            'redirect_uri' => '/authservice/provider=odnoklassniki',
            'public_key' => 'CBADCBMKABABABABA'
        ),
        'mailru' => array(
            'client_id' => '',
            'client_secret' => '',
            'redirect_uri' => '/authservice/?provider=mailru'
        ),
        'yandex' => array(
            'client_id' => '',
            'client_secret' => '',
            'redirect_uri' => '/authservice/?provider=yandex'
        ),
        'google' => array(
            'client_id' => '',
            'client_secret' => '',
            'redirect_uri' => '/authservice/?provider=google'
        ),
        'facebook' => array(
            'client_id' => '',
            'client_secret' => '',
            'redirect_uri' => '/authservice/?provider=facebook'
        )
    )
);

return $adapterConfigs;
