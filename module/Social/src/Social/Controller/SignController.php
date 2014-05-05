<?php
namespace Social\Controller; // пространтво имен текущего контроллера

// объявляю зависимости от главных Zend ActionConroller , Zend ViewModel
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Social\Form; // Констуктор форм
use Zend\Session\Container; // Сессии (для установки переменных регистрации)
use SW\String\Translit;

/**
 * Контроллер обработки страниц авторизации, регистрации, восстановления аккаунта
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Social/src/Social/Controller/SignController.php
 */
class SignController extends AbstractActionController
{
    /**
     * $_lng Свойство объекта Zend l18 translator
     * @access protected
     * @var type object
     */
    protected $_lng;

    /**
     * $_register Свойство хранит регистрационную информацию в сессии
     * @access protected
     * @var type object
     */
    protected $_register;

    /**
     * $_restore Свойство хранит информацию для антифлуда при восстановения пароля
     * @access protected
     * @var type object
     */
    protected $_restore;
    
    /**
     * zfService() Менеджер зарегистрированных сервисов ZF2
     * @access public
     * @return ServiceManager
     */
    public function zfService()
    {
        return  $this->getServiceLocator();
    }
    
    /**
     * step1Action() STEP 1. Заполнение профиля в сессию
     * @access public
     * @return \Zend\View\Model\ViewModel
     */
    public function step1Action()
    {
        // Объявляю сервисы и сессию
        $this->_register        = new Container('register'); // достаю контейнер сессии с регистрацией
        // Проверяю наличие в сесси заполненых в прошлой форме данных
        if($this->_register->offsetExists('login') && $this->_register->offsetExists('password') && $this->_register->offsetExists('csrf'))
        {
            $this->_lng             = $this->zfService()->get('MvcTranslator'); // загружаю переводчик
            $countries              = $this->zfService()->get('countries.Service'); // достаю страны
            $registerStep1Form      = new Form\RegisterStep1Form($countries, $this->_lng); // Форма регистрации (шаг 1)
            $request    = $this->getRequest(); // Запрос через форму
            if($request->isPost()) //  пошла POST форма
            {
                // Устанавливаю фильтры и валидацию
                $registerProfileValidator   = $this->zfService()->get('registerStep1.Validator');
                $registerStep1Form->setInputFilter($registerProfileValidator->getInputFilter());

                $arrPost = $request->getPost()->toArray();
                $arrFile = $request->getFiles()->toArray();

                $data = array_merge(
                    $arrPost,
                    array('file' => $arrFile['filename']['name'])
                );

                $registerStep1Form->setData($data); // применяю валидацию
                if($registerStep1Form->isValid())
                {
                    // Если форма валидна, проверяю параметры upload файла если он есть
                    $result = $registerStep1Form->getData(); // для хранения результата
                    if(isset($data['file']) && $data['file'] !='')
                    {
                        // Вызываю конфиг для чтения из JSON
                        $reader = new \Zend\Config\Reader\Json();
                        $pathConfig = (object)$reader->fromFile(DOCUMENT_ROOT.DS.'config'.DS.'paths.json'); // настройки директорий

                        // Начинаю обработку загружаемого файла
                        $s = array('min' => '3kb','max' => '2mb');
                        $validFile = new \Zend\File\Transfer\Adapter\Http();
                        $size = new \Zend\Validator\File\Size($s); // фильтр на размер
                        $isimage = new \Zend\Validator\File\IsImage(); // валидация на изображение
                        $validFile->setValidators(array($size, $isimage), $data['file']);
                        if(!$validFile->isValid())
                        {
                            // Записую ошибки при валидации файла
                            $imgErrors = array();
                            $validFileErrors = $validFile->getErrors(); // коды ошибок

                            // Переопределяю стандартные шаблон ошибок
                            $errorTemplates = array(
                                    'fileIsImageFalseType'  => $this->_lng->translate("Download file is no image", 'errors'),
                                    'fileSizeTooSmall'  => $this->_lng->translate("Download image size is too small.", 'errors')." ".$this->_lng->translate("The should be less than", 'errors').' '.$s['min'],
                                    'fileSizeTooBig'    => $this->_lng->translate("Download image size is too big.", 'errors')." ".$this->_lng->translate("The should be more than", 'errors').' '.$s['max'],
                                    'fileIsImageNotDetected' => $this->_lng->translate("The mimetype could not be detected from the file", 'errors'),
                                    'fileIsImageNotReadable' => $this->_lng->translate("File is not readable or does not exist", 'errors'),
                            );
                            foreach($validFileErrors as $key => $row)
                            {
                                $imgErrors[] = $errorTemplates[$row]; //$this->_lng->translate($row, 'error');
                            }
                            if(!empty($imgErrors)) $registerStep1Form->setMessages(array('filename' => $imgErrors)); // в форму передаю ошибки

                        }
                        else
                        {
                            // У нас все в порядке! Загружаю файл (изображение)
                            //$validFile->setDestination('public/tmp/');
                            $fileinfo = $validFile->getFileInfo();
                            // Рандомизирую имя файла
                            $validFile->addFilter('File\Rename',
                                array(
                                    'target' => $pathConfig->tmp_original_images_dir.DS.Translit::transliterate($fileinfo['filename']['name'], '', true),
                                    'overwrite' => true,
                                    //'randomize' => true,
                                )
                            ); // загрузил оригинал
                            if($validFile->receive($fileinfo['filename']['name']))
                            {
                                // Если загружаемый файл полностью валидный
                                // Подключаю сервисы для обработки изображений
                                if(extension_loaded('imagemagic')) $thumbnailer =  $this->zfService()->get('ImageMagic.Service'); 
                                else  $thumbnailer =  $this->zfService()->get('GD2.Service'); 
                                
                                // Достаю информацию о загруженном файле

                                $fullname = $validFile->getFileName(); // полный путь
                                $basename = basename($validFile->getFileName()); // имя файла

                                // Создаю превью и профильное изображение из оригинала
                                
                                $thumbnailer($fullname);
                                $thumbnailer->createThumb($pathConfig->tmp_thumb_images_dir.DS.$basename, 65, 65);    // сохраняю в превью
                                $thumbnailer->createThumb($pathConfig->tmp_profile_images_dir.DS.$basename, 320, 240); // сохраняю в профиль
                                
                                // Ставлю водяной знак
                                
                                $thumbnailer->setWatermark($pathConfig->tmp_profile_images_dir.DS.$basename, $pathConfig->watermark_dir.DS.'watermark.png');
                                $thumbnailer->close();


                                // Сохраняю результат обработки файла
                                
                                $result['photo']    = $fullname;
                                $result['profile']  = $pathConfig->tmp_profile_images_dir.DS.$basename;
                                $result['thumb']    = $pathConfig->tmp_thumb_images_dir.DS.$basename;
                            }
                        }
                    }

                    if(!isset($imgErrors))
                    {
                        // Если нет никаких ошибок, записываю в сессию содержимое этой формы и отправляю дальше
                        if(isset($result['photo']) && isset($result['thumb']) && isset($result['profile']))
                        {
                            $this->_register->photo         = basename($result['photo']);
                            $this->_register->original      = $result['photo'];
                            $this->_register->thumb         = $result['thumb'];
                            $this->_register->profile       = $result['profile'];
                        }
                        $this->_register->name          = $result['name'];
                        $this->_register->gender        = $result['gender'];
                        $this->_register->country_id    = $result['country'];
                        $this->_register->birthday      = $result['birthday'];
                        return $this->redirect()->toUrl('/sign/step2');
                    }
                    else
                    {
                        // Уничтожаю сессию с форм
                        if($this->_register->offsetExists('photo'))         $this->_register->offsetUnset('photo');
                        if($this->_register->offsetExists('original'))      $this->_register->offsetUnset('original');
                        if($this->_register->offsetExists('thumb'))         $this->_register->offsetUnset('thumb');
                        if($this->_register->offsetExists('profile'))       $this->_register->offsetUnset('profile');
                        if($this->_register->offsetExists('name'))          $this->_register->offsetUnset('name');
                        if($this->_register->offsetExists('gender'))        $this->_register->offsetUnset('gender');
                        if($this->_register->offsetExists('country_id'))  $this->_register->offsetUnset('country_id');
                        if($this->_register->offsetExists('birthday'))      $this->_register->offsetUnset('birthday');
                    }
                }
            }
            
            // Устанавливаю заголовок со страницы
            $renderer = $this->zfService()->get('Zend\View\Renderer\PhpRenderer');
            $renderer->headTitle($this->_lng->translate('Setup your basic profile. Step 1', 'default'));
            
            $registerView = new ViewModel(
                array(
                    'registerProfileForm'   => $registerStep1Form, // Форма регистрации (шаг2)
                    )
            );
            return $registerView;
        }
        else return $this->redirect()->toUrl('/sign'); // отправляю назад
    }

