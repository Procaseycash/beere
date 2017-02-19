<?php
/** 
 * Created by PhpStorm.
 * User: Kazeem Olanipekun  <kezyolanipekun@gmail.com>
 * Date: 12/12/2016
 * Time: 06:05 PM
 */
require_once("../utils/RestfulResponse.php");
require_once("../utils/Connection.php");
require_once("BeereInterfaces.php");

class BeereOperations extends Connection  implements BeereInterfaces
{
    /**
     * @param $table_name
     * @param $data
     * @return string
     * This is used to save data
     */
    public function save($table_name, array $data):string
    {
        // TODO: Implement save() method.
        if(empty($data)) return (new RestfulResponse(400, 'No Data Created',$data,0))->expose();

        $query="insert Ignore into {$table_name} (";
        $content='';
        $count=0;
        foreach ($data as $key => $value) {
            $value= is_array($value)?'['.$this->connection->real_escape_string(stripslashes(implode(',',$value))).']':$this->connection->real_escape_string(stripslashes($value));
            if($value==null) continue;
            if($count==0) {
                $content .=$key;
                ++$count;
            }else{
                $content.=",{$key}";
                ++$count;
            }
        }
        $content.=") values(";
        $count=0;
        foreach ($data as $key => $value) {
            $value= is_array($value)?'['.$this->connection->real_escape_string(stripslashes(implode(',',$value))).']':$this->connection->real_escape_string(stripslashes($value));
            if($value==null) continue;
            if($count==0) {
                $content .="'{$value}'";
                ++$count;
            }else{
                $content.=",'{$value}'";
                ++$count;
            }
        }
        $content.=")";
        $query.=$content;
        //  echo $query;
        if($this->connection->query($query) && $this->connection->affected_rows>0){
            if(isset($data['key_fetch']))
                return $this->getADataByParam($table_name,array('key_fetch'=>$data['key_fetch']));
            else
                return(
                (new RestfulResponse(200, 'Saved Successfully',$data,1))->expose()
                );

        }else{
            return(
            (new RestfulResponse(400, 'Failed to Save',$data,0))->expose()
            );
        }
    }

    /**
     * @param $table_name
     * @param $data
     * @param $logic
     * @return string
     * This is used to list all datas
     */
    public function list($table_name, array $data,$logic='&&'):string
    {
        // TODO: Implement list() method.
        $query = "Select * from {$table_name}";
        if (!empty($data)){
            $query = "Select * from {$table_name} where ";
            $content = '';
            $count = 0;
            foreach ($data as $key => $value) {
                $value= is_array($value)?'['.$this->connection->real_escape_string(stripslashes(implode(',',$value))).']':$this->connection->real_escape_string(stripslashes($value));
                if ($value == null) continue;
                if ($count == 0) {
                    $content .= $key . " = '{$value}' ";
                    ++$count;
                } else {
                    $content .= " {$logic} " . $key . " = '{$value}' ";
                    ++$count;
                }
            }
            $query .= $content;
        }
        //echo $query;
        if($fetch=($this->connection->query($query))) {
            if($this->connection->affected_rows>0) {
                $result=  $this->implodeListDataToListArray($fetch->fetch_all(MYSQLI_ASSOC));
                return (
                (new RestfulResponse(200, 'Information Fetched Successfully',$result , 1))->expose()
                );
            }
            else   return(
            (new RestfulResponse(400, 'Failed to List',$data,0))->expose()
            );
        }
        else{
            return(
            (new RestfulResponse(400, 'Failed to List',$data,0))->expose()
            );
        }
    }

    /**
     * @param $table_name
     * @param $data
     * @param $logic
     * @return string
     * This is used to delete data by value passed
     */
    public function delete($table_name, array $data,$logic='&&'):string
    {
        // TODO: Implement delete() method.
        if (empty($data)) return (new RestfulResponse(400, 'No Data Passed',$data,0))->expose();

        $query = "delete from {$table_name} where ";
        $content = '';
        $count = 0;
        foreach ($data as $key => $value) {
            $value= is_array($value)?'['.$this->connection->real_escape_string(stripslashes(implode(',',$value))).']':$this->connection->real_escape_string(stripslashes($value));
            if($value==null) continue;
            if ($count == 0) {
                $content .= $key . " = '{$value}' ";
                ++$count;
            } else {
                $content .= " {$logic} " . $key . " LIKE '{$value}' ";
                ++$count;
            }
        }
        $query .= $content;
        $this->connection->query($query);
        if($this->connection->affected_rows>0) {
            return(
            (new RestfulResponse(200, 'Deleted Successfully', $data,1))->expose()
            );
        }
        else{
            return(
            (new RestfulResponse(400, 'Failed to Delete', $data,0))->expose()
            );
        }
    }

