<?php

class FilterModel extends CI_Model {
    
    private $filter;
    private $found;
    private $notfound;
    private $items;
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getFilters($project=FALSE) {
        $ret = array();
        $this->db->select('f.GlobalFilterID, f.FilterID, f.Name');
        $this->db->from('globalfilter f');
        if (isset($this->session->userdata['id']))
            $this->db->where('f.UsersID', $this->session->userdata('id'));
        else
            $this->db->where('f.SessionID', $this->session->userdata('session_id'));
        $this->db->order_by('f.TimestampCreated');
        
        if ($project) {
            $this->db->join('filterproject fp', 'f.GlobalFilterID=fp.FilterID');
            $this->db->where('fp.ProjectID', $project);
        }
        
        $query = $this->db->get();
        if ($query->num_rows()) {
            foreach ($query->result() as $row)
                $ret[$row->FilterID] = ($row->Name) ? $row->Name : $row->FilterID;
        }
        return $ret;
    }
    
    public function getProjectFilters($projectid=FALSE, $userid=FALSE) {
        $this->db->select('p.ProjectsID AS ProjectID, p.Name AS ProjectName, f.FilterID, f.Name AS FilterName');
        $this->db->from('projects p');
        $this->db->join('filterproject fp', 'p.ProjectsID=fp.ProjectID');
        $this->db->join('globalfilter f', 'fp.FilterID=f.GlobalFilterID AND f.IsProjectFilter=true', FALSE, FALSE);
        $this->db->order_by('ProjectName');
        $this->db->order_by('FilterName');
        
        if ($projectid) {
            $this->db->where('p.ProjectsID', $projectid);
        }
        if ($userid) {
            $this->db->join('projects_users pu', 'p.ProjectsID=pu.ProjectsID');
            $this->db->join('users u', 'pu.UsersID=u.UsersID');
            $this->db->where('u.UsersID', $userid);
            $this->db->where('pu.Role', 'Manager');
        }
        
        $query = $this->db->get();
        return $query->result_array();
    }
    
    public function manageFilters($project) {
        $this->db->select('f.GlobalFilterID, f.FilterID, f.Name, u.Username, pu.Role, f.IsProjectFilter=1 AS IsProjectFilter', FALSE);
        $this->db->from('globalfilter f');
        $this->db->join('filterproject fp', 'f.GlobalFilterID=fp.FilterID');
        $this->db->join('projects_users pu', 'fp.ProjectID=pu.ProjectsID AND f.UsersID=pu.UsersID');
        $this->db->join('users u', 'pu.UsersID=u.UsersID');
        $this->db->where('fp.ProjectID', $project);
        $this->db->group_by('f.GlobalFilterID');
        $this->db->having('count(f.GlobalFilterID)=1');
        $query = $this->db->get();
        return $query->result();
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
    
    public function setProjectFilter() {
        $isProjectFilter = ($this->input->post('is_project_filter')) ? 'true' : 'false';
        $filter = $this->input->post('filter_id');
        $update = "UPDATE globalfilter SET IsProjectFilter=$isProjectFilter WHERE FilterID='$filter'";
        $this->db->query($update);
        return $update;
        
        /*$this->db->where('FilterID', $this->input->post('filter_id'));
        $this->db->update('globalfilter', array('IsProjectFilter' => $this->input->post('is_project_filter')));
        return $this->db->last_query();*/
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
        //foreach ($taxa as $name) {
            $this->db->select('i.ItemsID, i.Name');
            $this->db->from('keys k');
            $this->db->join('leads l', 'k.KeysID=l.KeysID');
            $this->db->join('groupitem g0', 'l.ItemsID=g0.GroupID AND g0.OrderNumber=0', 'left', FALSE);
            $this->db->join('groupitem g1', 'l.ItemsID=g1.GroupID AND g1.OrderNumber=1', 'left', FALSE);
            $this->db->join('items i', 'COALESCE(g1.MemberID, g0.MemberID, l.ItemsID)=i.ItemsID', 'inner', FALSE);
            $this->db->where_in('i.Name', $taxa);
            if ($projects)
                $this->db->where_in('k.ProjectsID', $projects);
            $query = $this->db->get();
            if ($query->num_rows()) {
                foreach ($query->result() as $row) {
                    $this->found[] = $row->Name;
                    $this->items[] = $row->ItemsID;
                }
            }
            $this->notfound = array_diff($taxa, $this->found);
        //}
        
        if ($this->found) {
            sort($this->found);
        }
        if ($this->notfound) {
            sort($this->notfound);
        }
        return $this->items;
    }
    
    public function itemsFound() {
        return $this->found;
    }
    
    public function itemsNotFound() {
        return $this->notfound;
    }
    
    public function getKeys($projects=FALSE, $filterid=FALSE, $filtername=FALSE) {
        $projectids = $projects;
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
            'FilterProjects' => ($projectids) ? serialize($projectids) : NULL
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
                'UsersID' => (isset($this->session->userdata['id'])) ? $this->session->userdata['id'] : NULL,
                'IPAddress' => $this->input->ip_address(),
                'SessionID' => $this->session->userdata('session_id')
            ));
            $this->db->insert('globalfilter', $insertArray);
        }
        
        return $filterid;
    }
    
    public function updateFilter($projects=FALSE, $filterid=FALSE, $filtername=FALSE) {
        if ($filterid) {
            $this->db->select('GlobalFilterID');
            $this->db->where('FilterID', $filterid);
        }
        else {
            $this->db->select('MAX(GlobalFilterID)+1 AS GlobalFilterID', FALSE);
        }
        $this->db->from('globalfilter');
        $query = $this->db->get();
        $row = $query->row();
        $id = $row->GlobalFilterID;
        
        $filterArray = array(
            'Name' => ($filtername) ? $filtername : NULL,
            'FilterItems' => serialize($this->items),
            'FilterProjects' => ($projects) ? serialize($projects) : NULL
        );
        if ($this->notfound) {
            $filterArray['ItemsNotFound'] = implode('|', $this->notfound);
        }
        
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
                'GlobalFilterID' => $id,
                'FilterID' => $filterid,
                'TimestampCreated' => date('Y-m-d H:i:s'),
                'UsersID' => (isset($this->session->userdata['id'])) ? $this->session->userdata['id'] : NULL,
                'IPAddress' => $this->input->ip_address(),
                'SessionID' => $this->session->userdata('session_id'),
                'FilterProjects' => ($projects) ? serialize($projects) : NULL,
            ));
            $this->db->insert('globalfilter', $insertArray);
        }
        
        $this->db->where('FilterID', $id);
        $this->db->delete('filterproject');
        if ($projects) {
            foreach ($projects as $project) {
                $this->db->insert('filterproject', array(
                    'FilterID' => $id,
                    'ProjectID' => $project
                ));
            }
        } 
        return $filterid;
        
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
        $this->db->select('FilterProjects');
        $this->db->from('globalfilter');
        $this->db->where('FilterID', $filterid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            $projects = unserialize($row->FilterProjects);
            return $projects;
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
