<?php

require_once('playermodel.php');

class HtmlKeyModel extends PlayerModel {
    
    private $result;
    private $parents;
    private $nodes;
    private $firstnode;
    
    public function __construct() {
        parent::__construct();
    }
    
    function getHtmlKey($keyid, $type) {
        $this->db->select('p.ParentID, p.LeadText, p.LeadsID, l.NodeName, l.ItemsID, i.Name AS ItemName, l.LinkToItemsID, lti.Name AS LinkToItemName');
        $this->db->from('leads p');
        $this->db->join('leads l', 'p.LeadsID=l.ParentID AND l.NodeName IS NOT NULL', 'left');
        $this->db->join('items i', 'l.ItemsID=i.ItemsID', 'left');
        $this->db->join('items lti', 'l.LinkToItemsID=lti.ItemsID', 'left');
        $this->db->where('p.KeysID', $keyid);
        $this->db->where('p.LeadText IS NOT NULL', FALSE, FALSE);
        
        if ($this->FilterItems)
            $this->db->where_in('p.LeadsID', $this->FilterLeads);
        
        if ($type == 'bracketed')
            $this->db->order_by('p.ParentID');
        elseif ($type == 'indented')
            $this->db->order_by('p.NodeNumber');
        $query = $this->db->get();

        if ($query->num_rows()) {
            if ($this->FilterItems) {
                $result = array();
                $fromnodes = array();
                
                foreach ($query->result() as $row) {
                    $fromnodes[] = $row->ParentID;
                }
                
                foreach ($query->result() as $row) {
                    if (count(array_keys($fromnodes, $row->ParentID)) > 1) {
                        $nrow = array();
                        $nrow['ParentID'] = $row->ParentID;
                        $nrow['LeadText'] = $row->LeadText;
                        
                        if (!$row->ItemsID){
                            $to = $this->findNextNode ($row->LeadsID, $query->result(), $fromnodes);
                            $nrow['LeadsID'] = $to['ToNode'];
                            $nrow['ItemsID'] = $to['ToItem'];
                            $nrow['NodeName'] = $to['ToNodeName'];
                            $nrow['LinkToItemsID'] = FALSE;
                            $nrow['LinkToItemName'] = FALSE;
                        }
                        else {
                            $nrow['LeadsID'] = $row->LeadsID;
                            $nrow['ItemsID'] = $row->ItemsID;
                            $nrow['NodeName'] = $row->NodeName;
                            $nrow['LinkToItemsID'] = in_array($row->LinkToItemsID, $this->FilterItems) ? $row->LinkToItemsID : FALSE;
                            $nrow['LinkToItemName'] = in_array($row->LinkToItemsID, $this->FilterItems) ? $row->LinkToItemName : FALSE;
                        }
                        $result[] = (object) $nrow;
                    }
                }
                
            }
            else 
                $result = $query->result();
            
            return $result;
        }
    }
    
    public function createBracketedKey($keyid, $projectid=FALSE) {
        $this->result = $this->getHtmlKey($keyid, 'bracketed');
        //print_r($this->result);
        $result = array();
        $nodeids = array();
        $texts = array();
        $tonodes = array();
        $tonames = array();
        $itemids = array();
        $linktoitemids = array();
        $linktoitemnames = array();

        foreach ($this->result as $row) {
            $nodeids[] = $row->ParentID;
            $texts[] = $row->LeadText;
            $tonodes[] = $row->LeadsID;
            $tonames[] = $row->NodeName;
            $itemids[] = $row->ItemsID;
            $linktoitemids[] = $row->LinkToItemsID;
            $linktoitemnames[] = $row->LinkToItemName;
        }

        $parents = array_unique($nodeids);
        sort($parents);

        foreach ($parents as $i => $parent) {
            $leads = array_keys($nodeids, $parent);
            $node = array();
            $node['StepID'] = $parent;
            foreach ($leads as $j => $v) {
                if (!$tonames[$v])
                    $tonode = array_search($tonodes[$v], $parents) + 1;
                else
                    $tonode = FALSE;
                
                $nextkey = ($itemids[$v]) ? $this->nextKey($itemids[$v], $projectid) : FALSE;
                $linktonextkey = ($linktoitemids[$v]) ? $this->nextKey($linktoitemids[$v], $projectid) : FALSE;
                
                $node['Leads'][] = array(
                    'FromNode' => $i + 1,
                    'Text' => $texts[$v],
                    'ToNode' => $tonode,
                    'ToName' => $tonames[$v],
                    'LeadID' => $tonodes[$v],
                    'NextKey' => $nextkey,
                    'LinkToName' => $linktoitemnames[$v],
                    'LinkToNextKey' => $linktonextkey,
                );
            }
            $result[] = $node;
        }          

        return $result;
    }
    
