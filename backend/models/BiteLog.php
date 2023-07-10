<?php

namespace app\models;

use common\models\User;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "bite_log".
 *
 * @property int      $id
 * @property int      $apple_id   Яблоко
 * @property int      $eater_id   Едок
 * @property string   $created_at Дата создания
 * @property int|null $percent    Откушенный процент
 *
 * @property Apple    $apple
 * @property User     $eater
 */
class BiteLog extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'bite_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['apple_id', 'eater_id'], 'required'],
            [['apple_id', 'eater_id', 'percent'], 'integer'],
            [['created_at'], 'safe'],
            [['apple_id'], 'exist', 'skipOnError' => true, 'targetClass' => Apple::class, 'targetAttribute' => ['apple_id' => 'id']],
            [['eater_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['eater_id' => 'id']],
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id'         => 'ID',
            'apple_id'   => 'Яблоко',
            'eater_id'   => 'Едок',
            'created_at' => 'Дата создания',
            'percent'    => 'Откушенный процент',
        ];
    }

    public function getApple(): ActiveQuery
    {
        return $this->hasOne(Apple::class, ['id' => 'apple_id']);
    }

    public function getEater(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'eater_id']);
    }
}
