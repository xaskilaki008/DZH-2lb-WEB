<?php

interface DataRepository
{
    public function all();
    public function find($field, $value);
    public function save($data);
    public function delete($id);
    public static function createFromData($data);
}