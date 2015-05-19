<?php

/**
 * Class SiteController
 */
class SiteController extends Controller
{
    /**
     * Стартовая страница сайта.
     */
    public function actionIndex()
    {
        if (Yii::app()->user->isGuest) {
            $this->redirect('/site/register');
        }

        $model = User::model()->findByPk(Yii::app()->user->id);

        if (isset($_POST['User'])) {

            $model->attributes = $_POST['User'];

            if ($model->validate() && $model->save()) {
                Yii::app()->user->setFlash('success', 'Данные изменены.');
                $this->refresh();
            }
        }

        $this->render('index', ['model' => $model]);
    }

    /**
     * Регистрируем пользователя.
     */
    public function actionRegister()
    {
        if (!Yii::app()->user->isGuest) {
            $this->redirect('/site/index');
        }

        $model = new Register();

        if (isset($_POST['Register'])) {

            $model->attributes = $_POST['Register'];

            if ($model->validate()) {

                $newEmail = Register::model()->findByAttributes(['email' => $model->email]);

                // электронный адрес существует в базе данных
                if ($newEmail !== null) {
                    $model = $newEmail;
                }

                // одноразовый токен для авторизации
                if (empty($model->token)) {
                    $model->token = Yii::app()->getSecurityManager()
                        ->generateRandomString(Register::LENGTH_TOKEN);
                }

                if ($model->save()) {
                    $this->sendMail($model);

                    Yii::app()->user->setFlash('success', 'Вам на почту отправлен адресс для входа на сайт');
                    $this->refresh();
                }
            }
        }

        $this->render('register', ['model' => $model]);
    }

    /**
     * Авторизация по одноразовому токену.
     *
     * @param string $token
     * @throws CDbException
     * @throws CHttpException
     */
    public function actionToken($token)
    {
        if (!Yii::app()->user->isGuest) {
            $this->redirect('/site/index');
        }

        // авторизационный токен не указан
        if (empty($token) || strlen($token) < Register::LENGTH_TOKEN) {
            throw new CHttpException(404, 'Указанная страница не существует.');
        }

        // ищем токен в базе данных
        $model = Register::model()->findByAttributes(['token' => $token]);

        if ($model === null) {
            throw new CHttpException(404, 'Не верно указанный токен, проверьте письмо.');
        }

        $user = User::model()->findByAttributes(['register_id' => $model->id]);

        $transaction = Yii::app()->db->beginTransaction();

        try {

            if ($user === null) {
                list($name) = explode('@', $model->email);

                // создается учетная запись
                $user = new User();
                $user->register_id = $model->id;
                $user->name = $name;

                $user->insert();
            }

            // обновляем статус электронного адреса
            $model->token = null;
            $model->update();

            $transaction->commit();

        } catch (Exception $e) {
            $transaction->rollBack();

            throw new CHttpException(500, $e->getMessage());
        }

        // пользователь авторизуется
        $identity = new MyUserIdentity();
        $identity->setId($user->id)->setName($user->name);

        /* @var CWebUser Yii::app()->user */
        Yii::app()->user->login($identity);

        $this->redirect('/site/index');
    }

    public function actionLogout()
    {
        if (!Yii::app()->user->isGuest) {
            /* @var CWebUser Yii::app()->user */
            Yii::app()->user->logout();
            $this->redirect('/site/index');
        }
    }

    /**
     * Обработка ошибок.
     */
    public function actionError()
    {
        if ($error = Yii::app()->errorHandler->error) {
            $this->render('error', $error);
        }
    }

    /**
     * Отправка письма пользователю оставившему электронный адрес.
     *
     * @param Register $model
     */
    protected function sendMail(Register $model)
    {
        // текст письма
        $url = $this->createAbsoluteUrl('token', ['token' => $model->token]);
        $message = "<a href='{$url}'>{$url}</a>";

        // тема письма
        $subject = '=?UTF-8?B?' . base64_encode('Вы зарегистрировались на сайте.') . '?=';

        $headers = "From: {Yii::app()->params['adminEmail']} <{Yii::app()->params['adminEmail']}>\r\n" .
            "Reply-To: {Yii::app()->params['adminEmail']}\r\n" .
            "MIME-Version: 1.0\r\n" .
            "Content-Type: text/plain; charset=UTF-8";

        mail($model->email, $subject,  $message, $headers);
    }
}