ZF2-Submissions Mass mailings Service
===============================================================
This module is designed to organize mass mailings SMS and Email. The module serves as a container for multiple providers, organizing mass mailings Email and SMS. 
---------------------------------------------------------------
Unisender Mass mailing service inside!

![Alt text](http://www.unisender.com/images/logo.png "Unisender.com")

---------------------------------------------------------------
Installation:

Require PHP 5.4+ extends SPL Library

1.  Add module "Submissions" in your application.config.php

2.  To load default settings provider, add in your "config_global_path"   array( './vendor/Submissions/config/providers/*.php') 
```php
<?php 
            'config_glob_paths' => array(
                './vendor/Submissions/config/providers/*.php'
            ) 
?>
```

2. In the directory /config/providers/*.php provider settings are located. When you add new, follow the pattern of the floor.
Then you have to create the same name used by the API methods and properties (example API reside in the same directory ../src/Submissions/Provider/*.php ... Look there and do the same)

3. How to use in the controller actions?
```php
<?php  

        // Get Factory container
        $factory        = $this->getServiceLocator()->get('Submissions\Factory\ProviderFactory');

        // Get Provider @see /src/Submissions/Provider/Unisender.php etc..
        $provider       = $factory->getProvider('Unisender');   
        
        // Get config from selected provider (ex. /config/providers/Unisender.php )
        $provider->getConfig();

        // email subscribe
        $provider->subscribe(['email' => 'test@mail.ua'], $list_id = 12345);
        
        // email unsubscribe
        $provider->unsubscribe('test@mail.ua', $list_id = 12345);
        
        // export contacts from provider service
        $provider->exportContacts();
        
        // import contacts
        $provider->importContacts($params);

        $provider->createEmailMessage('Sender Name', 'email@email.com', 'Subject', '<b>Message...</b>', $subscriber_list_id);

        // send created message
        $provider->sendMessage($message_id);

        //... end more operations
?>
```
--------------------------------------
In order to start using the module clone the repo in your vendor directory or add it as a submodule if you're already using git for your project:

    git clone https://github.com/stanislav-web/ZF2-Submissions.git vendor/Submissions
    or
    git submodule add     git clone https://github.com/stanislav-web/ZF2-Submissions.git vendor/Submissions

The module will also be available as a Composer package soon.
Learn more and discuss can always in the group of ZendFramework 2 developers http://vk.com/zf2development
