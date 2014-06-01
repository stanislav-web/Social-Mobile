<?php
namespace Social\Factory;
// Подключаю классы SMTP
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Фабрика для установки конфигурации SMTP. Smpt transport object
 * $smtp = $this->serviceManager->get('factory.SmtpTransport');
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Social/src/Social/Factory/SmtpTransportFactory.php
 */
class SmtpTransportFactory implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @return Smtp
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config  = $serviceLocator->get('Configuration');

        $options = new SmtpOptions($config['mailer']['smtp_options']);
        $smtpTransport = new Smtp($options);

        return $smtpTransport;
    }
}