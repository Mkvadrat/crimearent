<?php

class ApartmentSubscription extends CActiveRecord {

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }


    public function tableName() {
        return '{{apartment_subscriptions}}';
    }


    public function rules() {
        return array(
            array('name', 'required'),
            array('name', 'length', 'max' => 255),
            array('email', 'email'),
            array('phone', 'checkPhone'),
            array('email, phone', 'unique')
        );
    }

    public function attributeLabels() {
        return array(
            'id' => tc('ID'),
            'name' => tc('Username'),
            'email' => tc('Email'),
            'phone' => tc('Phone'),
            'date_added' => tc('Date added'),
            'ip' => tc('IP')
        );
    }

    public function checkPhone() {
        if (!empty($this->phone)) {
            $this->phone = str_replace(' ','',$this->phone);
            $this->phone = str_replace('-','',$this->phone);
            if (!preg_match('/^[+][\d]{11,}$/',$this->phone) && !preg_match('/^[8][\d]{10,}$/',$this->phone)) {
                $this->addError('phone', 'Телефон должен быть в международном формате');
            }
        }
    }

    public function search()
    {

        $criteria = new CDbCriteria;
        $tmp = 'title_' . Yii::app()->language;

        if ($this->id) {
            $criteria->compare($this->getTableAlias() . '.id', $this->id);
        }

        if ($this->name) {
            $criteria->addCondition('name LIKE "%' . $this->name . '%"');
        }

        if ($this->email) {
            $criteria->addCondition('email LIKE "%' . $this->email . '%"');
        }

        if ($this->phone) {
            $criteria->addCondition('phone LIKE "%' . $this->phone . '%"');
        }

        if ($this->ip) {
            $criteria->addCondition('ip LIKE "%' . $this->ip . '%"');
        }

        if ($this->date_added) {
            $criteria->addCondition('date_added LIKE "%' . $this->date_added . '%"');
        }

        $criteria->order = $this->getTableAlias() . '.date_added DESC';

        return new CustomActiveDataProvider($this, array(
            'criteria' => $criteria,
            //'sort'=>array('defaultOrder'=>'sorter'),
            'pagination' => array(
                'pageSize' => param('adminPaginationPageSize', 20),
            ),
        ));
    }

    public function sendConfirmEmail() {
        // TODO
        return;
        if (empty($this->email)) return;

        //$mail = new EMailer;
        $mail = Yii::app()->mailer;

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'rent24.mail@gmail.com';
        $mail->Password = 'mkvadrat';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->From = 'noreply@'.$_SERVER['HTTP_HOST'];
        $mail->FromName = 'Королевский Дом';
        $mail->addAddress($this->email);
        $mail->Subject = 'Подтверждение адреса';

        $mail->CharSet = 'UTF-8';
        $mail->isHTML(true);

        $mail->Body    = '<p>Пожалуйста подтвердите ваш адрес email</p>';

        $mail->send();
    }

    public function sendConfirmSMS() {
        // TODO
        return;
    }
}