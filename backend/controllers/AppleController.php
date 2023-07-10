<?php

namespace backend\controllers;

use app\models\Apple;
use DateTime;
use Yii;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\Response;

class AppleController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access'  => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'bite', 'create', 'delete', 'drop', 'list', 'updates'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
            'verbs'   => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'index'   => ['GET'],
                    'list'    => ['GET'],
                    'updates' => ['GET'],
                    'create'  => ['POST'],
                    'bite'    => ['POST', 'PUT'],
                    'drop'    => ['POST', 'PUT'],
                    'delete'  => ['POST', 'DELETE'],
                ],
            ],
            'content' => [
                'class'   => ContentNegotiator::class,
                'except'  => ['index'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionList()
    {
        return [
            'date'   => date('Y-m-d H:i:s'),
            'apples' => array_map(
                static fn(Apple $apple) => $apple->compile(),
                Apple::find()
                    //->where(['or', ['dropped_at' => null], ['>', 'dropped_at', (new DateTime('-5hours'))->format('Y-m-d H:i:s')]])
                    ->andWhere(['deleted_at' => null])
                    ->andWhere(['>', 'percent', 0])
                    ->all()
            ),
        ];
    }

    public function actionUpdates(string $datetime)
    {
        $dt = new DateTime($datetime);
        if (!$dt) {
            throw new BadRequestHttpException('Wrong datetime format');
        }

        return [
            'date'   => date('Y-m-d H:i:s'),
            'apples' => array_map(
                static fn(Apple $apple) => $apple->compile(),
                Apple::find()
                    ->where(['>=', 'updated_at', $dt->format('Y-m-d H:i:s')])
                    ->all()
            ),
        ];
    }

    public function actionBite(int $id, int $percent)
    {
        $apple = Apple::find()->where(['id' => $id])->one();
        if (!$apple->bite($percent)) {
            throw new BadRequestHttpException('Can\'t bite the apple');
        }
    }

    public function actionDelete(int $id)
    {
        $apple = Apple::find()->where(['id' => $id])->one();
        if (!$apple->hide()) {
            throw new BadRequestHttpException('Can\'t delete the apple');
        }
    }

    public function actionDrop(int $id)
    {
        $apple = Apple::find()->where(['id' => $id])->one();
        if (!$apple->drop()) {
            throw new BadRequestHttpException('Can\'t drop the apple');
        }
    }

    public function actionCreate(int $count)
    {
        $apples      = [];
        $transaction = Yii::$app->getDb()->beginTransaction();
        try {
            if (empty($transaction)) {
                throw new Exception('Can\'t start transaction');
            }
            for ($i = 0; $i < $count; $i++) {
                $apple = new Apple();
                if (!$apple->save()) {
                    Yii::error(current($apple->firstErrors));
                    throw new Exception('Can\'t create Apple');
                }

                $apples[] = $apple->compile();
            }
            $transaction->commit();
        } catch (\Throwable $t) {
            $transaction->rollBack();
            throw $t;
        }

        return ['apples' => $apples];
    }
}