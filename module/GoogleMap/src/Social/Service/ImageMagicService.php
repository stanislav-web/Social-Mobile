<?php
namespace Social\Service;
/**
 * ImageMagicService работа с Image через ImageMagic
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Social/src/Social/Service/ImageMagicService.php
 */
class ImageMagicService extends \Imagick
{
    /**
     * Полный путь к изображению для обработки
     * @access public
     * @var type string
     */
    public $image = null;
    
    /**
     * Инициализирую родительский конструктор
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    public function __invoke($image)
    {
         if(!$image) throw new \Exception('No found image transfer');
        else $this->image    =   $image;
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
        if(!$this->readimage($this->image)) throw new \Exception('Can\'t read the image '.$this->image);
        $imageprops  = $this->getImageGeometry();
        
        if($imageprops['width'] <= $width && $imageprops['height'] <= $height) $this->writeImage($path);
        else
        {
            $this->resizeImage($width, $height, self::FILTER_LANCZOS, 0.9, true);
            $this->writeImage($path);
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
            $watermark  = new \Imagick;
            if(!$watermark->readImage($sprite)) throw new \Exception('Can\'t read the watermark image '.$sprite);
            
            // Проверяю на сколько большое исходное изображение и водяной знак
        
            $iWidth     = $this->getImageWidth();
            $iHeight    = $this->getImageHeight();
            $wWidth     = $watermark->getImageWidth();
            $wHeight    = $watermark->getImageHeight();
            
            if($iHeight < $wHeight || $iWidth < $wWidth) 
            {
                // Делаю ресайз водяного знака и повторно выситываю его координаты
                
                $watermark->scaleImage($iWidth, $iHeight); 
                $wWidth = $watermark->getImageWidth();
                $wHeight = $watermark->getImageHeight();
            }
            
            // отсчитываю координаты и делаю накладку
            
            $x = ($iWidth - $wWidth) - 10;
            $y = ($iHeight - $wHeight) - 10;
            
            $this->compositeImage($watermark,  self::COMPOSITE_DEFAULT, $x, $y);
            $this->writeImage($this->image);
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