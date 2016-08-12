<?php

class ExportService {
    private $key;
    
    private $itemids;
    private $leadids;
    private $parentids;
    private $parentids_uniq;
    
    public function __construct() {
        $this->ci =& get_instance();
        $this->ci->load->library('KeyService');
    }
    
    public function export($keyid, $format='json') {
        $this->key = $this->ci->keyservice->getKey($keyid);
        $this->orderItems();
        
        switch ($format) {
            case 'json':
                $json = json_encode($this->key);
                return $json;
            case 'sdd':
                $sdd = $this->exportToSdd();
                return $sdd;
            case 'lpxk':
                $this->linkThrough();
                $lpxk = $this->exportToLpxk();
                return $lpxk;
            case 'csv':
                $this->linkThrough();
                $csv = $this->exportToCsv(',');
                return $csv;
            case 'txt':
                $this->linkThrough();
                $csv = $this->exportToCsv("\t");
                return $csv;
            default:
                break;
        }
    }
    
    private function orderItems() {
        $items = $this->key->items;
        $names = array();
        foreach ($items as $item) {
            $names[] = $item->item_name;
        }
        array_multisort($names, $items);
        $this->key->items = $items;
    }
    
    private function linkThrough() {
        $leads = $this->key->leads;
        $leadIds = array();
        $leadTexts = array();
        foreach ($leads as $lead) {
            $leadIds[] = $lead->lead_id;
            $leadTexts[] = (isset($lead->lead_text)) ? $lead->lead_text : FALSE;
        }
        if (array_keys($leadTexts, '[link through]')) {
            $items = $this->key->items;
            $itemIds = array();
            foreach ($items as $item) {
                $itemIds[] = $item->item_id;
            }

            $links = array();
            foreach (array_keys($leadTexts, '[link through]') as $lkey) {
                $lt = $leads[$lkey];
                $k = array_search($lt->parent_id, $leadIds);
                if ($k !== FALSE) {
                    $leads[$k]->item .= '_' . $lt->item;
                    $links[] = $leads[$k]->item;
                    unset($leads[$lkey]);
                }
            }
            
            $links = array_unique($links);
            foreach ($links as $link) {
                $bits = explode('_', $link);
                $j = array_search($bits[0], $itemIds);
                $i = array_search($bits[1], $itemIds);
                $items[$i]->item_id = $items[$j]->item_id . '_' . $items[$i]->item_id;
                $items[$i]->item_name = $items[$j]->item_name . ' {' . $items[$i]->item_name . '}';
            }
            
            $this->key->items = $items;
            $this->key->leads = $leads;
        }
    }
    
    private function exportToSdd() {
        $doc = new DOMDocument('1.0', 'UTF-8');
        $root = $doc->createElementNS('http://rs.tdwg.org/UBIF/2006/', 'Datasets');
        $doc->appendChild($root);
        $root->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $root->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'schemaLocation', 'http://rs.tdwg.org/UBIF/2006/ http://rs.tdwg.org/UBIF/2006/Schema/1.1/SDD.xsd');
        
//        $comment = "...";
//        $comment = $doc->createComment($comment);
//        $root->appendChild($comment);
        
        $tech = $doc->createElement('TechnicalMetadata');
        $created = $doc->createAttribute('created');
        $created->value = date('Y-m-d\Th:i:s');
        $tech->appendChild($created);
        $generator = $doc->createElement('Generator');
        $name = $doc->createAttribute('name');
        $name->value = 'KeyBase SDD export';
        $version = $doc->createAttribute('version');
        $version->value = '0';
        $generator->appendChild($name);
        $generator->appendChild($version);
        $tech->appendChild($generator);
        $root->appendChild($tech);
        
        $dataset = $doc->createElement('Dataset');
        $lang = $doc->createAttribute('xml:lang');
        $lang->value = 'en-au';
        $dataset->appendChild($lang);
        $root->appendChild($dataset);
        
        $representation = $doc->createElement('Representation');
        $dataset->appendChild($representation);
        $title = $doc->createElement('Label');
        $lang = $doc->createAttribute('xml:lang');
        $lang->value = 'en';
        $title->appendChild($lang);
        $title->nodeValue = $this->key->key_name;
        $representation->appendChild($title);
        
        $description = $doc->createElement('Detail');
        $lang = $doc->createAttribute('xml:lang');
        $lang->value = 'en';
        $description->appendChild($lang);
        $role = $doc->createAttribute('role');
        $role->value = 'Description';
        //$description->appendChild($role);
        //$description->nodeValue = 'Description ...';
        //$representation->appendChild($description);
        
        
        $taxonnames = $doc->createElement('TaxonNames');
        $dataset->appendChild($taxonnames);
        
