<?php

if (!isset($argv[1]))
    exit('No key ID passed');

require_once('pdoconnect.php');

$select = "SELECT k.Url, m.FileName, m.OriginalFileName
    FROM `keys` k
    JOIN media m ON k.KeysID=m.KeysID
    WHERE k.KeysID=$argv[1]";

//echo $select . "\n\n";

$query = $db->query($select);
while ($row = $query->fetch(PDO::FETCH_OBJ)) {
    $extension = pathinfo($row->OriginalFileName, PATHINFO_EXTENSION);
    $url = pathinfo($row->Url, PATHINFO_DIRNAME) . '/' . substr($row->OriginalFileName, strpos($row->OriginalFileName, '/'));
    
    echo $url . "\n";
    switch ($extension) {
        case 'jpg':
        case 'JPG':
        case 'JPEG':
            $image = imagecreatefromjpeg($url);
            imagejpeg($image, '../../images/' . $row->FileName);
            break;

        case 'gif':
        case 'GIF':
            $image = imagecreatefromgif($url);
            imagegif($image, '../../images/' . $row->FileName);
            break;

        case 'png':
        case 'PNG':
            $image = imagecreatefrompng($url);
            imagepng($image, '../../images/' . $row->FileName);
            break;

        default:
            break;
    }
}

?>
