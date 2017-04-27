<?php

class My_Instagram_Instagram {

    private $_access_token = '1603491147.8d34610.4d7d39b19db44cf2924a995159b66ae8';


    public function getUsersSelfMediaRecent() {

        $result = array();
        $urlUsersSelfMediaRecent = "https://api.instagram.com/v1/users/self/media/recent";

        $urlParams = array(
            'access_token' => $this->_access_token,
            'count' => 6
        );

        $frontendOptions = array(
            'lifetime' => 3600 * 12, // cache lifetime 24 hodin
            'automatic_serialization' => true,
            'automatic_cleaning_factor' => 50
        );
        $backendOptions = array(
            'cache_dir' => '../cache' // Directory where to put the cache files
        );

// getting a Zend_Cache_Core object
        $zend_cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);

//k nacitani informaci o pobockach ulozenky pouzivam cache - nepotrebuju nacitat porad
        if (!$result = $zend_cache->load('Instagram_UsersSelfMediaRecent')) {

            $url = $urlUsersSelfMediaRecent . '?' . http_build_query($urlParams);
            $json = file_get_contents($url);

            if ($json) {
                $obj = json_decode($json);

                foreach ($obj->data as $data) {
                    $result[] = array(
                        'link' => $data->link,
                        'text' => $data->caption->text,
                        'image' => $data->images->standard_resolution->url
                    );
                }
            } else {
                //error
                return $result;
            }

            $zend_cache->save($result, 'Instagram_UsersSelfMediaRecent');
        } else {
            $result = $zend_cache->load('Instagram_UsersSelfMediaRecent');
        }

        return $result;
    }

}