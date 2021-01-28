<?php
/**
 * Дополнительные события модуля Обратная связь
 * 
 * Список событий модуля "Обратная связь"
 * 
 * "OnFeedbackNewMessageSuccess" - новое сообщение (параметры: $factory, $model)
 * 
 */
use common\ext\email\components\helpers\HEmail;

return [
    'OnFeedbackNewMessageSuccess'=>[
        function($event) {
            HEmail::cmsAdminSend(true, [
                'factory'=>$event->params['factory'],
                'model'=>$event->params['model'],
            ], 'feedback.views._email.new_message_success');
        }
    ]
];
