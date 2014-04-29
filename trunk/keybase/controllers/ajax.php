<?php

class Ajax extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->helper('url');
        $this->output->enable_profiler(false);
    }
    
    /**
     * nextCouplet method
     * 
     * Gets the next couplet in the key on AJAX call
     * 
     * @param integer $key
     * @param integer $node 
     */
    public function nextCouplet($key, $node=false) {
        $this->load->model('nothophoenixmodel', 'phoenix');
        $project = $this->phoenix->getProjectID($key);
        $this->phoenix->GlobalFilter($project, $key);
        $node = $this->phoenix->getNode($key, $node);
        $data = $this->phoenix->getNextCouplet($node);
        
        if (count($data) > 1) {
            foreach ($data as $lead) {
                echo '<a class="lead" href="' . base_url() . 'key/nothophoenix/' . $key . '/' .
                        $lead['id'] . '">' . $lead['lead'] . '</a>';
                if ($lead['media'])
                    echo '<div class="featureimg"><img src="' . base_url() . 'images/' . 
                        $lead['media'] . '" alt="' . $lead['lead'] . '" /></div>';
            }
        }
        else {
            $lead = $data[0];
            $result = $this->phoenix->getEndTaxon($lead['id']);
            echo '<p><span class="result">' . $result . '</span></p>';
        }
    }
    
    private function auxNextCouplet($key, $node) {
        $this->load->model('nothophoenixmodel', 'phoenix');
        $project = $this->phoenix->getProjectID($key);
        $this->phoenix->GlobalFilter($project, $key);
        $node = $this->phoenix->getNode($key, $node);
        return $this->phoenix->getNextCouplet($node);
    }
    
    public function nextCoupletJSON($key, $node=FALSE) {
        $data = $this->auxNextCouplet($key, $node);
        if (isset($_GET['callback']))
            echo $_GET['callback'] . '(' . json_encode ($data) . ')';
        else
            echo json_encode($data);
    }
    
    public function path($key, $node) {
        $this->load->model('nothophoenixmodel', 'phoenix');
        $project = $this->phoenix->getProjectID($key);
        $this->phoenix->GlobalFilter($project, $key);
        $node = $this->phoenix->getNode($key, $node);
        $currentnode = $this->phoenix->getCurrentNode($node);
        $path = $this->phoenix->getPath($key, $currentnode);
        
        echo '<ol>';
        foreach ($path as $lead) {
            if ($lead['automatic'])
                echo '<li style="color:gray">' . $lead['lead'] . '</li>';
            else
                echo '<li><a href="' . site_url() . 'key/nothophoenix/' . $key . '/' . 
                    $lead['parentid'] . '">' . $lead['lead'] . '</a></li>';
        }
        $result = $this->phoenix->isResult($node);
        if ($result)
            $text = 'Result';
        else
            $text = 'Pending question';
        
        echo '<li class="pending"><a href="' . site_url() . 'key/nothophoenix/' . $key . '/' . 
                $node . '">'. $text . '</a></li>';

        echo '</ol>';
    }
    
    private function auxPath($key, $node) {
        $this->load->model('nothophoenixmodel', 'phoenix');
        $project = $this->phoenix->getProjectID($key);
        $this->phoenix->GlobalFilter($project, $key);
        $node = $this->phoenix->getNode($key, $node);
        $currentnode = $this->phoenix->getCurrentNode($node);
        return $this->phoenix->getPath($key, $currentnode);
    }
    
    public function pathJSON($key, $node=false) {
        $path = FALSE;
        if ($node)
            $path = $this->auxPath($key, $node);
        if (isset($_GET['callback']))
            echo $_GET['callback'] . '(' . json_encode ($path) . ')';
        else
            echo json_encode($path);
    }
    
    
    private function auxRemaining($key, $node=FALSE) {
        $this->load->model('nothophoenixmodel', 'phoenix');
        $project = $this->phoenix->getProjectID($key);
        $this->phoenix->GlobalFilter($project, $key);
        if ($node) {
            $node = $this->phoenix->getNode($key, $node);
            $currentnode = $this->phoenix->getCurrentNode($node);
            $remaining = $this->phoenix->auxRemainingEntities($key, $currentnode);
        }
        else 
            $remaining = FALSE;
        return $this->phoenix->getRemainingEntities($key, $remaining);
    }
    
    public function remainingItemsJSON($key, $node=FALSE) {
        $entities = $this->auxRemaining($key, $node=FALSE);
        if (isset($_GET['callback']))
            echo $_GET['callback'] . '(' . json_encode ($entities) . ')';
        else
            echo json_encode($entities);
    }
    
    private function auxDiscarded($key, $node) {
        $this->load->model('nothophoenixmodel', 'phoenix');
        $project = $this->phoenix->getProjectID($key);
        $this->phoenix->GlobalFilter($project, $key);
        $node = $this->phoenix->getNode($key, $node);
        $currentnode = $this->phoenix->getCurrentNode($node);
        $remaining = $this->phoenix->auxRemainingEntities($key, $currentnode);
        return $this->phoenix->getDiscardedEntities($key, $remaining);;
    }
    
    public function discardedItemsJSON($key, $node=FALSE) {
        if ($node)
            $entities = $this->auxDiscarded ($key, $node);
        else
            $entities = FALSE;
        if (isset($_GET['callback']))
            echo $_GET['callback'] . '(' . json_encode ($entities) . ')';
        else
            echo json_encode($entities);
    }
    
    private function auxParent($key, $node) {
        $this->load->model('nothophoenixmodel', 'phoenix');
        $currentnode = $this->phoenix->getCurrentNode($node);
        return $this->phoenix->getParent($currentnode);
    }
    
    public function parentJSON($key, $node=FALSE) {
        if ($node) {
            $data = array('id' => $this->auxParent ($key, $node));
            $json = json_encode((object) $data);
        }
        else
            $json = json_encode(FALSE);
        if (isset($_GET['callback']))
            echo $_GET['callback'] . '(' . $json . ')';
        else
            echo json_encode($json);
    }
    
    public function parent($key, $node=false) {
        if ($node) {
            $this->load->model('nothophoenixmodel', 'phoenix');
            $currentnode = $this->phoenix->getCurrentNode($node);
            echo $this->phoenix->getParent($currentnode);
        }
    }
    
    public function coupletJSON($key, $node=FALSE) {
        $data = array(
            'parent' => FALSE,
            'currentNode' => FALSE,
            'path' => FALSE,
            'remainingItems' => FALSE,
            'discardedItems' => FALSE
        );
        
        $data['currentNode'] = $this->auxNextCouplet($key, $node);
        $data['remainingItems'] = $this->auxRemaining($key, $node);
        
        if ($node) {
            $data['parent'] = (object) array('id' => $this->auxParent($key, $node));
            $data['path'] = $this->auxPath($key, $node);
            $data['discardedItems'] = $this->auxDiscarded($key, $node);
        }
        
        $json = json_encode((object) $data);
        
        if ($_GET['callback'])
            echo $_GET['callback'] . '(' . $json . ')';
        else
            echo $json;
            
    }
    
    public function projectkeys_hierarchy($project, $filterid=FALSE) {
        $this->load->model('keymodel');
        $this->load->model('projectmodel');
        $projectdata = $this->keymodel->getProjectData($project);
        
        $filter = false;
        if (isset($this->session->userdata['GlobalFilterOn']) && $this->session->userdata['GlobalFilterOn']) {
            $filter = $this->projectmodel->getFilterKeys($project);
        }
        elseif ($filterid) {
            $filter = $this->projectmodel->getFilterKeys($project, $filterid);
        }
        
        $linked = $this->keymodel->getProjectKeysLinked($project, $filter);
        $orphan = $this->keymodel->getProjectKeysOrphaned($project, $filter);
        
        
        
        $json = '[{';
        $json .= '"title": "' . $projectdata['Name'] . '",';
        $json .= '"isFolder": true,';
        $json .= '"addClass":"keybase-dynatree-project-folder",';
        $json .= '"children": [';
        
        if ($linked) {
            $n = count($linked);
            for ($i = 0; $i < $n; $i++) {
                $json .= '{ ';
                $json .= '"title": "' . $linked[$i]['Name'] . '", ';
                $json .= '"href": "' . base_url() . 'key/nothophoenix/' . $linked[$i]['KeysID'] . '",';
                $json .= '"addClass":"keybase-dynatree-key"';
                if ($i < $n - 1 && $linked[$i]['Depth'] < $linked[$i+1]['Depth'])
                    $json .= ', "children": [';
                elseif ($i < $n - 1 && $linked[$i]['Depth'] == $linked[$i+1]['Depth'])
                    $json .= '}, ';
                elseif ($i < $n - 1 && $linked[$i]['Depth'] > $linked[$i+1]['Depth'])
                    $json .= str_repeat('}]', $linked[$i]['Depth'] - $linked[$i+1]['Depth']) . '},';
                elseif ($i == $n - 1)
                    $json .= '}' . str_repeat(']}', $linked[$i]['Depth'] - 1);
            }
            if ($orphan)
                $json .= ', ';
        }
        
        if ($orphan) {
            if ($linked) {
                $json .= '{';
                $json .= '"title": "Orphan keys",';
                $json .= '"isFolder": true, ';
                $json .= '"children": [';
            }
            
            $n = count($orphan);
            for ($i = 0; $i < $n; $i++) {
                $json .= '{ ';
                $json .= '"title": "' . $orphan[$i]['Name'] . '", ';
                $json .= '"href": "' . base_url() . 'key/nothophoenix/' . $orphan[$i]['KeysID'] . '",';
                $json .= '"addClass": "keybase-dynatree-key"';
                if ($i < $n - 1 && $orphan[$i]['Depth'] < $orphan[$i+1]['Depth'])
                    $json .= ', "children": [';
                elseif ($i < $n - 1 && $orphan[$i]['Depth'] == $orphan[$i+1]['Depth'])
                    $json .= '}, ';
                elseif ($i < $n - 1 && $orphan[$i]['Depth'] > $orphan[$i+1]['Depth'])
                    $json .= '}]}, ';
                elseif ($i == $n - 1)
                    $json .= '}' . str_repeat(']}', $orphan[$i]['Depth'] - 1);
            }
            
            if ($linked) {
                $json .= ']}';
            }
        }
        
        $json .= ']';  // end children root node
        $json .= '}]';
        
        header('Content-type: application/json');
        echo $json;
    }
    
    public function projectkeys_alphabetical($project) {
        $this->load->model('keymodel');
        $this->load->model('projectmodel');

        $user = false;
        if (isset($this->session->userdata['id']) && $this->session->userdata['id'])
            $user = $this->session->userdata['id'];

        $filter = false;
        if (isset($this->session->userdata['GlobalFilterOn']) && $this->session->userdata['GlobalFilterOn']) {
            $filter = $this->projectmodel->getFilterKeys($project);
        }

        $data = $this->keymodel->getProjectKeys($project, $user, $filter);
        $json = json_encode($data);
        header('Content-type: application/json');
        echo $json;
    }
    
    public function getGlobalFilterTaxa($filterid) {
        if (!$filterid) exit;
        $this->load->model('filtermodel');
        $data = $this->filtermodel->getGlobalfilterTaxa($filterid);
        echo implode("\r\n", $data);
    }
    
    public function getGlobalFilterProjects($filterid) {
        if (!$filterid) exit;
        $this->load->model('filtermodel');
        $data = $this->filtermodel->getGlobalfilterProjects($filterid);
        $json = json_encode($data);
        header('Content-type: application/json');
        echo $json;
    }
    
    public function getGlobalFilterMetadata($filterid) {
        if (!$filterid) exit;
        $this->load->model('filtermodel');
        $data = $this->filtermodel->getGlobalfilterMetadata($filterid);
        $json = json_encode($data);
        
        $this->setGlobalFilter($data->FilterID);
        
        header('Content-type: application/json');
        echo $json;
    }

    public function getGlobalFilterKeys($filterid) {
        if (!$filterid) exit;
        $this->load->model('filtermodel');
        $data = $this->filtermodel->getKeysFromFilter($filterid);
        
        if ($data) {
            $filtername = $this->filtermodel->getGlobalFilterName($filterid);
            if ($filtername)
                $filtername .= " [ID: $filterid]";
            else
                $filtername = "[ID: $filterid]";
            
            $json = '{';
            $json .= '"title":"' . $filtername . '",';
            $json .= '"isFolder":true,';
            $json .= '"children":';
            
            
            $json .= '[';
            foreach ($data as $pindex => $project) {
                $json .= '{';
                $json .= '"title":"' . $project['name'] . '",';
                $json .= '"href":"' . site_url() . 'key/project/' . $project['id'] . '",';
                //$json .= '"expand":true,';
                $json .= '"addClass":"keybase-dynatree-project",';
                $json .= '"children":[';
                
                $keys = $project['keys'];
                $n = count($keys);
                for ($i = 0; $i < $n; $i++) {
                    $json .= '{ ';
                    $json .= '"title": "' . $keys[$i]['name'] . '", ';
                    $json .= '"href": "' . site_url() . 'key/nothophoenix/' . $keys[$i]['id'] . '",';
                    //$json .= '"expand":true,';
                    $json .= '"addClass":"keybase-dynatree-key",';
                    
                    $taxa = $keys[$i]['items'];
                    $m = count($taxa);
                    $json .= '"children":[{';
                    $json .= '"title":"Taxa (' . $m . ')",';
                    $json .= '"isFolder":true,';
                    $json .= '"addClass":"keybase-dynatree-items-folder",';
                    $json .= '"children":[';
                        foreach ($taxa as $j => $item) {
                            $json .= '{';
                            $json .= '"title":"' . $item['name'] . '",';
                            $json .= '"addClass":"keybase-dynatree-item"';
                            $json .= '}';
                            if ($j < $m - 1) $json .= ',';     
                        }
                    
                    $json .= ']}';
                    
                    if ($i < $n - 1 && $keys[$i]['depth'] < $keys[$i+1]['depth'])
                        $json .= ',';
                    elseif ($i < $n - 1 && $keys[$i]['depth'] == $keys[$i+1]['depth'])
                        $json .= ']}, ';
                    elseif ($i < $n - 1 && $keys[$i]['depth'] > $keys[$i+1]['depth'])
                        $json .= ']' . str_repeat('}]', $keys[$i]['depth'] - $keys[$i+1]['depth']) . '},';
                    elseif ($i == $n - 1)
                        $json .= ']}' . str_repeat(']}', $keys[$i]['depth'] - 1);
                }
                
                $json .= ']';
                $json .= '}';
                if ($pindex < count($data)-1) $json .= ',';
            }
            
            $json .= ']';
            $json .= '}';
            
            header('Content-type: application/json');
            echo $json;
        }

    }
    
    private function setGlobalFilter($filterid) {
        $unset = array(
            'GlobalFilter' => '',
            'GlobalFilterOn' => '',
            'GlobalFilter' => ''
        );
        $this->session->unset_userdata($unset);
        $set = array(
            'GlobalFilter' => $filterid,
            'GlobalFilterOn' => 1
        );
        $this->session->set_userdata($set);
    }
    
    public function getGlobalFilterID($filterid) {
        if (!$filterid) exit;
        $this->load->model('filtermodel');
        $gid = $this->filtermodel->getGlobalFilterID($filterid);
        echo $gid;
    }
    
    private function arrayToCsv($data) {
        $csv = array();
        $csv[] = $this->arrayToCsvRow(array_keys((array) $data[0]), ',');
        foreach ($data as $row) {
            $csv[] = $this->arrayToCsvRow(array_values((array) $row), ',');
        }
        return implode("\n", $csv);
    }
    
    private function arrayToCsvRow( array &$fields, $delimiter = ';', $enclosure = '"', $encloseAll = false, $nullToMysqlNull = false ) {
        $delimiter_esc = preg_quote($delimiter, '/');
        $enclosure_esc = preg_quote($enclosure, '/');

        $output = array();
        foreach ( $fields as $field ) {
            if ($field === null && $nullToMysqlNull) {
                $output[] = 'NULL';
                continue;
            }

            // Enclose fields containing $delimiter, $enclosure or whitespace
            if ( $encloseAll || preg_match( "/(?:${delimiter_esc}|${enclosure_esc}|\s)/", $field ) ) {
                $output[] = $enclosure . str_replace($enclosure, $enclosure . $enclosure, $field) . $enclosure;
            }
            else {
                $output[] = $field;
            }
        }

        return implode( $delimiter, $output );
    }
    
}


/* End of file ajax.php */
/* Location: ./controllers/ajax.php */
