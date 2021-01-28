<?php
class AccordionList extends CInputWidget {
    public function run()
    {	
    	$accordion_list = Accordion::model()->findAll();
        if(!empty($accordion_list)){
		  $this->render('buttons', compact('accordion_list'));
        }
    }
}


