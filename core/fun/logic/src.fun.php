<?php

function src($website, $path){

    $tmp = explode('.', $path);

    return $website . '/' . implode('/', $tmp);
}