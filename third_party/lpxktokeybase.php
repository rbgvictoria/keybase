<?php

require_once('../libraries/Encoding.php');

class LpxkToKeyBase {
    private $db;
    private $UserID;
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
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function LpxkToKeyBase($keyid, $filename, $format, $loadimages=FALSE, $delimiter=FALSE, $userid=FALSE) {
        set_time_limit(900);
        $this->keysid = $keyid;
        $this->filename = $filename;
        $this->loadimages = $loadimages;
        $this->delimiter = $delimiter;
        
        $this->UserID = ($userid) ? $userid : 1;
        
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
    
    private function Items() {
        $select = "SELECT max(ItemsID) as max
            FROM items";
        $maxStmt = $this->db->prepare($select);
        $maxStmt->execute();
        $row = $maxStmt->fetch(PDO::FETCH_OBJ);
        $newitemsid = $row->max + 1;
        
        $select = "SELECT count(ItemsID) as cnt
            FROM items
            WHERE Name=?";
        $countStmt = $this->db->prepare($select);

        $select = "SELECT ItemsID
            FROM items
            WHERE Name=?";
        $selectStmt = $this->db->prepare($select);

        $insert = "INSERT INTO items (ItemsID, Name)
            VALUES (?, ?)";
        $insertStmt = $this->db->prepare($insert);
        
        foreach ($this->items as $key=>$item) {
            $countStmt->execute(array($item['name']));
            $row = $countStmt->fetch(PDO::FETCH_OBJ);
            
            if ($row->cnt) { // item already in database
                $selectStmt->execute(array($item['name']));
                $row = $selectStmt->fetch(PDO::FETCH_OBJ);
                $this->items[$key]['ItemsID'] = $row->ItemsID;
            }
            else {
                $insertStmt->execute(array($newitemsid, $item['name']));
                $this->items[$key]['ItemsID'] = $newitemsid;
                $newitemsid++;
            }
        }
    }
    
    private function Leads() {
        $count = "SELECT count(KeysID) AS cnt
            FROM leads
            WHERE KeysID=?";
        $countStmt = $this->db->prepare($count);
        
        $delete = "DELETE FROM leads
            WHERE KeysID=?";
        $deleteStmt = $this->db->prepare($delete);
        
        $countStmt->execute(array($this->keysid));
        $row = $countStmt->fetch(PDO::FETCH_OBJ);
        if ($row->cnt) {
            $deleteStmt->execute(array($this->keysid));
        }
        
        $max = "SELECT MAX(LeadsID) as max
            FROM leads";
        $maxStmt = $this->db->prepare($max);
        $maxStmt->execute();
        $row = $maxStmt->fetch(PDO::FETCH_OBJ);
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
        $insertStmt = $this->db->prepare($insert);
        
        foreach ($this->newleads as $row) {
            $insertStmt->execute(array_values($row));
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
                    $endnode['NodeName'] = $this->items[$key]['name'];
                    $endnode['ItemsID'] = $this->items[$key]['ItemsID'];
                    if ($thisLead['linkto']) {
                        $endnode['LinkToItem'] = $thisLead['linkto'];
                        $lkey = array_search($thisLead['linkto'], $this->itemIDs);
                        if ($lkey !== FALSE) {
                            //$endnode['LinkToItem'] = $this->items[$lkey]['name'];
                            $endnode['LinkToItemsID'] = $this->items[$lkey]['ItemsID'];
                        }
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
        $update = "UPDATE `keys`
            SET TimestampModified=?, ModifiedByID=?
            WHERE KeysID=?";
        $updateStmt = $this->db->prepare($update);
        $updateStmt->execute(array(date('Y-m-d H:i:s'), $this->UserID, $this->keysid));
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