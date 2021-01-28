<?php
class DAllowControllerFilter extends \CFilter
{
    /**
     * @var array массив разрешенных контроллеров и действий, вида:
     *  array(
     *      'myController1'=>['myAction1', 'myAction2'],
     *      'myController2'=>true // разрешены все действия.
     *  )
     * Все остальные действия и контроллеры будут запрещены.
     */
	public $controllers=[];
	
	/**
	 * (non-PHPdoc)
	 * @see CFilter::preFilter()
	 */
	public function preFilter($filterChain)
	{
        if(array_key_exists($filterChain->controller->id, $this->controllers)) {
            if(($this->controllers[$filterChain->controller->id] === true) || 
                in_array($filterChain->action->id, $this->controllers[$filterChain->controller->id]))
            {
                return true;
            }
        }
        
		throw new \CHttpException(404);
	}
}