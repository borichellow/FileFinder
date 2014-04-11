<?php
print "---------------------------------\n
            !!! END !!!
    \n Full time of work: ".(int)(microtime(true) - $time). " seconds\n  "
    .count($ID_deposit_shutter)." files from ".count($Pre['items']). " were found\n
    \n---------------------------------\n";