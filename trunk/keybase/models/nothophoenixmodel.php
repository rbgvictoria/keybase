<?php
require_once('playermodel.php');

class NothophoenixModel extends PlayerModel {
    private $Node;
    protected $hasProjectItems;

    function  __construct() {
        parent::__construct();
    }
    
    /**
     *
     * @return integer|boolean 
     */
    function getParent($currentnode) {
        if ($currentnode->ParentID)
            return $currentnode->ParentID;
        else
            return false;
    }
    
    /**
     *
     * @return integer 
     */
    public function getNode($key, $node) {
        if (!$node) {
            $this->db->select('LeadsID');
            $this->db->from('leads');
            $this->db->where('KeysID', $key);
            $this->db->where('ParentID');
            $query = $this->db->get();
            $row = $query->row();
            $node = $row->LeadsID;
        }
        
        $this->Node = $node;

        if ($this->FilterLeads)
            $this->getFilteredNode($key, $node);
        
        return $this->Node;
    }
    
    private function getFilteredNode($key, $node) {
        $this->db->select('LeadsID');
        $this->db->from('leads');
        $this->db->where('KeysID', $key);
        $this->db->where('ParentID', $node);
        $this->db->where_in('LeadsID', $this->FilterLeads);
        $this->db->where('LeadText IS NOT NULL', FALSE, FALSE);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            $row = $query->row();
            $this->getFilteredNode($key, $row->LeadsID);
        }
        else {
            $this->Node = $node;
        }
    }
    
    function getCurrentNode($node) {
        $this->db->select('LeadsID, ParentID, NodeNumber, HighestDescendantNodeNumber, LeadText');
        $this->db->from('leads');
        $this->db->where('LeadsID', $node);
        $query = $this->db->get();
        return $query->row();        
    }
    
    /**
     * @param $node integer
     * @return array 
     */
    function getNextCouplet($node) {
        $ret = array();
        $this->db->select('l.LeadsID, l.LeadText, m.Filename');
        $this->db->from('leads l');
        $this->db->join('media m', 'l.MediaID=m.MediaID', 'left');
        $this->db->where('l.ParentID', $node);
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $lead = array();
            $lead['id'] = $row->LeadsID;
            $lead['lead'] = $row->LeadText;
            $lead['media'] = $row->Filename;
            $ret[] = $lead;
        }
        return $ret;
    }

    /**
     *
     * @return array 
     */
    function getPath($key, $currentnode) {
        $pathleads = array();
        if ($this->FilterLeads) {
            $this->db->select('ParentID');
            $this->db->from('leads');
            $this->db->where('KeysID', $key);
            $this->db->where('ParentID IS NOT NULL', FALSE, FALSE);
            $this->db->where_in('LeadsID', $this->FilterLeads);
            $this->db->group_by('ParentID');
            $this->db->having('count(*)>1');
            $query = $this->db->get();
            foreach ($query->result() as $row) {
                $pathleads[] = $row->ParentID;
            }
        }
        
        $ret = array();
        $this->db->select('LeadsID, ParentID, LeadText');
        $this->db->from('leads');
        $nodenumber = $currentnode->NodeNumber;
        $this->db->where('KeysID', $key);
        $this->db->where("$nodenumber BETWEEN NodeNumber AND HighestDescendantNodeNumber", false, false);
        $this->db->where('!isnull(LeadText)', false, false);
        $this->db->order_by('NodeNumber');
        $query = $this->db->get();
        if ($query->num_rows()) {
            foreach ($query->result() as $row) {
                $lead = array();
                $lead['parentid'] = $row->ParentID;
                $lead['lead'] = $row->LeadText;
                $lead['automatic'] = ($pathleads && !in_array($row->ParentID, $pathleads)) ? 1 : 0;
                $ret[] = $lead;
            }
            return $ret;
        }
        else return false;
    }

    /**
     *
     * @return array 
     */
    function auxRemainingEntities($key, $currentnode) {
        $ret = array();
        $this->db->select('ItemsID');
        $this->db->from('leads');
        $this->db->where('KeysID', $key);
        $this->db->where('!isnull(NodeName)', false, false);
        if ($currentnode) {
            $this->db->where('NodeNumber>', $currentnode->NodeNumber, false);
            $this->db->where('NodeNumber<=', $currentnode->HighestDescendantNodeNumber, false);
        }
        $this->db->where('!isnull(ParentID)', false, false);
        $this->db->group_by('ItemsID');
        $query = $this->db->get();
        
        if ($query->num_rows()) {
            foreach ($query->result() as $row)
                $ret[] = $row->ItemsID;
            return $ret;
        }
        else
            return FALSE;
    }
    
    public function hasProjectItems($project) {
        parent::hasProjectItems($project);
    }

    /**
     *
     * @return array|boolean 
     */
    function getRemainingEntities($key, $remaining, $which='remaining') {
        $this->db->select("i.Name, m.Filename, i.ItemsID, l.KeysID");
        $this->db->from("leads l");
        $this->db->join('items i', 'l.ItemsID=i.ItemsID');
        $this->db->join('media m', 'l.MediaID=m.MediaID', 'left');
        $this->db->where('l.KeysID', $key);
        $this->db->where('!isnull(l.ItemsID)', false, false);
        if ($which == 'remaining')
            $this->db->where_in('l.ItemsID', $remaining);
        else
            $this->db->where_not_in('l.ItemsID', $remaining);
        if ($this->FilterItems)
            $this->db->where_in('i.ItemsID', $this->FilterItems);
        $this->db->group_by('i.Name');
        $query = $this->db->get();
        
        
        if ($query->num_rows()) {
            $ret = array();
            foreach ($query->result() as $row) {
                $items = $this->nextKey($row->ItemsID, parent::getProjectID($key));
                $thisitem = isset($items[0]) ? $items[0] : FALSE;
                $linkToItem = (count($items) > 1) ? $items[1] : FALSE;
                
                $name = array();
                $name['name'] = ($thisitem) ? $thisitem->ItemName : $row->ItemsID;
                $name['NamesID'] = NULL;
                $name['tokey'] = ($thisitem) ? $thisitem->KeysID : FALSE;
                $name['LinkTo'] = ($linkToItem) ? $linkToItem->ItemName : FALSE;
                $name['LinkToKey'] = ($linkToItem) ? $linkToItem->KeysID : FALSE;
                $name['media'] = $row->Filename;
                $name['url'] = ($thisitem) ? $thisitem->ItemUrl : FALSE;
                $name['linkToUrl'] = ($linkToItem) ? $linkToItem->ItemUrl : FALSE;
                $ret[] = $name;
            }
            return $ret;
        }
        else return false;
    }

    private function nextKey($item, $project) {
        /*
         * SELECT coalesce(m.Name, i.Name) AS ItemName, coalesce(mpi.Url, m.Url, pi.Url, i.Url) AS ItemUrl, k.KeysID
FROM items i
LEFT JOIN projectitems pi ON i.ItemsID=pi.ItemsID AND pi.ProjectsID=10
LEFT JOIN groupitem g ON i.ItemsID=g.GroupID
LEFT JOIN items m ON g.MemberID=m.ItemsID
LEFT JOIN projectitems mpi ON m.ItemsID=mpi.ItemsID AND mpi.ProjectsID=1
LEFT JOIN `keys` k ON COALESCE(g.MemberID, i.ItemsID)=k.TaxonomicScopeID
WHERE k.ProjectsID=10 AND i.ItemsID=293
         */
        
        $this->db->select("coalesce(m.Name, i.Name) AS ItemName, coalesce(mpi.Url, m.Url, pi.Url, i.Url) AS ItemUrl, k.KeysID", FALSE);
        $this->db->from('items i');
        $this->db->join('projectitems pi', "i.ItemsID=pi.ItemsID AND pi.ProjectsID=$project", 'left', FALSE);
        $this->db->join('groupitem g', 'i.ItemsID=g.GroupID', 'left');
        $this->db->join('items m', 'g.MemberID=m.ItemsID', 'left');
        $this->db->join('projectitems mpi', "m.ItemsID=mpi.ItemsID AND mpi.ProjectsID=$project", 'left', FALSE);
        $this->db->join('`keys` k', "COALESCE(g.MemberID, i.ItemsID)=k.TaxonomicScopeID AND k.ProjectsID=$project", 'left', FALSE);
        $this->db->where('i.ItemsID', $item);
        $query = $this->db->get();
        return $query->result();
    }

    /**
     *
     * @return array|boolean 
     */
    function getHigherKey($key) {
        $this->db->select('keys.Name, keys.NameUrl, k.ParentID');
        $this->db->from('leads k');
        $this->db->join('keys', 'k.KeysID=keys.KeysID');
        $this->db->where('k.ToKeyID', $key);
        $query = $this->db->get();
        if ($query->num_rows())
            return $query->row_array();
        else
            return false;
    }
    
    function getEndTaxon($lead) {
        $this->db->select('i.Name');
        $this->db->from('leads l');
        $this->db->join('items i', 'l.ItemsID=i.ItemsID');
        $this->db->where('l.LeadsID', $lead);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            return $row->Name;
        }
        else
            return FALSE;
    }
    
    function isResult($leadid) {
        $this->db->select('LeadsID');
        $this->db->from('leads');
        $this->db->where('ParentID', $leadid);
        $this->db->where('ItemsID IS NOT NULL', FALSE, FALSE);
        $query = $this->db->get();
        if ($query->num_rows)
            return TRUE;
        else 
            return FALSE;
    }

}


?>
