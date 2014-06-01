<?php
namespace Payments\Controller; // Namespaces of current controller

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Debug\Debug;
use Payments\Exception;
use Payments\Form;

/**
 * Payments service controller
 * @package Zend Framework 2
 * @subpackage Payments 
 * @since PHP >=5.4
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Payments/src/Payments/Controller/PaymentsController.php
 */
class PaymentsController extends AbstractActionController
{
    /**
     * $provider provider name
     * @access public
     * @var object Payments\Factory\ProviderFactory
     */
    public $provider    =   null;
    
    /**
     * selectAction() Selected payments form
     * @access public
     * @return object \Zend\View\ViewModel
     */     
    public function selectAction()
    {
        $id = $this->params('id');     // get provider
        
        try 
        {
            $factory        = $this->getServiceLocator()->get('Payments\Factory\ProviderFactory');
            // get selected provider
            $this->provider       = $factory->getProvider($id);
            
            // Head Title 
            $renderer	= $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
            $renderer->headTitle($id);            
            
            return new ViewModel([
                'provider'  =>  $id,
                'params'    =>  $this->provider->getFormParams(), // get required fields for costom template
            ]);
            
        } 
        catch(Exception\ExceptionStrategy $e) 
        {
            echo $e->getMessage();
        }
    }
    
    
    /**
     * indexAction() Pay methods
     * @access public
     * @return object \Zend\View\ViewModel
     */    
    public function indexAction()
    {    
        
        // get providers select form
        $config = $this->getServiceLocator()->get('Config')["Payments\Provider\Config"];
        $form   = new Form\ProvidersList($config);

        // check if request exist
        $request    = $this->getRequest(); 

        if($request->isPost() /*&& $request->isXmlHttpRequest()*/)
        {
            $messages    =   [];
            
            // ajax request handler
            // check and set POST request throught filters
            $validator =    new Form\ProvidersListValidator(array_keys($config));
            $form->setInputFilter($validator->getInputFilter());
            $form->setData($request->getPost());
            
            if($form->isValid()) // if get valid values
            {
                // redirect to payment system terminal
                return $this->redirect()->toUrl('/pay/'.$form->getData()['id']);
            }
            else 
            {
                // set error messages                
                foreach($form->getMessages() as $key   => $error)
                {
                    if(!empty($error) && $key != 'submit') 
                    {
                        foreach($error as $keyer => $rower)
                        {
                            // save error(s) per-element that
                            $this->flashMessenger()->addMessage($rower);   
                        }
                    }
                }
                return $this->redirect()->refresh();
            }                
        }
        else
        {
            // get Provider's Factory
            $factory = $this->getServiceLocator()->get('Payments\Factory\ProviderFactory');
            
            // take all providers for building form
            return new ViewModel([
                'selectForm'    =>  $form,
                'flashMessages' => $this->flashMessenger()->getMessages()
            ]);           
        }
    }
    
    /**
     * processAction() Pay handler
     * @access public
     * @return object \Zend\View\ViewModel
     */    
    public function processAction()
    {
        $request    = $this->getRequest(); // get request from payment form
        
        try 
        {
            $factory    = $this->getServiceLocator()->get('Payments\Factory\ProviderFactory');
            
            // detect request params
            if($request->isPost()) $formData   =   $request->getPost();
            else $formData   =   $request->getQuery();
           
            // get provider

            $this->provider =   $factory->getProvider($formData->provider);
            
            // active request handling...
            
            $this->provider->processForm($formData);
            
        } 
        catch(Exception\ExceptionStrategy $e) 
        {
            echo $e->getMessage();
        }

        return $this->redirect()->toRoute('payments');
    }    
}
