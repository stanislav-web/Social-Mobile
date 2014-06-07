<?php

namespace Social; // объявяю пространство имен для текущего модуля "Social"

use Zend\Mvc\MvcEvent; //событийная модель MVC
use Zend\View\Model\ViewModel; // модель вида
use Zend\View\View;

/**
 * Для наглядности, модуль обеспечивающий базовую функциональность для модуля "Social".
 * Так же этот класс является названием корневой папки модуля и пространства имен одновременно
 * Zend Framework 2 имеет менеджер модулей ModuleManager, который служит для загрузки модулей.
 * Он будет искать этот файл в корневой директории модуля (module/Social), который должен содержать класс Social\Module.
 * Т.е классы модуля должны принадлежать пространству имен с названием идентичным названию модуля, которым является название директории модуля.
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Social/Module.php
 */
class Module
{

    /**
     * isMobile($e) метод подставляет базовый layout (шаблон)
     * для отображения на мобильных устройствах
     * @param object $e
     * return object шаблон
     */
    public function isMobile($e)
    {

         $mobile = new \SW\Http\Header\MobileDetect(); // создаю объект
         if($mobile->isMobile())
         {
            // для мобильных ус-тв устанавливаю мобильный каркас шаблона
            $viewModel = $e->getViewModel()->setTemplate('layout/mobile');
            return $viewModel;
         }
         else return false;
    }

    /**
     * setCacheStorage($sec) возвращает параметры адаптера сессионного хранилища
     * @param int $sec секунды на сохранение
     * @access private
     * return object \Zend\Cache\StorageFactory::factory()
     */
    private function __setCacheStorage($sec)
    {
        return \Zend\Cache\StorageFactory::factory(array(
            'adapter' => array(
            'name' => 'filesystem',
            'options' => array(
                'cache_dir' => __DIR__ . '/../../data/cache/translator/',
                'ttl' => $sec
            ),
        ),
        'plugins' => array(
            array(
                    'name' => 'serializer',
                    'options' => array(
                    )
                ),
            // Don't throw exceptions on cache errors
                'exception_handler' => array(
                    'throw_exceptions' => false
                ),
            )
        ));
    }

    /**
     * initLocale(MvcEvent $e) метод установки локализации сайта
     * по URL , запись в кукис или сессию (по defaul из HTTP_ACCEPT_LANGUAGE )
     * @param \Zend\Mvc\MvcEvent $e
     * @access public
     * @return null
     */
    public function initLocale(MvcEvent $e)
    {
        $app = $e->getApplication(); // приложение
        $acceptLocale = $e->getRequest()->getServer('HTTP_ACCEPT_LANGUAGE');
        $acceptLang = substr(strtolower($acceptLocale), 0, 2);
        $translator = $app->getServiceManager()->get('MvcTranslator'); // получаем объект translator'a
        $cookies = $app->getRequest()->getCookie(); // достаю куки
        $shortLang = $e->getRouteMatch()->getParam('lang'); // ищу в url &lang=??
        $config =  $app->getServiceManager()->get('Config'); // достаю настройки
        //@TODO Кэширование локалей
        $translator->setCache($this->__setCacheStorage(300)); // кэширую локаль
        if(isset($shortLang) && preg_match("/[a-z]{2}?/i", $shortLang)) // если нашли в URL
        {
            if(isset($config['languages'][$shortLang]))
            {
                // Устанавливаю локаль и куки на 1 мес
                $translator->setLocale($config['languages'][$shortLang]['locale']);
                setcookie('lang', $shortLang, time()+2878400, '/', $e->getRequest()->getServer('HTTP_HOST'));
                $e->getViewModel()->setVariables(array('lang' => $shortLang)); // устанавливаю в layout
            }
        }
        else // если не нашли в URL, читаем сначала из кук
        {
            if(isset($cookies['lang']) && preg_match("/[a-z]{2}?/", strtolower($cookies['lang'])))
            {
                if(isset($config['languages'][$cookies['lang']]['locale'])) {
                    $translator->setLocale($config['languages'][$cookies['lang']]['locale']);
                    $e->getViewModel()->setVariables(array('lang' => $cookies['lang'])); // устанавливаю в layout
                }
                else {
                    $translator->setLocale($config['languages'][$acceptLang]['locale']);
                    $e->getViewModel()->setVariables(array('lang' => $acceptLang)); // устанавливаю в layout
                }
            }
            else
            {
                $translator->setLocale($config['languages'][$acceptLang]['locale']); // ставлю из браузера
                $e->getViewModel()->setVariables(array('lang' => $acceptLang)); // устанавливаю в layout
            }
        }
    }
    
