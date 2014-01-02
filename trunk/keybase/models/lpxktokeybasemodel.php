<?php

require_once('libraries/Encoding.php');

class LpxkToKeyBaseModel extends CI_Model {
    public $filename;
    public $loadimages;
    
    public $lpxk;
    
    public $title;
    public $firstStepID;
    public $media;
    public $icons;
    public $items;
    public $leads;
    public $itemIDs;
    public $stepIDs;
    
    public $keysid;
    public $newleads;
    
    public $nextleadid;
    public $nodenumber;
    
    public $leadids;
    public $parentids;
    
    public function __construct() {
        parent::__construct();
    }
    
    public function LpxkToKeyBase($keyid, $filename, $format, $loadimages=FALSE, $delimiter=FALSE) {
        set_time_limit(900);
        $this->keysid = $keyid;
        $this->filename = $filename;
        $this->loadimages = $loadimages;
        $this->delimiter = $delimiter;
        
        $this->UserID = 1;
        
        $this->media = array();
        $this->icons = array();
        $this->items = array();
        $this->itemIDs = array();
        $this->leads = array();
        $this->stepIDs = array();
        
        $this->leadids = array();
        $this->parentids = array();
        
        if ($format == 'lpxk') {
            $this->lpxk = new DOMDocument('1.0', 'UTF-8');
            $this->lpxk->load($this->filename);
            $this->parseLpxk();
        }
        elseif ($format == 'delimitedtext') {
            $this->parseDelimitedText();
        }
        
        $this->Media();
        $this->Items();
        $this->Leads();
        $this->updateKeys();
        
        
    }
    
    private function parseLpxk() {
        // Get the title
        if (!$this->title)
            $this->title = $this->lpxk->getElementsByTagName('PhoenixKey')->item(0)->getAttribute('title');

        // Get end taxa
        $list = $this->lpxk->getElementsByTagName('Identity');
        if ($list->length) {
            foreach ($list as $item) {
                $this->items[] = array(
                    'id' => $item->getAttribute('id'),
                    'name' => $item->getAttribute('name'),
                    'icon' => $item->getAttribute('icon'),
                    'url' => $item->getAttribute('url')
                );
                $this->itemIDs[] = $item->getAttribute('id');
                if ($item->getAttribute('icon'))
                    $this->media[] = array('icon' => $item->getAttribute('icon'));
            }
        }

        // Get the ID of the first step in the key
        $list = $this->lpxk->getElementsByTagName('Steps');
        if ($list->length) {
            $this->firstStepID = $list->item(0)->getAttribute('firstStepID');
        }

        // Get all the leads
        $list = $this->lpxk->getElementsByTagName('Lead');
        if ($list->length) {
            foreach ($list as $lead) {
                $text = $lead->getElementsByTagName('Text');
                $text = ($text->length) ? $text->item(0)->nodeValue : NULL;
                $this->leads[] = array(
                    'stepid' => $lead->getAttribute('stepid'),
                    'leadid' => $lead->getAttribute('leadid'),
                    'goto' => $lead->getAttribute('goto'),
                    'icon' => $lead->getAttribute('icon'),
                    'leadtext' => $text
                );
                $this->stepIDs[] = $lead->getAttribute('stepid');
                if ($lead->getAttribute('icon'))
                    $this->media[] = array('icon' => $lead->getAttribute('icon'));
            }
        }
    }

