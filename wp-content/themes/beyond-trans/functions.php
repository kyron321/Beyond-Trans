<?php
//Register function files		
$functions = array_diff(scandir(get_template_directory() . '/functions'), array('.', '..', '.DS_Store'));
foreach ($functions as $function) {
    include('functions/' . $function);
}