    private function findNextNode($tonode, $result, $fromnodes) {
        $ret = array(
            'ToItem' => FALSE,
            'LinkToItem' => FALSE,
            'ToNodeName' => FALSE,
            'ToNode' => FALSE,
        );
        $parents = array_keys($fromnodes, $tonode);
        if ($parents) {
            if (count($parents) > 1) {
                $ret['ToNode'] = $fromnodes[$parents[0]];
            }
            else {
                $lead = $result[$parents[0]];
                if ($lead->ItemsID) {
                    $ret['ToItem'] = $lead->ItemsID;
                    $ret['LinkToItem'] = $lead->LinkToItemsID;
                    $ret['ToNode'] = $lead->LeadsID;
                    $ret['ToNodeName'] = $lead->NodeName;
                }
                else {
                    return $this->findNextNode($lead->LeadsID, $result, $fromnodes);
                }
            }
        }
        return $ret;
    }
    
    private function nextKey($itemid, $projectid=FALSE) {
        $this->db->select('KeysID');
        $this->db->from('keys');
        $this->db->where('TaxonomicScopeID', $itemid);
        if ($projectid)
            $this->db->where('ProjectsID', $projectid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            return $row->KeysID;
        }
        else
            return FALSE;
    }
    
    public function createIndentedKey($keyid, $projectid=FALSE, $maxdepth=20) {
        $this->result = $this->getHtmlKey($keyid, 'indented');
            if (count($this->result) > 1) {
            $this->parents = array();
            foreach ($this->result as $row)
                $this->parents[] = $row->ParentID;
            $this->nodes = array_unique($this->parents);
            sort($this->nodes);

            $this->firstnode = $this->nodes[0];

            $html = '<table>' . "\n";
            $html .= $this->getIndentedKeyNode($this->firstnode, $maxdepth, 1, $projectid);
            $html .= "\n" . '</table>';
            return $html;
        }
        return
            FALSE;
    }
    
    private function getIndentedKeyNode($parentid, $maxdepth, $depth=1, $projectid=FALSE) {
        $html = '';
        $parents = array_keys($this->parents, $parentid);
        
        $level = floor($depth/$maxdepth);
        $adjdepth = ($level) ? ($depth % $maxdepth) + 2 : $depth % $maxdepth;
        $colspan = $maxdepth-$adjdepth-$level+5;
        
        foreach ($parents as $index => $i) {
            $lead = $this->result[$i];
            //print_r($lead);
            $key = array_search($lead->ParentID, $this->nodes);
            $nodeid = $key + 1;
            $nodeid .= ($index == 1) ? ':' : '';
            
            $html .= '<tr class="level_' . $level . '" id="s' . $lead->ParentID . 'l' . $lead->LeadsID . '">';
            $j = 1;
            while ($j < $adjdepth+$level) {
                $html .= '<td>&nbsp;</td>';
                $j++;
            }
            
            $html .= '<td class="from">' . $nodeid . '</td>';
            $html .= '<td class="text" colspan="' . $colspan . '">' . $lead->LeadText;
            
            if ($lead->NodeName) {
                $html .= ' <span class="to">';
                $html .= $lead->NodeName;
                if ($lead->ItemsID && $this->nextKey($lead->ItemsID)) {
                    $html .= '&nbsp;<a href="' . site_url() . 'key/indentedkey/' . $this->nextKey($lead->ItemsID, $projectid) . '">&#x25BA;</a>';
                }
                if ($lead->LinkToItemName) {
                    $html .= ' (' . $lead->LinkToItemName;
                    if ($lead->LinkToItemsID && $this->nextKey($lead->LinkToItemsID)) {
                        $html .= '&nbsp;<a href="' . site_url() . 'key/indentedkey/' . $this->nextKey($lead->LinkToItemsID, $projectid) . '">&#x25BA;</a>';
                    }
                    $html .= ')';
                }
                
                $html .= '</span></td>';
                $html .= "</tr>\n\n";
            }
            else {
                $html .= "</td></tr>\n\n";
                $html .= $this->getIndentedKeyNode($lead->LeadsID, $maxdepth, $depth+1, $projectid);
            }
       }
        return $html;
    }
    
    private function getIndentedKeyNodeOld($parentid, $depth=1) {
        $html = '';
        $html .= "<table>";
        
        $parents = array_keys($this->parents, $parentid);
        foreach ($parents as $index => $i) {
            $lead = $this->result[$i];
            $key = array_search($lead['ParentID'], $this->nodes);
            $nodeid = $key + 1;
            $nodeid .= ($index == 1) ? ':' : '';
            
            $html .= "<tr>";
            $rowspan = ($lead['NodeName']) ? '1' : '2';
            $html .= '<td class="from" rowspan="' . $rowspan . '">' . $nodeid . '</td>';
            $html .= '<td class="text">' . $lead['LeadText'];
            
            if ($lead['NodeName']) {
                $html .= ' <span class="to">' . $lead['NodeName'] . '</span></td>';
                $html .= "</tr>";
            }
            else {
                $html .= '</td>';
                $html .= '</tr>';
                $html .= '<tr>';
                $html .= '<td>';
                $html .= $this->getIndentedKeyNode($lead['LeadsID'], $depth+1);
                $html .= '</td>';
                $html .= '</tr>';
            }
        }
        $html .= "</table>";
        
        return $html;
    }
    
    private function getKeyID($keyname) {
        $this->db->select('KeysID');
        $this->db->from('keys');
        $this->db->where('NameUrl', $keyname);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            return $row->KeysID;
        }
        else
            return FALSE;
    }
}

?>