    /**
     * initOnline(MvcEvent $e) определение кто онлайн
     * @param \Zend\Mvc\MvcEvent $e
     * @access public
     * @return null
     */
    public function initOnline(MvcEvent $e)
    {
        // инициализирую сервисы и событийную модель
        $application   = $e->getApplication();
        $sm            = $application->getServiceManager();
        
        // Удаляю тех кто уже отмотал срок в онлайне :-)
        
        $online = $sm->get('online.Model');        
        $online->deleteItems();
        
        // Проверяю авторизацию
                    
        $auth = $sm->get('authentification.Service');
        if($auth->hasIdentity() == true)
        {
            // подключаю необходимые модели
            $user       = $sm->get('user.Model');
            
            // получаю авторизованного пользователя

            $userFetch =  $user->getUser($auth->getIdentity());
            
            // получаю заголовок страницы
            $viewHelperManager = $sm->get('viewHelperManager');
            $title = strip_tags($viewHelperManager->get('headTitle'));            
            // удаляем всех, кто уже пробыл $_timeon секунд или у кого ИП текущий

            // вставляем свою запись
            $insert = $online->insertItem($userFetch->id, $title);
            if($insert)
            {       
                // удачно записал, обновляю в пользователях статус онлайн и время на этого юзера
                $user->setTimeOnline($userFetch);
            }          
        }
    }

    /**
     * initLayout(MvcEvent $e) Определение слоя layout
     * для авторизированных и не авторизированных пользователей
     * @param @param \Zend\Mvc\MvcEvent $e
     * @access public
     * @return null
     */
    public function initLayout(MvcEvent $e)
    {
        // инициализирую сервисы и событийную модель
        $sm            = $e->getApplication()->getServiceManager();

        // Проверяю авторизацию

        $auth = $sm->get('authentification.Service');
        if($auth->hasIdentity() == true)
        {
            // Авторизирован
            
            if(in_array($e->getRouteMatch()->getMatchedRouteName(), [
                'admin', 
                'plugins', 
                'plugins/edit', 
                'plugins/add',
                'users', 
                'users/edit', 
                'users/view',                
                'distributions',
                'distributions/provider',
                ])) $e->getViewModel()->setTemplate('layout/admin');
            else  $e->getViewModel()->setTemplate('layout/user');
        }
        else
        {
            // Не авторизирован
            $e->getViewModel()->setTemplate('layout/layout');
        }
    }
    
    /**
     * getConfig() метод загрузки конфигуратора приложения
     * @access public
     * @return file
     */
    public function getConfig()
    {
        return include __DIR__.'/config/module.config.php';
    }
    
    /**
     * getServiceConfig() метод загрузки сервисов
     * @access public
     * @return file
     */
    public function getServiceConfig()
    {
        return include __DIR__.'/config/service.config.php';
    }

    /**
     * getAutoloaderConfig() метод установки автозагрузчиков
     * В моем случае, я подключаю карту классов
     * и устанавливаю пространство имен для MVC директории приложения
     * @access public
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader'    =>  array(
                // подгружаю карту библиотек
                __DIR__.'/autoload_classmap.php',
            ),

            // устанавливаю пространство имен для MVC директории с приложением
            'Zend\Loader\StandardAutoloader'    =>  array(
                'namespaces'=>array(
                    __NAMESPACE__=>__DIR__.'/src/'.__NAMESPACE__,
                ),
            ),
        );
    }

    /**
     * getViewHelperConfig() метод установки class'ов хелперов в автозагрузку
     * @access public
     * @return object
     */
    public function getViewHelperConfig()
    {
        return include __DIR__.'/config/helper.config.php';
    }

    /**
     * onBootstrap(MvcEvent $e) Этот метод слушатель (listeners) события bootstrap
     * в рамках событийной модели ZF2
     * инкапсулятор различных палагинов (сервисов) при автозагрузке
     * @access public
     * @param \Zend\Mvc\MvcEvent $e
     * @return object
     */
    public function onBootstrap(MvcEvent $e)
    {
        $app = $e->getApplication();        // Наше приложение
        $sm = $app->getServiceManager();    // Сервис менеджер
        $event = $app->getEventManager();   // Менеджер событий
        $request = $e->getRequest();        // исходящий запрос
        
        // Загружаю события только для HTTP запросов
        
        if($request instanceof \Zend\Http\Request) {
            
            $sm->get('viewhelpermanager')->setFactory('getRoute', function($sm) use ($app) {
                return new \Social\Helper\RouteHelper($app); // получаю текущий сегмент URL
            });            
            $event->attach(MvcEvent::EVENT_ROUTE,       array($this, 'initLocale'));   // локаль
            $event->attach(MvcEvent::EVENT_RENDER,      array($this, 'initOnline'));   // онлайн
            $event->attach(MvcEvent::EVENT_DISPATCH,    array($this, 'initLayout'));   // смена шаблона
            //$e->attach('dispatch', array($this, 'isMobile'), -100); // проверка на моб. тел.            
        }
    }
}
