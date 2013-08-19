<?php

class AutoCompleteModel extends CI_Model {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getTaxa($q) {
        $this->db->select('i.Name');
        $this->db->from('keys k');
        $this->db->join('items i', 'k.TaxonomicScopeID=i.ItemsID');
        $this->db->like('i.Name', $q, 'after');
        $this->db->group_by('Name');
        $query = $this->db->get();
        
        if ($query->num_rows()) {
            $ret = array();
            foreach ($query->result() as $row) {
                $ret[] = $row->Name;
            }
            return $ret;
        }
    }
    
    public function getProjectTaxa($projectid, $q, $filter=FALSE) {
        $this->db->select('i.Name');
        $this->db->from('keys k');
        $this->db->join('items i', 'k.TaxonomicScopeID=i.ItemsID');
        $this->db->where('k.ProjectsID', $projectid);
        $this->db->like('i.Name', $q, 'after');
        if ($filter)
            $this->db->where_in('k.KeysID', $filter);
        $this->db->order_by('Name');
        $query = $this->db->get();
        
        if ($query->num_rows()) {
            $ret = array();
            foreach ($query->result() as $row) {
                $ret[] = $row->Name;
            }
            return $ret;
        }
    }
    
}

/* End of file autocompletemodel.php */
/* Location: ./models/autocompletemodel.php */