<?php
namespace Payments\Form; // declare namespace for the current form of ProvidersList

use Zend\Form\Form;
use Zend\Captcha;
use Payments\Exception;

/**
 * Payments list form
 * @package Zend Framework 2
 * @subpackage Payments
 * @since PHP >=5.4
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Payments/src/Payments/Form/ProvidersList.php
 */
class ProvidersList extends Form
{
    
    /**
     * $_config Form configuration
     * @access protected
     * @var  array
     */
    private $_config    =   null;
    
    /**
     * Initialize form
     * @param array $config (from providers/)
     * @access public
     * @return object Form
     */
    public function __construct(array $config)
    {
        parent::__construct('pay'); // form name

        $this->setAttributes([
            'method'    =>  'post',
        ]);
        
        // Add form fields from config
        $selectList =   ['' => 'Please select payment method'];
        
        foreach($config as $key => $value) 
        {
            // ... add existing payments providers to list
            $selectList[$key] = $key;
        }

        $this->add([
            'type' => 'Zend\Form\Element\Select',
            'name' => 'id',
            'options' => [
                'label'     => '',
                'value_options' =>  $selectList
            ]
        ]);       
        
        /**
        $this->add([
            'type' => 'Zend\Form\Element\Captcha',
            'name' => 'captcha',
            'attributes'    => [
                'placeholder'     =>  _('Please verify you are human'),
            ],            
            'options' => [
                'captcha'   => new Captcha\Figlet([
                    'name'      =>  'captcha',
                    'wordLen'   =>  6,
                    'timeout'   =>  300,
                ]),
            ],
        ]);        
        */
        
        $this->add([ // open dialog button
            'name'  => 'submit',
            'type'  =>  'Zend\Form\Element\Submit',
            'attributes'    => [
                'value'     =>  _('Choise'),
            ],
        ]);        
    }
}