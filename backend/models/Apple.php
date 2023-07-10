<?php

namespace app\models;

use common\models\User;
use DateTime;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\db\Expression;

/**
 * This is the model class for table "apple".
 *
 * @property int         $id
 * @property int         $owner_id   Создатель
 * @property string      $created_at Дата создания
 * @property string      $updated_at Дата обновления
 * @property string|null $dropped_at Дата падения
 * @property string|null $deleted_at Удалено
 * @property string      $color      Цвет
 * @property int|null    $percent    Целостность
 *
 * @property BiteLog[]   $biteLogs
 * @property User        $owner
 * @property int         $status
 */
class Apple extends ActiveRecord
{
    public const STATUS_CREATED = 0;
    public const STATUS_DROPPED = 1;
    public const STATUS_ROTTEN  = 2;
    public const STATUS_DELETED = 3;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'apple';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['owner_id', 'color'], 'required'],
            [['percent'], 'default', 'value' => 100],
            [['owner_id', 'percent'], 'integer'],
            [['created_at', 'updated_at', 'dropped_at', 'deleted_at'], 'safe'],
            [['color'], 'string', 'max' => 255],
            [['owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['owner_id' => 'id']],
        ];
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
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
            'owner_id'   => 'Создатель',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
            'dropped_at' => 'Дата падения',
            'deleted_at' => 'Удалено',
            'color'      => 'Цвет',
            'percent'    => 'Целостность',
        ];
    }

    public function getBiteLogs(): ActiveQuery
    {
        return $this->hasMany(BiteLog::class, ['apple_id' => 'id']);
    }

    public function getOwner(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'owner_id']);
    }

    public function beforeValidate(): bool
    {
        if ($this->isNewRecord) {
            if (is_null($this->owner_id) && !Yii::$app->request->isConsoleRequest && !Yii::$app->user->isGuest) {
                $this->owner_id = Yii::$app->user->identity->getId();
            }
            if (is_null($this->color)) {
                $this->color = '#' . str_pad(dechex(random_int(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT) . '75';
            }
        }

        return parent::beforeValidate();
    }

    public function afterSave($insert, $changedAttributes): void
    {
        if (!$insert && array_key_exists('percent', $changedAttributes)) {
            (new BiteLog([
                'apple_id' => $this->id,
                'eater_id' => Yii::$app->user->identity->getId(),
                'percent'  => $changedAttributes['percent'] - $this->percent,
            ]))->save();
        }
        parent::afterSave($insert, $changedAttributes);
    }

    public function getStatus(): int
    {
        if ($this->deleted_at || (int)$this->percent <= 0) {
            return self::STATUS_DELETED;
        }

        if ($this->dropped_at) {
            if (new DateTime($this->dropped_at) <= new DateTime('-5hours')) {
                return self::STATUS_ROTTEN;
            }

            return self::STATUS_DROPPED;
        }

        return self::STATUS_CREATED;
    }

    public function bite(int $percent): bool
    {
        if ($this->status !== self::STATUS_DROPPED || $this->percent < $percent) {
            return false;
        }

        $this->percent -= $percent;

        return $this->save();
    }

    public function drop(): bool
    {
        if ($this->status !== self::STATUS_CREATED) {
            return false;
        }

        $this->dropped_at = date('Y-m-d H:i:s');

        return $this->save();
    }

    public function hide(): bool
    {
        if (!is_null($this->deleted_at)) {
            return true;
        }

        $this->deleted_at = date('Y-m-d H:i:s');

        return $this->save();
    }

    public function compile()
    {
        return [
            'id'         => $this->id,
            'your'       => (int)$this->owner_id === Yii::$app->user->identity->getId(),
            'owner'      => $this->owner->username,
            'color'      => $this->color,
            'percent'    => $this->percent,
            'status'     => $this->getStatus(),
            'created_at' => $this->created_at,
            'dropped_at' => $this->dropped_at,
        ];
    }
}
