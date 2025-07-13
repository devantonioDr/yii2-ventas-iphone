<?php

namespace common\models\telefono;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\telefono\Telefono;

/**
 * TelefonoSearch represents the model behind the search form of `common\models\telefono\Telefono`.
 */
class TelefonoSearch extends Telefono
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['imei', 'marca', 'modelo'], 'string'],
            [['fecha_ingreso'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Telefono::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'fecha_ingreso' => SORT_DESC,
                    'id' => SORT_DESC,
                ]
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere(['like', 'imei', $this->imei])
              ->andFilterWhere(['marca' => $this->marca])
              ->andFilterWhere(['modelo' => $this->modelo]);

        return $dataProvider;
    }
} 