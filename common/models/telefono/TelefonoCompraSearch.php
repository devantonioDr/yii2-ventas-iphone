<?php

namespace common\models\telefono;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\telefono\TelefonoCompra;

/**
 * TelefonoCompraSearch represents the model behind the search form of `common\models\telefono\TelefonoCompra`.
 */
class TelefonoCompraSearch extends TelefonoCompra
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['fecha_compra', 'suplidor', 'codigo_factura'], 'safe'],
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
        $query = TelefonoCompra::find()->with('telefonos');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'fecha_compra' => SORT_DESC,
                    'id' => SORT_DESC,
                ]
            ],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'suplidor', $this->suplidor])
              ->andFilterWhere(['like', 'codigo_factura', $this->codigo_factura]);

        if (!empty($this->fecha_compra)) {
            $query->andFilterWhere(['DATE(fecha_compra)' => $this->fecha_compra]);
        }

        return $dataProvider;
    }
} 