    /**
     * @param $table_name
     * @param $data
     * @param $sets
     * @param $logic
     * @return string
     * This is used to update a particular data and return the updated data synchronously.
     */
    public function update($table_name, array $data,array $sets,$logic='&&'):string
    {
        // TODO: Implement update() method.
        if (empty($sets)) return (new RestfulResponse(400, 'No Set Data Passed',$sets,0))->expose();

        $query = "Update {$table_name} set ";
        $content = '';
        $count = 0;
        foreach ($sets as $key => $set) {
            if ($set === null) continue;
            $set= is_array($set)?'['.$this->connection->real_escape_string(stripslashes(implode(',',$set))).']':$this->connection->real_escape_string($set);
            if ($count == 0) {
                $content .=$key."='{$set}'";
                ++$count;
            }else{
                $content .=",".$key."='{$set}'";
                ++$count;
            }
        }
        if(!empty($data)) {
            $content .= " where ";
            $count = 0;
            foreach ($data as $key => $value) {
                if ($value === null) continue;
                $value= is_array($value)?'['.$this->connection->real_escape_string(stripslashes(implode(',',$value))).']':$this->connection->real_escape_string(stripslashes($value));
                if ($count == 0) {
                    $content .= $key . " = '{$value}' ";
                    ++$count;
                } else {
                    $content .= " {$logic} " . $key . " = '{$value}' ";
                    ++$count;
                }
            }
        }
        $query .= $content;
        if($this->connection->query($query)) {
            return $this->getADataByParam($table_name,$data);
        }
        else{
            return(
            (new RestfulResponse(400, 'Failed to Update', $data,1))->expose()
            );
        }
    }

    /**
     * @param $table_name
     * @param $data
     * @param $sets
     * @return string
     * This is used to update multiple data and return list of updated data synchronously
     */
    public function updateAll($table_name, array $data, array $sets):string
    {
        // TODO: Implement update() method.
        if (empty($sets)) return (new RestfulResponse(400, 'No Set Data Passed',$sets,0))->expose();

        $query = "Update {$table_name} set ";
        $content = '';
        $count = 0;
        foreach ($sets as $key => $set) {
            if ($set === null) continue;
            $set= is_array($set)?'['.$this->connection->real_escape_string(stripslashes(implode(',',$set))).']':$this->connection->real_escape_string($set);
            if ($count == 0) {
                $content .=$key."='{$set}'";
                ++$count;
            }else{
                $content .=",".$key."='{$set}'";
                ++$count;
            }
        }
        if(!empty($data)) {
            $content .= " Where ";
            $whereContent=implode(',',$data);
            $content.=" id in (".$whereContent.")";
        }
        $query .= $content;
        if($this->connection->query($query)) {
            return(
            (new RestfulResponse(200, 'Update All data Successfully', $data,1))->expose()
            );
        }
        else{
            return(
            (new RestfulResponse(400, 'Failed to Update All data', $data,0))->expose()
            );
        }
    }