    /**
     * step2Action() STEP 2. Заполнение профиля в сессию
     * @access public
     * @return \Zend\View\Model\ViewModel
     */
    public function step2Action()
    {
        $this->_register        = new Container('register'); // достаю контейнер сессии с регистрацией

        if($this->_register->offsetExists('login') && $this->_register->offsetExists('password') && $this->_register->offsetExists('csrf')
           && $this->_register->offsetExists('name') && $this->_register->offsetExists('gender') && $this->_register->offsetExists('country_id')
           && $this->_register->offsetExists('birthday'))
        {
            // Если прошли все обязательные параметры в сессию, продолжаю регистрацию
            $this->_lng         = $this->zfService()->get('MvcTranslator'); // загружаю переводчик
            $regions = $this->zfService()->get('regions.Service'); // достаю регионы
            $registerStep2Form   = new Form\RegisterStep2Form($regions, $this->_register->offsetGet('country_id'), $this->_lng); // Форма регистрации. Шаг 2
            /*
             *  Если сессия существует
             */
            $request    = $this->getRequest(); // Запрос через форму

            if($request->isPost()) //  пошла POST форма
            {
                if(isset($request->getPost()->back))
                {

                    // Возвращию на шаг назад. Уничтожаю сессию с форм
                    if($this->_register->offsetExists('photo'))         $this->_register->offsetUnset('photo');
                    if($this->_register->offsetExists('original'))      $this->_register->offsetUnset('original');
                    if($this->_register->offsetExists('thumb'))         $this->_register->offsetUnset('thumb');
                    if($this->_register->offsetExists('profile'))       $this->_register->offsetUnset('profile');
                    if($this->_register->offsetExists('name'))          $this->_register->offsetUnset('name');
                    if($this->_register->offsetExists('gender'))        $this->_register->offsetUnset('gender');
                    if($this->_register->offsetExists('country_id'))  $this->_register->offsetUnset('country_id');
                    if($this->_register->offsetExists('birthday'))      $this->_register->offsetUnset('birthday');
                    return $this->redirect()->toUrl('/sign/step1');
                }
                else
                {
                    // Обрабатываю выборку валидатором

                    $registerProfileValidator   = $this->zfService()->get('registerStep2.Validator'); // валидатор формы регистрации (финал)
                    $registerStep2Form->setInputFilter($registerProfileValidator->getInputFilter()); // устанавливаю фильтры на форму восстановления пароля
                    $registerStep2Form->setData($request->getPost());
                    if($registerStep2Form->isValid())
                    {
                        // С формой все впорядке , сохраняю и перебрасываю на третий шаг
                        $result = $registerStep2Form->getData(); // для хранения результата
                        $this->_register->region_id          = $result['region'];
                        return $this->redirect()->toUrl('/sign/step3');
                    }
                    else
                    {
                        // Чищу запись
                        if($this->_register->offsetExists('region_id')) $this->_register->offsetUnset('region_id');
                    }
                }
            }
            
            // Устанавливаю заголовок со страницы
            $renderer = $this->zfService()->get('Zend\View\Renderer\PhpRenderer');
            $renderer->headTitle($this->_lng->translate('Setup your basic profile. Step 2', 'default'));
            
            $registerView = new ViewModel(
                array(
                    'registerProfileForm'   => $registerStep2Form, // Форма регистрации (шаг 2)
                    )
            );
            return $registerView;
        }
        else return $this->redirect()->toUrl('/sign'); // отправляю назад
    }

