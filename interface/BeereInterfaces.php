<?php
/**
 * Created by PhpStorm.
 * User: Kazeem Olanipekun <kezyolanipekun@gmail.com>
 * Date: 12/12/2016
 * Time: 06:35 PM
 */

interface BeereInterfaces
{
public function save($table_name,array $data):string;
public function delete($table_name,array $data,$logic):string;
public function update($table_name, array $data, array $sets,$logic):string;
public function validate($table_name,array $data,$logic):string;
public function list($table_name,array $data,$logic):string;
public function getAll($table_name,array $data,$logic):string;
public function getADataByParam($table_name,array $data):string;
public function getByRole($table_name,array $data,$logic):string;
public function saveMultiple($table_name,array $data):string;
public function updateAll($table_name, array $data,array $sets):string;
public function listByLimit($table_name,array $data,$logic,$page,$limit):string;
public function getLastIndex($table_name,array $data,$logic):string;
}