    private function parseDelimitedText() {
        $array = array();
        $handle = fopen($this->filename, 'r');
        while (!feof($handle)) {
            if ($this->delimiter == 'tab') {
                $line = fgetcsv($handle, 0, "\t");
            }
            elseif ($this->delimiter == 'comma') {
                $line = fgetcsv($handle);
            }
            $array[] = array(
                'fromNode' => trim(str_replace(array(':', '.'), '', $line[0])),
                'leadText' => Encoding::toUTF8(trim($line[1])),
                'toNode' => Encoding::toUTF8(trim($line[2])),
            );
        }
        
        // Get the items
        foreach ($array as $row) {
            if (is_numeric($row['toNode']) || !strpos($row['toNode'], '{')) {
                $goto = $row['toNode'];
                $linkto = NULL;
            }
            else {
                $goto = trim(substr($row['toNode'], 0, strpos($row['toNode'], '{')));
                $linkto = trim(substr($row['toNode'], strpos($row['toNode'], '{')+1, strpos($row['toNode'], '}')-strpos($row['toNode'], '{')-1));
            }
            if ($row && !is_numeric($row['toNode'])) {
                $this->items[] = array(
                    'id' => $goto,
                    'name' => $goto,
                    'icon' => NULL,
                    'url' => NULL
                );
                $this->itemIDs[] = $goto;
                
                if ($linkto) {
                    $this->items[] = array(
                        'id' => $linkto,
                        'name' => $linkto,
                        'icon' => NULL,
                        'url' => NULL
                    );
                    $this->itemIDs[] = $linkto;
                }
            }
        }
        
        
        // Id of the first step
        $this->firstStepID = $array[0]['fromNode'];
        
        // Get all the leads
        foreach ($array as $index => $row) {
            if (is_numeric($row['toNode']) || !strpos($row['toNode'], '{')) {
                $goto = $row['toNode'];
                $linkto = NULL;
            }
            else {
                $goto = trim(substr($row['toNode'], 0, strpos($row['toNode'], '{')));
                $linkto = trim(substr($row['toNode'], strpos($row['toNode'], '{')+1, strpos($row['toNode'], '}')-strpos($row['toNode'], '{')-1));
            }
            $this->stepIDs[] = $row['fromNode'];
            $this->leads[] = array(
                'stepid' => $row['fromNode'],
                'leadid' => $index + 1,
                'goto' => $goto,
                'linkto' => $linkto,
                'icon' => NULL,
                'leadtext' => $row['leadText'],
            );
        }
    }
    
