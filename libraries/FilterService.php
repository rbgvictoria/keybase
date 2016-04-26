<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

require_once 'Service.php';

class FilterService extends Service {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getFilters($project=FALSE, $user=FALSE, $session=FALSE) {
        $uri = '/';
        if ($project) {
            $uri .= 'project/' . $project;
        }
        if ($user) {
            $uri .= 'user/' . $user;
        }
        if ($session) {
            $uri .= 'session/' . $session;
        }
        if (!$uri) {
            return FALSE;
        }
        $url = $this->ws_url() . 'ws/filters_get' . $uri;
        $response = doCurl($url, FALSE, TRUE);
        return json_decode($response);
    }
    
    public function getProjectFilters($project=FALSE, $user=FALSE) {
        $uri = '/';
        if ($project) {
            $uri .= 'project/' . $project;
        }
        if ($user) {
            $uri .= 'user/' . $user;
        }
        if (!$uri) {
            return FALSE;
        }
        $url = $this->ws_url() . 'ws/project_filters_get' . $uri;
        $response = doCurl($url, FALSE, TRUE);
        return json_decode($response);
    }
    
    public function getManageFilters($project) {
        $url = $this->ws_url() . 'ws/manage_filters_get/' . $project;
        $response = doCurl($url, FALSE, TRUE);
        return json_decode($response);
    }
    
    public function updateFilter($filter, $data) {
        $url = $this->ws_url() . 'ws/filter_put/' . $filter;
        $data['keybase_user_id'] = $this->ci->session->userdata('id');
        $data['session'] = $this->ci->session->userdata('session_id');
        $data = http_build_query($data);
        $response = curl_post($url, $data, TRUE);
        return json_decode($response);
    }
    
    public function createFilter($data) {
        $url = $this->ws_url() . 'ws/filter_post';
        $data['keybase_user_id'] = $this->ci->session->userdata('id');
        $data['session'] = $this->ci->session->userdata('session_id');
        $data = http_build_query($data);
        $response = curl_post($url, $data, TRUE);
        return json_decode($response);
    }
    
    public function deleteFilter($filter) {
        $url = $this->ws_url() . 'ws/filter_delete';
        $data = array('keybase_user_id' => $this->ci->session->userdata('id'));
        $response = curl_delete($url, $filter, $data, TRUE);
        return json_decode($response);
    }
    
    public function setProjectFilter($filter) {
        $url = $this->ws_url() . 'ws/set_project_filter';
        $data = array('is_project_filter' => $this->input->post('is_project_filter'));
        $response = curl_post($url, $data, TRUE);
        return json_decode($response);
    }
    
    
    
    
}

/* End of file FilterService.php */
/* Location: ./libraries/FilterService.php */
