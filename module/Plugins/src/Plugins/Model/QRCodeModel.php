<?php
namespace Plugins\Model; // инициализирую текущее пространство имен

// подключаю интерфейсы ServiceLocator
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Модель вывода QR Кода в шаблоне
 * $sm->get('QRCode.Model');
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Plugins/src/Plugins/Model/QRCodeModel.php
 */
class QRCodeModel implements ServiceLocatorAwareInterface
{
    /**
     * $_serviceLocator Свойство для хрения сервис менеджера
     * @access protected
     * @var type object
     */
    protected $_serviceLocator;

    /**
     * Системный код плагина
     * @access protected
     * @var string $table;
     */
    protected $table    = 'qrcode';
    
   /**
     * Свойства QR Кода
     * @var array
     */
    private $properties = array();
    
    /**
     * Точка входа URL сервиса
     * @var string
     */
    private $endpoint = null;
    
    /**
     * Начальная точка входа
     */
    const END_POINT = 'chart.googleapis.com/chart?';    
    
    
    /**
     * setServiceLocator(ServiceLocatorInterface $_serviceLocator) Метод реализующий интерфейс установки сервис локатора
     * @access public
     * @var object
     */
    public function setServiceLocator(ServiceLocatorInterface $_serviceLocator)
    {
        $this->_serviceLocator = $_serviceLocator;
    }

    /**
     * getServiceLocator(ServiceLocatorInterface $_serviceLocator) Метод реализующий интерфейс загрузки сервис локатора
     * @access public
     * @var object
     */
    public function getServiceLocator()
    {
        return $this->_serviceLocator;
    }

    /**
     * get($system) Метод выдает уведомления по коду
     * @param string $system Системный код уведомления
     * @access public
     * @param \Zend\Db\TableGateway\Feature\EventFeature\TableGatewayEvent
     * @return object DB initialize
     */
    
    public function get($param)
    {
        $service    = $this->getServiceLocator()->get('plugins.Service');   // Мой менеджер плагинов
        foreach($service->getPlugins() as $value)
        {
            if($this->table == $value['system'])
            {
                $this->setCharset();
                $this->setCorrectionLevel();
                $this->setTypeChart(); 
                $this->isHttp(); // or $qr->isHttp();
                $this->setDimensions(50, 50);
                $this->setData($param);
                $result = $this->getResult();
                break;
            }
        } 
        return (!isset($result)) ? '' : $result; // возвращаю свойство картинки
    }
    
   /**
    * isHttp() Is Http? Вход через http://
    * @access public
    * @return this
    */
    public function isHttp()
    {
        $this->endpoint = 'http://'.self::END_POINT;
        return $this;
    }
    
   /**
    * isHttps() Is Https? Вход через https://
    * @access public
    * @return this
    */
    public function isHttps()
    {
        $this->endpoint = 'https://'.self::END_POINT;
        return $this;
    }    
    
    /**
     * setTypeChart($chart = 'qr') Установка qr свойства, 'qr' - по умолчанию 
     * @param string $chart свойство chart
     * @access public
     * @return this
     */
    public function setTypeChart($chart = 'qr') 
    {
        $this->properties['cht'] = $chart;
        return $this;
    }

    /**
     * getTypeChart() Получение qr свойства
     * @access public
     * @return array
     */
    public function getTypeChart() 
    {
        return $this->properties['cht'];
    }

    /**
     * getResult() Получение ответа от сервиса генерации
     * @access public
     * @return object
     */
    public function getResult() 
    {
        $result = $this->endpoint.http_build_query($this->properties);
        if(!$result) throw new \Exception('Cannot connect to server QR Code Generator');
        else return $result;
    }    
    
    /**
     * setDimensions($w, $h) Установка разрешения изображения (ширина / высота)
     * @param int $w ширина
     * @param int $h высота
     * @throws \InvalidArgumentException
     * @access public
     * @return this
     */
    public function setDimensions($w, $h) 
    {
        if(is_int($w) && is_int($h)) 
        {
            $this->properties['chs'] = "{$w}x{$h}";
        }
        else
        {
            throw new \InvalidArgumentException('The parameter $w and $h must be integer type');
        }
        return $this;
    } 
    
    /**
     * getDimensions() Получение установленного расширения
     * @access public
     * @return string
     */    
    public function getDimensions() 
    {
        return $this->properties['chs'];
    }
    
    /**
     * setCharset($charset = 'UTF-8') Кодировка зашифрованного текста
     * @param string $charset кодировка текста
     * @access public
     * @return this
     */    
    public function setCharset($charset = 'UTF-8') 
    {
        $this->properties['choe'] = $charset;
        return $this;
    }
    
    /**
     * getCharset() Получение установленной кодировки
     * @access public
     * @return string
     */     
    public function getCharset() 
    {
        return $this->properties['choe'];
    }
    
    /**
     * setCorrectionLevel($cl = 'L',$m = 0) Установка уровня коррекции изображения
     * @param string $cl уровень сплоченности пикселей
     * @param int $m внешние отступы
     * @access public
     * @return this
     */    
    public function setCorrectionLevel($cl = 'L',$m = 0)
    {
        $this->properties['chld'] = "{$cl}|{$m}";
        return $this;
    }
    
    /**
     * getCorrectionLevel() Получение уровня коррекции
     * @access public
     * @return string
     */       
    public function getCorrectionLevel()
    {
        return $this->properties['chld'] ;
    }
    
    /**
     * setData($data) Установка зашифрованной строки
     * @param string $data строка для шифрования
     * @access public
     * @throws \InvalidArgumentException
     * @return this
     */     
    public function setData($data)
    {
        if(is_null($data)) throw new \Exception('Did not set the data for QR');
        $this->properties['chl'] = urlencode($data);
        return $this;
    }
    
    /**
     * getData() Получение зашифрованной строки
     * @access public
     * @return string
     */     
    public function getData()
    {
        return urldecode($this->properties['chl']);
    }    
}