<?php
namespace Decoda;
use Zend\View\Helper\AbstractHelper;
class Bbcode extends AbstractHelper
{

    public function __invoke($request)
    {
        $bbcode = new Decoda($request);
        $bbcode->defaults();
        $bbcode->addHook(new \Decoda\Hook\ClickableHook());
        $bbcode->addHook(new \Decoda\Hook\EmoticonHook(array('path' => '../../images/emoticons/')));
        $bbcode->addHook(new \Decoda\Hook\CensorHook());
        //$bbcode->addHook(new \Decoda\Hook\EmptyHook());
        return $bbcode->parse();
    }
}
