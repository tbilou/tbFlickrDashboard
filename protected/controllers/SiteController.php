<?php

require_once( dirname(__FILE__) . '/../Keys.php');

class SiteController extends Controller {

    /**
     * Declares class-based actions.
     */
    public function actions() {
        return array(
            // captcha action renders the CAPTCHA image displayed on the contact page
            'captcha' => array(
                'class' => 'CCaptchaAction',
                'backColor' => 0xFFFFFF,
            ),
            // page action renders "static" pages stored under 'protected/views/site/pages'
            // They can be accessed via: index.php?r=site/page&view=FileName
            'page' => array(
                'class' => 'CViewAction',
            ),
        );
    }

    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex() {
        // renders the view file 'protected/views/site/index.php'
        // using the default layout 'protected/views/layouts/main.php'
        $this->render('index');
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError() {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }

    /**
     * Displays the contact page
     */
    public function actionContact() {
        $model = new ContactForm;
        if (isset($_POST['ContactForm'])) {
            $model->attributes = $_POST['ContactForm'];
            if ($model->validate()) {
                $headers = "From: {$model->email}\r\nReply-To: {$model->email}";
                mail(Yii::app()->params['adminEmail'], $model->subject, $model->body, $headers);
                Yii::app()->user->setFlash('contact', 'Thank you for contacting us. We will respond to you as soon as possible.');
                $this->refresh();
            }
        }
        $this->render('contact', array('model' => $model));
    }

    /**
     * Displays the login page
     */
    public function actionLogin() {
        $model = new LoginForm;

        // if it is ajax validation request
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        // collect user input data
        if (isset($_POST['LoginForm'])) {
            $model->attributes = $_POST['LoginForm'];
            // validate user input and redirect to the previous page if valid
            if ($model->validate() && $model->login())
                $this->redirect(Yii::app()->user->returnUrl);
        }
        // display the login form
        $this->render('login', array('model' => $model));
    }

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout() {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }

    public function actionStatus() {
        $this->render('status');
    }

    public function actionDoBackup() {

        // Read the post vars for the redis server and port

        if (isset($_POST["backup"])) {
            $redis_server = $_POST['redis-server'];
            $redis_port = $_POST['redis-port'];

            $redis = new Redis() or die("Can't load redis module.");
            $redis->connect($redis_server, $redis_port) or die("Can't connect to the Redis Server");

            // Delete everything
            $redis->del(Keys::DOWNLOADS_INFO);
            $redis->del(Keys::DOWNLOADS_KILL_QUEUE);
            $redis->del(Keys::DOWNLOADS_QUEUE);

            $redis->del(Keys::PHOTOSETS_GETLIST_INFO);
            $redis->del(Keys::PHOTOSETS_GETLIST_KILL_QUEUE);
            $redis->del(Keys::PHOTOSETS_GETLIST_QUEUE);

            $redis->del(Keys::PHOTOSETS_GETPHOTOS_INFO);
            $redis->del(Keys::PHOTOSETS_GETPHOTOS_KILL_QUEUE);
            $redis->del(Keys::PHOTOSETS_GETPHOTOS_QUEUE);

            $redis->del(Keys::CONFIG_INFO);
            $redis->del(Keys::DOWNLOADED_PHOTOS);

            //update the configuration
            $redis->hset(Keys::CONFIG_INFO, "DOWNLOAD_PATH", $_POST["cpath"]);
            $redis->hset(Keys::CONFIG_INFO, "LOG_PATH", $_POST["clog"]);
            switch ($_POST["cache-type"]) {
                case "No Cache":
                    $cacheClass = "noCache";
                    break;
                case "FileSystem":
                    $cacheClass = "FileCache";
                    break;
                case "Redis":
                    $cacheClass = "RedisCache";
                    break;
            }

            $redis->hset(Keys::CONFIG_INFO, "CACHE_CLASS", $cacheClass);
            $redis->hset(Keys::CONFIG_INFO, "CACHE_TTL", $_POST["cache-ttl"]);
            $redis->hset(Keys::CONFIG_INFO, "WORKERS_PATH", $_POST["workerspath"]);

            // Start
            $redis->rPush(Keys::PHOTOSETS_GETLIST_QUEUE, "go");

            $redis->close();



            // Start the instances
            for ($i = 0; $i < $_POST["c1"]; $i++) {
                $this->startPhotosetsGetListWorker();
            }
            for ($i = 0; $i < $_POST["c2"]; $i++) {
                $this->startPhotosetsGetPhotosWorker();
            }
            for ($i = 0; $i < $_POST["c3"]; $i++) {
                $this->startDownloadWorker();
            }
        }

        $this->redirect(array('site/status'));
    }

    public function actionGetQueueStatus($type) {
        $redis = new Redis() or die("Can't load redis module.");
        $redis->connect("127.0.0.1");

        switch ($type) {
            case 'p1':
                $info = Keys::PHOTOSETS_GETLIST_INFO;
                $queue = Keys::PHOTOSETS_GETLIST_QUEUE;
                break;
            case 'p2':
                $info = Keys::PHOTOSETS_GETPHOTOS_INFO;
                $queue = Keys::PHOTOSETS_GETPHOTOS_QUEUE;
                break;
            case 'p3':
                $info = Keys::DOWNLOADS_INFO;
                $queue = Keys::DOWNLOADS_QUEUE;
                break;
        }

        $instances = $redis->hget($info, "instances");
        $instances = ($instances < 0) ? 0 : $instances;

        $jobsTotal = $redis->hget($info, "messages");
        $jobs = $redis->llen($queue);

        if ($jobsTotal != 0)
            $total = round((($jobsTotal - $jobs) * 100) / $jobsTotal);
        else
            $total = 0;

        // Elapsed Time
        $start = new DateTime($redis->hget($info, "start"));

        $now = new DateTime('NOW');
        // Check if this step is complete
        if ($total == 100) {
            $redis->hSetNx($info, 'end', date_format(new DateTime('NOW'), DATE_RFC822));
            $now = new DateTime($redis->hget($info, "end"));
        }



        $diff = $now->diff($start);
        $elapsed = sprintf('%d days, %d hours, %d minutes, %d seconds', $diff->d, $diff->h, $diff->i, $diff->s);

        $arr = array(
            'percent' => $total,
            'instances' => $instances,
            'elapsed' => $elapsed,
            'total' => $jobsTotal,
            'inQueue' => $jobs,
            'processed' => $jobsTotal - $jobs
        );



        echo json_encode($arr);

        $redis->close();
    }

    public function actionManageInstance($type, $op) {
        switch ($type) {
            case 'p1':
                if ($op == 'del')
                    $this->stopPhotosetsGetListWorker();
                else if ($op == 'all')
                    $this->stopAllPhotosetsGetListWorkers();
                else if ($op == 'add')
                    $this->startPhotosetsGetListWorker();
                break;
            case 'p2':
                if ($op == 'del')
                    $this->stopPhotosetsGetPhotosWorker();
                else if ($op == 'all')
                    $this->stopAllPhotosetsGetPhotosWorkers();
                else if ($op == 'add')
                    $this->startPhotosetsGetPhotosWorker();
                break;
            case 'p3':
                if ($op == 'del')
                    $this->stopDownloadWorker();
                else if ($op == 'all')
                    $this->stopAllDownloadWorkers();
                else if ($op == 'add')
                    $this->startDownloadWorker();
                break;
        }
    }

    private function stopDownloadWorker() {
        $redis = new Redis() or die("Can't load redis module.");
        $redis->connect("127.0.0.1");

        $redis->rPush(Keys::DOWNLOADS_KILL_QUEUE, "die");

        $redis->close();
    }

    private function stopPhotosetsGetListWorker() {
        $redis = new Redis() or die("Can't load redis module.");
        $redis->connect("127.0.0.1");

        $redis->rPush(Keys::PHOTOSETS_GETLIST_KILL_QUEUE, "die");

        $redis->close();
    }

    private function stopPhotosetsGetPhotosWorker() {
        $redis = new Redis() or die("Can't load redis module.");
        $redis->connect("127.0.0.1");

        $redis->rPush(Keys::PHOTOSETS_GETPHOTOS_KILL_QUEUE, "die");

        $redis->close();
    }

    private function stopAllDownloadWorkers() {
        $redis = new Redis() or die("Can't load redis module.");
        $redis->connect("127.0.0.1");

        for ($i = 0; $i < $redis->hget(Keys::DOWNLOADS_INFO, "instances"); $i++) {
            $redis->rPush(Keys::DOWNLOADS_KILL_QUEUE, "die");
        }

        $redis->close();
    }

    private function stopAllPhotosetsGetListWorkers() {
        $redis = new Redis() or die("Can't load redis module.");
        $redis->connect("127.0.0.1");

        for ($i = 0; $i < $redis->hget(Keys::PHOTOSETS_GETLIST_INFO, "instances"); $i++) {
            $redis->rPush(Keys::PHOTOSETS_GETLIST_KILL_QUEUE, "die");
        }
        $redis->close();
    }

    private function stopAllPhotosetsGetPhotosWorkers() {
        $redis = new Redis() or die("Can't load redis module.");
        $redis->connect("127.0.0.1");

        for ($i = 0; $i < $redis->hget(Keys::PHOTOSETS_GETPHOTOS_INFO, "instances"); $i++) {
            $redis->rPush(Keys::PHOTOSETS_GETPHOTOS_KILL_QUEUE, "die");
        }
        $redis->close();
    }

    private function startDownloadWorker() {
        // Using the < /dev/null from this thread        
        // http://ubuntuforums.org/showthread.php?t=977332
        $redis = new Redis() or die("Can't load redis module.");
        $redis->connect("127.0.0.1");
        $path = $redis->hget(Keys::CONFIG_INFO, "WORKERS_PATH");
        $redis->close();
        
        $script = $path . "workerDownloadPhoto.php";
        $cmd = "/usr/bin/php $script < /dev/null &";


        exec($cmd);
    }

    private function startPhotosetsGetListWorker() {
        $redis = new Redis() or die("Can't load redis module.");
        $redis->connect("127.0.0.1");
        $path = $redis->hget(Keys::CONFIG_INFO, "WORKERS_PATH");
        $redis->close();
        
        $script = $path . "workerPhotosetsGetList.php";
        $cmd = "/usr/bin/php $script < /dev/null > /dev/null 2>/dev/null &";

        exec($cmd);
    }

    private function startPhotosetsGetPhotosWorker() {
        $redis = new Redis() or die("Can't load redis module.");
        $redis->connect("127.0.0.1");
        $path = $redis->hget(Keys::CONFIG_INFO, "WORKERS_PATH");
        $redis->close();
        
        $script = $path . "workerPhotosetsGetPhotos.php";
        $cmd = "/usr/bin/php $script < /dev/null > /dev/null 2>/dev/null &";

        exec($cmd);
    }

}