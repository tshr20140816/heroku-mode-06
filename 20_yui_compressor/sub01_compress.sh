<?php

file=$1
ext=$2

mv ${file} ${file}.org
hash=$(sha512sum ${file}.org | awk '{print $1}')
php ./get_file.php ${file} ${hash}
if [ $? -ne 0 ]; then
  ./jre*/bin/java -jar ./yuicompressor-2.4.8.jar --type ${ext} -o ${file} ${file}.org
  php update.php ${file} ${hash}
else
  echo -e "pass\n"
fi

?>