        $mediaobjects = array();
        foreach ($this->key->items as $item) {
            $taxonname = $doc->createElement('TaxonName');
            $taxonnames->appendChild($taxonname);
            $id = $doc->createAttribute('id');
            $id->value = 'i' . $item->item_id;
            $taxonname->appendChild($id);
            
            $representation = $doc->createElement('Representation');
            $label = $doc->createElement('Label', $item->item_name);
            $representation->appendChild($label);
            $taxonname->appendChild($representation);
            
            if ($item->url) {
                $mediaobjects[] = array(
                    'href' => $item->url,
                    'type' => 'Text',
                    'label' => $item->item_name,
                );
                $mo = $doc->createElement('MediaObject');
                $ref = $doc->createAttribute('ref');
                $ref->value = 'mo' . count($mediaobjects);
                $mo->appendChild($ref);
                $type = $doc->createAttribute('type');
                $type->value = 'Text';
                $mo->appendChild($type);
                $role = $doc->createAttribute('role');
                $role->value = 'Secondary';
                $mo->appendChild($role);
                $representation->appendChild($mo);
            }
        }
        
        $identificationkeys = $doc->createElement('IdentificationKeys');
        $dataset->appendChild($identificationkeys);
        
        $identificationkey = $doc->createElement('IdentificationKey');
        $id = $doc->createAttribute('id');
        $id->value = 'k' . $this->key->key_id;
        $identificationkey->appendChild($id);
        $identificationkeys->appendChild($identificationkey);
        
        $representation = $doc->createElement('Representation');
        $identificationkey->appendChild($representation);
        $title = $doc->createElement('Label');
        $lang = $doc->createAttribute('xml:lang');
        $lang->value = 'en';
        $title->appendChild($lang);
        $title->nodeValue = $this->key->key_name;
        $representation->appendChild($title);

        $description = $doc->createElement('Detail');
        $lang = $doc->createAttribute('xml:lang');
        $lang->value = 'en';
        $description->appendChild($lang);
        $role = $doc->createAttribute('role');
        $role->value = 'Description';
        //$description->appendChild($role);
        //$description->nodeValue = 'Description ...';
        //$representation->appendChild($description);
        
        $leads = $doc->createElement('Leads');
        $identificationkey->appendChild($leads);
        
        foreach ($this->key->leads as $l) {
            $lead = $doc->createElement('Lead');
            $leads->appendChild($lead);
            $id = $doc->createAttribute('id');
            $id->value = 'l' . $l->lead_id;
            $lead->appendChild($id);

            if ($l->parent_id !== $this->key->first_step->root_node_id) {
                $parent = $doc->createElement('Parent');
                $ref = $doc->createAttribute('ref');
                $ref->value = 'l' .  $l->parent_id;
                $parent->appendChild($ref);
                $lead->appendChild($parent);
            }

            $stmt = $doc->createElement('Statement', $l->lead_text);
            $lead->appendChild($stmt);

            if ($l->item) {
                $taxonname = $doc->createElement('TaxonName');
                $ref = $doc->createAttribute('ref');
                $ref->value = 'i' . $l->item;
                $taxonname->appendChild($ref);
                $lead->appendChild($taxonname);
            }
        }
        
        if ($mediaobjects) {
            $mos = $doc->createElement('MediaObjects');
            $dataset->appendChild($mos);
            
            foreach ($mediaobjects as $index=>$object) {
                $mo = $doc->createElement('MediaObject');
                $mos->appendChild($mo);
                
                $id = $doc->createAttribute('id');
                $id->value = 'mo' . ($index+1);
                $mo->appendChild($id);
                
                $representation = $doc->createElement('Representation');
                $mo->appendChild($representation);
                
                $label = $doc->createElement('Label', $object['label']);
                $representation->appendChild($label);
                
                $type = $doc->createElement('Type', $object['type']);
                $mo->appendChild($type);
                
                $source = $doc->createElement('Source');
                $mo->appendChild($source);
                $href = $doc->createAttribute('href');
                $href->value = $object['href'];
                $source->appendChild($href);
            }
        }
        
        $doc->preserveWhiteSpace = FALSE;
        $doc->formatOutput = TRUE;
        
