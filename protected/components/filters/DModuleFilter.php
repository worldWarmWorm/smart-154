<?php
class DModuleFilter extends \CFilter
{
	/**
	 * @var mixed имя модуля. Может быть передан массив имен. 
	 */
	public $name;
	
	/**
	 * (non-PHPdoc)
	 * @see CFilter::preFilter()
	 */
	public function preFilter($filterChain)
	{
		if(is_array($this->name)) {
			foreach($this->name as $n) 
				$this->_checkActive($n);
		}
		else 
			$this->_checkActive($this->name);
			
		return true;
	}
	
	/**
	 * Проверяет активен ли модуль или нет.
	 * @param string $name имя модуля.
     * @throws \CHttpException 404, если модуль не активен.
	 * @return boolean
	 */
	private function _checkActive($name) 
	{
		if(!D::yd()->isActive($name)) 
			throw new \CHttpException(404);
	}
}