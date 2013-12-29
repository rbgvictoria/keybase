<?php

class Export {
    
    public function exportToLpxk($key) {
        $doc = new DOMDocument('1.0', 'UTF-8');
        $root = $doc->createElement('PhoenixKey');
        
        $title = $doc->createAttribute('title');
        $title->value = $key['Title'];
        $root->appendChild($title);
        
        $created = $doc->createAttribute('CreatedIn');
        $created->value = 'Lucid Phoenix Builder. File Format © Copyright Centre for Biological Information Technology, The University of Queensland.';
        $root->appendChild($created);
        
        foreach ($key['Items'] as $item) {
            $identity = $doc->createElement('Identity');
            $id = $doc->createAttribute('id');
            $id->value = $item['id'];
            $identity->appendChild($id);
            
            $name = $doc->createAttribute('name');
            $name->value = $item['name'];
            $identity->appendChild($name);
            
            $icon = $doc->createAttribute('icon');
            $icon->value = $item['icon'];
            $identity->appendChild($icon);
            
            $url = $doc->createAttribute('url');
            $url->value = $item['url'];
            $identity->appendChild($url);
            
            $root->appendChild($identity);
        }
        
        $steps = $doc->createElement('Steps');
        $firststepid = $doc->createAttribute('firstStepID');
        $firststepid->value = 's0';
        $steps->appendChild($firststepid);
        
        $stepno = 0;
        
        foreach ($key['Steps'] as $couplet) {
            $step = $doc->createElement('Step');
            $id = $doc->createAttribute('id');
            $id->value = $couplet['id'];
            $step->appendChild($id);
            
            $stepno++;
            $text = $doc->createAttribute('text');
            $text->value = 'Step ' . $stepno;
            $step->appendChild($text);
            
            //$text = $doc->createAttribute('text');
            //$text->value = $couplet['text'];
            //$step->appendChild($text);
            
            foreach ($couplet['leads'] as $alt) {
                $lead = $doc->createElement('Lead');
                $stepid = $doc->createAttribute('stepid');
                $stepid->value = $alt['stepid'];
                $lead->appendChild($stepid);
                
                $leadid = $doc->createAttribute('leadid');
                $leadid->value = $alt['leadid'];
                $lead->appendChild($leadid);
                
                $goto = $doc->createAttribute('goto');
                $goto->value = $alt['goto'];
                $lead->appendChild($goto);
                
                $icon = $doc->createAttribute('icon');
                $icon->value = $alt['icon'];
                $lead->appendChild($icon);
                
                $text = $doc->createElement('Text', $alt['leadtext']);
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
    
    public function exportToCsv($key, $delimiter=FALSE) {
        if ($delimiter && $delimiter == 'tab') {
            $filename  = 'key_' . uniqid() . '.txt';
            $delimiter = "\t";
        }
        else {
            $filename = 'key_' . uniqid() . '.txt';
            $delimiter = ',';
        }
        $handle = fopen('temp_out/' . $filename, 'w');
        
        
        foreach ($key['Steps'] as $step) {
            foreach ($step['leads'] as $lead) {
                fputcsv($handle, array($lead['fromnode'], $lead['leadtext'], $lead['tonode']), $delimiter);
            }
        }
        fclose($handle);
        return $filename;
    }
    
    public function exportToSdd($key) {
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
        $title->nodeValue = $key['Title'];
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
        foreach ($key['Items'] as $item) {
            $taxonname = $doc->createElement('TaxonName');
            $taxonnames->appendChild($taxonname);
            $id = $doc->createAttribute('id');
            $id->value = $item['id'];
            $taxonname->appendChild($id);
            
            $representation = $doc->createElement('Representation');
            $label = $doc->createElement('Label', $item['name']);
            $representation->appendChild($label);
            $taxonname->appendChild($representation);
            
            if ($item['icon']) {
                $mediaobjects[] = array(
                    'href' => $item['icon'],
                    'type' => 'Image',
                    'label' => $item['name'],
                );
                $mo = $doc->createElement('MediaObject');
                $ref = $doc->createAttribute('ref');
                $ref->value = 'mo' . count($mediaobjects);
                $mo->appendChild($ref);
                $type = $doc->createAttribute('type');
                $type->value = 'Image';
                $mo->appendChild($type);
                $role = $doc->createAttribute('role');
                $role->value = 'Iconic';
                $mo->appendChild($role);
                $representation->appendChild($mo);
            }

            if ($item['url']) {
                $mediaobjects[] = array(
                    'href' => $item['url'],
                    'type' => 'Text',
                    'label' => $item['name'],
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
        $id->value = 'k1';
        $identificationkey->appendChild($id);
        $identificationkeys->appendChild($identificationkey);
        
        $representation = $doc->createElement('Representation');
        $identificationkey->appendChild($representation);
        $title = $doc->createElement('Label');
        $lang = $doc->createAttribute('xml:lang');
        $lang->value = 'en';
        $title->appendChild($lang);
        $title->nodeValue = $key['Title'];
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
        
        foreach ($key['Steps'] as $i => $step) {
            foreach ($step['leads'] as $l) {
                $lead = $doc->createElement('Lead');
                $leads->appendChild($lead);
                $id = $doc->createAttribute('id');
                $id->value = 'k1l' . $l['sddlead'];
                $lead->appendChild($id);
                
                if ($i > 0) {
                    $parent = $doc->createElement('Parent');
                    $ref = $doc->createAttribute('ref');
                    $ref->value = 'k1l' . $l['sddparent'];
                    $parent->appendChild($ref);
                    $lead->appendChild($parent);
                }
                
                $stmt = $doc->createElement('Statement', $l['leadtext']);
                $lead->appendChild($stmt);
                
                if ($l['icon']) {
                    $mediaobjects[] = array(
                        'href' => $l['icon'],
                        'type' => 'Image',
                        'label' => 'Illustration of lead k1l' . $l['sddlead'],
                    );
                    $mo = $doc->createElement('MediaObject');
                    $ref = $doc->createAttribute('ref');
                    $ref->value = 'mo' . count($mediaobjects);
                    $mo->appendChild($ref);
                    $type = $doc->createAttribute('type');
                    $type->value = 'Image';
                    $mo->appendChild($type);
                    $role = $doc->createAttribute('role');
                    $role->value = 'Diagnostic';
                    $mo->appendChild($role);
                    $lead->appendChild($mo);
                }
                
                if (!is_numeric($l['tonode'])) {
                    $taxonname = $doc->createElement('TaxonName');
                    $ref = $doc->createAttribute('ref');
                    $ref->value = $l['goto'];
                    $taxonname->appendChild($ref);
                    $lead->appendChild($taxonname);
                }
                
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
}
?>
