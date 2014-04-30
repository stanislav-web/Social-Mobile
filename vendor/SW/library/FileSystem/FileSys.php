<?php
namespace SW\FileSystem; // пространство имен для моей библиотеки

/**
 * Класс для работы с файловой системой CRUD
 * @package Zend Framework 2
 * @subpackage SWEB Library
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /vendor/SW/library/FileSystem/FileSys.php
 */
class FileSys
{
    /**
     * Устанавливает базовый путь директории для обработки
     * @var string $path
     * @access public
     * @static
     */
    public $pathfrom;

    /**
     * Устанавливает путь для коненой директории или файла
     * @var string $path
     * @access public
     * @static
     */
    public $pathto;

     /**
     * Права доступа
     * @var int $chmod
     * @access public
     * @static
     */
    public $chmod;

    /**
     * Конструктор. Устанавливаю путь с которым работаю и права доступа
     * @param string $path
     * @param int $chmod права на директорию
     * @access public
     * @static
     */
    public function __construct($pathfrom = '', $pathto = '', $chmod = '0770')
    {
        if(isset($pathto))      $this->pathto     = $pathto;    // куда
        if(isset($pathfrom))    $this->pathfrom    = $pathfrom; // откуда
        if(isset($chmod))       $this->chmod    = $chmod;       // доступ
        return $this;
    }

    /**
     * __IfFileExists() Проверяю, файл (директорию) на существование
     * @access private
     * @static
     */
    private function __IfFileExists($path)
    {
        return file_exists($path);
    }

    /**
     * move() Перемещение файлов
     * @access public
     * return boolean
     */
    public function move()
    {
        if($this->__IfFileExists($this->pathfrom))
        {
            // если директории не существует
            if(!mkdir(dirname($this->pathto), $this->chmod)) throw new \Exception("Cannot create directory ".dirname($this->pathto)." and setup chmod ".$this->chmod);
        }
        else throw new \Exception("Cannot find the ".$this->pathto);



        if(!isset($this->pathfrom) && !isset($this->pathto))  throw new \Exception(" Move \"From\" or move \"To\" does not exist");
        else
        {
            if(is_writable(dirname($this->pathto)))
            {
                if(!rename($this->pathfrom, $this->pathto)) throw new \Exception("Cannot move object from  ".$this->pathfrom." to ".$this->pathto);
                else return true;
            }
            else throw new \Exception(dirname($this->pathto)." is not writable");
        }
        return false;
    }

    /**
     * access($access) Установка прав доступа к файлу или директории
     * @access public
     * return boolean
     */
    public function access($access)
    {
        if(!chmod(dirname($this->pathto), $access)) throw new \Exception("Cannot setup access ".$access." to ".dirname($this->pathto));
        else return true;
    }
}