    /**
     * step3Action() STEP 3. Заполнение профиля в сессию
     * @access public
     * @return \Zend\View\Model\ViewModel
     */
    public function step3Action()
    {

        $this->_register        = new Container('register'); // достаю контейнер сессии с регистрацией
        if($this->_register->offsetExists('login') && $this->_register->offsetExists('password') && $this->_register->offsetExists('csrf')
           && $this->_register->offsetExists('name') && $this->_register->offsetExists('gender') && $this->_register->offsetExists('country_id')
           && $this->_register->offsetExists('birthday') && $this->_register->offsetExists('region_id'))
        {
            // Если прошли все обязательные параметры в сессию, продолжаю регистрацию
            $this->_lng         = $this->zfService()->get('MvcTranslator'); // загружаю переводчик
	    
            $cities = $this->zfService()->get('cities.Service'); // достаю города
            $letters = $cities->getFirstLetter($this->_register->offsetGet('country_id'), $this->_register->offsetGet('region_id')); // достаю первые буквы
	    
            $registerStep3Form   = new Form\RegisterStep3Form($cities, $this->_register->offsetGet('country_id'), $this->_register->offsetGet('region_id'),$this->_lng); // Форма регистрации. Шаг 3
            /*
             *  Если сессия существует
             */
            $request    = $this->getRequest(); // Запрос через форму

            if($request->isPost()) //  пошла POST форма
            {
                if(isset($request->getPost()->back))
                {
                    // Возвращию на шаг назад. Уничтожаю сессию с форм
                    if($this->_register->offsetExists('region_id'))  $this->_register->offsetUnset('region_id');
                    return $this->redirect()->toUrl('/sign/step2');
                }
                else
                {
                    // Обрабатываю выборку валидатором
                    $registerProfileValidator   = $this->zfService()->get('registerStep3.Validator'); // валидатор формы регистрации (финал)
                    $registerStep3Form->setInputFilter($registerProfileValidator->getInputFilter()); // устанавливаю фильтры на форму восстановления пароля
                    $registerStep3Form->setData($request->getPost());
                    if($registerStep3Form->isValid())
                    {
                        // С формой все впорядке , сохраняю и делаю финальную обработку
                        $result                         = $registerStep3Form->getData(); // для хранения результата
                        $this->_register->city_id       = $result['city'];
                        $this->_register->online        = 1;
                        $this->_register->activation    = 1;
                        $this->_register->ip            = $request->getServer('REMOTE_ADDR');
                        $this->_register->agent         = $request->getServer('HTTP_USER_AGENT');
                        // Генерирую пароль
                        $this->_register->password = md5(md5($this->_register->password).$this->_register->csrf); // md5(md5(ПАРОЛЬ)+КОД ЗАЩИТЫ)

                        // @TODO Сессия вся сформирована! Что делать ?
                        // А делать нужно сервис, который запишет регистрацию в zf_users и zf-users_profile,
                        // отправит на почту уведомление о регистрации или отправит СМС
                        // ну и тут же авторизирует и перенаправит на свою Анкету
                        // Notice: необходим будет пароль в чистом виде, но только для отправки на почту или телефон
                        try {
                            // 1. Сохраняю в базу и беру результат (Регистрация)

                            $this->zfService()->get('sign.Model')->signRegister();

                            // 2. Переношу аватару из tmp в папку пользователя

                            if($this->_register->offsetExists('photo'))
                            {
                                // Вызываю конфиг для чтения из JSON
                                $reader = new \Zend\Config\Reader\Json();
                                $pathConfig = (object)$reader->fromFile(DS.'config'.DS.'paths.json'); // настройки директорий

                                // Если были зарегистрированы файлы

                                
                                if($this->_register->original)
                                {   // Перемещаю оригинал
                                    $removeOrig      = new \SW\FileSystem\FileSys($this->_register->original, $pathConfig->original_images_dir.DS.$this->_register->user_id.DS.$this->_register->photo, '0777');
                                    $removeOrig->move();
                                    $removeOrig->access('0755');
                                }
                                
                                if($this->_register->profile)
                                {   // Перемещаю профиль
                                    $removeProfile    = new \SW\FileSystem\FileSys($this->_register->profile, $pathConfig->profile_images_dir.DS.$this->_register->user_id.DS.$this->_register->photo, '0777');
                                    $removeProfile->move();
                                    $removeProfile->access('0755');
                                }
                                
                                if($this->_register->thumb)
                                {
                                    // Перемещаю thumb
                                    $removeThumb    = new \SW\FileSystem\FileSys($this->_register->thumb, $pathConfig->thumb_images_dir.DS.$this->_register->user_id.DS.$this->_register->photo, '0777');
                                    $removeThumb->move();
                                    $removeThumb->access('0755');
                                }
                            }

                            // 3. Авторизирую

                            $this->zfService()->get('sign.Model')->signAuth($this->_register->login, $this->_register->passwordfree, $remember = 1);

                            // 4. Создаю событие о регистрации новому пользователю
                            // передаю шаблон события и данные пользователя в виде массива объектов
                            
                            $this->zfService()->get('userEvents.Model')->setEventForRegister('register',
                                    array(
                                        'id'    =>  $this->_register->user_id,
                                        'name'  =>  $this->_register->name,
                                        'title' =>  array(
                                        'ru' => $this->_lng->translate('Social Mobile', 'default', 'ru_RU'),					    'en' => $this->_lng->translate('Social Mobile', 'default', 'en_US'),				        'ua' => $this->_lng->translate('Social Mobile', 'default', 'ua_UA'),					    )
                                    )
                             );			    
			    
			    // 5. Создаю уведомление зарегистрированному на почту или SMS
			    
                            if(is_numeric($this->_register->login))
                            {
                                // Подключаю SMS сервис. Номер идентифицирован
                            }
                            else
                            {
                                // Организовую отправку на email. PS: Почтовые события настроены из конфига Базы!
                                $mailer = $this->zfService()->get('mail.Service');
                                $plugContent = $this->zfService()->get('plugContent.Service');
                                $tplConfig = $plugContent('mailtemplates')->get('register'); // шаблон регистрации из БД
                                $message = $mailer->createHtmlMessage($this->_register->login, sprintf($tplConfig->subject, $this->_register->name, $this->_lng->translate('Social Mobile', 'default')), $tplConfig->template, array(
                                    'login'         => $this->_register->login,
                                    'password'      => $this->_register->passwordfree,
                                ));
                                $mailer->send($message);
                            }
                            
                            // 6. Очищаю контейнер сессии регистрации
                            $this->_register->getManager()->getStorage()->clear('register');

                        }
                        catch(Zend_Exception $e)
                        {
                                echo "Caught exception: " . get_class($e) . "\n";
                                echo "Message: " . $e->getMessage() . "\n";
                        }
                        return $this->redirect()->toUrl('/profile');
                    }
                }
            }
            
            // Устанавливаю заголовок со страницы
            $renderer = $this->zfService()->get('Zend\View\Renderer\PhpRenderer');
            $renderer->headTitle($this->_lng->translate('Finaly step of registration', 'default'));
            $registerView = new ViewModel(
                array(
		    'letters'		    => $letters,
                    'registerProfileForm'   => $registerStep3Form, // Форма регистрации (шаг 2)
                    )
            );
            return $registerView;
        }
        else return $this->redirect()->toUrl('/sign'); // отправляю назад
    }

