ZF2-Payments ALL (Visa QIWI Wallet inside)
============
Module for the simple implementation of payments any providers.

--------------------------------------

Require PHP 5.4+

extends SPL Library

Configurations :

1.  Add module "Payments" in your application.config.php

2.  To load default settings provider, add in your "config_global_path"   array( './vendor/Payments/config/providers/*.php') 
        ```php
        <?php 
            'config_glob_paths' => array(
                './vendor/Payments/config/providers/*.php'
            ) 
        ?>
        ```


2. Change configurations in module.config.php

3. Call pay form using route http://yourdomain.dev/privat24 or use a partial helper from view
```php
<?php  echo $this->formPrivate24(new \Privat24\Form\Privat24Form($config['array'], $order['array'])); // for setup see module.config.php ?>
```
--------------------------------------
In order to start using the module clone the repo in your vendor directory or add it as a submodule if you're already using git for your project:

    git clone https://github.com/stanislav-web/ZF2-Privat24.git vendor/Privat24
    or
    git submodule add     git clone https://github.com/stanislav-web/ZF2-Privat24.git vendor/Privat24

The module will also be available as a Composer package soon.