    /**
     * @param $table_name
     * @param $data
     * @param $logic
     * @return string
     * This is used to list any related data searched for.
     */
    public function getAll($table_name, array $data,$logic='&&'):string
    {
        // TODO: Implement get() method.
        $query = "Select * from {$table_name}";
        if (!empty($data)) {
            $query = "Select * from {$table_name} where ";
            $content = '';
            $count = 0;
            foreach ($data as $key => $value) {
                $value= is_array($value)?'['.$this->connection->real_escape_string(stripslashes(implode(',',$value))).']':$this->connection->real_escape_string(stripslashes($value));
                if ($value == null) continue;
                if ($count == 0) {
                    $content .= $key . " LIKE '%{$value}%' ";
                    ++$count;
                } else {
                    $content .= " {$logic} " . $key . " LIKE '%{$value}%' ";
                    ++$count;
                }
            }
            $query .= $content;
        }
        if($fetch=($this->connection->query($query))) {
            if ($this->connection->affected_rows > 0){
                $result=  $this->implodeListDataToListArray($fetch->fetch_all(MYSQLI_ASSOC));
                return (
                (new RestfulResponse(200, 'Get all Successfully', $result, 1))->expose()
                );
            }
            else return(
            (new RestfulResponse(400, 'Failed to get all', $data,0))->expose()
            );
        }
        else{
            return(
            (new RestfulResponse(400, 'Encountered fatal error', $data,0))->expose()
            );
        }
    }

    /**
     * @param $table_name
     * @param $data
     * @return string
     * This is used to get data by single value passed
     */
    public function getADataByParam($table_name, array $data):string
    {
        // TODO: Implement get() method.
        if (empty($data)) return (new RestfulResponse(400, 'No Data Passed',$data,0))->expose();

        $query = "Select * from {$table_name} where ";
        $content = '';
        $count = 0;
        foreach ($data as $key => $value) {
            $value= is_array($value)?'['.$this->connection->real_escape_string(stripslashes(implode(',',$value))).']':$this->connection->real_escape_string(stripslashes($value));
            if($value==null) continue;
            if ($count == 0) {
                $content .= $key . " = '{$value}' ";
                ++$count;
            }else {
                $content .= " && " . $key . " = '{$value}' ";
                ++$count;
            }
        }
        $query .= $content;
        if($fetch=($this->connection->query($query))) {
            if($this->connection->affected_rows>0) {
                $result = $this->implodeDataToArray($fetch->fetch_assoc());
                return (
                (new RestfulResponse(200, 'Fetched Successfully', $result, 1))->expose()
                );
            }
            else return(
            (new RestfulResponse(400, 'Failed to fetched Information', $data,0))->expose()
            );
        }
        else{
            return(
            (new RestfulResponse(400, 'Encountered Error', $data,0))->expose()
            );
        }
    }

    /**
     * @param $table_name
     * @param $data
     * @param $logic
     * @return string
     * This is used to get users based on role passed
     */
    public function getByRole($table_name, array $data,$logic='&&'):string
    {
        // TODO: Implement get() method.
        if (empty($data)) return (new RestfulResponse(400, 'No Data Passed',$data,0))->expose();

        $query = "Select * from {$table_name} where ";
        $content = '';
        $count = 0;
        foreach ($data as $value) {
            if($value==null) continue;
            $value= is_array($value)?'['.$this->connection->real_escape_string(stripslashes(implode(',',$value))).']':$this->connection->real_escape_string(stripslashes($value));
            if ($count == 0) {
                $content .= 'roles' . " LIKE '%{$value}%' ";
                ++$count;
            } else {
                $content .= " {$logic} " . 'roles' . " LIKE '%{$value}%' ";
                ++$count;
            }
        }
        $query .= $content;
        if($fetch=($this->connection->query($query))) {
            if ($this->connection->affected_rows > 0){
                $result=$this->implodeListDataToListArray($fetch->fetch_all(MYSQLI_ASSOC));
                return (
                (new RestfulResponse(200, 'Fetched by Roles Successfully', $result, 1))->expose()
                );
            }
            else return(
            (new RestfulResponse(400, 'Failed to fetch by roles', $data,0))->expose()
            );
        }
        else{
            return(
            (new RestfulResponse(400, 'Encountered Fatal Error', $data,0))->expose()
            );
        }
    }

