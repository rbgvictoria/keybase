<?php

class KeyHierarchyModel extends CI_Model {
    private $projectid;
    private $hierarchy;
    private $nodenumber;
    private $parentids;
    private $keyids;
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getHierarchy($projectid) {
        $this->projectid = $projectid;
        
        // initialise
        $start = $this->getFirstKey();
        
        if ($start) {
            $this->nodenumber = 1;
            $this->parentids[] = FALSE;
            $this->keyids[] = 999999;
            $this->hierarchy[] = array('ParentKeyID' => NULL,
                'ProjectsID' => $this->projectid,
                'KeysID' => 999999,
                'NodeNumber' => $this->nodenumber,
                'HighestDescendantNodeNumber' => NULL,
                'Depth' => 0);
            
            foreach ($start as $keyid) {
                $this->nodenumber++;
                $this->parentids[] = 999999;
                $this->keyids[] = $keyid;
                $this->hierarchy[] = array('ParentKeyID' => 999999,
                    'ProjectsID' => $this->projectid,
                    'KeysID' => $keyid, 
                    'NodeNumber' => $this->nodenumber,
                    'HighestDescendantNodeNumber' => NULL,
                    'Depth' => 1);
                $this->getNextKey($keyid, 1);
            }
            $this->getHighestDescendantNodeNumbers();
        }
        
        if ($this->hierarchy) {
            // Delete existing hierarchy
            $this->db->where('ProjectsID', $this->projectid);
            $this->db->delete('keyhierarchy');

            foreach ($this->hierarchy as $element) {
                array_shift($element);
                if ($element['KeysID'] == 999999)
                    $element['KeysID'] = NULL;
                $this->db->insert('keyhierarchy', $element);
            }
            
            return 'Updating of hierarchy successful';
        }
        else {
            return 'Could not initialise key hierarchy';
        }
        
    }
    
    private function getFirstKey() {
        $ret = array();
        
        /*
         * SELECT coalesce(tk.TaxonomicScopeID, mtk.TaxonomicScopeID, mltk.TaxonomicScopeID) AS ID
FROM (`leads` l)
LEFT JOIN `keys` tk ON l.ItemsID=tk.TaxonomicScopeID AND tk.ProjectsID=6
LEFT JOIN groupitem g ON l.ItemsID=g.GroupID
LEFT JOIN items m ON g.MemberID=m.ItemsID AND g.OrderNumber=0
LEFT JOIN `keys` mtk ON m.ItemsID=mtk.TaxonomicScopeID AND mtk.ProjectsID=6
LEFT JOIN items ml ON g.MemberID=ml.ItemsID AND g.OrderNumber=1
LEFT JOIN `keys` mltk ON ml.ItemsID=`mltk`.`TaxonomicScopeID` AND mltk.ProjectsID=6
JOIN `keys` k ON `l`.`KeysID`=`k`.`KeysID`
WHERE `k`.`ProjectsID` = '6' AND coalesce(tk.KeysID, mtk.KeysID, mltk.KeysID) IS NOT NULL
         */
        
        $this->db->select('coalesce(tk.TaxonomicScopeID, mtk.TaxonomicScopeID, mltk.TaxonomicScopeID) AS ID', FALSE);
        $this->db->from('leads l');
        $this->db->join('`keys` tk', "l.ItemsID=tk.TaxonomicScopeID AND tk.ProjectsID=$this->projectid", 'left', FALSE);
        $this->db->join('groupitem g', 'l.ItemsID=g.GroupID', 'left');
        $this->db->join('items m', 'g.MemberID=m.ItemsID AND g.OrderNumber=0', 'left', FALSE);
        $this->db->join('`keys` mtk', "m.ItemsID=mtk.TaxonomicScopeID AND mtk.ProjectsID=$this->projectid", 'left', FALSE);
        $this->db->join('items ml', "g.MemberID=ml.ItemsID AND g.OrderNumber=1", 'left', FALSE);
        $this->db->join('`keys` mltk', "ml.ItemsID=mltk.TaxonomicScopeID AND mltk.ProjectsID=$this->projectid", 'left', FALSE);
         $this->db->join('keys k', 'l.KeysID=k.KeysID');
        $this->db->where('k.ProjectsID', $this->projectid);
        $this->db->where('coalesce(tk.KeysID, mtk.KeysID, mltk.KeysID) IS NOT NULL', FALSE, FALSE);
        $subquery = $this->db->get();
        $linkeditems = array();
        if ($subquery->num_rows()) {
            foreach ($subquery->result() as $row) {
                $linkeditems[] = $row->ID;
            }
        }
        $linkeditems = array_unique($linkeditems);
        
        $this->db->select('k.KeysID, IF(k.TaxonomicScopeID=p.TaxonomicScopeID, 1, 0) AS keyorder', FALSE);
        $this->db->from('keys k');
        $this->db->join('projects p', 'k.ProjectsID=p.ProjectsID');
        $this->db->where('k.ProjectsID', $this->projectid);
        $this->db->where_not_in('k.TaxonomicScopeID', $linkeditems);
        $this->db->order_by('keyorder DESC, k.Name');
        
        $query = $this->db->get();
        if ($query->num_rows()) {
            foreach ($query->result() as $row)
                $ret[] = $row->KeysID;
        }
        return $ret;
    }
    /*private function getFirstKey() {
        $ret = array();
        $this->db->select('TaxonomicScopeID');
        $this->db->from('projects');
        $this->db->where('ProjectsID', $this->projectid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            $this->db->select('KeysID');
            $this->db->from('keys');
            $this->db->where('ProjectsID', $this->projectid);
            $this->db->where('TaxonomicScopeID', $row->TaxonomicScopeID);
            $q = $this->db->get();
            
            if ($q->num_rows()) {
                foreach ($q->result() as $row)
                    $ret[] = $row->KeysID;
            }
        }
        
        $orphans = $this->getOrphanKeys();
        if ($orphans);
        
        $ret = array_merge($ret, $orphans);
        
        return $ret;
    }*/
    
