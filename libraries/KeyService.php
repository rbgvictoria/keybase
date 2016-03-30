<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

require_once 'Service.php';

class KeyService extends Service {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getKeyMetadata($keyid) {
        $url = $this->ws_url() . 'ws/key_meta_get/' . $keyid;
        $result = doCurl($url, FALSE, TRUE);
        return json_decode($result);
    }
    
    public function search($searchstring) {
        $url = $this->ws_url() . 'ws/search_items/' . $searchstring;
        $result = doCurl($url, FALSE, TRUE);
        return json_decode($result);
    }
    
    public function editKey($key, $data) {
        $url = $this->ws_url() . 'ws/key_post/' . $key;
        $data['keybase_user_id'] = $this->ci->session->userdata('id');
        $response = curl_post($url, $data, TRUE);
        return json_decode($response);
    }
    
    public function deleteKey($key) {
        $url = $this->ws_url() . 'ws/key_delete';
        $data = array('keybase_user_id' => $this->ci->session->userdata('id'));
        $response = curl_delete($url, $key, $data, TRUE);
        return json_decode($response);
    }
}

/* End of file KeyService.php */
/* Location: ./libraries/KeyService.php */