    /**
     * @param $table_name
     * @param $data
     * @param $logic
     * @return string
     * This is used to validate the existence of a user by return its object
     */
    public function validate($table_name, array $data, $logic='&&'):string
    {
        // TODO: Implement validate() method.
        // TODO: Implement get() method.
        if (empty($data)) return (new RestfulResponse(400, 'No Data Passed',$data,0))->expose();

        $query = "Select * from {$table_name} where ";
        $content = '';
        $count = 0;
        foreach ($data as $key => $value) {
            $value= is_array($value)?'['.$this->connection->real_escape_string(stripslashes(implode(',',$value))).']':$this->connection->real_escape_string(stripslashes($value));
            if ($value == null) continue;
            if ($count == 0) {
                $content .= $key . " = '{$value}' ";
                ++$count;
            } else {
                $content .= " {$logic} " . $key . " = '{$value}' ";
                ++$count;
            }
        }
        $query .= $content;
        if($fetch=($this->connection->query($query))) {
            if($this->connection->affected_rows>0) {
                $result = $this->implodeDataToArray($fetch->fetch_assoc());
                return (
                (new RestfulResponse(200, 'Validated Successfully', $result, 1))->expose()
                );
            }
            else return(
            (new RestfulResponse(400, 'Failed to Validate', $data,0))->expose()
            );
        }
        else{
            return(
            (new RestfulResponse(400, 'Encountered Error while validating', $data,0))->expose()
            );
        }
    }

    /**
     * @param $table_name
     * @param $data
     * @return string
     * This is used to save multiple record at a time synchronously.
     */
    public function saveMultiple($table_name,array $data):string
    {

        if (empty($data)) return (new RestfulResponse(400, 'No Data Passed',$data,0))->expose();

        // TODO: Implement save() method.
        $query="insert Ignore  into {$table_name} (";
        $content='';
        $count=0;
        foreach ($data[0] as $key => $value) {
            $value= is_array($value)?'['.$this->connection->real_escape_string(stripslashes(implode(',',$value))).']':$this->connection->real_escape_string(stripslashes($value));
            if($value==null) continue;
            if($count==0) {
                $content .=$key;
                ++$count;
            }else{
                $content.=",{$key}";
                ++$count;
            }
        }
        $content.=") values(";
        $lastDataIndex=0;
        $dataLen=count($data);
        foreach($data as $k=>$item) {
            $count=0;
            foreach ($item as $key => $value) {
                $value= is_array($value)?'['.$this->connection->real_escape_string(stripslashes(implode(',',$value))).']':$this->connection->real_escape_string(stripslashes($value));
                if ($value == null) continue;
                if ($count == 0) {
                    $content .= "'{$value}'";
                    ++$count;
                } else {
                    $content .= ",'{$value}'";
                    ++$count;
                }
            }
            if($dataLen==1) { $content.=")"; break;}
            if($dataLen-1>$lastDataIndex)  $content .= "),(";
            if($dataLen-1==$lastDataIndex) $content.=")";

            $lastDataIndex++;
        }
        $query.=$content;
        //echo $query;
        //return;
        if($this->connection->query($query) && $this->connection->affected_rows>0){
            if(isset($data[0]['key_fetch']))
                return $this->list($table_name, array('key_fetch'=>$data[0]['key_fetch']),'||');
            else
                return(
                (new RestfulResponse(200, 'Multiple Data Saved Successfully',$data,1))->expose()
                );

        }else{
            return(
            (new RestfulResponse(400, 'Failed to Save',$data,0))->expose()
            );
        }
    }

