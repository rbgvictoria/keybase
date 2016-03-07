<?php

class Exportmodel extends CI_Model {
    
    private $fromnodes;
    private $leads;
    private $tonodes;
    private $items;
    private $uitems;
    
    private $steps;
    private $reorderedKey;

    /**
     *  
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     *
     * @param string $keyid
     * @return array 
     */
    public function export($keyid) {
        $this->reorderedKey['Title'] = $this->getKeyName($keyid);
        $result = $this->getKey($keyid);
        $this->reorderKey($result);
        return $this->reorderedKey;
    }
    
    /**
     *
     * @param string $keyid
     * @return string|boolean 
     */
    private function getKeyName($keyid) {
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
    
    /**
     *
     * @param string $keyid
     * @return array|boolean 
     */
    private function getKey($keyid) {
        $this->db->select('l.ParentID, l.LeadText, l.LeadsID, c.NodeName,
            m.FileName as LeadIcon, im.FileName as ItemIcon, c.ItemUrl');
        $this->db->from('leads l');
        $this->db->join('leads c', 'l.LeadsID=c.ParentID AND c.NodeName IS NOT NULL', 'left');
        $this->db->join('media m', 'l.MediaID=m.MediaID', 'left');
        $this->db->join('media im', 'c.MediaID=im.MediaID', 'left');
        $this->db->where('l.KeysID', $keyid);
        $this->db->where('l.LeadText IS NOT NULL', FALSE, FALSE);
        $this->db->order_by('l.NodeNumber');
        $query = $this->db->get();
        if ($query->num_rows())
            return $query->result();
        else
            return FALSE;
    }
    
    /**
     *
     * @param array $result 
     */
    private function reorderKey($result) {
        $this->fromnodes = array();
        $this->leads = array();
        $this->tonodes = array();
        $this->items = array();
        
        foreach ($result as $row) {
            $this->fromnodes[] = $row->ParentID;
            $this->tonodes[] = $row->LeadsID;
            $this->items[] = $row->NodeName;
        }
        
        $sddLeads = sort($this->tonodes);
        
        $items = array_unique($this->items);
        foreach (array_keys($items, FALSE) as $e) unset ($items[$e]);
        sort($items);
        
        foreach ($items as $index=>$item) {
            if ($item) {
                $key = array_search($item, $this->items);
                $identity = array(
                    'id' => 'i' . $index,
                    'name' => $result[$key]->NodeName,
                    'icon' => ($result[$key]->ItemIcon) ? base_url() . 'images/' . 
                        $result[$key]->ItemIcon : '',
                    'url' => $result[$key]->ItemUrl,
                );
                $this->reorderedKey['Items'][] = $identity;
            }
        }     
        
        
        $this->steps = array();
        foreach (array_unique($this->fromnodes) as $step) {
            $this->steps[] = $step;
        }
                
        foreach ($this->steps as $i=>$step) {
            $leads = array_keys($this->fromnodes, $step);
            $steps = array();
            $steps['id'] = 's' . $i;
            $steps['text'] = '';
            
            $sddparent = array_search($step, $this->tonodes) + 1;
            
            foreach ($leads as $j=>$lead) {
                $k= array_search($this->tonodes[$lead], $this->steps);
                
                //if ($this->items[$lead])
                if ($result[$lead]->NodeName)
                    //$goto = 'i' . array_search($this->items[$lead], $this->uitems);
                    $goto = 'i' . array_search($result[$lead]->NodeName, $items);
                else 
                    $goto = 's' . $k;
                
                $tonode = $k + 1;
                if ($result[$lead]->NodeName) {
                    $tonode = $result[$lead]->NodeName;
                }
                
                $steps['leads'][] = array(
                    'fromnode' => $i + 1,
                    'stepid' => 's' . $i,
                    'leadid' => 's' . $i . 'l' . $j,
                    'leadtext' => trim($result[$lead]->LeadText),
                    'tonode' => $tonode,
                    'goto' => $goto,
                    'icon' => ($result[$lead]->LeadIcon) ? base_url() . 'images/' . 
                        $result[$lead]->LeadIcon : '',
                    'sddlead' => $lead + 1,
                    'sddparent' => $sddparent,
                );
            }
            $this->reorderedKey['Steps'][] = $steps;
        }
    }
    
}

?>
