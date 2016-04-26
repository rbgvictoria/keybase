<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

require_once 'Service.php';

class ProjectService extends Service {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getProjects() {
        $url = $this->ws_url() . 'ws/projects_get';
        $response = doCurl($url, FALSE, TRUE);
        return json_decode($response);
    }
    
    public function getTotalNumberOfItems() {
        $url = $this->ws_url() . 'ws/total_items_get';
        $response = doCurl($url, FALSE, TRUE);
        return json_decode($response);
    }
    
    public function getProjectMetadata($project) {
        $url = $this->ws_url() . 'ws/project_meta_get/' . $project;
        $response = doCurl($url, FALSE, TRUE);
        return json_decode($response);
    }
    
    public function getProjectUsers($project) {
        $url = $this->ws_url() . 'ws/project_user_get/' . $project;
        $response = doCurl($url, FALSE, TRUE);
        return json_decode($response);
    }
    
    public function editProjectMetadata($data) {
        $url = $this->ws_url() . 'ws/project_post';
        $response = curl_post($url, $data, TRUE);
        return json_decode($response);
    }
    
    public function createProject($data) {
        $url = $this->ws_url() . 'ws/project_post';
        $response = curl_post($url, $data, TRUE);
        return json_decode($response);
    }
    
    public function deleteProject($project) {
        $url = $this->ws_url() . 'ws/project_delete/' . $project;
        $data = array('keybase_user_id' => $this->ci->session->userdata('id'));
        $response = curl_delete($url, $project, $data, TRUE);
        return json_decode($response);
    }
    
}

/* End of file ProjectService.php */
/* Location: ./libraries/ProjectService.php */
