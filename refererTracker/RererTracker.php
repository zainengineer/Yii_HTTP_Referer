<?php
/**
 */

class RefererTracker extends CApplicationComponent
{
    public $cookieName = 'referTracker';
    private $data = array();

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
        $cookie = new CHttpCookie($this->cookieName, $this->createCookieContent());
        $cookie->expire = time() + 24 * 60 * 60 * 365 * 1;
        app()->request->cookies[$this->cookieName] = $cookie;
    }

    private function createCookieContent()
    {
        $this->data = array(
            'landingPage' => Yii::app()->request->getUrl(),
            'referer' => Yii::app()->request->urlReferrer,
        );
        $this->data = json_encode($this->data);
        return $this->data;
    }

    public function getCookieContent()
    {
        if (!isset(Yii::app()->request->cookies[$this->cookieName])) {
            return false;
        }
        $this->data = Yii::app()->request->cookies[$this->cookieName];
        $this->data = json_decode($this->data, true);
        return $this->data;
    }

    public function reset()
    {
        unset(Yii::app()->request->cookies[$this->cookieName]);

    }

    public function setData($landingPage, $referer)
    {
        $this->data['landingPage'] = $landingPage;
        $this->data['referer'] = $referer;
    }

    public function getKeywords($referer)
    {
        $engineDomains = array(
            'dmoz' => 'dmoz',
            'aol' => 'AOL',
            'ask' => 'ASK',
            'google' => 'Google',
            'bing' => 'Bing',
            'hotbot' => 'hotbot',
            'teoma' => 'teoma',
            'yahoo' => 'Yahoo',
            'altavista' => 'Altavista',
            'lycos' => 'Lycos',
            'kanoodle' => 'Kanoodle',
        );


        $urlParts = parse_url($referer);
        $host = vd($urlParts['host']);
        if (strpos($host,'www.')===0){
            $host = substr($host,4);
        }
        if (!$host) {
            return '';
        }
        $engine = $host;
        foreach ($engineDomains as $k => $v) {
            //domain key is present in the host
            if (strpos($host, $k) !== false) {
                $engine = $v;
                break;
            }
        }

        if ($engine == 'Google') {
            if (vd($urlParts['path']) == '/url') {
                $engine = 'Google SEO';
            }
            if (vd($urlParts['path']) == '/aclk') {
                $engine = 'Google Adwords';
            }
        }

        $queryParts = array();
        parse_str(vd($urlParts['query']), $queryParts);
        $keywords = vd($queryParts['q']);
        $return = array(
            'engine' => $engine,
            'keywords' => $keywords,

        );
        return $return;
    }

} 
