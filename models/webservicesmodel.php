<?php

class WebServicesModel extends KeyModel {
    private $filterProjects;
    private $filterKeys;
    private $filterKeyIDs;
    private $filterItems;
    private $filterNumItems;
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getProjects() {
        
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
        $this->db->select('i.ItemsID, i.Name AS ItemName, pi.Url AS ItemUrl, 
            k.KeysID, k.Name AS KeyName, ts.Name AS TaxonomicScope,
            p.ProjectsID, p.Name AS ProjectName');
        $this->db->from('keys k');
        $this->db->join('projects p', 'k.ProjectsID=p.ProjectsID');
        $this->db->join('leads l', 'k.KeysID=l.KeysID');
        $this->db->join('items i', 'l.ItemsID=i.ItemsID');
        $this->db->join('projectitems pi', 'i.ItemsID=pi.ItemsID AND p.ProjectsID=pi.ProjectsID', 'left');
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
    
    public function getKey($keyid) {
        $this->db->select("k.KeysID AS key_id, 
            k.Name AS key_name, 
            k.UID, 
            k.Description AS description, 
            k.Rank AS rank, 
            k.TaxonomicScope AS taxonomic_scope, 
            k.GeographicScope AS geographic_scope, 
            k.Notes AS notes,
            CONCAT(u.FirstName, ' ', u.LastName) AS owner", FALSE);
        $this->db->from('keys k');
        $this->db->join('users u', 'k.CreatedByID=u.UsersID');
        $this->db->where('k.KeysID', $keyid);
        $query = $this->db->get();
        
        if ($query->num_rows()) {
            return $query->row_array();
        }
    }
    
    public function getProjectDetails($keyid) {
        $this->db->select('p.ProjectsID AS project_id, p.Name AS project_name, p.ProjectIcon AS project_icon');
        $this->db->from('projects p');
        $this->db->join('keys k', 'p.ProjectsID=k.ProjectsID');
        $this->db->where('k.KeysID', $keyid);
        $query = $this->db->get();
        return $query->row_array();
    }
    
    function getSource($keyid) {
        $this->db->select("s.Authors AS author, s.Year AS publication_year, s.Title AS title, s.InAuthors AS in_author, 
            s.InTitle AS in_title, s.Edition AS edition, s.Journal AS journal, s.Series AS series, s.Volume AS volume, 
            s.Part AS part, s.Publisher AS publisher, s.PlaceOfPublication AS place_of_publication, s.Pages AS page, 
            s.Modified AS is_modified, s.Url AS url");
        $this->db->from('keys k');
        $this->db->join('sources s', 'k.SourcesID=s.SourcesID', 'left');
        $this->db->where('k.KeysID', $keyid);
        $query = $this->db->get();
        
        if ($query->num_rows()) {
            return $query->row_array();
        }
    }
    
    public function getKeyItems($keysid) {
        $bie = 'http://bie.ala.org.au/species/';
        
        $query = $this->db->query("SELECT count(*) AS num FROM projectitems WHERE ProjectsID=(SELECT ProjectsID FROM `keys` WHERE KeysID=$keysid)", FALSE);
        $row = $query->row();
        $hasProjectItems = ($row->num) ? TRUE : FALSE;
        
        if ($hasProjectItems) {
            $this->db->select('i.ItemsID AS item_id, 
                coalesce(m.Name, i.Name) AS item_name,
                coalesce(mpi.Url, pi.Url) AS url,
                coalesce(mkto.KeysID, kto.KeysID) AS to_key,
                mlt.ItemsID AS link_to_item_id,
                mlt.Name AS link_to_item_name,
                lpi.Url AS link_to_url,
                mltkto.KeysID AS link_to_key', FALSE);
        }
        else {
            $this->db->select("i.ItemsID AS item_id, 
                coalesce(m.Name, i.Name) AS item_name,
                concat('$bie', coalesce(m.LSID, i.LSID)) AS url,
                coalesce(mkto.KeysID, kto.KeysID) AS to_key,
                mlt.ItemsID AS link_to_item_id,
                mlt.Name AS link_to_item_name,
                concat('$bie', mlt.LSID) AS link_to_url,
                mltkto.KeysID AS link_to_key", FALSE);
        }
        $this->db->from('leads l');
        $this->db->join('keys k', 'l.keysID=k.KeysID');
        $this->db->join('items i', 'l.ItemsID=i.ItemsID');
        $this->db->join('keys kto', 'l.ItemsID=kto.TaxonomicScopeID AND k.ProjectsID=kto.ProjectsID', 'left');
        $this->db->join('groupitem gi', 'l.ItemsID=gi.GroupID AND gi.OrderNumber=0', 'left', FALSE);
        $this->db->join('items m', 'gi.MemberID=m.ItemsID', 'left');
        $this->db->join('keys mkto', 'm.ItemsID=mkto.TaxonomicScopeID AND k.ProjectsID=mkto.ProjectsID', 'left');
        $this->db->join('groupitem gilt', 'l.ItemsID=gilt.GroupID AND gilt.OrderNumber=1', 'left', FALSE);
        $this->db->join('items mlt', 'gilt.MemberID=mlt.ItemsID', 'left');
        $this->db->join('keys mltkto', 'mlt.ItemsID=mltkto.TaxonomicScopeID AND k.ProjectsID=mltkto.ProjectsID', 'left');
        
        if ($hasProjectItems) {
            $this->db->join('projectitems pi', "i.ItemsID=pi.ItemsID AND pi.ProjectsID=k.ProjectsID", 'left', FALSE);
            $this->db->join('projectitems mpi', "m.ItemsID=mpi.ItemsID AND mpi.ProjectsID=k.ProjectsID", 'left', FALSE);
            $this->db->join('projectitems lpi', "mlt.ItemsID=lpi.ItemsID AND lpi.ProjectsID=k.ProjectsID", 'left', FALSE);
        }
        
        $this->db->where('l.KeysID', $keysid);
        $this->db->group_by('item_id');
        $this->db->order_by('item_name, link_to_item_name');
        
        $query = $this->db->get();
        if ($query->num_rows())
            return $query->result_array();
        else
            return FALSE;
    }
    
    public function getLeads($keysid) {
        $this->db->select("p.ParentID AS parent_id, p.LeadsID AS lead_id, /*p.NodeNumber AS `left`, p.HighestDescendantNodeNumber AS `right`,*/ 
            p.LeadText AS lead_text, l.ItemsID AS item", false);
        $this->db->from('leads p');
        $this->db->join('leads l', 'p.LeadsID=l.ParentID AND l.NodeName IS NOT NULL', 'left', false);
        $this->db->where('p.KeysID', $keysid);
        $this->db->where('p.LeadText IS NOT NULL', false, false);
        $this->db->order_by('p.ParentID, p.NodeNumber');
        $query = $this->db->get();
        return $query->result();
    }
    
    public function getRootNode($keysID) {
        $this->db->select('LeadsID AS root_node_id /*, NodeNumber AS `left`, HighestDescendantNodeNumber AS `right`*/', FALSE);
        $this->db->from('leads');
        $this->db->where('KeysID', $keysID);
        $this->db->where('NodeNumber', 1);
        $query = $this->db->get();
        return $query->row();
    }
    
    /**
     * getProjectsStats
     * 
     * Extension of the function in the parent class. Adds taxonomic scope, geographic scope and first key
     * (key with the same taxonomic scope as the project).
     * 
     * @return array
     */
    public function getProjectStats($project=FALSE) {
        $this->db->select('p.ProjectsID, p.Name AS ProjectName, p.TaxonomicScopeID, p.TaxonomicScope,
            p.GeographicScope, p.ProjectIcon, fk.KeysID AS FirstKeyID, fk.Name AS FirstKeyName, count(DISTINCT k.KeysID) AS NumKeys, 
            count(DISTINCT coalesce(gi.MemberID, l.ItemsID)) AS NumTaxa', FALSE);
        $this->db->from('projects p');
        $this->db->join('keys k', 'p.ProjectsID=k.ProjectsID');
        $this->db->join('keys fk', 'p.TaxonomicScopeID=fk.TaxonomicScopeID AND p.ProjectsID=fk.ProjectsID', 'left');
        $this->db->join('leads l', 'k.KeysID=l.KeysID', 'left');
        $this->db->join('groupitem gi', 'l.ItemsID=gi.GroupID', 'left');
        if ($project) {
            $this->db->where('p.ProjectsID', $project);
        }
        $this->db->group_by('p.ProjectsID');
        $this->db->order_by('NumKeys', 'desc');
        $query = $this->db->get();
        if ($query->num_rows()) {
            $ret = array();
            foreach ($query->result_array() as $row) {
                $this->db->select('count(distinct UsersID) AS NumUsers', FALSE);
                $this->db->from('projects_users');
                $this->db->where('ProjectsID', $row['ProjectsID']);
                $q = $this->db->get();
                $ret[] = array_merge($row, $q->row_array());
            }
            return $ret;
        }
    }
    
    public function getProjectItems($project) {
        $this->db->select("coalesce(i.ItemsID,gi.ItemsID) AS item_id, coalesce(i.Name, gi.Name) AS item_name", FALSE);
        $this->db->from('leads l');
        $this->db->join('keys k', 'l.KeysID=k.KeysID');
        $this->db->join('items i', 'l.ItemsID=i.ItemsID AND l.ItemsID NOT IN (SELECT GroupID FROM groupitem)', 'left', FALSE);
        $this->db->join('groupitem g0', 'l.ItemsID=g0.GroupID AND g0.OrderNumber=0', 'left', FALSE);
        $this->db->join('groupitem g1', 'l.ItemsID=g1.GroupID AND g1.OrderNumber=1', 'left', FALSE);
        $this->db->join('items gi', 'coalesce(g0.MemberID, g1.MemberID)=gi.ItemsID', 'left', FALSE);
        $this->db->where('k.ProjectsID', $project);
        $this->db->where('coalesce(i.ItemsID,gi.ItemsID) IS NOT NULL', FALSE, FALSE);
        $this->db->group_by('item_id');
        $this->db->order_by('item_name');
        $query = $this->db->get();
        return $query->result();
    }
    
    /**
     * getProjectKeys()
     * 
     * Extends method in the parent class by adding key hierarchy
     * 
     * @param integer $project
     * @return array
     */
    public function getProjectKeys($project, $filter=false) {
        $andWhere = '';
        if ($filter) {
            $andWhere = ' AND k.KeysID IN (' . implode(',', $filter) . ')';
        }
        $query = $this->db->query("SELECT k.KeysID, k.Name, k.TaxonomicScopeID, i.Name AS TaxonomicScope, s.KeysID AS ParentKeyID, s.Name AS ParentKeyName
            FROM `keys` k
            LEFT JOIN (
            SELECT coalesce(slk.KeysID, sgk.KeysID, sglk.KeysID) AS KeyID, sk.KeysID, sk.Name, sk.TaxonomicScopeID
            FROM `keys` sk
            JOIN leads sl ON sk.KeysID=sl.KeysID
            LEFT JOIN `keys` slk ON sl.ItemsID=slk.TaxonomicScopeID AND slk.ProjectsID=$project
            LEFT JOIN groupitem sg ON sl.ItemsID=sg.GroupID
            LEFT JOIN `keys` sgk ON sg.MemberID=sgk.TaxonomicScopeID AND sg.OrderNumber=0 AND sgk.ProjectsID=$project
            LEFT JOIN `keys` sglk ON sg.MemberID=sglk.TaxonomicScopeID AND sg.OrderNumber=1 AND sglk.ProjectsID=1
            WHERE sk.ProjectsID=$project AND coalesce(slk.KeysID, sgk.KeysID, sglk.KeysID) IS NOT NULL
            GROUP BY KeyID
            ) as s ON k.KeysID=s.KeyID
            LEFT JOIN items i ON k.TaxonomicScopeID=i.ItemsID
            WHERE k.ProjectsID=$project{$andWhere}
            ORDER BY k.Name");
        return $query->result();
    }
    
    
    public function globalFilter($filter) {
        $this->filterKeys = array();
        $this->filterKeyIDs = array();
        $this->db->select('FilterItems, FilterProjects, FilterID, Name, TimestampCreated');
        $this->db->from('globalfilter');
        $this->db->where('FilterID', $filter);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            $filterItems = unserialize($row->FilterItems);
            $projects = NULL;
            $projects = unserialize($row->FilterProjects);
            $this->getGlobalFilterProjects($projects);
            $this->getGlobalFilterKeys($filterItems, $projects);
            $this->getGlobalFilterItems();
            return (object) array(
                'filterID' => $row->FilterID,
                'filterName' => $row->Name,
                'created' => $row->TimestampCreated,
                'numItems' => count($this->filterItems),
                'numItemsOrig' => count(unserialize($row->FilterItems)),
                'numKeys' => count($this->filterKeys),
                'projects' => $this->filterProjects,
                'items' => $this->filterItems,
                'keys' => $this->filterKeys
            );
        }
        else {
            return FALSE;
        }
    }
    
    private function getGlobalFilterProjects($projects) {
        $this->db->select('ProjectsID AS projectID, Name AS projectName, taxonomicScopeID');
        $this->db->from('projects');
        $this->db->where_in('ProjectsID', $projects);
        $query = $this->db->get();
        $this->filterProjects = $query->result();
    }
    
    private function getGlobalFilterItems() {
        $itemIDs = array();
        foreach ($this->filterKeys as $key) {
            $itemIDs = array_merge($itemIDs, $key->items);
        }
        $itemIDs = array_unique($itemIDs);
        
        $this->db->select('ItemsID AS itemID, Name AS itemName');
        $this->db->from('items');
        $this->db->where_in('ItemsID', $itemIDs);
        $query = $this->db->get();
        $this->filterItems = $query->result();
    }
    
    private function getGlobalFilterKeys($items, $projects=FALSE) {
        $newItems = array();
        $this->db->select('k.ProjectsID, k.KeysID, k.TaxonomicScopeID, k.Name AS KeyName, 
            group_concat(DISTINCT cast(l.ItemsID as char)) AS Items', FALSE);
        $this->db->from('keys k');
        $this->db->join('leads l', 'k.KeysID=l.KeysID');
        $this->db->join('groupitem g0', 'l.ItemsID=g0.GroupID AND g0.OrderNumber=0', 'left', FALSE);
        $this->db->join('groupitem g1', 'l.ItemsID=g1.GroupID AND g1.OrderNumber=1', 'left', FALSE);
        $this->db->join('items i', 'coalesce(g1.MemberID, g0.MemberID, l.ItemsID)=i.ItemsID', 'inner', FALSE);
        if ($projects) {
            $this->db->where_in('k.ProjectsID', $projects);
        }
        $this->db->where_in('i.itemsID', $items);
        $this->db->group_by('k.KeysID');
        $query = $this->db->get();
        if ($query->num_rows()) {
            foreach ($query->result() as $row) {
                $key = array();
                $key['keyID'] = $row->KeysID;
                $key['projectID'] = $row->ProjectsID;
                $key['keyName'] = $row->KeyName;
                $key['taxonomicScopeID'] = $row->TaxonomicScopeID;
                $key['items'] = explode(',', $row->Items);
                
                if (in_array($key['keyID'], $this->filterKeyIDs)) {
                    $k = array_search($key['keyID'], $this->filterKeyIDs);
                    $this->filterKeys[$k]->items = array_unique(array_merge($this->filterKeys[$k]->items, $key['items']));
                }
                else {
                    $this->filterKeyIDs[] = $key['keyID'];
                    $this->filterKeys[] = (object) $key;
                    $newItems[] = $row->TaxonomicScopeID;
                }
            }
            
            if ($newItems) {
                $this->getGlobalFilterKeys($newItems, $projects);
            }
        }
    }
    
    public function getFilterProjects($project=false) {
        $ret = array();
        $this->db->select('FilterID, Name, Filter');
        $this->db->from('globalfilter');
        $this->db->where('Name IS NOT NULL', FALSE, FALSE);
        $query = $this->db->get();
        if ($query->num_rows) {
            foreach ($query->result() as $row) {
                $filter = unserialize($row->Filter);
                if (is_array($filter)) {
                    $projects = array_keys($filter);
                    if (!$project || in_array($project, $projects)) {
                        $ret[] = (object) array(
                            'filter_id' => $row->FilterID,
                            'filter_name' => $row->Name,
                            'projects' => $projects
                        );
                    }
                }
            }
        }
        return $ret;
    }

    public function getFilterItemsForKey($filter, $key) {
        $this->globalFilter($filter);
       
        /*$this->db->select('ItemsID');
        $this->db->from('leads');
        $this->db->where('KeysID', $key);
        $this->db->where('ItemsID IS NOT NULL', FALSE, FALSE);
        $this->db->group_by('ItemsID');
        $query = $this->db->get();
        $keyItems = array();
        foreach($query->result() as $row) {
            $keyItems[] = $row->ItemsID;
        }*/
        
        $k = array_search($key, $this->filterKeyIDs);
        if ($k !== FALSE) {
            return $this->filterKeys[$k]->items;
        }
        
    }

    



}

?>