    private function getOrphanKeys() {
        $orphans = array();
        $orphansts = array();
        $this->db->select('k.KeysID, k.TaxonomicScopeID');
        $this->db->from('keys k');
        $this->db->join('keyhierarchy h', 'k.KeysID=h.KeysID AND k.ProjectsID=h.ProjectsID', 'left');
        $this->db->where('k.ProjectsID', $this->projectid);
        $this->db->where('h.KeyHierarchyId');
        $query = $this->db->get();
        if ($query->num_rows()) {
            foreach($query->result() as $row) {
                $orphans[$row->KeysID] = $row->TaxonomicScopeID;
                $orphansts[] = $row->TaxonomicScopeID;
            }
            
            $this->db->select('l.ItemsID');
            $this->db->from('leads l');
            $this->db->where_in('l.KeysID', array_keys($orphans));
            $this->db->where('l.ItemsID IS NOT NULL', FALSE, FALSE);
            $this->db->group_by('l.ItemsID');
            $query = $this->db->get();
            $orphanitems = array();
            foreach($query->result() as $row)
                $orphanitems[] = $row->ItemsID;
            
            $orphan_diff = array_diff(array_values($orphans), $orphanitems);
            
            $orphan_start = array();
            $this->db->select('KeysID');
            $this->db->from('keys');
            $this->db->where_in('TaxonomicScopeID', $orphan_diff);
            $this->db->where('ProjectsID', $this->projectid);
            $this->db->order_by('Name');
            $query = $this->db->get();
            foreach ($query->result() as $row)
                $orphan_start[] = $row->KeysID;
            
            return $orphan_start;
        }
    }
    
