<?php

class WebServicesModel extends CI_Model {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function retrieveFilter($t, $v) {
        $this->db->select('FilterID, Name, Filter, FilterItems,  UsersID, TimestampCreated');
        $this->db->from('globalfilter');
        if ($t == 'id')
            $this->db->where('FilterID', $v);
        else
            $this->db->where('Name', $v);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            $filter = unserialize($row->Filter);
            $filteritems = unserialize($row->FilterItems);
            
            $ret = array();
            $ret['ID'] = $row->FilterID;
            $ret['Name'] = ($row->Name) ? $row->Name : FALSE;
            if ($row->UsersID) {
                $this->db->select("CONCAT(FirstName, ' ', LastName) AS Name", FALSE);
                $this->db->from('users');
                $this->db->where('UsersID', $row->UsersID);
                $query = $this->db->get();
                $r = $query->row();
                $ret['CreatedBy'] = $r->Name;
            }
            else
                $ret['CreatedBy'] = FALSE;
            $ret['TimestampCreated'] = $row->TimestampCreated;
            $ret['TimestampDownloaded'] = date('Y-m-d H:i:s');
            
            $projects = array();
            if (!empty($filter)) {
                foreach ($filter as $pid => $keys) {
                    $this->db->select('Name');
                    $this->db->from('projects');
                    $this->db->where('ProjectsID', $pid);
                    $query = $this->db->get();
                    $row = $query->row();
                    $pname = $row->Name;
                    $projectkeys = array();
                    foreach($keys as $kid => $items) {
                        $this->db->select('Name');
                        $this->db->from('keys');
                        $this->db->where('KeysID', $kid);
                        $query = $this->db->get();
                        $row = $query->row();
                        $kname = $row->Name;
                        $keyitems = array();
                        foreach ($items as $iid) {
                            $this->db->select('Name');
                            $this->db->from('items');
                            $this->db->where('ItemsID', $iid);
                            $query = $this->db->get();
                            $row = $query->row();
                            $iname = $row->Name;
                            $keyitems[] = array(
                                'id' => $iid,
                                'name' => $iname
                            );
                        }
                        $projectkeys[] = array(
                            'id' => $kid,
                            'name' => $kname,
                            'taxa' => $keyitems
                        );
                    }
                    $projects[] = array(
                        'id' => $pid,
                        'name' => $pname,
                        'keys' => $projectkeys
                    );
                }
            }
            $ret['Projects'] = $projects;
            
            $taxa = array();
            $this->db->select('ItemsID, Name');
            $this->db->from('items');
            $this->db->where_in('ItemsID', $filteritems);
            $this->db->order_by('Name');
            $query = $this->db->get();
            foreach ($query->result() as $row) {
                $taxa[] = array(
                    'id' => $row->ItemsID,
                    'name' => $row->Name
                );
            }
            $ret['Taxa'] = $taxa;
            
            return $ret;
        }
    }
    
    public function ws_getItems($params) {
        $this->db->select('i.ItemsID, i.Name AS ItemName, i.LSID AS ItemLSID, 
            k.KeysID, k.Name AS KeyName, ts.Name AS TaxonomicScope,
            p.ProjectsID, p.Name AS ProjectName');
        $this->db->from('keys k');
        $this->db->join('leads l', 'k.KeysID=l.KeysID');
        $this->db->join('items i', 'l.ItemsID=i.ItemsID');
        $this->db->join('projects p', 'k.ProjectsID=p.ProjectsID');
        $this->db->join('items ts', 'k.TaxonomicScopeID=ts.ItemsID', 'left');
        $this->db->group_by('i.ItemsID, k.KeysID');
        $this->db->order_by('KeyName, ItemName');
        
        if (!empty($params['project'])) 
            $this->db->where('p.ProjectsID', $params['project']);
        
        if (!empty($params['key']))
            $this->db->where('k.KeysID', $params['key']);
        
        if (!empty($params['pageSize']) && is_numeric($params['pageSize']) && $params['pageSize'] != 0) {
            $limit = (int) $params['pageSize'];
            if (!empty($params['page']) & is_numeric($params['page']))
                $offset = $limit * ($params['page'] - 1);
            else 
                $offset = 0;
            $this->db->limit($limit, $offset);
        }
        
        $query = $this->db->get();
        if ($query->num_rows())
            return $query->result();
        else
            return FALSE;
    }
    
    public function ws_getKeys($params) {
        $this->db->select('p.ProjectsID, p.Name AS ProjectName, k.KeysID, k.Name AS KeyName, ts.Name AS TaxonomicScope');
        $this->db->from('projects p');
        $this->db->join('keys k', 'p.ProjectsID=k.ProjectsID');
        $this->db->join('items ts', 'k.TaxonomicScopeID=ts.ItemsID');
        
        if (!empty($params['project']) && is_numeric($params['project']))
            $this->db->where('p.ProjectsID', $params['project']);
        if (!empty($params['tscope']))
            $this->db->where('ts.Name', $params['tscope']);
        
        $query = $this->db->get();
        if ($query->num_rows())
            return $query->result();
        else
            return FALSE;
    }
}

?>
