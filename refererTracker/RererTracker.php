<?php
/**
 */

class RefererTracker extends CApplicationComponent
{
    public $cookieName = 'referTracker';

    public function returnCurrentCookies()
    {
        foreach (app()->request->cookies as $cookie) {
            printr($cookie);
        }
    }

    public function trackByCookie()
    {
        if (!$this->getCookieContent()) {
            $this->addCookie();
        }
    }

    private function addCookie()
    {
        app()->request->cookies[$this->cookieName] = new CHttpCookie($this->cookieName, $this->createCookieContent());
    }

    private function createCookieContent()
    {
        $data = array(
            'landingPage' => Yii::app()->request->getUrl(),
            'referer' => Yii::app()->request->urlReferrer,
        );
        $data = json_encode($data);
        return $data;
    }

    public function getCookieContent()
    {
        if (!isset(Yii::app()->request->cookies[$this->cookieName])) {
            return false;
        }
        $data = Yii::app()->request->cookies[$this->cookieName];
        $data = json_decode($data, true);
        return $data;
    }

    public function reset()
    {
        unset(Yii::app()->request->cookies[$this->cookieName]);

    }
} 