    private function Key() {
        $select = "SELECT MAX(KeysID) AS max FROM `keys`";
        $query = $this->db->query($select);
        $row = $query->row();
        $newkeysid = $row->max + 1;
        
        $select = "SELECT KeysID
            FROM `keys`
            WHERE Name=?";
        
        $insert = "INSERT INTO `keys` (KeysID, Name, NameUrl, Url, TaxonomicScope, GeographicScope)
            VALUES (?, ?, ?, ?, ?, ?)";
        
        $geographicscope = (isset($this->keymetadata['GeographicScope'])) ?
            $this->keymetadata['GeographicScope'] : NULL;
        $taxonomicscope = (isset($this->keymetadata['TaxonomicScope'])) ?
            $this->keymetadata['TaxonomicScope'] : NULL;

        
        $query = $this->db->query($select, array($this->title));
        if ($query->num_rows()) {
            $row = $query->row();
            $this->keysid = $row->KeysID;
            if ($geographicscope || $taxonomicscope) {
                $this->db->query("UPDATE `keys`
                    SET TaxonomicScope='$taxonomicscope', GeographicScope='$geographicscope'
                    WHERE KeysID=$row->KeysID");
            }
       }
        else {
            $nameurl = str_replace(' ', '_', strtolower($this->title));
            $url = (substr($this->filename, 0, 7) == 'http://') ? $this->filename : NULL;
            $this->db->query($insert, array($newkeysid, $this->title, $nameurl, $url,
                $taxonomicscope, $geographicscope));
            $this->keysid = $newkeysid;
        }
    }
    
    private function Media() {
        $max = "SELECT max(MediaID) as max
            FROM media";
        $query = $this->db->query($max);
        $row = $query->row();
        $nextmediaid = $row->max;
        
        $select = "SELECT MediaID 
            FROM media 
            WHERE KeysID=? AND OriginalFilename=?";
        
        $insert = "INSERT INTO media (MediaID, KeysID, OriginalFilename, Filename)
            VALUES (?, ?, ?, ?)";
        
        foreach ($this->media as $key=>$img) {
            $this->icons[] = $img['icon'];
            $query = $this->db->query($select, array($this->keysid, $img['icon']));
            if ($query->num_rows()) {
                $row = $query->row();
                $this->media[$key]['id'] = $row->MediaID;
            }
            else {
                $nextmediaid++;
                $insertArray = array();
                $insertArray[] = $nextmediaid;
                $insertArray[] = $this->keysid;
                $insertArray[] = $img['icon'];
                if ($this->loadimages)
                    $insertArray[] = $this->loadImage($img['icon']);
                else
                    $insertArray[] = NULL;
                
                $this->db->query($insert, $insertArray);
                
                $this->media[$key]['id'] = $nextmediaid;
            }
        }
    }
    
    private function Items() {
        $select = "SELECT max(ItemsID) as max
            FROM items";
        $query = $this->db->query($select);
        $row = $query->row();
        $newitemsid = $row->max + 1;
        
        $select = "SELECT ItemsID
            FROM items
            WHERE Name=?";

        $insert = "INSERT INTO items (ItemsID, Name, LSID)
            VALUES (?, ?, ?)";
        
        $apcLSID = "SELECT TaxonLSID
            FROM apc_taxa
            WHERE ScientificName=?";
        
        foreach ($this->items as $key=>$item) {
            $query = $this->db->query($select, array($item['name']));
            if ($query->num_rows()) { // item already in database
                $row = $query->row();
                $this->items[$key]['ItemsID'] = $row->ItemsID;
            }
            else {
                $query = $this->db->query($apcLSID, array($item['name']));
                if ($query->num_rows()) {
                    $row = $query->row();
                    $lsid = $row->TaxonLSID;
                }
                else
                    $lsid = NULL;
                
                $this->db->query($insert, array($newitemsid, $item['name'], $lsid));
                $this->items[$key]['ItemsID'] = $newitemsid;
                $newitemsid++;
            }
        }
    }
    
    private function Leads() {
        $select = "SELECT KeysID
            FROM leads
            WHERE KeysID=?";
        
        $delete = "DELETE FROM leads
            WHERE KeysID=?";
        
        $query = $this->db->query($select, array($this->keysid));
        if ($query->num_rows()) {
            $this->db->query($delete, array($this->keysid));
        }
        
        $max = "SELECT MAX(LeadsID) as max
            FROM leads";
        $query = $this->db->query($max);
        $row = $query->row();
        $this->nextleadid = $row->max + 1;
        $this->nodenumber = 1;

        $this->newleads = array();
        $newlead = (array) new Lead();
        $newlead['KeysID'] = $this->keysid;
        $newlead['LeadsID'] = $this->nextleadid;
        $newlead['NodeName'] = $this->title;
        $newlead['NodeNumber'] = $this->nodenumber;
        $newlead['TimestampModified'] = date('Y-m-d H:i:s');
        $newlead['ModifiedByAgentID'] = $this->UserID;
        
        $this->newleads[] = $newlead;
        $this->leadids[] = $this->nextleadid;
        $this->parentids[] = NULL;
        
        $this->nextleadid++;
        $this->nodenumber++;
        
        $this->nextLead($newlead['LeadsID'], $this->firstStepID);
        
        $this->getHighestDescendantNodeNumbers();
        
        // insert into database
        $fields = array_keys($this->newleads[0]);
        $values = array();
        foreach ($fields as $field)
            $values[] = '?';
        
        $fields = implode(', ', $fields);
        $values = implode(', ', $values);
        
        $insert = "INSERT INTO leads ($fields)
            VALUES ($values)";
        
        foreach ($this->newleads as $row) {
            $this->db->query($insert, array_values($row));
        } 
        
    }
    
    function nextLead($parentid, $goto) {
        $nextLeadIDs = array_keys($this->stepIDs, $goto);
        foreach ($nextLeadIDs as $key) {
            $thisLead = $this->leads[$key];
            $newlead = (array) new Lead();
            $newlead['KeysID'] = $this->keysid;
            $newlead['LeadsID'] = $this->nextleadid;
            $newlead['LeadText'] = $thisLead['leadtext'];
            $newlead['NodeNumber'] = $this->nodenumber;
            $newlead['ParentID'] = $parentid;
            $newlead['TimestampModified'] = date('Y-m-d H:i:s');
            $newlead['ModifiedByAgentID'] = $this->UserID;
            
            if ($thisLead['icon']) {
                $key = array_search($thisLead['icon'], $this->icons);
                $newlead['MediaID'] = $this->media[$key]['id'];
            }
            
            $this->newleads[] = $newlead;
            $this->leadids[] = $this->nextleadid;
            $this->parentids[] = $parentid;
            
            $this->nextleadid++;
            $this->nodenumber++;
            
            if (in_array($thisLead['goto'], $this->stepIDs)) {
                $this->nextLead($newlead['LeadsID'], $thisLead['goto']);
            }
            elseif (in_array($thisLead['goto'], $this->itemIDs)) {
                $endnode = (array) new Lead();
                $endnode['KeysID'] = $this->keysid;
                $endnode['LeadsID'] = $this->nextleadid;
                $endnode['TimestampModified'] = date('Y-m-d H:i:s');
                $endnode['ModifiedByAgentID'] = $this->UserID;
                $key = array_search($thisLead['goto'], $this->itemIDs);
                if ($key !== FALSE) {
                    if ($thisLead['linkto']) {
                        $endnode['NodeName'] = $thisLead['linkto'];
                        $lkey = array_search($thisLead['linkto'], $this->itemIDs);
                        if ($lkey !== FALSE) {
                            $endnode['ItemsID'] = $this->items[$lkey]['ItemsID'];
                        }
                        $endnode['LinkToItem'] = $this->items[$key]['name'];
                        $endnode['LinkToItemsIDItemsID'] = $this->items[$key]['ItemsID'];
                    }
                    else {
                        $endnode['NodeName'] = $this->items[$key]['name'];
                        $endnode['ItemsID'] = $this->items[$key]['ItemsID'];
                    }
                    if ($this->items[$key]['icon']) {
                        $ikey = array_search($this->items[$key]['icon'], $this->icons);
                        $endnode['MediaID'] = $this->media[$ikey]['id'];
                    }
                    if ($this->items[$key]['url']) {
                        if (substr($this->filename, 0, 7) == 'http://')
                            $endnode['ItemUrl'] = pathinfo ($this->filename, PATHINFO_DIRNAME) . 
                                    substr ($this->items[$key]['url'], strpos ($this->items[$key]['url'], '/'));
                        else
                            $endnode['ItemUrl'] = $this->items[$key]['url'];
                    }
                }
                $endnode['NodeNumber'] = $this->nodenumber;
                $endnode['ParentID'] = $newlead['LeadsID'];
                $this->newleads[] = $endnode;
                $this->leadids[] = $this->nextleadid;
                $this->parentids[] = $newlead['LeadsID'];
                
                $this->nextleadid++;
                $this->nodenumber++;
            }
        }
    }
    
    private function getHighestDescendantNodeNumbers() {
        foreach ($this->newleads as $key=>$lead) {
            $this->getHighestDescendantNodeNumber($key, $lead['LeadsID']);
        }
    }
    
    private function getHighestDescendantNodeNumber($key, $leadid) {
        $parentids = array_keys($this->parentids, $leadid);
        if ($parentids) {
            foreach ($parentids as $parentid) {
                $lead = $this->newleads[$parentid];
                $this->getHighestDescendantNodeNumber($key, $lead['LeadsID']);
            }
        }
        else {
            $skey = array_search($leadid, $this->leadids);
            if ($skey !== FALSE) {
                $lead = $this->newleads[$skey];
                $this->newleads[$key]['HighestDescendantNodeNumber'] = $lead['NodeNumber']; 
            }
        }
    }
    
    private function loadImage($icon) {
        $url = pathInfo($this->filename, PATHINFO_DIRNAME) . substr($icon, strpos($icon, '/'));
        $extension = pathinfo($icon, PATHINFO_EXTENSION);
        
        $newname = 'keybase.' . uniqid() . '.' . $extension;
        
        switch ($extension) {
            case 'jpg':
            case 'JPG':
                $img = imagecreatefromjpeg($url);
                imagejpeg($img, 'images/' . $newname);
                break;

            case 'gif':
            case 'GIF':
                $img = imagecreatefromgif($url);
                imagegif($img, 'images/' . $newname);
                break;

            case 'png':
            case 'PNG':
                $img = imagecreatefrompng($url);
                imagepng($img, 'images/' . $newname);
                break;

            default:
                break;
        }
        
        return $newname;
    }
    
    private function updateKeys () {
        $data = array(
            'TimestampModified' => date('Y-m-d H:i:s'),
            'ModifiedByID' => $this->session->userdata['id'],
        );
        $this->db->where('KeysID', $this->keysid);
        $this->db->update('keys', $data);
    }
}

class Lead {
    var $LeadsID = NULL;
    var $KeysID = NULL;
    var $NodeName = NULL;
    var $LeadText = NULL;
    var $ParentID = NULL;
    var $NodeNumber = NULL;
    var $HighestDescendantNodeNumber = NULL;
    var $ItemsID = NULL;
    var $LinkToItem = NULL;
    var $LinkToItemsID = NULL;
    var $MediaID = NULL;
    var $ItemUrl = NULL;
    var $TimestampCreated = NULL;
    var $TimestampModified = NULL;
    var $ModifiedByAgentID = NULL;
}

?>