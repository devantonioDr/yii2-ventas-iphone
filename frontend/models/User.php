<?php

namespace frontend\models;

use Yii;
use frontend\models\SignupForm;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string|null $firstname
 * @property string|null $lastname
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $new_password
 * @property string|null $password_reset_token
 * @property string $email
 * @property string $cedula_pasaporte
 * @property string $role
 * @property string|null $verification_token
 * @property string|null $access_token
 * @property int $status
 * @property int $created_by
 * @property int $created_at
 * @property int $updated_at
 */
class User extends \yii\db\ActiveRecord
{

  const STATUS_DELETED = 0;
  const STATUS_INACTIVE = 9;
  const STATUS_ACTIVE = 10;

  public function load($data, $formName = null)
  {
      if (parent::load($data, $formName)) {
          $this->username = str_replace('-', '', $this->username);
          // var_dump($this->username);
          // exit();
          return true;
      }
      return false;
  }
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'user';
  }

  public function afterSave($insert, $changedAttributes)
  {
    if (!$insert) {
      $assigment = Yii::$app->authManager;
      $role = $assigment->getRole($this->role);
      $assigment->revokeAll($this->id);
      $assigment->assign($role, $this->id);
    }
    return parent::afterSave($insert, $changedAttributes);
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['username', 'email',], 'required'],
      [['status'], 'integer'],
      ['firstname', 'trim'],
      ['lastname', 'trim'],
      [['username', 'email'], 'string', 'max' => 255],
    ];
  }


  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => 'ID',
      'username' => 'Usuario',
      'firstname' => 'Nombre',
      'lastname' => 'Apellido',
      'auth_key' => 'Auth Key',
      'password_hash' => 'Password Hash',
      'password_reset_token' => 'Password Reset Token',
      'email' => 'Email',
      'verification_token' => 'Verification Token',
      'access_token' => 'Access Token',
      'status' => 'Estado',
      'cedula_pasaporte' => 'Identificación',
      'role' => 'Role',
      'created_at' => 'Created At',
      'updated_at' => 'Updated At',
    ];
  }


  static public function status($name = false)
  {
    $status = [
      self::STATUS_DELETED => 'Eliminado',
      self::STATUS_INACTIVE => 'Inactivo',
      self::STATUS_ACTIVE => 'Activo'
    ];
    if ($name) {
      return $status[$name];
    } else {
      return $status;
    }
  }


  static public function getRoles()
  {
    $result = [];
    if ($roles = Yii::$app->authManager->getRoles()) {
      foreach ($roles as $r) {
        $result[$r->name] = $r->name;
      }
    }

    return $result;
  }




  static public function userList()
  {
    return ArrayHelper::map(User::find()->all(), 'id', 'username');
  }


  public function getCreatedBy($id)
  {
    return ($id) ? User::findOne($id)->username : 'SA';
  }
}
