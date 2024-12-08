#!/usr/bin/php
<?php
echo "use j329nish\n";
for ($level = 1; $level <= 5; $level++) {
$file = file("L$level.txt");
for ($i = 0; $i < count($file); $i += 3) {
$id = (int)rtrim($file[$i]);
$word = addslashes(rtrim($file[$i + 1]));
$meaning = addslashes(rtrim($file[$i + 2]));
echo "INSERT INTO svl5000(level,id,word,meaning) VALUES('$level', '$id', '$word','$meaning');\n";
}
}
?>