<?php
namespace Social\Controller; // пространтво имен текущего контроллера

// объявляю зависимости от главных Zend ActionConroller , Zend ViewModel
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Debug\Debug;
use SW\String\Translit;

/**
 * Контроллер управления выбора местоположения
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Social/src/Social/Controller/LocationController.php
 */
class LocationController extends AbstractActionController
{
    
    /**
     * $_lng Свойство объекта Zend l18 translator
     * @access protected
     * @var type object
     */
    protected $_lng;
    
    /**
     * zfService() Менеджер зарегистрированных сервисов ZF2
     * @access public
     * @return ServiceManager
     */
    public function zfService()
    {
        return $this->getServiceLocator();
    }   
    
    /**
     * indexAction() Вывод всей карты городов с зарегистрированными пользователями
     * Обязательно должен быть КЭШ!
     * @access public
     * @return \Zend\View\Model\ViewModel
     */    
    public function indexAction()
    {
	$this->_lng     = $this->zfService()->get('MvcTranslator'); // загружаю переводчик
	$renderer = $this->zfService()->get('Zend\View\Renderer\PhpRenderer');
	$renderer->headTitle($this->_lng->translate('Search for friends here on this Map!', 'default'));
	// Добавляю скрипты сервисов Google
	$viewrender = $this->zfService()->get('viewhelpermanager')->get('headScript');
	$viewrender->appendFile('http://maps.google.com/maps/api/js?sensor=false&libraries=places&language='.substr($this->_lng->getLocale(), 0,2));	
	
        return new ViewModel();        
    }
    
    /**
     * longAction() Поиск города по полному названию
     * @access public
     * @return \Zend\View\Model\ViewModel
     */    
    public function longAction()
    {
        $long = $this->params()->fromRoute('long');     // определяю какой город запрошен
        exit('Параметр: '.$long);
    }
    
    /**
     * shortAction() Города по короткому названию
     * @access public
     * @return \Zend\View\Model\ViewModel
     */
    public function shortAction()
    {
        $country    = $this->params()->fromRoute('country');     // определяю какой город запрошен
        $region	    = $this->params()->fromRoute('region');     // определяю какой город запрошен
        $city	    = $this->params()->fromRoute('city');     // определяю какой город запрошен
	
	// оперделяю 404 уведомленно
	if(!$country) return $this->notFoundAction();
	if(!$region) return $this->notFoundAction();
	if(!$city) return $this->notFoundAction();
	
	// подключаю модель городов
	$citiesModel = $this->zfService()->get('cities.Service');
	
	// опеределяю текущую локаль пользователя
	$locale = $citiesModel->getLocaleCode();
	
	// перевожу на киррилицу если локаль английская
	if($locale != 'en') $city =  Translit::translateToCyr($city);	
	
	$res = $citiesModel->getDBCitiesByShort($country, $region, $city);
	
        return new ViewModel(array(
	    'cities'	=>  $res,
	));        
    }    
}
