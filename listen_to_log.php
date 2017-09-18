<?php
//function follow($file)
//{
//    $size = 0;
//    while (true) {
//        clearstatcache();
//        $currentSize = filesize($file);
//        if ($size == $currentSize) {
//            usleep(100);
//            continue;
//        }
//        $fh = fopen($file, "r");
//        fseek($fh, $size);
//
//        while ($d = fgets($fh)) {
//            echo $d;
//            $pos = strpos($d,"Accepted");
//            if($pos !== false) {
//                echo "Found\n";
//            }
//        }
//        fclose($fh);
//        $size = $currentSize;
//    }
//}
//
//follow("/var/log/auth.log");

$handle = popen("tail -f /var/log/auth.log 2>&1", 'r');
while(!feof($handle)) {
    $buffer = fgets($handle);
    echo "$buffer\n";
    $pos = strpos($buffer,"Accepted");
    if($pos !== false) {
        echo "ssh connection found!\n";
    }
    flush();
}
pclose($handle);


?>