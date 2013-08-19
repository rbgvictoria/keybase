<?php

require_once('pdoconnect.php');

//new LpxkToKeyBase($db, 'lpxk/dicranoloma.lpxk');
//new LpxkToKeyBase($db, 'http://keys.lucidcentral.org/phoenix/keys/key%20to%20the%20order%20of%20fishes/FishOrders.lpxk');
new LpxkToKeyBase($db, 'http://keys.lucidcentral.org/phoenix/keys/insect%20orders/Insects.lpxk');
//new LpxkToKeyBase($db, 'http://keys.lucidcentral.org/phoenix/keys/pupal%20key%20to%20genera%20of%20white%20flies/WhiteFlies.lpxk');
//new LpxkToKeyBase($db, 'http://herbarium.usu.edu/IMRkeys/Grass%20Tribes%20for%20the%20Intermountain%20Region/Grass%20Tribes%20for%20the%20Intermountain%20Region.lpxk');

class LpxkToKeyBase {
    private $db;
    private $filename;
    private $loadimages;
    
    private $lpxk;
    
    private $title;
    private $firstStepID;
    private $media;
    private $icons;
    private $items;
    private $itemIDs;
    private $stepIDs;
    
    private $keysid;
    private $newleads;
    
    private $nextleadid;
    private $nodenumber;
    
    private $leadids;
    private $parentids;
    
    public function __construct($db, $filename, $loadimages=FALSE) {
        $this->db = $db;
        
        $this->lpxk = new DOMDocument('1.0', 'UTF-8');
        $this->lpxk->load($filename);
        $this->filename = $filename;
        
        $this->loadimages = $loadimages;
        
        $this->media = array();
        $this->icons = array();
        $this->items = array();
        $this->itemIDs = array();
        $this->leads = array();
        $this->stepIDs = array();
        
        $this->leadids = array();
        $this->parentids = array();
        
        $this->parseLpxk();
        $this->Key();
        $this->Media();
        $this->Items();
        $this->Leads();
    }

