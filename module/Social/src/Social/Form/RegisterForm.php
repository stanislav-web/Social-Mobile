<?php
namespace Social\Form; // имя в пространстве имен
use Zend\Form\Form;
/**
 * Форма регистрации (старт)
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Social/src/Form/RegisterForm.php
 */
class RegisterForm extends Form
{
    /**
     * Конструктор формы Регистрации
     * @access public
     * @param string $name Имя формы при вызове в шаблоне
     * @return object Form
     */
    public function __construct($translator = null)
    {
        /*
         *  Создаю форму Search
         */
        parent::__construct('register'); // имя формы
        $this->setAttributes( // аттрибуты
                array(
                    'method'    =>  'post',
                    'data-ajax' =>  'false',
                )
        );

        /**
         *  Добавляю поля с аттрибутами
         */

        $this->add(array( // Поле логин
            'type'  =>  'Zend\Form\Element\Text',
            'name'  =>  'login',
            'attributes' => array(
                'id'                =>  'login',
                'required'          =>  true,
                'data-clear-btn'    =>  'true',
                'maxlength'         => '128',
                'data-storage'      =>  'registerLogin',
                'placeholder'       =>  _('Your email or a mobile number'),
                'data-mini'         =>  'true',
                'data-tooltip'      =>  'true',
                'title'             =>  $translator->translate('This field is required and must contain your future Login for authenticate'),
            ),
            'options'   => array(
                    'label' =>  _('E-mail or mobile'),
                )
        ));

        $this->add(array( // идентификатор регистрации
            'type'  =>  'Zend\Form\Element\Hidden',
            'name'  =>  'type',
            'attributes' => array(
                'value'     =>  'register',
            ),
            'options'   => array(
                    'label' => true,
                )
            )
        );

        $this->add(array( // фажок соглашения с условиями
            'type'  =>  'Zend\Form\Element\Checkbox',
            'name'  =>  'agree',
            'attributes'    =>  array(
                'type'      => 'checkbox',
                'id'        => 'agree',
                'checked'   => false,
                'data-mini' =>  'true',
                'data-tooltip'      =>  'true',
                'title'             =>  $translator->translate('Without it register will close for You!'),
            ),
            'options'   => array(
                    'label' =>  _('I read the rules and agree to the terms'),
                    'checked_value' => '1',
                    'unchecked_value' => '',
                    'use_hidden_element' => false
                )
            )
        );

        $this->add(array( // Поле пароль
            'type'  =>  'Zend\Form\Element\Password',
            'name'  =>  'password',
            'attributes' => array(
                'id'                =>  'password',
                'data-clear-btn'    =>  'true',
                'required'          =>  true,
                'placeholder'       =>  _('Wishes password'),
                'data-mini'         =>  'true',
                'data-tooltip'      =>  'true',
                'title'             =>  $translator->translate('Please setup something realy stronger! A\'int worry about, anytime you can reset it'),
            ),
            'options'   => array(
                    'label' =>  _('Password'),
                    )
            )
        );

        $this->add(array( // системное поле защиты от спама
            'name' => 'csrf',
            'type' => 'Zend\Form\Element\Csrf',
            'options'   =>  array(
                'csrf_options' => array(
                        'timeout' => 300 // время истечения токена
                    )
                )
        ));

        $this->add(array( // кнопка submit
            'type'  =>  'Zend\Form\Element\Submit',
            'name' => 'submit',
            'attributes' => array(
                'data-inline'   => 'true',
                'data-icon'     => 'check',
                'value'         =>  _('Register me'),
                'data-mini'     =>  'true',
            ),
        ));
    }
}