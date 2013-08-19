<?php

class FilterModel extends CI_Model {
    
    private $filter;
    private $found;
    private $notfound;
    private $items;
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getFilters() {
        $ret = array();
        $this->db->select('GlobalFilterID, FilterID, Name');
        $this->db->from('globalfilter');
        if (isset($this->session->userdata['id']))
            $this->db->where('UsersID', $this->session->userdata('id'));
        else
            $this->db->where('SessionID', $this->session->userdata('session_id'));
        $this->db->order_by('TimestampCreated');
        $query = $this->db->get();
        if ($query->num_rows()) {
            foreach ($query->result() as $row)
                $ret[$row->FilterID] = ($row->Name) ? $row->Name : $row->FilterID;
        }
        return $ret;
    }
    
    public function getProjects() {
        $this->db->select('p.ProjectsID, p.Name');
        $this->db->from('projects p');
        $this->db->where('p.ParentID IS NOT NULL');
        $this->db->join('keys k', 'p.ProjectsID=k.ProjectsID');
        $this->db->order_by('p.Name');
        $query = $this->db->get();
        if ($query->num_rows()) {
            $ret = array();
            $ret[''] = '(select one or more projects)';
            foreach ($query->result() as $row)
                $ret[$row->ProjectsID] = $row->Name;
            return $ret;
        }
    }
    
    public function getKeysFiltered($taxa, $projects=FALSE) {
        $this->db->select('k.KeysID, p.Name AS ProjectName, k.Name AS KeyName, i.ItemsID, i.Name AS TaxonName');
        $this->db->from('projects p');
        $this->db->join('keys k', 'p.ProjectsID=k.ProjectsID');
        $this->db->join('leads l', 'k.KeysID=l.KeysID');
        $this->db->join('items i', 'l.ItemsID=i.ItemsID');
        $this->db->group_by('k.KeysID, i.ItemsID');
        $this->db->order_by('p.Name, k.Name, i.Name');
        $this->db->where_in('i.Name', $taxa);
        if ($projects)
            $this->db->where_in('p.ProjectsID', $projects);
        $query = $this->db->get();
        if ($query->num_rows())
            return $query->result_array();
    }
    
    public function findinKeyBase ($taxa, $projects=FALSE) {
        $this->found = array();
        $this->notfound = array();
        foreach ($taxa as $name) {
            $this->db->select('i.ItemsID, i.Name');
            $this->db->from('keys k');
            $this->db->join('leads l', 'k.KeysID=l.KeysID');
            $this->db->join('items i', 'l.ItemsID=i.ItemsID');
            $this->db->where('i.Name', $name);
            if ($projects)
                $this->db->where_in('k.ProjectsID', $projects);
            $query = $this->db->get();
            
            if ($query->num_rows()) {
                $row = $query->row();
                $this->found[] = $row->Name;
                $this->items[] = $row->ItemsID;
            }
            else {
                $this->notfound[] = $name;
            }
        }
        
        if ($this->found) {
            sort($this->found);
        }
        if ($this->notfound) {
            sort($this->notfound);
        }
    }
    
    public function itemsFound() {
        return $this->found;
    }
    
    public function itemsNotFound() {
        return $this->notfound;
    }
    