    /**
     * indexAction() По умолчанию , это действие при входе на страницу Sign (авторизация, регистрация , форма восстановления)
     * @access public
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $errorMsg           = array(
            'auth'      =>  '',
            'reg'       =>  '',
            'restore'   =>  ''
        ); // Для сообщений об ошибках

        $successMsg         = array(
            'auth'      =>  '',
            'reg'       =>  '',
            'restore'   =>  ''
        ); // Для валидных сообщений
        $floodTime          = '60'; // Время до повторной отправки (антифлуд)
        $this->_lng         = $this->zfService()->get('MvcTranslator'); // загружаю переводчик

        // Достаю параметры с GET или POST
        
        $restoreStringGet = $this->params()->fromQuery('restore');
        $restoreStringPost = $this->params()->fromPost('restore');
        
        // Восстановение доступа по коду Email и SMS
        if($restoreStringGet == 'mobile' || $restoreStringPost == 'mobile')
        {
            // Восстановление по SMS

        }
        elseif($restoreStringGet == 'email' || $restoreStringPost == 'email')
        {
            // Восстановление по email

            if($restoreStringGet)
            {
                // Если GET то показую форму
                $emailString       = $this->params()->fromQuery('email'); // получаю email
                $keyString         = $this->params()->fromQuery('key'); // получаю код
}
            else
            {
                // Обработчк $_POST, валидация и сверка > выдача результата
                $SetPasswordFormEmail    = new Form\SetPasswordFormEmail($this->_lng); // Форма установки пароля
                $SetPasswordFormEmailValidator   = $this->zfService()->get('setpassemail.Validator'); // валидатор формы восст. по email
                $SetPasswordFormEmail->setInputFilter($SetPasswordFormEmailValidator->getInputFilter()); // устанавливаю фильтры на форму восст. пароля
                $SetPasswordFormEmail->setData($request->getPost());
                if($SetPasswordFormEmail->isValid())
                {
                    // Все отлично! Форма валидна
                    $fromResult = $SetPasswordFormEmail->getData();

                    // Проверяю валидность паролей и устанавливаю новый пароль

                    //\Zend\Debug\Debug::dump($fromResult);

                    $dbResult = $this->zfService()->get('sign.Model')->setNewPassword($fromResult);
                    if(!$dbResult) $errorMsg['restore'] = 'This key is not correct!';
                    else
                    {
                        // Идентификация прошла успешно, пароль изменен, авторизирую и кидаю на профиль
                        $auth = $this->zfService()->get('sign.Model')->signAuth($fromResult['login'], $fromResult['password'], $remember = 0);
                        if($auth)
                        {
                            // успешная авторизация
                            $this->zfService()->get('sign.Model')->dropRestoreCode($dbResult->id, $type = 'email');     // Удаляю ключ
                            $this->redirect()->toUrl('profile');                                // Перенаправляю в личный кабинет
                        }
                        else
                        {
                            // ошибка при авторизации
                            $errorMsg['restore'] = "Authentication failed! Wrong Login or Password"; // Авторизация провалена
                        }
                    }
                }
            }
            
            // Устанавливаю заголовок со страницы
            $renderer = $this->zfService()->get('Zend\View\Renderer\PhpRenderer');
            $renderer->headTitle($this->_lng->translate('Setup new password', 'default'));
            
            $setPassView = new ViewModel(array(
                                        'SetPasswordFormEmail'   => $SetPasswordFormEmail,
                                        'email'         => (isset($emailString)) ? $emailString : $request->getPost()->login,
                                        'keyString'     => (isset($keyString)) ? $keyString : $request->getPost()->mail_code,
                                        'restore'       => (isset($restoreStringGet)) ? $restoreStringGet : 'email',
                                        'errorMsg'      => $errorMsg['restore'], // сообщения об ошибках
                                        'successMsg'    => $successMsg['restore'], // успешно
                                    )
            );
            $setPassView->setTemplate('social/sign/setpasswordemail'); // показываю шаблон установки кода
            return $setPassView;
        }

        /**
         * Делаю проверку на авторизацию
         */
        if($this->zfService()->get('authentification.Service')->hasIdentity())
        {
            // если уже авторизирован, выносим его отсюда
            // $auth->clearIdentity();
            $errorMsg['auth'] = 'You are already authorized. If need to do sign with another account please re-authorize';
            return $this->redirect()->toUrl('/user');
        }