    /**
     * @param $table_name
     * @param $data
     * @param $logic
     * @param $page
     * @param int $limit
     * @return string
     * This is used to list data based on speculated limit with offset and condition passed
     */
    public function listByLimit($table_name,array $data,$logic='&&',$page=1,$limit=200):string
    {
        // TODO: Implement list() method.
        $page=(int)$page;
        $total=0;
        if($page==1) {
            $getTotal = "Select COUNT(DISTINCT id) as totalLength from {$table_name}";
            if (!empty($data)){
                $getTotal = "Select COUNT(DISTINCT id) as totalLength from {$table_name} where ";
                $content = '';
                $count = 0;
                foreach ($data as $key => $value) {
                    $value= is_array($value)?'['.$this->connection->real_escape_string(stripslashes(implode(',',$value))).']':$this->connection->real_escape_string(stripslashes($value));
                    if ($value == null) continue;
                    if ($count == 0) {
                        $content .= $key . " LIKE '%{$value}%' ";
                        ++$count;
                    } else {
                        $content .= " {$logic} " . $key . " LIKE '%{$value}%' ";
                        ++$count;
                    }
                }
                $getTotal .= $content;
            }
            $myTotal = $this->connection->query($getTotal);
            $total=(int)($myTotal->fetch_assoc()['totalLength']);
        }
        $offset=($page-1)*$limit;
        $query = "Select * from {$table_name} LIMIT {$limit}  OFFSET {$offset} ";
        if (!empty($data)){
            $query = "Select * from {$table_name} where ";
            $content = '';
            $count = 0;
            foreach ($data as $key => $value) {
                $value= is_array($value)?'['.$this->connection->real_escape_string(stripslashes(implode(',',$value))).']':$this->connection->real_escape_string(stripslashes($value));
                if ($value == null) continue;
                if ($count == 0) {
                    $content .= $key . " LIKE '%{$value}%' ";
                    ++$count;
                } else {
                    $content .= " {$logic} " . $key . " LIKE '%{$value}%' ";
                    ++$count;
                }
            }
            $content.=" LIMIT {$limit}  OFFSET {$offset} ";
            $query .= $content;
        }
        //echo $query; return;
        if($fetch=($this->connection->query($query))) {
            if($this->connection->affected_rows>0) {
                $result=  $this->implodeListDataToListArray($fetch->fetch_all(MYSQLI_ASSOC));
                //print_r($total);
                if($page==1) {
                    return (
                    (new RestfulResponse(200, 'Information Fetched Successfully', $result, 1, $total))->expose());
                }else{
                    return  ((new RestfulResponse(200, 'Information Fetched Successfully', $result, 1))->expose());
                }
            }
            else {
                $data=array(0);
                return (
                (new RestfulResponse(400, 'Failed to List', $data, 0))->expose()
                );
            }
        }
        else{
            $data=array(0);
            return(
            (new RestfulResponse(400, 'Failed to List',$data,0))->expose()
            );
        }
    }


    /**
     * @param $table_name
     * @param $data
     * @param $logic
     * @return string
     */
    public function getLastIndex($table_name,array $data,$logic='&&'):string
    {
        $query = "Select * from {$table_name} ORDER BY id DESC LIMIT 1 ";
        if (!empty($data)){
            $query = "Select * from {$table_name} where ";
            $content = '';
            $count = 0;
            foreach ($data as $key => $value) {
                $value= is_array($value)?'['.$this->connection->real_escape_string(stripslashes(implode(',',$value))).']':$this->connection->real_escape_string(stripslashes($value));
                if ($value == null) continue;
                if ($count == 0) {
                    $content .= $key . " = '{$value}' ";
                    ++$count;
                } else {
                    $content .= " {$logic} " . $key . " = '{$value}' ";
                    ++$count;
                }
            }
            $query .= $content;
            $query.=" ORDER BY id  DESC LIMIT 1";
        }
        if($fetch=($this->connection->query($query))) {
            $result = $this->implodeDataToArray($fetch->fetch_assoc());
            if(empty($result)) $result=array();
            return (
            (new RestfulResponse(200, 'Last Index data fetched successfully', $result, 0))->expose()
            );
        }
        else{
            $result=array();
            return(
            (new RestfulResponse(400, 'Encountered Error while getting last index data', $result,0))->expose()
            );
        }
    }


    /**
     * @param $item
     * @return mixed
     * This is used to check imploded array created during save operation back to array while selection operation is done with fetch_assoc/array... .
     */
    public function implodeDataToArray(array $item):array
    {
        foreach ($item as $key => &$value) {
            if(strpos($value,'[')===false && strrpos($value, ']')===false) continue; //skip the loop and continue
            $data_length=strlen($value);
            $value=substr($value, 1,$data_length-2);
            $value=explode(',', $value);
        }

        return $item;
    }

    /**
     * @param $data
     * @return mixed
     * This is used to check imploded array created during save operation back to array while selection operation is done with fetch_all method .
     */
    public function implodeListDataToListArray(array $data):array
    {
        foreach ($data as $index => &$item) {
            foreach ($item as $key => &$value) {
                if(strpos($value,'[')===false && strrpos($value, ']')===false) continue; //skip the loop and continue
                $data_length=strlen($value);
                $value=substr($value, 1,$data_length-2);
                $value=explode(',', $value);
            }
        }
        return $data;
    }
}