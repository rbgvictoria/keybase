<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Citation {
    
    public function createReference($source) {
        $ret = '';
        if ($source->author && $source->publication_year && $source->title) {
            $ret .= $source->author . '(' . $source->publication_year . '). ';
        }
        return $ret;
    }
    
    public function createCitation($source) {
        $ret = '';
        if ($source->author && $source->publication_year && $source->title) {
            $ret .= '<b>' . $source->author . '</b> (' . $source->publication_year . '). ';
            if ($source->journal) {
                $ret .= $source->title . '. <i>' . $source->journal . '</i>';
                if ($source->series)
                    $ret .= ', ser. ' . $source->series;
                $ret .= ' <b>' . $source->volume . '</b>';
                if ($source->part) 
                    $ret .= '(' . $source->part . ')';
                $ret .= ':' . $source->page . '.';
            }
            elseif ($source->in_title) {
                $ret .= $source->title . '. In: ';
                if ($source->in_author) 
                    $ret .= $source->in_author . ', ';
                $ret .= '<i>' . $source->in_title . '</i>';
                if ($source->volume) 
                    $ret .= ' <b>' . $source->volume . '</b>';
                if ($source->page)
                    $ret .= ', pp. ' . $source->page;
                $ret .= '.';
                if ($source->publisher) {
                    $ret .= ' ' . $source->publisher;
                    if ($source->place_of_publication)
                        $ret .= ', ';
                    else
                        $ret .= '.';
                }
                if ($source->place_of_publication)
                    $ret .= ' ' . $source->place_of_publication . '.';
            }
            else {
                $ret .= '<i>' . $source->title . '</i>.';
                if ($source->publisher) {
                    $ret .= ' ' . $source->publisher;
                    if ($source->place_of_publication)
                        $ret .= ', ';
                    else
                        $ret .= '.';
                }
                if ($source->place_of_publication)
                    $ret .= ' ' . $source->place_of_publication . '.';
            }
        }
        return $ret;
    }
}

/* End of file Citation.php */
/* Location: ./libraries/Citation.php */
