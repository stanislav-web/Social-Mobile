<?php
namespace Social\Factory;

use Zend\Mail\Message;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Фабрика для установки конфигурации отправителя Email. Costom Message object
 * $message = $this->serviceManager->get('factory.MessageFactory');
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Social/src/Social/Factory/MessageFactory.php
 */
class MessageFactory implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @return Message
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config  = $serviceLocator->get('Configuration'); // достаю настройки
        $options = $config['mailer']['default_message'];
        $from    = $options['from'];

        $message = new Message();
        $message->addFrom($from['email'], $from['name'])
                ->setEncoding($options['encoding']);

        // Устанавиваю кодировку в не зависимости, от передачи в шаблоне тега meta charset
        $headers = $message->getHeaders();
        $headers->removeHeader('Content-Type');
        $headers->addHeaderLine('Content-Type', 'text/html; charset='.$options['encoding']);
        return $message;
    }
}