    public function getKeys($projects=FALSE, $filterid=FALSE, $filtername=FALSE) {
        $keys = array();
        foreach ($this->items as $item) {
            $this->db->select('p.ProjectsID, p.Name AS ProjectName, k.KeysID, k.Name AS KeyName, l.ItemsID, i.Name AS ItemName, h.NodeNumber');
            $this->db->from('leads l');
            $this->db->join('items i', 'l.ItemsID=i.ItemsID');
            $this->db->join('keys k', 'l.KeysID=k.KeysID');
            $this->db->join('projects p', 'k.ProjectsID=p.ProjectsID');
            $this->db->join('keyhierarchy h', 'k.KeysID=h.KeysID AND k.ProjectsID=h.ProjectsID', 'left');
            $this->db->where('l.ItemsID', $item);
            if ($projects)
                $this->db->where_in('k.ProjectsID', $projects);
            $this->db->group_by('k.ProjectsID, k.KeysID');
            $query = $this->db->get();
            if ($query->num_rows()) {
                foreach ($query->result() as $row) {
                    if ($row->NodeNumber) {
                        $this->db->select('k.ProjectsID, p.Name AS ProjectName, k.KeysID, k.Name AS KeyName, 
                            k.TaxonomicScopeID AS ItemsID, k.TaxonomicScope AS ItemName, h.NodeNumber, h.HighestDescendantNodeNumber, h.Depth');
                        $this->db->from('keyhierarchy h');
                        $this->db->join('keys k', 'h.KeysID=k.KeysID');
                        $this->db->join('projects p', 'k.ProjectsID=p.ProjectsID');
                        $this->db->where('h.NodeNumber <=', $row->NodeNumber);
                        $this->db->where('h.HighestDescendantNodeNumber >=', $row->NodeNumber);
                        $this->db->where('p.ProjectsID', $row->ProjectsID);
                        $this->db->order_by('NodeNumber');
                        $q = $this->db->get();
                        if ($q->num_rows()) {
                            $result = $q->result_array();
                            for ($i = 0; $i < $q->num_rows(); $i++) {
                                if ($i < $q->num_rows()-1) {
                                    $result[$i]['ItemsID'] = $result[$i+1]['ItemsID'];
                                    $result[$i]['ItemName'] = $result[$i+1]['ItemName'];
                                }
                                else {
                                    $result[$i]['ItemsID'] = $row->ItemsID;
                                    $result[$i]['ItemName'] = $row->ItemName;
                                }
                            }
                            $keys = array_merge($keys, $result);
                        }
                    }
                    else {
                        $keys[] = array(
                            'ProjectsID' => $row->ProjectsID,
                            'ProjectName' => $row->ProjectName,
                            'KeysID' => $row->KeysID,
                            'KeyName' => $row->KeyName,
                            'ItemsID' => $row->ItemsID,
                            'ItemName' => $row->ItemName,
                            'NodeNumber' => 'ZZZ',
                            'HighestDescendantNodeNumber' => FALSE,
                            'Depth' => FALSE,
                        );
                    }
                }
            }
        }
        
        $uniquekeys = array_map('unserialize', array_unique(array_map('serialize', $keys)));
        
        $projects = array();
        $nodenumbers = array();
        $keys = array();
        $items = array();
        foreach ($uniquekeys as $key) {
            $projects[] = $key['ProjectName'];
            $nodenumbers[] = $key['NodeNumber'];
            $keys[] = $key['KeyName'];
            $items[] = $key['ItemName'];
        }
        
        array_multisort($projects, SORT_ASC, $nodenumbers, SORT_ASC, $keys, SORT_ASC, $items, SORT_ASC, $uniquekeys);
        
        $count = count($uniquekeys);
        $keys = array();
        $items = array();
        foreach ($uniquekeys as $i => $row) {
            $items[] = $row['ItemsID'];
            if ($i == $count-1 || $uniquekeys[$i+1]['KeysID'] != $row['KeysID']  || $uniquekeys[$i+1]['ProjectsID'] != $row['ProjectsID']) {
                $keys[$row['KeysID']] = $items;
                $items = array();
            }
            if ($i == $count-1 || $uniquekeys[$i+1]['ProjectsID'] != $row['ProjectsID']) {
                $this->filter[$row['ProjectsID']] = $keys;
                $keys = array();
            }
        }
        
        $filterArray = array(
            'Name' => ($filtername) ? $filtername : NULL,
            'Filter' => serialize($this->filter),
            'FilterItems' => serialize($this->items),
        );
        if ($filterid) {
            $this->db->where("GlobalfilterID IN (SELECT GlobalFilterID FROM globalfilter WHERE FilterID='$filterid')", FALSE, FALSE);
            $this->db->delete('globalfilter_key');
            
            $this->db->where('FilterID', $filterid);
            $updateArray = array_merge($filterArray, array(
                'TimestampModified' => date('Y-m-d H:i:s')
            ));
            $this->db->update('globalfilter', $updateArray);
        }
        else {
            $filterid = uniqid();
            $insertArray = array_merge($filterArray, array(
                'FilterID' => $filterid,
                'TimestampCreated' => date('Y-m-d H:i:s'),
                'UsersID' => (isset($this->session->userdata['id'])) ? isset($this->session->userdata['id']) : NULL,
                'IPAddress' => $this->input->ip_address(),
                'SessionID' => $this->session->userdata('session_id')
            ));
            $this->db->insert('globalfilter', $insertArray);
        }
        
        $this->session->set_userdata('GlobalFilter', $filterid);
        $this->session->set_userdata('GlobalFilterOn', TRUE);
        
        return $filterid;
    }
    
    public function getKeysFromFilter($filterid=FALSE) {
        $this->db->select('Filter');
        $this->db->from('globalfilter');
        if ($filterid) 
            $this->db->where('FilterID', $filterid);
        else
            $this->db->where('FilterID', $this->session->userdata['GlobalFilter']);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            $this->filter = unserialize($row->Filter);
            
            $projects = array();
            foreach ($this->filter as $i => $project) {
                $proj = array();
                $this->db->select('p.Name AS ProjectName');
                $this->db->from('projects p');
                $this->db->where('ProjectsID', $i);
                $p_query = $this->db->get();
                $p_row = $p_query->row();
                $proj = array(
                    'id' => $i,
                    'name' => $p_row->ProjectName
                );
                
                $keys = array();
                
                $this->db->select("k.KeysID, k.Name AS KeyName, 
                    IF(h.NodeNumber IS NOT NULL, LPAD(h.NodeNumber, 6, '0'), 'ZZZ') AS NodeNumber, 
                    IF(h.Depth IS NOT NULL, h.Depth, 1) AS Depth", FALSE);
                $this->db->from('keys k');
                $this->db->join('keyhierarchy h', 'k.KeysID=h.KeysID AND k.ProjectsID=h.ProjectsID', 'left');
                $this->db->where_in('k.KeysID', array_keys($project));
                $this->db->order_by('NodeNumber');
                $this->db->order_by('KeyName');
                $k_query = $this->db->get();
                
                $linked_keys = array();
                foreach ($k_query->result() as $k_row) {
                    $linked_keys[] = $k_row->KeysID;
                    $key = array(
                        'id' => $k_row->KeysID,
                        'name' => $k_row->KeyName,
                        'nodenumber' => $k_row->NodeNumber,
                        'depth' => $k_row->Depth,
                    );
                    
                    $this->db->select('i.ItemsID, i.Name AS ItemName');
                    $this->db->from('items i');
                    $this->db->where_in('i.ItemsID', $this->filter[$i][$k_row->KeysID]);
                    $this->db->order_by('ItemName');
                    $i_query = $this->db->get();
                    $items = array();
                    foreach ($i_query->result() as $i_row) {
                        $items[] = array(
                            'id' => $i_row->ItemsID,
                            'name' => $i_row->ItemName
                        );
                    }
                    $key['items'] = $items;
                    $keys[] = $key;
                }
                $proj['keys'] = $keys;
                $projects[] = $proj;
            }
            return $projects;
        }
    }
    
    public function retrieveFilterForKey($key) {
        $this->db->select('ProjectsID');
        $this->db->from('keys');
        $this->db->where('KeysID', $key);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            $project = $row->ProjectsID;

            $this->db->select('Filter');
            $this->db->from('globalfilter');
            $this->db->where('FilterID', $this->session->userdata['GlobalFilter']);
            $query = $this->db->get();
            if ($query->num_rows()) {
                $row = $query->row();
                $this->filter = unserialize($row->Filter);
                return $this->filter[$project][$key];
            }
        }
    }
    