        /**
         * Объявляю формы, которые должны присутствовать на странице
         */

        $registerForm       = new Form\RegisterForm($this->_lng); // Форма регистрации
        $authForm           = new Form\AuthForm($this->_lng); // Форма авторизации
        $restoreForm        = new Form\RestoreForm($this->_lng); // Форма восстановления пароля

        /**
         * Проверяю, когда форма была отправлена
         */
        $request            = $this->getRequest(); // Запрос через форму        

        if($request->isPost())
        {
            if(isset($request->getPost()->type))
            {
                switch($request->getPost()->type)
                {
                    case 'register': // Обработка регистрации
                    $registerValidator   = $this->zfService()->get('register.Validator'); // валидатор формы регистрации
                    $registerForm->setInputFilter($registerValidator->getInputFilter()); // устанавливаю фильтры на форму восстановления пароля
                    $registerForm->setData($request->getPost());
                    if($registerForm->isValid())
                    {
                        // Фиксирую результат формы в сессию, контейнер `register`
                        $result = $registerForm->getData();
                        $this->_register = new Container('register');
                        if(!isset($this->_register->init))
                        {
                            $this->_register->init = 1; // ID инициализации
                        }
                        $this->_register->login = $result['login']; // `login`
                        $this->_register->password = $result['password']; // `password`
                        $this->_register->passwordfree  = $result['password']; // оригинал пароля

                        $this->_register->csrf = $result['csrf']; // `csrf`
                        return $this->redirect()->toUrl('sign/step1'); // перехожу на следующий шаг
                    }
                    break;

                    case 'auth': // Обработка авторизации
                    $authValidator   = $this->zfService()->get('auth.Validator'); // валидатор формы авторизации
                    $authForm->setInputFilter($authValidator->getInputFilter()); // устанавливаю фильтры на форму авторизации
                    $authForm->setData($request->getPost());
                    if($authForm->isValid())
                    {
                        // теперь проверяю по базе пользователей
                        // вытягиваю сервис авторизации
                        
                        $auth = $this->zfService()->get('sign.Model')->signAuth($request->getPost('login'), $request->getPost('password'), $request->getPost('remember'));
                        if($auth) return $this->redirect()->toRoute('profile'); // успешная авторизация
                        else
                        {
                            // ошибка при авторизации
                            $errorMsg['auth'] = "Authentication failed! Wrong Login or Password"; // Авторизация провалена
                        }
                    }

                    break;
                    case 'restore': // Обработка формы восстановления пароля

                    // Проверка на флуд
                    $this->_restore = new Container('restore');

                    if($this->_restore->offsetExists('flood') && $this->_restore->flood > time())
                    {
                        // Все.. пиздец, начали флудить

                        $floodTime = ($this->_restore->flood -time());
                        $errorMsg['restore'] = "Restore failed! You are alredy recieved a message about the password recovery. Please wait %s to resubmit"; // обнаружен флуд

                    }
                    else
                    {

                        // Все ок.. пошил отправлять
                        $this->_restore->getManager()->getStorage()->clear('restore'); // очищаю контейнер с временем
                        $restoreValidator   = $this->zfService()->get('restore.Validator'); // валидатор формы восстановления
                        $restoreForm->setInputFilter($restoreValidator->getInputFilter()); // устанавливаю фильтры на форму восстановления пароля
                        $restoreForm->setData($request->getPost());
                        if($restoreForm->isValid())
                        {
                            /*
                            * Вытягиваю из модели метод восстановления, и отправляю
                            */
                            $result = $restoreForm->getData();
                            if(is_numeric($result['resign']) == true)
                            {
                                //@TODO Восстановление по SMS. Coming soon...
                                // Генерирую код восстановления для пересылки по SMS
                                $resultArr = $this->zfService()->get('sign.Model')->setRestore($result['resign'], 'mobile');
                                $successMsg['restore'] = "Instructions for restoration of the password has been sent on your mobile"; // Успешно

                            }
                            else
                            {
                                // Генерирую код восстановления для пересылки по email
                                $resultArr = $this->zfService()->get('sign.Model')->setRestore($result['resign'], 'email');
                                // Организовую отправку на email. PS: Почтовые события настроены из конфига Базы!
                                $mailer = $this->zfService()->get('mail.Service');
                                $plugContent = $this->zfService()->get('plugContent.Service');
                                $tplConfig = $plugContent('mailtemplates')->get('restore'); // шаблон восстановления из БД 
                                $message = $mailer->createHtmlMessage($result['resign'], sprintf($tplConfig->subject, $resultArr['name'], $this->_lng->translate('Social Mobile', 'default')), $tplConfig->template, array(

                                        // Создаю контент, передаю шаблон из базы
                                        'content'       => $tplConfig->message,
                                        'name'          => $resultArr['name'],
                                        'email'         => $result['resign'],
                                        'siteurl'       => $request->getServer('HTTP_HOST'),
                                        'sitename'      => $this->_lng->translate('Social Mobile', 'default'),
                                        'restorepage'   => '/sign/?restore=email',
                                        'restorekey'    => $resultArr['code'],
                                ));
                                $mailer->send($message);
                                $successMsg['restore'] = "Instructions for restoration of the password has been sent on your email"; // Успешно
                            }
                            $this->_restore->flood = time()+180; // к текущему времени 3 min
                        }
                    }
                    break;
                }
            }
        }
        
