<?php
/**
 * @copyright Anton Tuyakhov <atuyakhov@gmail.com>
 */

namespace tuyakhov\notifications\channels;


use tuyakhov\notifications\messages\DatabaseMessage;
use tuyakhov\notifications\NotifiableInterface;
use tuyakhov\notifications\NotificationInterface;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\db\BaseActiveRecord;
use yii\helpers\Json;

use tuyakhov\notifications\models\Notification;

class ActiveRecordChannel extends Component implements ChannelInterface
{
    /**
     * @var BaseActiveRecord|string
     */
    public $model = 'tuyakhov\notifications\models\Notification';

    public function send(NotifiableInterface $recipient, NotificationInterface $notification)
    {
        $message = $notification->exportFor('database');
	\Yii::warning(print_r($message,true));
        list($notifiableType, $notifiableId) = $recipient->routeNotificationFor('database');
        $data = [
            'level' => $message->level,
            'subject' => $message->subject,
            'body' => $message->body,
            'notifiable_type' => $notifiableType,
            'notifiable_id' => $notifiableId,
            'data' => Json::encode($message->data),
        ];

	$model = new Notification($data);
	$ret = $model->save();

	if(!$ret)
	{
		\Yii::warning("A problem: " . print_r($model->getErrors(),true));
	}
	

        return $ret;
    }
}
