<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "requests".
 *
 * @property int $id
 * @property string $url
 * @property string $params
 * @property int $response_code
 * @property int $response_time
 * @property string $request_type
 * @property string $request_date
 */
class Request extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'requests';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['url', 'response_code', 'response_time', 'request_type'], 'required'],
            [['response_code', 'response_time'], 'integer'],
            [['request_date'], 'safe'],
            [['url', 'params'], 'string', 'max' => 255],
            [['request_type'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'url' => 'URL',
            'params' => 'Params',
            'response_code' => 'Response Code',
            'response_time' => 'Response Time (ms)',
            'request_type' => 'Request Type',
            'request_date' => 'Request Date',
        ];
    }
}
