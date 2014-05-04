<?php
namespace Social\Service;

// подключаю интерфейсы ServiceLocator для доступа к POST формы
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * GD2Service работа с Image через GD2 библиотеку
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Social/src/Social/Service/GD2Service.php
 */
class GD2Service implements ServiceLocatorAwareInterface
{
    /**
     * Модуль работы через GD2 (Thumbnailer)
     * @var object $thumbnailer
     * @access protected
     */
    protected $thumbnailer = null;
    
    /**
     * Свойство для хранения Service Locator объекта
     * @access protected
     * @var object $sm ServiceLocator Instance object
     */
    protected $sm;
    
    /**
     * Полный путь к изображению для обработки
     * @access public
     * @var type string
     */
    public $image = null;
    
    /**
     * setServiceLocator(ServiceLocatorInterface $_serviceLocator) Метод реализующий интерфейс установки сервис локатора
     * @access public
     * @var object
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->sm = $serviceLocator;
    }

    /**
     * getServiceLocator(ServiceLocatorInterface $_serviceLocator) Метод реализующий интерфейс загрузки сервис локатора
     * @access public
     * @var object
     */
    public function getServiceLocator()
    {
        return $this->sm;
    }    
    
    /**
     * Инициализирую конструктор
     */
    public function __invoke($image)
    {
        if(!$image) throw new \Exception('No found image transfer');
        else $this->image       =   $image;
        $this->thumbnailer      =   $this->sm->get('WebinoImageThumb');
    }
    
    /**
     * createThumb($path, $width, $height) Создание превью с размерами
     * @access public
     * @param type $path полный пть нового изображения
     * @param type $width новая ширина
     * @param type $height новая высота
     * @throws \Exception исключение
     * return null;
     */
    public function createThumb($path, $width, $height)
    {
      
        if(!file_exists($this->image)) throw new \Exception('Can\'t read the image '.$this->image);
        
        $image  = $this->thumbnailer->create($this->image, $options = array(), $plugins = array());
        $imageprops = $image->getCurrentDimensions();

        if($imageprops['width'] <= $width && $imageprops["\0*\0currentDimensions"]['height'] <= $height) $this->writeImage($path);
        else
        {
            $image->resize($width, $height);
            $image->save($path);
        } 
    }
    
    /**
     * setWatermark($path, $width, $height) Создаю накладку watermark
     * @access public
     * @param string $original полный путь к изображению
     * @param string $sprite полный путь к watermark
     * @throws \Exception
     * @return null;
     */
    public function setWatermark($original, $sprite)
    {
        if(!$original) throw new \Exception('Can\'t find the original image '.$original);
        else
        {
            $this->image = $original;

            if(!file_exists($sprite)) throw new \Exception('Can\'t read the watermark image '.$sprite);

            // Проверяю на сколько большое исходное изображение и водяной знак
            $image  = $this->thumbnailer->create($this->image, $options = array(), $plugins = array());
            $imageprops =  $image->getCurrentDimensions();
            $iWidth     = $imageprops['width'];
            $iHeight    = $imageprops['height'];
            
            $watermark  = $this->thumbnailer->create($sprite, $options = array(), $plugins = array());
            $watermarkprops =  $watermark->getCurrentDimensions();
            $wWidth     = $watermarkprops['width'];
            $wHeight    = $watermarkprops['height'];
            
            if($iHeight < $wHeight || $iWidth < $wWidth) 
            {
                // Делаю ресайз водяного знака и повторно выситываю его координаты
                
                $watermark->adaptiveResize($iWidth, $iHeight);
                $watermarkprops =  $watermark->getNewDimensions();

                $wWidth = $watermarkprops['width'];
                $wHeight = $watermarkprops['height'];
            }
            
            // отсчитываю координаты и делаю накладку
            
            $x = ($iWidth - $wWidth) - 10;
            $y = ($iHeight - $wHeight) - 10;

            //$image->save($this->image);
        }
    }      
    
    /**
     * close() Очищаю объект
     * @access public
     * return null;
     */
    public function close()
    {
        $this->image = null;
    }
}