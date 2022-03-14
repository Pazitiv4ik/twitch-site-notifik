<?php

ini_set( 'display_errors', '0' );
error_reporting( E_ALL );


class TwitchApi
    {
        const HELIX_URL    = 'https://api.twitch.tv/helix';
    
        // Регистрируем приложение и получаем ключи тут - https://dev.twitch.tv/console/apps/
        const CLIENT_ID        = 'xxx';
        const CLIENT_SECRET    = 'xxx';
    
        private $curl = null;
    
        public function __construct()
        {
            $this->curl = curl_init();
        }
    
        /**
         * @link https://dev.twitch.tv/docs/api/reference#get-streams
         */
    
        public function getStreams( array $arr = null )
        {
            $url = self::HELIX_URL . '/streams?' . http_build_query($arr,"", null, PHP_QUERY_RFC3986);
            return $this->_curl_exec($url);
        }
    
        /**
         * @link https://dev.twitch.tv/docs/api/reference#get-channel-information
         */
    
        public function getChannel(array $arr = null)
        {
            $url = self::HELIX_URL . '/channels?' . http_build_query($arr,"", null, PHP_QUERY_RFC3986);
            return $this->_curl_exec($url);
        }
      
        private function _curl_exec(string $url)
        {
            curl_setopt_array( $this->curl, [
                    CURLOPT_URL            => $url,
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_TIMEOUT        => 1,
                    CURLOPT_HTTPHEADER     => [
                        'Authorization: Bearer ' . $this->_getAccessToken(),
                        'Client-Id: ' . self::CLIENT_ID
                    ]
                ]
            );
    
            return json_decode( curl_exec( $this->curl ), true );
        }
    
        private function _getAccessToken()
        {
            $url = 'https://id.twitch.tv/oauth2/token';
            $data = array('client_id' => self::CLIENT_ID, 'client_secret' => self::CLIENT_SECRET, 'grant_type' => 'client_credentials');
    
            $options = array(
                'http' => array(
                    'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method' => 'POST',
                    'content' => http_build_query($data)
                )
            );
    
            $context = stream_context_create($options);
            $result = json_decode(file_get_contents($url, false, $context), true );
    
            return $result['access_token'];
        }
    
        public function __destruct()
        {
            if(!is_null($this->curl))
                curl_close($this->curl);
        }
        
    }


$api = new TwitchApi();

 // эта штука что-бы вы могли взять нужную информацию и вывести в скрипт на 100 линии
  echo '<pre>';
  print_r( $api->getStreams(['user_login' => 'lizzvega']));
  echo '</pre>';

  // Список каналов
$a = array('lizzvega', 'pazitiv4ik_doc', 'halfcomet', 'nike_fil', 'furube666');
$w = '200'; // ширина картинки стрима
$h ='200'; // высота картинки стрима

foreach ($a as $value) {
  $b = $api->getStreams(['user_login' => $value]);

  foreach ($b as $key) {
    if ($key[0]['type']<>NULL) {
      echo $key[0]['user_name'].'<br><img src="https://static-cdn.jtvnw.net/previews-ttv/live_user_'.$key[0]['user_login'].'-'.$w.'x'.$h.'.jpg"><br>';
      echo 'https://www.twitch.tv/'.$key[0]['user_login'].'<br>';
    }
  }
}
