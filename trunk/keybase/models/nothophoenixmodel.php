<?php
require_once('playermodel.php');

class NothophoenixModel extends PlayerModel {
    
    private $Node;

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

    /**
     *
     * @return array|boolean 
     */
    function getRemainingEntities($key, $remaining) {
        $this->db->select('i.Name, m.Filename, l.ItemUrl, i.ItemsID, i.LSID, l.LinkToItemsID, lti.Name AS LinkToItem, lti.LSID AS LinkToLSID, l.KeysID');
        $this->db->from("leads l");
        $this->db->join('items i', 'l.ItemsID=i.ItemsID');
        $this->db->join('items lti', 'l.LinkToItemsID=lti.ItemsID', 'left');
        $this->db->join('media m', 'l.MediaID=m.MediaID', 'left');
        $this->db->where('!isnull(l.ItemsID)', false, false);
        $this->db->where_in('l.ItemsID', $remaining);
        if ($this->FilterItems)
            $this->db->where_in('i.ItemsID', $this->FilterItems);
        $this->db->group_by('i.Name');
        $query = $this->db->get();
        if ($query->num_rows()) {
            $ret = array();
            foreach ($query->result() as $row) {
                $name = array();
                $name['name'] = $row->Name;
                $name['NamesID'] = NULL;
                $tokey = $this->nextKey($row->ItemsID, parent::getProjectID($key));
                $name['tokey'] = ($tokey) ? $tokey : FALSE;
                $name['LinkTo'] = $row->LinkToItem;
                $linktokey = $this->nextKey($row->LinkToItemsID, parent::getProjectID($key));
                $name['LinkToKey'] = ($linktokey) ? $linktokey : FALSE;
                $name['media'] = $row->Filename;
                $name['url'] = ($row->LSID) ? 'http://bie.ala.org.au/species/' . urlencode($row->LSID) : FALSE;
                $name['linkToUrl'] = ($row->LinkToLSID) ? 'http://bie.ala.org.au/species/' . urlencode($row->LinkToLSID) : FALSE;
                $ret[] = $name;
            }
            return $ret;
        }
        else return false;
    }

    /**
     * 
     * @return array|boolean 
     */
    function getDiscardedEntities($key, $remaining) {
        $this->db->select('i.Name, m.Filename, l.ItemUrl, i.ItemsID, i.LSID, l.LinkToItemsID, lti.Name AS LinkToItem, lti.LSID AS LinkToLSID');
        $this->db->from("leads l");
        $this->db->join('items i', 'l.ItemsID=i.ItemsID');
        $this->db->join('items lti', 'l.LinkToItemsID=lti.ItemsID', 'left');
        $this->db->join('media m', 'l.MediaID=m.MediaID', 'left');
        $this->db->where('!isnull(l.ItemsID)', false, false);
        $this->db->where('l.KeysID', $key);
        $this->db->where_not_in('l.ItemsID', $remaining);
        if ($this->FilterItems)
            $this->db->where_in('i.ItemsID', $this->FilterItems);
        $this->db->group_by('i.Name');
        $query = $this->db->get();
        if ($query->num_rows()) {
            $ret = array();
            foreach ($query->result() as $row) {
                $name = array();
                $name['name'] = $row->Name;
                $name['NamesID'] = NULL;
                $tokey = $this->nextKey($row->ItemsID, parent::getProjectID($key));
                $name['tokey'] = ($tokey) ? $tokey : FALSE;
                $name['LinkTo'] = $row->LinkToItem;
                $linktokey = $this->nextKey($row->LinkToItemsID, parent::getProjectID($key));
                $name['LinkToKey'] = ($linktokey) ? $linktokey : FALSE;
                $name['media'] = $row->Filename;
                $name['url'] = ($row->LSID) ? 'http://bie.ala.org.au/species/' . urlencode($row->LSID) : FALSE;
                $name['linkToUrl'] = ($row->LinkToLSID) ? 'http://bie.ala.org.au/species/' . urlencode($row->LinkToLSID) : FALSE;
                $ret[] = $name;
            }
            return $ret;
        }
        else return false;
    }
    
    private function nextKey($item, $project) {
        $this->db->select('KeysID');
        $this->db->from('keys');
        $this->db->where('TaxonomicScopeID', $item);
        $this->db->where('ProjectsID', $project);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            return $row->KeysID;
        }
        else
            return FALSE;
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
