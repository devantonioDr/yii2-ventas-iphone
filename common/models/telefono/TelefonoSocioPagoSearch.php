<?php

namespace common\models\telefono;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\telefono\TelefonoSocioPago;

class TelefonoSocioPagoSearch extends TelefonoSocioPago
{
    public function rules()
    {
        return [
            [['id', 'socio_id', 'cantidad_telefonos'], 'integer'],
            [['fecha_pago', 'codigo_factura'], 'safe'],
            [['ganancia_socio', 'ganancia_empresa', 'ganancia_neta', 'gastos', 'invertido'], 'number'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = TelefonoSocioPago::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'socio_id' => $this->socio_id,
            'fecha_pago' => $this->fecha_pago,
            'cantidad_telefonos' => $this->cantidad_telefonos,
            'ganancia_socio' => $this->ganancia_socio,
            'ganancia_empresa' => $this->ganancia_empresa,
            'ganancia_neta' => $this->ganancia_neta,
            'gastos' => $this->gastos,
            'invertido' => $this->invertido,
        ]);

        $query->andFilterWhere(['like', 'codigo_factura', $this->codigo_factura]);

        return $dataProvider;
    }
} 