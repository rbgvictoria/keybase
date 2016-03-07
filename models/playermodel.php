<?php

class PlayerModel extends CI_Model {
    
    public $FilterItems;
    public $FilterLeads;
    private $BreadCrumbs;
    protected $hasProjectItems;
    
    public function __construct() {
        parent::__construct();
        $this->FilterItems = array();
        $this->FilterLeads = array();
    }

    /**
     *
     * @return string 
     */
    public function getKeyName($keyid) {
        $this->db->select('Name');
        $this->db->from('keys');
        $this->db->where('KeysID', $keyid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            return $row->Name;
        }
        else
            return FALSE;
    }
    
    public function getProjectID($key) {
        $this->db->select('ProjectsID');
        $this->db->from('keys');
        $this->db->where('KeysID', $key);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            return $row->ProjectsID;
        }
        else
            return FALSE;
    }
    
    public function keyInFilter($key) {
        $infilter = FALSE;
        if (isset($this->session->userdata['GlobalFilter']) && $this->session->userdata('GlobalFilter')) {
            $this->db->select('GlobalFilterID, FilterItems');
            $this->db->from('globalfilter');
            $this->db->where('FilterID', $this->session->userdata['GlobalFilter']);
            $query = $this->db->get();
            $row = $query->row();
            $filter = unserialize($row->FilterItems);
            
            $this->db->select('k.KeysID');
            $this->db->from('keys k');
            $this->db->join('leads l', 'k.KeysID=l.KeysID');
            $this->db->join('groupitem g', 'l.ItemsID=g.GroupID AND OrderNumber=1', 'left', FALSE);
            $this->db->where_in('COALESCE(g.MemberID, l.ItemsID)', $filter, FALSE);
            $this->db->group_by('k.KeysID');
            $query = $this->db->get();
            
            foreach ($query->result() as $row) {
                if ($row->KeysID == $key) {
                    $infilter = true;
                }
            }
            
            /*foreach ($filter as $index => $keys) {
                if (in_array($key, array_keys($keys)))
                    $infilter = TRUE;
            }*/
        }
        return $infilter;
    }
    
    public function GlobalFilter($project, $key) {
        if (isset($this->session->userdata['LocalFilter']) && $this->session->userdata['LocalFilterKey']==$key && $this->session->userdata['LocalFilterOn']) {
            $this->db->select('FilterItems, FilterLeads');
            $this->db->from('localfilter');
            $this->db->where('FilterID', $this->session->userdata['LocalFilter']);
            $query = $this->db->get();
            if ($query->num_rows()) {
                $row = $query->row();
                $this->FilterItems = unserialize($row->FilterItems);
                $this->FilterLeads = unserialize($row->FilterLeads);
            }
        }
        else {
            $unset = array(
                'LocalFilter' => '',
                'LocalFilterKey' => '',
                'LocalFilterOn' => '',
            );
            $this->session->unset_userdata($unset);
            if (isset($this->session->userdata['GlobalFilter']) && isset($this->session->userdata['GlobalFilterOn']) &&
                $this->session->userdata('GlobalFilter') && $this->session->userdata('GlobalFilterOn')) {
                $this->db->select('gfk.FilterItems, gfk.FilterLeads');
                $this->db->from('globalfilter gf');
                $this->db->join('globalfilter_key gfk', 'gf.GlobalFilterID=gfk.GlobalFilterID');
                $this->db->where('gf.FilterID', $this->session->userdata['GlobalFilter']);
                $this->db->where('gfk.KeysID', $key);
                $query = $this->db->get();
                if ($query->num_rows()) {
                    $row = $query->row();
                    $this->FilterItems = unserialize($row->FilterItems);
                    $this->FilterLeads = unserialize($row->FilterLeads);
                }
                else {
                    $this->db->select('GlobalFilterID, Filter');
                    $this->db->from('globalfilter');
                    $this->db->where('FilterID', $this->session->userdata['GlobalFilter']);
                    $query = $this->db->get();
                    $row = $query->row();
                    $filter = unserialize($row->Filter);
                    $globalfilterid = $row->GlobalFilterID;
                    if (isset($filter[$project][$key]))
                        $this->FilterItems = $filter[$project][$key];

                    if ($this->FilterItems) {
                        $this->db->select('NodeNumber');
                        $this->db->from('leads');
                        $this->db->where('KeysID', $key);
                        $this->db->where_in('ItemsID', $this->FilterItems);
                        $query = $this->db->get();
                        $leads = array();
                        foreach ($query->result() as $row) {
                            $this->db->select('LeadsID');
                            $this->db->from('leads');
                            $this->db->where('KeysID', $key);
                            $this->db->where('NodeNumber <=', $row->NodeNumber);
                            $this->db->where('HighestDescendantNodeNumber >=', $row->NodeNumber);
                            $q = $this->db->get();
                            foreach ($q->result() as $r)
                                $leads[] = $r->LeadsID;
                        }
                        $this->FilterLeads = array_unique($leads);

                        $insertArray = array(
                            'GlobalFilterID' => $globalfilterid,
                            'KeysID' => $key,
                            'TimestampCreated' => date('Y-m-d H:i:s'),
                            'FilterItems' => serialize($this->FilterItems),
                            'FilterLeads' => serialize($this->FilterLeads),
                        );

                        $this->db->insert('globalfilter_key', $insertArray);
                    }
                    else {
                        $this->FilterItems = array();
                        $this->FilterLeads = array();
                    }
                }
            }
        }
    }
    
    function getBreadCrumbs($key) {
        $this->BreadCrumbs = array();
        $this->getCrumb($key);
        return array_reverse($this->BreadCrumbs);
    }
    
    function getCrumb($key) {
        $this->db->select('coalesce(pk.KeysID, gpk.KeysID, gltpk.KeysID) AS key_id, coalesce(pk.Name, gpk.Name, gltpk.Name) AS key_name', FALSE);
        $this->db->from('keys k');
        $this->db->join('leads l', 'k.TaxonomicScopeID=l.ItemsID', 'left');
        $this->db->join('keys pk', 'l.KeysID=pk.KeysID AND k.ProjectsID=pk.ProjectsID', 'left');
        $this->db->join('groupitem g', 'k.TaxonomicScopeID=g.MemberID AND g.OrderNumber=0', 'left', FALSE);
        $this->db->join('leads gl', 'g.GroupID=gl.ItemsID', 'left');
        $this->db->join('keys gpk', 'gl.KeysID=gpk.KeysID AND k.ProjectsID=gpk.ProjectsID', 'left');
        $this->db->join('groupitem glt', 'k.TaxonomicScopeID=glt.MemberID AND glt.OrderNumber=1', 'left', FALSE);
        $this->db->join('leads gltl', 'glt.GroupID=gltl.ItemsID', 'left');
        $this->db->join('keys gltpk', 'gltl.KeysID=gltpk.KeysID AND k.ProjectsID=gltpk.ProjectsID', 'left');
        $this->db->where('k.KeysID', $key);
        $this->db->where('coalesce(pk.KeysID, gpk.KeysID, gltpk.KeysID) IS NOT NULL', FALSE, FALSE);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row_array();
            $this->BreadCrumbs[] = $row;
            $this->getCrumb($row['key_id']);
        }
    }
    
    public function hasProjectItems($project) {
        $this->db->select('count(Url) as Url', FALSE);
        $this->db->from('projectitems');
        $this->db->where('ProjectsID', $project);
        $query = $this->db->get();
        $row = $query->row();
        $this->hasProjectItems = ($row->Url) ? TRUE : FALSE;
    }
    
    
    
    
}

?>
