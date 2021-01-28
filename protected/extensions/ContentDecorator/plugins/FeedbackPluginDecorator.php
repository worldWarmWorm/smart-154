<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 04.10.11
 * Time: 13:08
 */
use common\ext\email\components\helpers\HEmail;
 
class FeedbackPluginDecorator extends PluginDecorator
{
    public $point = '{form_feedback}';
    
    public function processModel($model, $attribute = 'text')
    {
        $result = $this->checkPoint($model->$attribute);
        if (!$result)
            return;

        $form = $this->contactForm();

        if ($form['status'])
            $to = $form['form'];
        else
            $to = str_replace($this->point, $form['form'], $model->$attribute);

        $model->$attribute = $to;
    }

    /**
     * Processing feedback form
     * @return array
     */
    private function contactForm()
    {
		$model = new ContactForm;

		if (isset($_POST['ContactForm'])) {
			$model->attributes=$_POST['ContactForm'];

            if ($model->validate()) {
                $params=[
                    '{username}'=>$model->name,
                    '{email}'=>$model->email,
                    '{phone}'=>$model->phone,
                    '{message}'=>$model->body
                ];
                $hash=md5(serialize($params));
                if($hash != Yii::app()->user->getState($hash)) {
                    $ok=HEmail::cmsAdminSend(true, Yii::t('email', 'contact_email', $params));
                }
                else {
                    $ok=null;
                }

                if ($ok) {
                	Yii::app()->user->setState($hash, $hash);
				    Yii::app()->user->setFlash('contact', 'Спасибо, ваше письмо отправлено!');
				}
                elseif($ok !== null) {
                    Yii::app()->user->setFlash('contact', 'При отправке письма произошла ошибка!');
				}
                else {
                	$model=new ContactForm;
                }
			}
		}

        return array(
            'form' => Yii::app()->getController()->renderPartial('/site/_form_contact',array('model'=>$model), true),
            'status' => isset($ok) ? true : false
        );
    }
}
