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
    
    function IsProjectUser($projectid, $userid) {
        $this->db->select('ProjectsUsersID');
        $this->db->from('projects_users');
        $this->db->where('ProjectsID', $projectid);
        $this->db->where('UsersID', $userid);
        $query = $this->db->get();
        if ($query->num_rows())
            return TRUE;
        else
            return FALSE;
    }

    function IsProjectManager($projectid, $userid) {
        $this->db->select('ProjectsUsersID');
        $this->db->from('projects_users');
        $this->db->where('ProjectsID', $projectid);
        $this->db->where('UsersID', $userid);
        $this->db->where('Role', 'Manager');
        $query = $this->db->get();
        if ($query->num_rows())
            return TRUE;
        else
            return FALSE;
    }
    
    function loadProjectItems($project, $csv) {
        $this->db->where('ProjectsID', $project);
        $this->db->delete('projectitems');
        
        $columns = array(
            'TaxonRank',
            'ScientificNameAuthorship',
            'Kingdom',
            'Phylum',
            'Class',
            'Subclass',
            'Superorder',
            'Order',
            'Family',
            'Genus',
            'SpecificEpithet',
            'InfraspecificEpithet',
            'Url'
        );
        $header = array_shift($csv);
        $scinamecolumn = array_search(strtolower('ScientificName'), array_map('strtolower', $header));
        if ($scinamecolumn === FALSE)
            return FALSE;
        
        $assoc = array();
        foreach ($csv as $row) {
            $item = array();
            foreach ($row as $key=>$value) {
                $item[$header[$key]] = ($value) ? $value : NULL;
            }
            $assoc[] = $item;
        }
        
        
        // get highest itemsid
        $this->db->select('MAX(ItemsID)+1 AS NewItemsID', FALSE);
        $this->db->from('items');
        $query = $this->db->get();
        $max = $query->row();
        $newitemsid = $max->NewItemsID;
        
        $families = array();
        $genera = array();
        
        foreach ($assoc as $row) {
            $insert = array();
            $insert['ProjectsID'] = $project;
            
            $nrow = array_change_key_case($row);
            if (!$nrow['scientificname'])
                continue;
            
            $this->db->select('ItemsID');
            $this->db->from('items');
            $this->db->where('Name', $nrow['scientificname']);
            $query = $this->db->get();
            if ($query->num_rows()) {
                $r = $query->row();
                $insert['ItemsID'] = $r->ItemsID;
            }
            else {
                $insert['ItemsID'] = $newitemsid;
                $ins = array(
                    'ItemsID' => $newitemsid,
                    'Name' => $nrow['scientificname']
                );
                $this->db->insert('items', $ins);
                $newitemsid++;
            }
            
            $insert['ScientificName'] = $nrow['scientificname'];
            
            foreach ($nrow as $key => $value) {
                if (($ckey = array_search($key, array_map('strtolower', $columns))) !== FALSE)
                    $insert[$columns[$ckey]] = $value;
            }
            
            $this->db->insert('projectitems', $insert);
        }
        return TRUE;
    }

}



/* End of file keymodel.php */
/* Location: ./models/keymodel.php */