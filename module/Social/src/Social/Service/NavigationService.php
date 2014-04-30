<?php
namespace Social\Service; // инициализирую текущее пространство имен

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Navigation\Service\DefaultNavigationFactory;

/**
 * Сервис навигации для сайта
 * использовать сервис менеджер в модели
 * $sm->get('navigation.Service');
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Social/src/Social/Service/NavigationService.php
 */
class NavigationService extends DefaultNavigationFactory
{
    protected function getPages(ServiceLocatorInterface $serviceLocator, $menu)
    {
        if(null === $this->pages) 
        {
            // Достаю параметры для выбранного меню
            $fetchMenu = $serviceLocator->get('menuItems.Service')->getMenuItems($menu);
 
            $configuration['navigation'][$this->getName()] = array();
            foreach($fetchMenu as $key=>$row)
            {
                $configuration['navigation'][$this->getName()][$row['alias']] = array(
                    'label' => $row['title'],
                    'route' => $row['alias'],
                );
            }
            print_r($configuration); exit;
            if (!isset($configuration['navigation'])) {
                throw new Exception\InvalidArgumentException('Could not find navigation configuration key');
            }
            if (!isset($configuration['navigation'][$this->getName()])) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Failed to find a navigation container by the name "%s"',
                    $this->getName()
                ));
            }
 
            $application = $serviceLocator->get('Application');
            $routeMatch  = $application->getMvcEvent()->getRouteMatch();
            $router      = $application->getMvcEvent()->getRouter();
            $pages       = $this->getPagesFromConfig($configuration['navigation'][$this->getName()]);
 
            $this->pages = $this->injectComponents($pages, $routeMatch, $router);
        }
        return $this->pages;
    }  
}