    private function parseLpxk() {
        // Get the title
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
    
    private function Key() {
        $select = "SELECT MAX(KeysID) FROM `keys`";
        $stmt = $this->db->prepare($select);
        $stmt->execute();
        $max = $stmt->fetchColumn();
        $newkeysid = $max + 1;
        
        $count = "SELECT count(*)
            FROM `keys`
            WHERE Name=?";
        $countStmt = $this->db->prepare($count);
        
        $select = "SELECT KeysID
            FROM `keys`
            WHERE Name=?";
        $selectStmt = $this->db->prepare($select);
        
        $insert = "INSERT INTO `keys` (KeysID, Name, NameUrl, Url)
            VALUES (?, ?, ?, ?)";
        $insertStmt = $this->db->prepare($insert);
        
        $countStmt->execute(array($this->title));
        if ($countStmt->fetchColumn() > 0) {
            $selectStmt->execute(array($this->title));
            $this->keysid = $selectStmt->fetchColumn();
        }
        else {
            $nameurl = str_replace(' ', '_', strtolower($this->title));
            $url = (substr($this->filename, 0, 7) == 'http://') ? $this->filename : NULL;
            $insertStmt->execute(array($newkeysid, $this->title, $nameurl, $url));
            if ($insertStmt->errorCode() != '00000'){
                $error = $insertStmt->errorInfo();
                array_unshift($error, 'key: ' . $this->title);
                print_r($error);
            }
            $this->keysid = $newkeysid;
        }
    }
    
    private function Media() {
        $max = "SELECT max(MediaID)
            FROM media";
        $maxStmt = $this->db->prepare($max);
        $maxStmt->execute();
        $nextmediaid = $maxStmt->fetchColumn() + 1;
        
        $count = "SELECT count(MediaID)
            FROM media 
            WHERE KeysID=? AND OriginalFilename=?";
        $countStmt = $this->db->prepare($count);

        $select = "SELECT MediaID 
            FROM media 
            WHERE KeysID=? AND OriginalFilename=?";
        $selectStmt = $this->db->prepare($select);
        
        $insert = "INSERT INTO media (MediaID, KeysID, OriginalFilename, Filename)
            VALUES (?, ?, ?, ?)";
        $insertStmt = $this->db->prepare($insert);
        
        foreach ($this->media as $key=>$img) {
            $this->icons[] = $img['icon'];
            $countStmt->execute(array($this->keysid, $img['icon']));
            if ($countStmt->fetchColumn() > 0) {
                $selectStmt->execute(array($this->keysid, $img['icon']));
                $this->media[$key]['id'] = $selectStmt->fetchColumn();
            }
            else {
                $insertArray = array();
                $insertArray[] = $nextmediaid;
                $insertArray[] = $this->keysid;
                $insertArray[] = $img['icon'];
                if ($this->loadimages)
                    $insertArray[] = $this->loadImage($img['icon']);
                else
                    $insertArray[] = NULL;
                
                $insertStmt->execute($insertArray);
                if ($insertStmt->errorCode() != '0000') {
                    $error = $insertStmt->errorInfo();
                    array_unshift($error, 'media');
                    print_r($error);
                }
                
                $this->media[$key]['id'] = $nextmediaid;
                $nextmediaid++;
            }
        }
    }
    
    private function Items() {
        $select = "SELECT max(ItemsID)
            FROM items";
        $stmt = $this->db->prepare($select);
        $stmt->execute();
        $max = $stmt->fetchColumn();
        $newitemsid = $max + 1;
        
        $count = "SELECT count(*)
            FROM items
            WHERE Name=?";
        $countStmt = $this->db->prepare($count);
        $select = "SELECT ItemsID
            FROM items
            WHERE Name=?";
        $selectStmt = $this->db->prepare($select);
        $insert = "INSERT INTO items (ItemsID, Name)
            VALUES (?, ?)";
        $insertStmt = $this->db->prepare($insert);
        
        foreach ($this->items as $key=>$item) {
            $countStmt->execute(array($item['name']));
            if ($countStmt->fetchColumn() > 0) { // item already in database
                $selectStmt->execute(array($item['name']));
                $this->items[$key]['ItemsID'] = $selectStmt->fetchColumn();
            }
            else {
                $insertStmt->execute(array($newitemsid, $item['name']));
                if ($insertStmt->errorCode() != '00000') {
                    print_r($insertStmt->errorInfo());
                }
                
                $this->items[$key]['ItemsID'] = $newitemsid;
                $newitemsid++;
            }
        }
    }
    
    private function Leads() {
        $count = "SELECT count(*)
            FROM leads
            WHERE KeysID=?";
        $countStmt = $this->db->prepare($count);
        
        $delete = "DELETE FROM leads
            WHERE KeysID=?";
        $deleteStmt = $this->db->prepare($delete);
        
        $countStmt->execute(array($this->keysid));
        if ($countStmt->fetchColumn() > 0) {
            $deleteStmt->execute(array($this->keysid));
        }
        
        $max = "SELECT MAX(LeadsID)
            FROM leads";
        $maxStmt = $this->db->prepare($max);
        $maxStmt->execute();
        $this->nextleadid = $maxStmt->fetchColumn() + 1;
        $this->nodenumber = 1;

        $this->newleads = array();
        $newlead = (array) new Lead();
        $newlead['KeysID'] = $this->keysid;
        $newlead['LeadsID'] = $this->nextleadid;
        $newlead['NodeName'] = $this->title;
        $newlead['NodeNumber'] = $this->nodenumber;
        $this->newleads[] = $newlead;
        $this->leadids[] = $this->nextleadid;
        $this->parentids[] = NULL;
        
        $this->nextleadid++;
        $this->nodenumber++;
        
        $this->nextLead($newlead['LeadsID'], $this->firstStepID);
        
        $this->getHighestDescendantNodeNumbers();
        //print_r($this->newleads);
        
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
            if ($insertStmt->errorCode() != '00000') {
                print_r($insertStmt->errorInfo());
            }
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
                $key = array_search($thisLead['goto'], $this->itemIDs);
                if ($key !== FALSE) {
                    $endnode['NodeName'] = $this->items[$key]['name'];
                    $endnode['ItemsID'] = $this->items[$key]['ItemsID'];
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
                imagejpeg($img, 'img/' . $newname);
                break;

            case 'gif':
            case 'GIF':
                $img = imagecreatefromgif($url);
                imagegif($img, 'img/' . $newname);
                break;

            case 'png':
            case 'PNG':
                $img = imagecreatefrompng($url);
                imagepng($img, 'img/' . $newname);
                break;

            default:
                break;
        }
        
        return $extension;
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
    var $MediaID = NULL;
    var $ItemUrl = NULL;
}


?>
