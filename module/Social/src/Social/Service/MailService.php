<?php
namespace Social\Service;

use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
// подключаю интерфейсы ServiceLocator для доступа к POST формы
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
/**
 * MailService сервис отправки email сообщений
 * $sm->get('cities.Storage');
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Social/src/Social/Service/MailService.php
 */
class MailService implements ServiceLocatorAwareInterface
{
    /**
     * Свойство для хранения Service Locator объекта
     * @access protected
     * @var object $serviceLocator ServiceLocator Instance object
     */
    protected $serviceLocator;

    /**
     * @var \Zend\View\Renderer\RendererInterface
     */
    protected $renderer;

    /**
     * @var \Zend\Mail\Transport\Smtp
     */
    protected $smtpTransport;

    /**
     * setServiceLocator(ServiceLocatorInterface $_serviceLocator) Метод реализующий интерфейс установки сервис локатора
     * @access public
     * @var object
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * getServiceLocator(ServiceLocatorInterface $_serviceLocator) Метод реализующий интерфейс загрузки сервис локатора
     * @access public
     * @var object
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Отправка email по HTML шаблону
     * @param  array|string                           $to          An array containing the recipients of the mail
     * @param  string                                 $subject     Subject of the mail
     * @param  string|\Zend\View\Model\ModelInterface $nameOrModel Either the template to use, or a ViewModel
     * @param  null|array                             $values      Values to use when the template is rendered
     * @return Message
     */
    public function createHtmlMessage($to, $subject, $nameOrModel, $values = array())
    {
        $renderer = $this->getRenderer();
        $content = $renderer->render($nameOrModel, $values); // парисинг шаблонов
        $content = $this->__patternReplacer($content, $values); // мой парсинг переменных в шаблоне
        //exit($content);

        $text = new MimePart('');
        $text->type = "text/plain";

        $html = new MimePart($content);
        $html->type = "text/html";

        $body = new MimeMessage();
        $body->setParts(array($text, $html));

        $message = $this->getDefaultMessage();
        $message->setSubject($subject)
                ->setBody($body)
                ->setTo($to);

        return $message;
    }

    /**
     * Отправка email в текстовом формате
     * @param  array|string                           $to          An array containing the recipients of the mail
     * @param  string                                 $subject     Subject of the mail
     * @param  string|\Zend\View\Model\ModelInterface $nameOrModel Either the template to use, or a ViewModel
     * @param  null|array                             $values      Values to use when the template is rendered
     * @return Message
     */
    public function createTextMessage($to, $subject, $nameOrModel, $values = array())
    {
        $renderer = $this->getRenderer();
        $content = $renderer->render($nameOrModel, $values); // парисинг шаблонов
        $content = $this->__patternReplacer($content, $values); // мой парсинг переменных в шаблоне
        $message = $this->getDefaultMessage();
        $message->setSubject($subject)
                ->setBody($content)
                ->setTo($to);

        return $message;
    }

    /**
     * Переопределяю Smtp send()
     *
     * @param Message $message
     */
    public function send(Message $message)
    {
	return $this->getSmtpTransport()->send($message);

        /*try
        {
            $this->getSmtpTransport()->send($message);
        }
        catch(\Zend_Exception $e)
        {
                                echo "Caught exception: " . get_class($e) . "\n";
                                echo "Message: " . $e->getMessage() . "\n";
        }*/
    }

    /**
     * Рендер шаблонов
     *
     * @return \Zend\View\Renderer\RendererInterface
     */
    protected function getRenderer()
    {
        if($this->renderer===null)
        {
            $this->renderer = $this->serviceLocator->get('ViewRenderer');
        }

        return $this->renderer;
    }

    /**
     * Фабрика SMTP транспортировки и настроек
     *
     * @return \Zend\Mail\Transport\Smtp
     */
    protected function getSmtpTransport()
    {
        if($this->smtpTransport===null)
        {
            $this->smtpTransport = $this->serviceLocator->get('factory.SmtpTransport');
        }

        return $this->smtpTransport;
    }

    /**
     * getDefaultMessage() Фабрика формирования сообщения
     * @access public
     * @return Message
     */
    protected function getDefaultMessage()
    {
        return $this->serviceLocator->get('factory.MessageFactory');
    }

    /**
     * getInfo() Просмотр информации о сообщении
     * @access public
     * @return Message
     */
    public function getInfo()
    {
        $message = $this->getDefaultMessage();
        return $message->getHeaders();
    }

    /**
     * __patternReplacer($content, $data) Автозамена переменных в шаблоне
     * @param string $content сообщение
     * @param array $data массив с автозаменой
     * @access private
     * @return Message
     */
    private function __patternReplacer($content, $data)
    {
        $keys = array_keys($data);
        $vals = array_values($data);
        $key = array();
        foreach($keys as $v)
        {
            $key[] = "#".trim($v)."#";
        }
        return str_replace($key, $vals, $content);
    }
}