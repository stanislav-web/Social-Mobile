<?php
namespace Social\Form; // имя в пространстве имен
use Zend\Form\Form;
/**
 * Простая форма поиска в меню пользователя
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Social/src/Form/MenuSearchForm.php
 */
class MenuSearchForm extends Form
{
    /**
     * Конструктор формы Поиска Simple
     * @access public
     * @param string $name Имя формы при вызове в шаблоне
     * @return object Form
     */
    public function __construct($name = null)
    {
        /*
         *  Создаю форму Search
         */
        parent::__construct('menusearch'); // имя формы
        $this->setAttributes( // аттрибуты
                array(
                    'method'    =>  'post',
                    'data-ajax' =>  'true',
                )
        );

        /**
         *  Добавляю поля с аттрибутами
         */

        $this->add(array( // Поле поиска
            'name'  =>  'menusearch',
            'type'  =>  'Zend\Form\Element\Text',
            'attributes' => array(
                'id'                => 'search',
                'data-clear-btn'    => 'true',
                'placeholder'       => _('Look for...'),
                'data-mini'         =>  'true',
            ),
        ));
    }
}