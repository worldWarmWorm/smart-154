<?php

class SearchWidget extends \CWidget
{
	public $id='q';
	public $placeholder='Поиск';
	public $submit='Найти';

	public $view='search_form';

	public function run()
	{
		$this->render($this->view);
	}
}