    public function getInItems($key, $filter) {
        $this->db->select('i.ItemsID, i.Name');
        $this->db->from('items i');
        $this->db->join('leads l', 'i.ItemsID=l.ItemsID');
        $this->db->where('l.KeysID', $key);
        if ($filter)
            $this->db->where_in('i.ItemsID', $filter);
        $this->db->group_by('i.ItemsID');
        $this->db->order_by('i.Name');
        $query = $this->db->get();
        if ($query->num_rows()) {
            $ret = array();
            foreach ($query->result() as $row)
                $ret[$row->ItemsID] = $row->Name;
            return $ret;
        }
    }

    public function getOutItems($key, $filter) {
        $this->db->select('i.ItemsID, i.Name');
        $this->db->from('items i');
        $this->db->join('leads l', 'i.ItemsID=l.ItemsID');
        $this->db->where('l.KeysID', $key);
        $this->db->where_not_in('i.ItemsID', $filter);
        $this->db->group_by('i.ItemsID');
        $this->db->order_by('i.Name');
        $query = $this->db->get();
        if ($query->num_rows()) {
            $ret = array();
            foreach ($query->result() as $row)
                $ret[$row->ItemsID] = $row->Name;
            return $ret;
        }
    }
    
    public function setLocalFilter($key, $items) {
        $items = explode(',', $items);
        $this->db->select('NodeNumber');
        $this->db->from('leads');
        $this->db->where('KeysID', $key);
        $this->db->where_in('ItemsID', $items);
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
        $leads = array_unique($leads);
        
        $filterid = uniqid();
        $insertArray = array(
            'FilterID' => $filterid,
            'KeysID' => $key,
            'FilterItems' => serialize($items),
            'FilterLeads' => serialize($leads),
            'TimestampCreated' => date('Y-m-d H:i:s'),
        );
        
        $this->db->insert('localfilter', $insertArray);
        
        $this->session->unset_userdata('LocalFilter');
        $this->session->unset_userdata('LocalFilterKey');
        $this->session->unset_userdata('LocalFilterOn');
        $this->session->set_userdata('LocalFilter', $filterid);
        $this->session->set_userdata('LocalFilterKey', $key);
        $this->session->set_userdata('LocalFilterOn', 1);
    }
    
