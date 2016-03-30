<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class StaticContent {
    private $ci;
    
    public function __construct() {
        $this->ci =& get_instance();
    }
    
    public function getStaticPages() {
        $this->ci->db->select('Uri, PageTitle');
        $this->ci->db->from('static');
        $query = $this->ci->db->get();
        return $query->result_array();
    }
    
    public function getStaticContent($uri) {
        $this->ci->db->select('StaticID, PageTitle, PageContent');
        $this->ci->db->from('static');
        $this->ci->db->where('Uri', $uri);
        $query = $this->ci->db->get();
        if ($query->num_rows())
            return $query->row_array();
    }
    
    public function updateStaticContent($data) {
        $update = array(
            'PageTitle' => $data['title'],
            'PageContent' => $data['pagecontent'],
            'TimestampModified' => date('Y-m-d H:i:s'),
            'ModifiedByAgentID' => 1,
        );
        $this->ci->db->where('StaticID', $data['id']);
        $this->ci->db->update('static', $update);
    }
    
    public function createNewStaticPage($data) {
        $insertArray = array(
            'Uri' => $data['uri'],
            'PageTitle' => $data['title'],
            'TimestampCreated' => date('Y-m-d H:i:s'),
            'CreatedByAgentID' => 1,
        );
        $this->ci->db->insert('static', $insertArray);
    }
}



/* End of file StaticContent.php */
/* Location: ./libraries/StaticContent.php */