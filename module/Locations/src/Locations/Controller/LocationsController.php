<?php
namespace Locations\Controller; // пространтво имен текущего контроллера

// объявляю зависимости от главных Zend ActionConroller , Zend ViewModel
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
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
 * @filesource /module/Social/src/Social/Controller/LocationsController.php
 */
class LocationsController extends AbstractActionController
{
    /**
     * $_lng Свойство объекта Zend l18 translator
     * @access protected
     * @var type object
     */
    protected $_lng;
    
    /**
     * indexAction() Вывод всей карты городов с зарегистрированными пользователями
     * Обязательно должен быть КЭШ!
     * @access public
     * @return \Zend\View\Model\ViewModel
     */    
    public function indexAction()
    {
	$this->_lng     = $this->getServiceLocator()->get('MvcTranslator'); // загружаю переводчик
	$renderer       = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
	$renderer->headTitle($this->_lng->translate('Search for friends here on this Map!', 'default'));
	// Добавляю скрипты сервисов Google
	$viewrender = $this->getServiceLocator()->get('viewhelpermanager')->get('headScript');
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
	$citiesModel = $this->getServiceLocator()->get('cities.Service');
	
	// опеределяю текущую локаль пользователя
	$locale = $citiesModel->getLocaleCode();
	
	// перевожу на киррилицу если локаль английская
	if($locale != 'en') $city =  Translit::translateToCyr($city);	
	
	$res = $citiesModel->getDBCitiesByShort($country, $region, $city);
	
        return new ViewModel([
	    'cities'	=>  $res,
	]);        
    }    
    
    /*
     * jsonAction() Ajax action
     * @access public
     * @return json
     */
    public function jsonAction()
    {
        $request    = $this->getRequest();
        $response   =   [];

        if($request->isXmlHttpRequest())
        {   
            // Проверяю метод передачи
            if($request->isPost()) $requestData =   $request->getPost();
            else $requestData =   $request->getQuery();

            switch($requestData['request']) 
            {
                case 'country': // поиск в странах
                    $response = $this->getServiceLocator()
                        ->get('countries.Service');
                break;

                case 'region':  // поиск в регионах
                    $response = $this->getServiceLocator()
                        ->get('regions.Service')
                        ->getDBRegions($requestData['country_id']);
                break;
            
                case 'city':    // поиск в городах
                    $response = $this->getServiceLocator()
                        ->get('cities.Service')
                        ->getDBCities(null, $requestData['region_id']);
                break;
                    
                default: 
                break;
            }
            // Получаю модель
        }     
        return new JsonModel($response);
    }     
}
