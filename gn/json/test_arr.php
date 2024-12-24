<?php
$data = array(
    'key1' => `value1`,
    'key2' => 2,
    'key3' => array(
        'name' => 'yse',
        'age' => 105
    )
);

$json_data = json_encode($data);
$decode_object = json_decode($json_data);
$decode_array = json_decode($json_data, true);

var_dump($data);
echo "<br /><br />";
var_dump($json_data);
echo "<br /><br />";
var_dump($decode_object);
echo "<br /><br />";
var_dump($decode_array);
echo "<br /><br />";