        return $doc->saveXML();
    }
    
    private function exportToLpxk() {
        $doc = new DOMDocument('1.0', 'UTF-8');
        $root = $doc->createElement('PhoenixKey');
        
        $title = $doc->createAttribute('title');
        $title->value = $this->key->key_name;
        $root->appendChild($title);
        
        $created = $doc->createAttribute('CreatedIn');
        $created->value = 'Lucid Phoenix Builder. File Format © Copyright Centre for Biological Information Technology, The University of Queensland.';
        $root->appendChild($created);
        
        $toItems = array();
        foreach($this->key->leads as $lead) {
            if (isset($lead->item) && $lead->item) {
                $toItems[] = $lead->item;
            }
        }
        
        foreach ($this->key->items as $item) {
            if (in_array($item->item_id, $toItems)) {
                $identity = $doc->createElement('Identity');
                $id = $doc->createAttribute('id');
                $id->value = 'i' . $item->item_id;
                $identity->appendChild($id);

                $name = $doc->createAttribute('name');
                $name->value = $item->item_name;
                $identity->appendChild($name);

                $url = $doc->createAttribute('url');
                $url->value = $item->url;
                $identity->appendChild($url);

                $root->appendChild($identity);
            }
        }
        
        $steps = $doc->createElement('Steps');
        $firststepid = $doc->createAttribute('firstStepID');
        $firststepid->value = 's' . $this->key->first_step->root_node_id;
        $steps->appendChild($firststepid);
        
        $leads = $this->key->leads;
        $parents = array();
        $leadids = array();
        foreach ($leads as $lead) {
            $parents[] = $lead->parent_id;
            $leadids[] = $lead->lead_id;
        }
        array_multisort($parents, $leadids, $leads);
        $couplets = array_unique($parents);
        
        foreach ($couplets as $couplet) {
            $step = $doc->createElement('Step');
            $id = $doc->createAttribute('id');
            $id->value = 's' . $couplet;
            $step->appendChild($id);
            
            $text = $doc->createAttribute('text');
            $text->value = 'Step ' . $couplet;
            $step->appendChild($text);
            
            $alts = array();
            foreach (array_keys($parents, $couplet) as $pindex) {
                $alts[] = $leads[$pindex];
            }
            
            foreach ($alts as $alt) {
                $lead = $doc->createElement('Lead');
                $stepid = $doc->createAttribute('stepid');
                $stepid->value = 's' . $couplet;
                $lead->appendChild($stepid);
                
                $leadid = $doc->createAttribute('leadid');
                $leadid->value = 'l' . $alt->lead_id;
                $lead->appendChild($leadid);
                
                $goto = $doc->createAttribute('goto');
                $goto->value = ($alt->item) ? 'i' . $alt->item : 's' . $alt->lead_id;
                $lead->appendChild($goto);
                
                $text = $doc->createElement('Text', $alt->lead_text);
                $lead->appendChild($text);
                
                $step->appendChild($lead);
            }
            
            $steps->appendChild($step);
        }
        $root->appendChild($steps);
        
        $doc->appendChild($root);
        
        $doc->preserveWhiteSpace = FALSE;
        $doc->formatOutput = TRUE;
        
        return $doc->saveXML();
    }
    
    private function exportToCsv($delimiter) {
        $this->ci->load->helper('csv');
        $this->reorder();
        
        $this->itemids = array();
        foreach ($this->key->items as $item) {
            $this->itemids[] = $item->item_id;
        }
        
        $csv = array();
        foreach ($this->leads as $lead) {
            $csvRow = array();
            $csvRow[] = $lead->fromNode;
            $csvRow[] = $lead->lead_text;
            if (isset($lead->item) && $lead->item) {
                $csvRow[] = $this->getItemName($lead->item);
            }
            else {
                $csvRow[] = $lead->toNode;
            }
            $csv[] = $csvRow;
        }
        return arrayToCsv($csv, $delimiter);
    }
    
    private function getItemName($id) {
        $key = array_search($id, $this->itemids);
        $item = $this->key->items[$key];
        return $item->item_name;
    }
    
    private function reorder() {
        $this->leads = array();
        $this->parentids = array();
        foreach ($this->key->leads as $lead) {
            $this->parentids[] = $lead->parent_id;
        }
        array_multisort($this->key->leads);
        $this->parentids_uniq = array();
        $this->getNode($this->key->first_step->root_node_id);
        $this->renumber();
    }
    
    private function getNode($parentid) {
        foreach (array_keys($this->parentids, $parentid) as $index) {
            $lead = $this->key->leads[$index];
            $this->leads[] = $lead;
            if (!in_array($parentid, $this->parentids_uniq)) {
                $this->parentids_uniq[] = $parentid;
            }
            if (!(isset($lead->item) && $lead->item)) {
                $this->getNode($lead->lead_id);
            }
        }
    }
    
    private function renumber() {
        $fromNodes = array();
        foreach ($this->leads as $index => $lead) {
            $lead->fromNode = array_search($lead->parent_id, $this->parentids_uniq) + 1;
            $fromNodes[] = $lead->fromNode;
            if (!(isset($lead->item) && $lead->item)) {
                $lead->toNode = array_search($lead->lead_id, $this->parentids_uniq) + 1;
            }
            $this->leads[$index] = $lead;
        }
        array_multisort($fromNodes, $this->leads);
    }
    
}
?>
