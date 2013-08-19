<?php
class ProjectModel extends CI_Model {
    private $ret;
    
    function  __construct() {
        parent::__construct();
    }
    
    function getFilterKeys($projectid, $filterid=FALSE) {
        $this->db->select('Filter');
        $this->db->from('globalfilter');
        if ($this->session->userdata('GlobalFilter') && $this->session->userdata('GlobalFilterOn'))
            $this->db->where('FilterID', $this->session->userdata('GlobalFilter'));
        elseif ($filterid)
            $this->db->where('FilterID', $filterid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            $filter = unserialize($row->Filter);
            if (isset($filter[$projectid]))
                return array_keys($filter[$projectid]);
            else 
                return FALSE;
        }
        else
            return FALSE;
    }

}

/* End of file keymodel.php */
/* Location: ./models/keymodel.php */