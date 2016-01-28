<?php
class KeyModel extends CI_Model {
    private $ret;
    
    function  __construct() {
        parent::__construct();
    }

    function getKeys($item=FALSE, $key=FALSE) {
        $this->db->select('k.KeysID, k.Name, k.Rank, k.TaxonomicScope, k.GeographicScope,
            p.ProjectsID, p.Name AS ProjectNamezz');
        $this->db->from('keys k');
        $this->db->join('projects p', 'k.ProjectsID=p.ProjectsID', 'left');
        if ($item)
            $this->db->where('k.TaxonomicScopeID', $item);
        if ($key)
            $this->db->where('KeysID', $key);
        $this->db->order_by('k.Name');
        $query = $this->db->get();
        if ($query->num_rows()) {
            if ($key && $query->num_rows() == 1) {
                $row = $query->row();
                $ret = array(
                        'id' => $row->KeysID,
                        'name' => $row->Name, 
                        'rank' => $row->Rank,
                        'taxonomicscope' => $row->TaxonomicScope,
                        'geographicscope' => $row->GeographicScope,
                        'projectid' => $row->ProjectsID,
                        'projectname' => $row->ProjectName,
                    
                    );
            } else {
                $ret = array();
                foreach ($query->result() as $row) {
                    $ret[] = array(
                            'id' => $row->KeysID,
                            'name' => $row->Name, 
                            'rank' => $row->Rank,
                            'taxonomicscope' => $row->TaxonomicScope,
                            'geographicscope' => $row->GeographicScope,
                            'projectid' => $row->ProjectsID,
                            'projectname' => $row->ProjectName,
                        );
                }
            }
            return $ret;
        }
    }
    
    function getKey($keyid) {
        $this->db->select("k.KeysID, k.Name, k.UID, k.Description, k.Rank, k.TaxonomicScope, k.GeographicScope, k.Notes,
            CONCAT(u.FirstName, ' ', u.LastName) AS Owner, s.Authors, s.`Year`, s.Title, s.InAuthors, s.InTitle, 
            s.Edition, s.Journal, s.Series, s.Volume, s.Part, s.Publisher, s.PlaceOfPublication, s.Pages, s.Modified, 
            s.Url, 
            k.ProjectsID, k.CreatedByID, p.Name AS ProjectName", FALSE);
        $this->db->from('keys k');
        $this->db->join('sources s', 'k.SourcesID=s.SourcesID', 'left');
        $this->db->join('users u', 'k.CreatedByID=u.UsersID');
        $this->db->join('projects p', 'k.ProjectsID=p.ProjectsID', 'left');
        $this->db->where('k.KeysID', $keyid);
        $query = $this->db->get();
        
        if ($query->num_rows()) {
            return $query->row_array();
        }
    }
    
    public function getKeyByUID($uid) {
        $this->db->select('KeysID');
        $this->db->from('keys');
        $this->db->where('UID', $uid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            return $row->KeysID;
        }
        else
            return FALSE;
    }
    
    public function getMyKeys($userid) {
        $ret = array();
        $this->db->select('k.KeysID, k.Name, k.Rank, k.TaxonomicScope, k.GeographicScope, 
            p.ProjectsID, p.Name AS ProjectName');
        $this->db->from('keys k');
        $this->db->join('projects p', 'k.ProjectsID=p.ProjectsID', 'left');
        $this->db->where('CreatedByID', $userid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            foreach ($query->result() as $row)
                $ret[] = array(
                            'id' => $row->KeysID,
                            'name' => $row->Name, 
                            'rank' => $row->Rank,
                            'taxonomicscope' => $row->TaxonomicScope,
                            'geographicscope' => $row->GeographicScope,
                            'projectid' => $row->ProjectsID,
                            'projectname' => $row->ProjectName,
                        );
        }
        return $ret;
    }
    
    public function getMyProjects($userid) {
        $ret = array();
        $this->db->select('p.ProjectsID, p.Name, pu.Role');
        $this->db->from('projects p');
        $this->db->join('projects_users pu', 'p.ProjectsID=pu.ProjectsID');
        $this->db->join('users u', 'pu.UsersID=u.UsersID');
        $this->db->where('u.UsersID', $userid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            foreach ($query->result() as $row) {
                $ret[] = array(
                    'id' => $row->ProjectsID,
                    'name' => $row->Name,
                    'myRole' => $row->Role
                );
            }
        }
        return $ret;
    }
    
    public function getProjects() {
        $ret = array();
        $this->db->select('ProjectsID, Name');
        $this->db->from('projects');
        $this->db->where('ParentID IS NOT NULL', FALSE, FALSE);
        $this->db->order_by('Name');
        $query = $this->db->get();
        if ($query->num_rows()) {
            foreach ($query->result() as $row) {
                $ret[] = array(
                    'id' => $row->ProjectsID,
                    'name' => $row->Name,
                );
            }
        }
        return $ret;
    }
    
    public function getProjectData($projectid) {
        $this->db->select('p.ProjectsID, p.Name, p.TaxonomicScope, p.GeographicScope, p.Description, p.ProjectIcon');
        $this->db->select('count(DISTINCT k.KeysID) AS NumKeys, count(DISTINCT l.ItemsID) AS NumTaxa');
        $this->db->from('projects p');
        $this->db->join('keys k', 'p.ProjectsID=k.ProjectsID', 'left');
        $this->db->join('leads l', 'k.KeysID=l.KeysID', 'left');
        $this->db->where('p.ProjectsID', $projectid);
        $this->db->group_by('p.ProjectsID');
        $query = $this->db->get();
        if ($query->num_rows()) {
            return $query->row_array();
        }
        else 
            return FALSE;
    }
    
    public function getProjectDetails($keyid) {
        $this->db->select('p.ProjectsID, p.Name, p.ProjectIcon');
        $this->db->from('projects p');
        $this->db->join('keys k', 'p.ProjectsID=k.ProjectsID');
        $this->db->where('k.KeysID', $keyid);
        $query = $this->db->get();
        return $query->row_array();
    }
    
    public function getProjectID($keyid) {
        $this->db->select('ProjectsID');
        $this->db->from('keys');
        $this->db->where('KeysID', $keyid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            return $row->ProjectsID;
        }
        else
            return FALSE;
            
    }
    
    public function getProjectStats() {
        $this->db->select('p.ProjectsID, p.Name, p.ProjectIcon, count(DISTINCT k.KeysID) AS NumKeys, 
            count(DISTINCT l.ItemsID) AS NumTaxa');
        $this->db->from('projects p');
        $this->db->join('keys k', 'p.ProjectsID=k.ProjectsID', 'left');
        $this->db->join('leads l', 'k.KeysID=l.KeysID', 'left');
        $this->db->where('p.ParentID IS NOT NULL', FALSE, FALSE);
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
    
    public function getItems($key) {
        return $this->getItemsByID($key);
    }
    
    private function getItemsByID($keysid) {
        $this->db->select('i.Name, i.LSID');
        $this->db->from('items i');
        $this->db->join('leads l', 'i.ItemsID=l.ItemsID');
        $this->db->where('l.KeysID', $keysid);
        $this->db->group_by('Name');
        
        $query = $this->db->get();
        if ($query->num_rows())
            return $query->result_array();
        else
            return FALSE;
    }
    
    public function keyTaxa($keysid) {
        
    }
    
    public function getItemName($itemid) {
        $this->db->select('Name');
        $this->db->from('items');
        $this->db->where('ItemsID', $itemid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            return $row->Name;
        }
        else
            return FALSE;
    }
    
    public function getItemID($itemname) {
        $this->db->select('ItemsID');
        $this->db->from('items');
        $this->db->where('Name', $itemname);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            return $row->ItemsID;
        }
        else 
            return FALSE;
    }
    
    public function getItemInfo($itemid) {
        $this->db->select('ItemsID, Name AS ItemName, LSID as ItemLSID');
        $this->db->from('items');
        $this->db->where('itemsID', $itemid);
        $query = $this->db->get();
        if ($query->num_rows())
            return $query->row();
        else 
            return FALSE;
    }
    
    public function compareKeys($itemid) {
        $this->db->select('KeysID');
        $this->db->from('keys');
        $this->db->where('TaxonomicScopeID', $itemid);
        $query = $this->db->get();
        if ($query->num_rows() > 1) {
            $itemsInKey = array();
            $items = array();
            foreach ($query->result() as $row) {
                $taxa = $this->getItemsByID($row->KeysID);
                $names = array();
                foreach ($taxa as $taxon) {
                    $names[] = $taxon['Name']; 
                }
               
                $itemsInKey[] = $names;
                foreach ($taxa as $item)
                    $items[] = $item['Name'];
            }
            
            $items = array_unique($items);
            sort($items);
            
            $ret = array();
            foreach ($items as $index => $item) {
                $ret[$index]['Name'] = $item;
                foreach ($itemsInKey as $key) {
                    if (in_array($item, $key))
                        $ret[$index]['keys'][] = 1;
                    else
                        $ret[$index]['keys'][] = 0;
                }
            }
            
            return $ret;
        }
        else
            return FALSE;
    }
    
    public function getProjectKeys($projectid, $userid=false, $filter=false) {
        $this->db->select('k.KeysID, k.Name, k.Description, k.CreatedByID');
        $this->db->from('keys k');
        $this->db->where('k.ProjectsID', $projectid);
        if ($userid) {
            $this->db->select("IF(pu.UsersID IS NOT NULL, 1, 0) AS Edit, IF(pu.Role='Manager' OR k.CreatedByID=$userid, 1, 0) AS `Delete`", FALSE);
            $this->db->join('projects p', 'k.ProjectsID=p.ProjectsID');
            $this->db->join('projects_users pu', "p.ProjectsID=pu.ProjectsID AND pu.UsersID=$userid", 'left');
        }
        if ($filter) {
            $this->db->where_in('k.KeysID', $filter);
        }
        $this->db->group_by('k.KeysID');
        $this->db->order_by('k.Name');
        $query = $this->db->get();
        if ($query->num_rows())
            return $query->result_array();
        else 
            return FALSE;
    }
    
    public function getProjectKeysLinked($projectid, $filter=false) {
        $this->db->select('k.KeysID, k.Name, h.Depth, k.CreatedByID');
        $this->db->from('keys k');
        $this->db->join('keyhierarchy h', 'k.KeysID=h.KeysID');
        $this->db->where('k.ProjectsID', $projectid);
        if ($filter)
            $this->db->where_in('k.KeysID', $filter);
        $this->db->order_by('h.NodeNumber');
        $query = $this->db->get();
        if ($query->num_rows())
            return $query->result_array();
        else 
            return FALSE;
    }
    
    public function getProjectKeysOrphaned($projectid, $filter=false) {
        $this->db->select('c.KeysID, c.Name, c.CreatedByID');
        $this->db->from('keys k');
        $this->db->join('keyhierarchy h', 'k.KeysID=h.KeysID', 'left');
        $this->db->join('leads l', 'k.KeysID=l.KeysID');
        $this->db->join('keys c', "l.ItemsID=c.TaxonomicScopeID AND c.ProjectsID=k.ProjectsID", 'right');
        $this->db->join('keyhierarchy ch', "c.KeysID=ch.KeysID", 'left');
        $this->db->where('c.ProjectsID', $projectid);
        $this->db->where('ch.KeysID IS NULL', false, false);
        $this->db->where('k.KeysID IS NULL', false, false);
        if ($filter) 
            $this->db->where_in('c.KeysID', $filter);
        $this->db->order_by('c.Name');
        $query = $this->db->get();
        if ($query->num_rows()) {
            $this->ret = array();
            foreach($query->result() as $row) {
                $this->getLinkedKey($row, 1, $projectid, $filter);
            }
            return $this->ret;
        }
        else 
            return FALSE;
    }
    
    private function getLinkedKey($row, $depth, $projectid, $filter=false) {
        $this->db->select('k.KeysID, k.Name, k.CreatedByID');
        $this->db->from('leads l');
        $this->db->join('keys k', 'l.ItemsID=k.TaxonomicScopeID');
        $this->db->where('l.KeysID', $row->KeysID);
        $this->db->where('k.ProjectsID', $projectid);
        if ($filter)
            $this->db->where_in('k.KeysID', $filter);
        $query = $this->db->get();
        
        $this->ret[] = array(
            'KeysID' => $row->KeysID,
            'Name' => $row->Name,
            'Depth' => $depth,
            'CreatedByID' => $row->CreatedByID,
        );
            
        if ($query->num_rows()) {
            $depth++;
            foreach ($query->result() as $row) {
                $this->getLinkedKey($row, $depth, $projectid);
            }
        }
    }
    
    public function getProjectUsers($projectid) {
        $this->db->select("pu.ProjectsUsersID, u.UsersID, CONCAT(u.FirstName, ' ', u.LastName) AS FullName, IF(pu.Role='User', 'Contributor', pu.Role) AS Role", FALSE);
        $this->db->from('projects_users pu');
        $this->db->join('users u', 'pu.UsersID=u.UsersID');
        $this->db->where('pu.ProjectsID', $projectid);
        $this->db->order_by('FullName');
        $query = $this->db->get();
        if ($query->num_rows())
            return $query->result_array();
        else
            return FALSE;
    }
    
    public function getNumberOfLeads($keyid) {
        $this->db->select('COUNT(*) AS NumLeads');
        $this->db->from('leads');
        $this->db->where('KeysID', $keyid);
        $query = $this->db->get();
        $row = $query->row();
        return $row->NumLeads;
    }
    
    
    public function editKeyMetadata($data, $userid=FALSE) {
        $updateArray = array(
            'Name' => $data['name'],
            'Description' => $data['description'],
            'TaxonomicScope' => $data['taxonomicscope'],
            'GeographicScope' => $data['geographicscope'],
            'Notes' => (isset($data['notes'])) ? $data['notes'] : FALSE,
            'ProjectsID' => $data['projectid'],
            'CreatedByID' => $data['createdbyid'],
        );
        
        if (!isset($data['keyid'])) {
            $insertArray = $updateArray;
            $this->db->select('MAX(KeysID) AS max, MAX(UID) AS maxuid', FALSE);
            $this->db->from('keys');
            $query = $this->db->get();
            $row = $query->row();
            $keysid = ($row->max) ? $row->max + 1 : 1;
            $insertArray['KeysID'] = $keysid;
            $insertArray['UID'] = ($row->maxuid) ? str_pad($row->maxuid + 1, 6, '0', STR_PAD_LEFT) : '000001';
            $insertArray['Name'] = $data['name'];
            $this->db->insert('keys', $insertArray);
            $insertArray = array();
        } else
            $keysid = $data['keyid'];
        
        if ($data['taxonomicscope']) {
            $this->db->select('ItemsID');
            $this->db->from('items');
            $this->db->where('Name', $data['taxonomicscope']);
            $query = $this->db->get();
            if ($query->num_rows()) {
                $row = $query->row();
                $updateArray['TaxonomicScopeID'] = $row->ItemsID;
            }
            else {
                $insertArray = array();
                $this->db->select('MAX(ItemsID) AS max', FALSE);
                $this->db->from('items');
                $query = $this->db->get();
                $row = $query->row();
                $itemsid = ($row->max) ? $row->max + 1 : 1;
                $updateArray['TaxonomicScopeID'] = $itemsid;
                $insertArray['ItemsID'] = $itemsid;
                $insertArray['Name'] = $data['taxonomicscope'];
                $this->db->insert('items', $insertArray);
            }
        }

        if ($data['authors'] || $data['title']) {
            $updArray = array(
                'Authors' => $data['authors'],
                'Year' => $data['year'],
                'Title' => $data['title'],
                'InAuthors' => $data['inauthors'],
                'InTitle' => $data['intitle'],
                'Edition' => $data['edition'],
                'Journal' => $data['journal'],
                'Volume' => $data['volume'],
                'Part' => $data['part'],
                'Pages' => $data['pages'],
                'Publisher' => $data['publisher'],
                'PlaceOfPublication' => $data['placeofpublication'],
                'Url' => $data['url'],
            );
            if (isset($data['modified']))
                $updArray['Modified'] = $data['modified'];
            else
                $updArray['Modified'] = NULL;

            $this->db->select('SourcesID');
            $this->db->from('keys');
            $this->db->where('KeysID', $keysid);
            $query = $this->db->get();
            $row = $query->row();
            if ($row->SourcesID) {
                $updateArray['SourcesID'] = $row->SourcesID;
                $this->db->where('SourcesID', $row->SourcesID);
                $this->db->update('sources', $updArray);
            }
            else {
                $this->db->select('MAX(SourcesID) AS max', FALSE);
                $this->db->from('sources');
                $query = $this->db->get();
                $row = $query->row();
                $sourcesid  = ($row->max) ? $row->max + 1 : 1;
                $updateArray['SourcesID'] = $sourcesid;
                $updArray['SourcesID'] = $sourcesid;
                $this->db->insert('sources', $updArray);
            }
        }
        elseif (isset($data['sourceid'])) {
            $updateArray['SourcesID'] = $data['sourceid'];
        }
        $timestamp = date('Y-m-d H:i:s');
        $updateArray['TimestampModified'] = $timestamp;
        $this->db->where('KeysID', $keysid);
        $this->db->update('keys', $updateArray);
        
        if (isset($data['keyid']) && (!empty($data['changecomment']))) {
            $changesArray = array(
                'KeysID' => $data['keyid'],
                'TimestampModified' => $timestamp,
            );
            if (isset($data['changecomment'])) 
                $changesArray['Comment'] = $data['changecomment'];
            if ($userid)
                $changesArray['ModifiedByAgentID'] = $userid;
            $this->db->insert('changes', $changesArray);
        }
        
        return $keysid;
    }
    
    public function addProject($data) {
        $taxonomicscopeid = NULL;
        if ($data['taxonomicscope']) {
            $this->db->select('ItemsID');
            $this->db->from('items');
            $this->db->where('Name', $data['taxonomicscope']);
            $query = $this->db->get();
            if ($query->num_rows()) {
                $row = $query->row();
                $taxonomicscopeid = $row->ItemsID;
            }
            else {
                $this->db->select('MAX(ItemsID) AS max', FALSE);
                $this->db->from('items');
                $query = $this->db->get();
                $row = $query->row();
                $taxonomicscopeid = $row->max + 1;
                
                $this->db->insert('items', array('ItemsID' => $taxonomicscopeid, 'Name' => $data['taxonomicscope']));
            }
        }

        $insertArray = array(
            'Name' => $data['name'],
            'TaxonomicScope' => $data['taxonomicscope'],
            'TaxonomicScopeID' => $taxonomicscopeid,
            'Geographicscope' => $data['geographicscope'],
            'Description' => $data['description'],
            'ParentID' => 3,
        );
        
        $this->db->select('MAX(ProjectsID) as max', FALSE);
        $this->db->from('projects');
        $query = $this->db->get();
        $row = $query->row();
        $projectid = ($row->max) ? $row->max + 1 : 1;
        
        $insertArray['ProjectsID'] = $projectid;
        $this->db->insert('projects', $insertArray);
        
        $insertArray = array(
            'ProjectsID' => $projectid,
            'UsersID' => $data['userid'],
            'Role' => 'Manager',
        );
        $this->db->insert('projects_users', $insertArray);
        
        return $projectid;
    }
    
    public function editProject($data) {
        $taxonomicscopeid = NULL;
        if ($data['taxonomicscope']) {
            $this->db->select('ItemsID');
            $this->db->from('items');
            $this->db->where('Name', $data['taxonomicscope']);
            $query = $this->db->get();
            if ($query->num_rows()) {
                $row = $query->row();
                $taxonomicscopeid = $row->ItemsID;
            }
            else {
                $this->db->select('MAX(ItemsID) AS max', FALSE);
                $this->db->from('items');
                $query = $this->db->get();
                $row = $query->row();
                $taxonomicscopeid = $row->max + 1;
                
                $this->db->insert('items', array('ItemsID' => $taxonomicscopeid, 'Name' => $data['taxonomicscope']));
            }
        }
        
        $updateArray = array(
            'Name' => $data['name'],
            'TaxonomicScope' => $data['taxonomicscope'],
            'TaxonomicScopeID' => $taxonomicscopeid,
            'GeographicScope' => $data['geographicscope'],
            'Description' =>$data['description'],
        );
        
        $this->db->where('ProjectsID', $data['projectid']);
        $this->db->update('projects', $updateArray);
    }
    
    public function getUsers($project=FALSE) {
        $projectusers = array();
        if ($project) {
            $this->db->select('UsersID');
            $this->db->from('projects_users');
            $this->db->where('ProjectsID', $project);
            $query = $this->db->get();
            foreach ($query->result() as $row)
                $projectusers[] = $row->UsersID;
        }
        
        $this->db->select('u.UsersID, u.FirstName, u.LastName, u.Username');
        $this->db->from('users u');
        $this->db->join('projects_users pu', 'u.UsersID=pu.UsersID', 'left');
        if ($projectusers) {
            $this->db->where_not_in('u.UsersID', $projectusers);
        }
        $query = $this->db->get();
        return $query->result_array();
    }
    
    public function addProjectUser($data) {
        $insertArray = array(
            'UsersID' => $data['userid'],
            'ProjectsID' => $data['projectid'],
            'Role' => $data['role'],
        );
        $this->db->insert('projects_users', $insertArray);
    }
    
    public function getTaxaWithoutLSID() {
        $this->db->select('ItemsID, Name');
        $this->db->from('items');
        $this->db->where('LSID IS NULL', FALSE, FALSE);
        $query = $this->db->get();
        return $query->result_array();
    }
    
    public function updateLSID($itemsid, $lsid) {
        $this->db->where('ItemsID', $itemsid);
        $this->db->update('items', array('LSID'=>$lsid));
    }
    
    public function deleteKey($keyid, $userid) {
        if ($this->checkPriviliges($keyid, $userid)) {
            $this->db->trans_start();
            
            $this->db->where('KeysID', $keyid);
            $this->db->delete('leads');
            
            $this->db->where('KeysID', $keyid);
            $this->db->delete('keys');
            
            $this->db->trans_complete();
        }
        else
            return FALSE;
    }
    
    private function checkPriviliges($keyid, $userid) {
        $this->db->select('k.KeysID');
        $this->db->from('keys k');
        $this->db->join('projects p', 'k.ProjectsID=p.ProjectsID', 'left');
        $this->db->join('projects_users pu', 'p.ProjectsID=pu.ProjectsID', 'left');
        $this->db->where("(k.CreatedByID=$userid OR (pu.UsersID=$userid AND pu.Role='Manager'))", FALSE, FALSE);
        $this->db->where('k.KeysID', $keyid);
        $query = $this->db->get();
        if ($query->num_rows())
            return TRUE;
        else
            return FALSE;
    }
    
    function deleteProjectUser($projectuserid, $userid) {
        $this->db->select('ProjectsID, Role');
        $this->db->from('projects_users');
        $this->db->where('ProjectsUsersID', $projectuserid);
        $query = $this->db->get();
        if (!$query->num_rows()) return FALSE;
        $row = $query->row();
        $projectid = $row->ProjectsID;
        $role = $row->Role;
        
        $this->db->select('UsersID');
        $this->db->from('projects_users');
        $this->db->where('ProjectsID', $projectid);
        $this->db->where('Role', 'Manager');
        $query = $this->db->get();
        if (!$query->num_rows()) return FALSE;
        
        $managers = array();
        foreach ($query->result() as $row)
            $managers[] = $row->UsersID;
        
        if (!in_array($userid, $managers)) return FALSE;
        if ($role == 'Manager' && !(count($managers) > 1)) return FALSE;
        
        $this->db->where('ProjectsUsersID', $projectuserid);
        $this->db->delete('projects_users');
    }
    
    public function getChanges($keyid) {
        $this->db->select("CONCAT_WS(' ', u.FirstName, u.LastName) AS FullName,TimestampModified, c.`Comment`", FALSE);
        $this->db->from('changes c');
        $this->db->join('users u', 'c.ModifiedByAgentID=u.UsersID');
        $this->db->where('c.KeysID', $keyid);
        $this->db->order_by('TimestampModified', 'desc');
        $query = $this->db->get();
        if ($query->num_rows())
            return $query->result_array();
        else
            return FALSE;
    }
    
    public function getCitation($keyid) {
        $this->db->select('s.Authors, s.`Year`, s.Title, s.InAuthors, s.InTitle, s.Journal, s.Series, s.Volume, s.Part, 
            s.Publisher, s.PlaceOfPublication, s.Pages, s.Modified');
        $this->db->from('sources s');
        $this->db->join('keys k', 's.SourcesID=k.SourcesID');
        $this->db->where('k.KeysID', $keyid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            $ret = FALSE;
            if ($row->Authors && $row->Year && $row->Title) {
                if ($row->Modified)
                    $ret .= 'Modified from: ';
                else
                    $ret .= 'From: ';
                $ret .= '<b>' . $row->Authors . '</b> (' . $row->Year . '). ';
                if ($row->Journal) {
                    $ret .= $row->Title . '. <i>' . $row->Journal . '</i>';
                    if ($row->Series)
                        $ret .= ', ser. ' . $row->Series;
                    $ret .= ' <b>' . $row->Volume . '</b>';
                    if ($row->Part) 
                        $ret .= '(' . $row->Part . ')';
                    $ret .= ':' . $row->Pages . '.';
                }
                elseif ($row->InTitle) {
                    $ret .= $row->Title . '. In: ';
                    if ($row->InAuthors) 
                        $ret .= $row->InAuthors . ', ';
                    $ret .= '<i>' . $row->InTitle . '</i>';
                    if ($row->Volume) 
                        $ret .= ' <b>' . $row->Volume . '</b>';
                    if ($row->Pages)
                        $ret .= ', pp. ' . $row->Pages;
                    $ret .= '.';
                    if ($row->Publisher) {
                        $ret .= ' ' . $row->Publisher;
                        if ($row->PlaceOfPublication)
                            $ret .= ', ';
                        else
                            $ret .= '.';
                    }
                    if ($row->PlaceOfPublication)
                        $ret .= ' ' . $row->PlaceOfPublication . '.';
                }
                else {
                    $ret .= '<i>' . $row->Title . '</i>.';
                    if ($row->Publisher) {
                        $ret .= ' ' . $row->Publisher;
                        if ($row->PlaceOfPublication)
                            $ret .= ', ';
                        else
                            $ret .= '.';
                    }
                    if ($row->PlaceOfPublication)
                        $ret .= ' ' . $row->PlaceOfPublication . '.';
                    
                }
            }
            return $ret;
        }
        else
            return FALSE;
    }
    
    public function getStaticContent($uri) {
        $this->db->select('StaticID, PageTitle, PageContent');
        $this->db->from('static');
        $this->db->where('Uri', $uri);
        $query = $this->db->get();
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
        $this->db->where('StaticID', $data['id']);
        $this->db->update('static', $update);
    }
    
    public function createNewStaticPage($data) {
        $insertArray = array(
            'Uri' => $data['uri'],
            'PageTitle' => $data['title'],
            'TimestampCreated' => date('Y-m-d H:i:s'),
            'CreatedByAgentID' => 1,
        );
        $this->db->insert('static', $insertArray);
    }
    
    public function getStaticPages() {
        $this->db->select('Uri, PageTitle');
        $this->db->from('static');
        $query = $this->db->get();
        return $query->result_array();
    }
    
    public function getTaxa($projectid, $filter=FALSE) {
        $this->db->select('i.Name');
        $this->db->from('keys k');
        $this->db->join('items i', 'k.TaxonomicScopeID=i.ItemsID');
        $this->db->where('k.ProjectsID', $projectid);
        if ($filter)
            $this->db->where_in('k.KeysID', $filter);
        $this->db->order_by('Name');
        $query = $this->db->get();
        if ($query->num_rows()) {
            $ret = array();
            foreach ($query->result() as $row)
                $ret[] = $row->Name;
            return $ret;
        }
    }
    
    public function getSearchResult($searchstring) {
        $this->db->select('p.ProjectsID, p.Name AS ProjectName, p.ProjectIcon, k.KeysID, k.Name AS KeyName, k.GeographicScope');
        $this->db->from('items i');
        $this->db->join('keys k', 'i.ItemsID=k.TaxonomicScopeID');
        $this->db->join('projects p', 'k.ProjectsID=p.ProjectsID');
        $this->db->where("i.Name LIKE '$searchstring'", FALSE, FALSE);
        $query = $this->db->get();
        if ($query->num_rows()) {
            return $query->result_array();
        }
        else
            return FALSE;
    }
    
    public function insertSource($source) {
        $this->db->select('MAX(SourcesID) as maxid', FALSE);
        $this->db->from('sources');
        $query = $this->db->get();
        $row = $query->row();
        $id = $row->maxid + 1;
        $source['SourcesID'] = $id;
        $this->db->insert('sources', $source);
        return $id;
    }

}

/* End of file keymodel.php */
/* Location: ./models/keymodel.php */