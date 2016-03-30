<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

require_once 'Service.php';

class ErrorCheckService extends Service {
    private $fromnodes;
    private $tonodes;
    private $numpaths;
    private $endnotes;
    private $loops;
    
    public function __construct() {
        parent::__construct();
    }
    
    public function checkForErrors($keyid, $tempfilename, $delimiter) {
        $delimiter = ($delimiter == 'tab') ? "\t" : ',';
        $infile = fopen('uploads/' . $tempfilename, 'r');
        $inkey = array();
        $this->fromnodes = array();
        $this->tonodes = array();
        while (!feof($infile)) {
            $row = fgetcsv($infile, 0, $delimiter);
            if ($row) {
                foreach ($row as $index => $value)
                    $row[$index] = trim($value);
                
                $inkey[] = $row;
                $this->fromnodes[] = $row[0];
                $this->tonodes[] = isset($row[2]) ? $row[2] : FALSE;
            }
        }
        
        $unique_nodes = array_unique($this->fromnodes);
        $unique_node_keys = array_keys($unique_nodes);
        
        $path = array();
        $this->numpaths = 0;
        $this->endnodes = array();
        $this->loops = array();
        
        $this->traverseKey($path, $unique_nodes[0]);
        
        $errors = array();
        $warnings = array();
        foreach($inkey as $k => $row) {
            $numcols = count($row);
            $fromnode = array_search(array_search($row[0], $unique_nodes), $unique_node_keys);
            if (isset($row[2])) {
                $key = array_search($row[2], $unique_nodes);
                if ($key !== FALSE)
                    $tonode = array_search($key, $unique_node_keys);
                else $tonode = FALSE;
            }
            
            $htmltablerow = array();
            
            /*
             * Check if all the necessary data is there
             */
            if ($numcols < 3) {
                $errors['too-few-columns'][] = $k;
            }
            
            $key = array_search($row[0], $this->tonodes);
            
            if (count(array_keys($this->fromnodes, $row[0])) < 2) {
                $errors['singleton-leads'][] = $k;
            }
            elseif (count(array_keys($this->fromnodes, $row[0])) > 2) {
                $warnings['polytomies'][] = $k;
            }
            elseif ($key === FALSE && $fromnode != 0) {
                $errors['orphan-couplets'][] = $k;
            }
            
            if (isset($row[2])) {
                if ($tonode) {
                    if (isset($this->loops[$k])) {
                        $errors['loops'][] = $k;
                    }
                    elseif (count(array_keys($this->tonodes, $row[2])) > 1) {
                        $warnings['reticulations'][] = $k;
                    }
                }
                else {
                    if (is_numeric($row[2])) {
                        $errors['dead-ends'][] = $k;
                    }
                    elseif (!(preg_match('/^[A-Z]{1,1}[a-z]+ {1,1}/', str_replace('×', '', $row[2])) || preg_match('/^[A-Z]{1,1}[a-z]+$/', str_replace('×', '', $row[2])))) {
                        $warnings['possible-dead-ends'][] = $k;
                    }
                    elseif (!isset($this->endnodes[$k])) {
                        $htmltablerow[] = '<td class="will-not-key-out">' . $row[2] . '</td>';
                        $warnings['will-not-key-out'][] = $k;
                    }
                }
            }
        }
        return (object) array(
            'errors' => $errors,
            'warnings' => $warnings,
            'leads' => $inkey
        );
    }
    
    private function traverseKey($path, $node) {
        $path[] = $node;
        $this->numpaths++;
        
        foreach (array_keys($this->fromnodes, $node) as $lead) {
            $goto = $this->tonodes[$lead];
            if ($goto) {
                if (in_array($goto, $this->fromnodes)) {
                    if (in_array($goto, $path)) {
                        $endpath = $path;
                        $endpath[] = $goto;
                        $this->numpaths++;
                        $this->loops[$lead] = $goto;
                    }
                    else {
                        $this->traverseKey($path, $goto);
                    }
                }
                else {
                    $endpath = $path;
                    $endpath[] = $goto;
                    //echo implode('->', $endpath) . '<br/>';
                    $this->numpaths++;
                    $this->endnodes[$lead] = $goto;
                }
            }
        }
    }
    
    public function errorKeyHtml($leads, $errors, $warnings) {
        if ($errors) {
            foreach ($errors as $type => $errleads) {
                foreach ($errleads as $i) {
                    $leads[$i][] = $type;
                }
            }
        }
        if ($warnings) {
            foreach ($warnings as $type => $errleads) {
                foreach ($errleads as $i) {
                    $leads[$i][] = $type;
                }
            }
        }
        
        $fromnodes = array();
        foreach ($leads as $lead) {
            $fromnodes[] = $lead[0];
        }
        $fromnodes = array_unique($fromnodes);
        
        $htmltable = array();
        $htmltable[] = '<table class="table table-bordered table-condensed">';
        foreach ($leads as $lead) {
            $htmltablerow = array();
            if (count($lead) > 3) {
                if ($lead[3] == 'too-few-columns') {
                    $htmltablerow[] = '<tr class="too-few-columns">';
                }
                else {
                    $htmltablerow[] = '<tr>';
                }
                
                if (in_array($lead[3], array(
                    'singleton-leads',
                    'polytomies',
                    'orphan-couplets'
                ))) {
                    $htmltablerow[] = '<td class="' . $lead[3] . '">' . $lead[0]. '</td>';
                }
                else {
                    $htmltablerow[] = '<td>' . $lead[0] . '</td>';
                }
                
                $htmltablerow[] = '<td>' . $lead[1] . '</td>';
                
                if (in_array($lead[3], array(
                    'loops', 'reticulations',
                    'dead-ends',
                    'possible-dead-ends',
                    'will-not-key-out'
                ))) {
                    $htmltablerow[] = '<td class="' . $lead[3] . '">' . $lead[2]. '</td>';
                }
                elseif (!in_array($lead[2], $fromnodes)) {
                    $htmltablerow[] = '<td class="endnode">' . $lead[2] . '</td>';
                }
                else {
                    $htmltablerow[] = '<td>' . $lead[2] . '</td>';
                }
                
            }
            else {
                $htmltablerow[] = '<tr>';
                $htmltablerow[] = '<td>' . $lead[0] . '</td>';
                $htmltablerow[] = '<td>' . $lead[1] . '</td>';
                if (!in_array($lead[2], $fromnodes)) {
                    $htmltablerow[] = '<td class="endnode">' . $lead[2] . '</td>';
                }
                else {
                    $htmltablerow[] = '<td>' . $lead[2] . '</td>';
                }
            }
            $htmltablerow[] = '</tr>';
            $htmltable[] = implode('', $htmltablerow);
        }
        $htmltable[] = '</table>';
        return implode('', $htmltable);
    }
}

/* End of file ErrorCheckService.php */
/* Location: ./libraries/ErrorCheckService.php */