        // Устанавливаю заголовок со страницы
        $renderer = $this->zfService()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle($this->_lng->translate('Sign in', 'default'));
            
        $view = new ViewModel();

        /**
         * Форма регистрации
         */
        $registerView = new ViewModel(
                                array(
                                        'regForm'   => $registerForm,
                                    ) // форма регистрации
                        );
        $registerView->setTemplate('social/sign/register');
        $view->addChild($registerView, 'registerTemplate');

        /**
         * Форма авторизации
         */
        $authView = new ViewModel(
                                array(
                                        'authForm'      => $authForm, // форма авторизации
                                        'errorMsgAuth'  => $errorMsg['auth'], // сообщения об ошибках
                                    )
                    );
        $authView->setTemplate('social/sign/auth');
        $view->addChild($authView, 'authTemplate');

        /**
         * Форма восстановления аккаунта
         */
        $restoreView = new ViewModel(
                                array(
                                        'restoreForm'       => $restoreForm,// форма восстановления
                                        'successMsgRestore' => $successMsg['restore'], // сообщение succcess
                                        'errorMsgRestore'   => $errorMsg['restore'],   // сообщения об ошибках
                                        'floodTime'         => $floodTime,  // секунд до повторной отправки

                                    ) // форма восстановления аккаунта
                );
        $restoreView->setTemplate('social/sign/restore');
        $view->addChild($restoreView, 'restoreTemplate');

        return $view;
    }
}