    private function getNextKey($keyid, $depth) {
        
        /*
         * SELECT IF(coalesce(mk.KeysID, k.KeysID) IS NOT NULL, coalesce(mk.KeysID, k.KeysID), lk.KeysID) AS KeyID,
  IF(coalesce(mk.Name, k.Name) IS NOT NULL, coalesce(mk.Name, k.Name), lk.Name) AS KeyName, l.KeysID AS ParentKeyID
FROM (`leads` l)
LEFT JOIN groupitem g ON l.ItemsID=g.GroupID AND g.OrderNumber=0
LEFT JOIN groupitem gl ON l.ItemsID=gl.GroupID AND gl.OrderNumber=1
LEFT JOIN `keys` k ON `l`.`ItemsID`=`k`.`TaxonomicScopeID` AND k.ProjectsID=10
LEFT JOIN `keys` mk ON g.MemberID=k.TaxonomicScopeID AND k.ProjectsID=10
LEFT JOIN `keys` lk ON gl.MemberID=`lk`.`TaxonomicScopeID` AND lk.ProjectsID=10
WHERE `l`.`KeysID` =  '1907'
AND (k.KeysID IS NOT NULL OR mk.KeysID IS NOT NULL OR lk.KeysID IS NOT NULL)
GROUP BY KeyID
ORDER BY KeyName;


         */
        $this->db->select('IF(coalesce(mk.KeysID, k.KeysID) IS NOT NULL, coalesce(mk.KeysID, k.KeysID), lk.KeysID) AS KeyID,
            IF(coalesce(mk.Name, k.Name) IS NOT NULL, coalesce(mk.Name, k.Name), lk.Name) AS KeyName, 
            l.KeysID AS ParentKeyID', FALSE);
        $this->db->from('leads l');
        $this->db->join('groupitem g', 'l.ItemsID=g.GroupID AND g.OrderNumber=0', 'left', FALSE);
        $this->db->join('groupitem gl', 'l.ItemsID=gl.GroupID AND gl.OrderNumber=1', 'left', FALSE);
        $this->db->join('`keys` k', "l.ItemsID=k.TaxonomicScopeID AND k.ProjectsID=$this->projectid", 'left', FALSE);
        $this->db->join('`keys` mk', "g.MemberID=k.TaxonomicScopeID AND k.ProjectsID=$this->projectid", 'left', FALSE);
        $this->db->join('`keys` lk', "gl.MemberID=lk.TaxonomicScopeID AND lk.ProjectsID=$this->projectid", 'left', FALSE);
        $this->db->where('l.KeysID', $keyid);
        $this->db->where('(k.KeysID IS NOT NULL OR mk.KeysID IS NOT NULL OR lk.KeysID IS NOT NULL)', FALSE, FALSE);
        $this->db->group_by('KeyID');
        $this->db->order_by('KeyName');
        $query = $this->db->get();
        
        if ($query->num_rows()) {
            $depth++;
            foreach($query->result() as $row) {
                $this->nodenumber++;
                $this->parentids[] = $row->ParentKeyID;
                $this->keyids[] = $row->KeyID;
                $this->hierarchy[] = array('ParentKeyID' => $row->ParentKeyID,
                    'ProjectsID' => $this->projectid,
                    'KeysID' => $row->KeyID, 
                    'NodeNumber' => $this->nodenumber,
                    'HighestDescendantNodeNumber' => NULL,
                    'Depth' => $depth);
                $this->getNextKey($row->KeyID, $depth);
            }
        }
        
    }
    
    private function getHighestDescendantNodeNumbers() {
        foreach ($this->hierarchy as $key=>$lead) {
            $this->getHighestDescendantNodeNumber($key, $lead['KeysID']);
        }
    }
    
    private function getHighestDescendantNodeNumber($key, $leadid) {
        set_time_limit(600);
        $parentids = array_keys($this->parentids, $leadid);
        if ($parentids) {
            foreach ($parentids as $parentid) {
                $lead = $this->hierarchy[$parentid];
                $this->getHighestDescendantNodeNumber($key, $lead['KeysID']);
            }
        }
        else {
            $skey = array_search($leadid, $this->keyids);
            if ($skey !== FALSE) {
                $lead = $this->hierarchy[$skey];
                $this->hierarchy[$key]['HighestDescendantNodeNumber'] = $lead['NodeNumber'];
            }
        }
    }   
}

/* End of file keyhierarchymodel.php */
/* Location: ./models/keyhierarchymodel.php */