    public function unsetLocalFilter() {
        if (isset($this->session->userdata['LocalFilterKey']) && 
                isset($this->session->userdata['LocalFilter'])) {
            $this->db->where('FilterID', $this->session->userdata('LocalFilter'));
            $this->db->delete('localfilter');
            
            $local = array(
                'LocalFilter' => '',
                'LocalFilterKey' => '',
                'LocalFilterOn' => ''
            );
            $this->session->unset_userdata($local);
        }
    }
    
    public function getLocalFilterItems() {
        $this->db->select('FilterItems');
        $this->db->from('localfilter');
        $this->db->where('FilterID', $this->session->userdata['LocalFilter']);
        $this->db->where('KeysID', $this->session->userdata['LocalFilterKey']);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            return unserialize($row->FilterItems);
        }
        else
            return false;
    }
    
    public function getGlobalFilterTaxa($filterid) {
        $this->db->select('FilterItems');
        $this->db->from('globalfilter');
        $this->db->where('FilterID', $filterid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            $ret = array();
            $items = unserialize($row->FilterItems);
            foreach ($items as $item)
                $ret[] = $this->getTaxonName ($item);
            sort($ret);
            return $ret;
        }
    }
    
    public function getGlobalfilterProjects($filterid) {
        $this->db->select('Filter');
        $this->db->from('globalfilter');
        $this->db->where('FilterID', $filterid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            $filter = unserialize($row->Filter);
            $ret = array();
            foreach ($filter as $key=>$array) {
                $ret[] = $key;
            }
            return $ret;
        }
        else
            return FALSE;
    }
    
    public function getGlobalfilterMetadata($filterid) {
        $this->db->select('Name AS FilterName, FilterID');
        $this->db->from('globalfilter');
        $this->db->where('FilterID', $filterid);
        $query = $this->db->get();
        if ($query->num_rows())
            return $query->row();
        else
            return FALSE;
    }
    
    private function getTaxonName($itemsid) {
        $this->db->select('Name');
        $this->db->from('items');
        $this->db->where('ItemsID', $itemsid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            return $row->Name;
        }
        else
            return FALSE;
    }
    
    public function getGlobalFilterID($filterid) {
        $this->db->select('GlobalFilterID');
        $this->db->from('globalfilter');
        $this->db->where('FilterID', $filterid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            return $row->GlobalFilterID;
        }
        else
            return FALSE;
    }
    
    public function getGlobalFilterName($filterid) {
        $this->db->select('Name');
        $this->db->from('globalfilter');
        $this->db->where('FilterID', $filterid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            return $row->Name;
        }
        else
            return FALSE;
        
    }
    
    public function deleteGlobalFilter($filterid) {
        $this->db->select('GlobalFilterID');
        $this->db->from('globalfilter');
        $this->db->where('FilterID', $filterid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            $this->db->where('GlobalFilterID', $row->GlobalFilterID);
            $this->db->delete('globalfilter_key');
            $this->db->where('GlobalFilterID', $row->GlobalFilterID);
            $this->db->delete('globalfilter');
        }
    }
    
    public function importGlobalFilter($filename) {
        if (!$filename) return FALSE;
        $json = file_get_contents($filename);
        $data = json_decode($json);
        
        $name = $data->Name . ' (imported)';
        
        $projects = array();
        foreach ($data->Projects as $project) {
            $projects[] = $project->id;
        }
        
        $taxa = array();
        foreach ($data->Taxa as $item) {
            $taxa[] = $item->id;
        }
        $this->items = $taxa;
        
        $filterid = $this->getKeys($projects, FALSE, $name);
        
        return $filterid;
    }
}


?>
