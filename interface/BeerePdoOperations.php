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

class BeerePdoOperations extends Connection implements BeereInterfaces
{

    public function __construct()
    {
        parent::__construct(self::CONNECTION_TYPE['pdo']); //this allow connection to pdo version
    }

    /**
     * @param $table_name
     * @param $data
     * @return string
     * This is used to save data --DONE
     */
    public function save($table_name, array $data):string
    {
        // TODO: Implement save() method.
        if (empty($data)) return (new RestfulResponse(400, 'No Data Created', $data, 0))->expose();

        $query = "insert Ignore into {$table_name} (";
        $content = '';
        $count = 0;
        $params=[];
        $passValue=[];
        foreach ($data as $key => $value) {
            $value = is_array($value) ? '[' . (stripslashes(implode(',', $value))) . ']' : (stripslashes($value));
            if ($value == null) continue;
            if ($count == 0) {
                $content .= $key;
                $params[]=$value;
                $passValue[] ='?';
                ++$count;
            } else {
                $content .= ",{$key}";
                $params[]=$value;
                $passValue[] ='?';
                ++$count;
            }
        }
        $content .= ") values( ";
        $content.= implode(', ', $passValue);
        $content .= " )";
        $query .= $content;
       $statement = $this->connection->prepare($query);
        try {
            if ($statement->execute($params) && $statement->rowCount()>0) {
                if (isset($data['key_fetch']))
                    return $this->getADataByParam($table_name, array('key_fetch' => $data['key_fetch']));
                else
                    return (
                    (new RestfulResponse(200, 'Saved Successfully', $data, 1))->expose()
                    );

            } else {
                return (
                (new RestfulResponse(400, 'Failed to Save', $data, 0))->expose()
                );
            }
        } catch (PDOException $e) {
            return (
            (new RestfulResponse(400, 'Unable to perform request with error message: ' . $e->getMessage(), $data, 0))->expose()
            );
        }
    }

    /**
     * @param $table_name
     * @param $data
     * @param $logic
     * @param $fields
     * @return string
     * This is used to list all data -DONE
     */
    public function list($table_name, array $data,$logic='&&',array $fields=['*']):string
    {
        // TODO: Implement list() method.
        $fields=$this->implodeFields($fields);
        $query = "Select {$fields} from {$table_name}";
        if (!empty($data)) {
            $query = "Select {$fields}  from {$table_name} where ";
            $content = '';
            $count = 0;
            $params=[];
            foreach ($data as $key => $value) {
                $value = is_array($value) ? '[' . (stripslashes(implode(',', $value))) . ']' : (stripslashes($value));
                if ($value == null) continue;
                if ($count == 0) {
                    $content .= $key . " = ? ";
                    $params[] = $value;
                    ++$count;
                } else {
                    $content .= " {$logic} " . $key . " = ? ";
                    $params[] = $value;
                    ++$count;
                }
            }
            $query .= $content;
        }
        //echo $query;
        try {
            $statement= $this->connection->prepare($query);
            $statement->execute($params);
                if ($statement->rowCount() > 0) {
                    $result = $this->implodeListDataToListArray($statement->fetchAll(PDO::FETCH_ASSOC));
                    return (
                    (new RestfulResponse(200, 'Information Fetched Successfully', $result, 1))->expose()
                    );
                } else   return (
                (new RestfulResponse(400, 'Failed to List', $data, 0))->expose()
                );

        } catch (PDOException $e) {
            return (
            (new RestfulResponse(400, 'Unable to perform request with error message: ' . $e->getMessage(), $data, 0))->expose()
            );
        }
    }

    /**
     * @param $table_name
     * @param $data
     * @param $logic
     * @return string
     * This is used to delete data by value passed --DONE
     */
    public function delete($table_name, array $data,$logic='&&'):string
    {
        // TODO: Implement delete() method.
        if (empty($data)) return (new RestfulResponse(400, 'No Data Passed', $data, 0))->expose();

        $query = "delete from {$table_name} where ";
        $content = '';
        $count = 0;
        $params=[];
        foreach ($data as $key => $value) {
            $value = is_array($value) ? '[' . (stripslashes(implode(',', $value))) . ']' : (stripslashes($value));
            if ($value == null) continue;
            if ($count == 0) {
                if($key=='id' || strpos($key,'_id')!==false)
                    $content .= $key . " = ? ";
                else
                    $content .= $key . " LIKE ? ";

                    $params[] = $value;
                    ++$count;
            } else {
                if($key=='id' || strpos($key,'_id')!==false)
                    $content .= " {$logic} " . $key . " = ? ";
                else
                    $content .= " {$logic} " . $key . " LIKE ? ";

                    $params[] = $value;
                    ++$count;
            }
        }
        $query .= $content;
        try {
            $statement = $this->connection->prepare($query);
            $statement->execute($params);
            if ($statement->rowCount() > 0) {
                return (
                (new RestfulResponse(200, 'Deleted Successfully', $data, 1))->expose()
                );
            } else {
                return (
                (new RestfulResponse(400, 'Failed to Delete', $data, 0))->expose()
                );
            }
        } catch (PDOException $e) {
            return (
            (new RestfulResponse(400, 'Unable to perform request with error message: ' . $e->getMessage(), $data, 0))->expose()
            );
        }
    }

    /**
     * @param $table_name
     * @param $data
     * @param $sets
     * @param $logic
     * @return string
     * This is used to update a particular data and return the updated data synchronously. --DONE
     */
    public function update($table_name, array $data,array $sets,$logic='&&'):string
    {
        // TODO: Implement update() method.
        if (empty($sets)) return (new RestfulResponse(400, 'No Set Data Passed', $sets, 0))->expose();

        $query = "Update {$table_name} set ";
        $content = '';
        $count = 0;
        $params=[];
        foreach ($sets as $key => $set) {
            if ($set === null) continue;
            $set = is_array($set) ? '[' . (stripslashes(implode(',', $set))) . ']' : ($set);
            if ($count == 0) {
                $content .= $key . "= ?";
                $params[] = $set;
                ++$count;
            } else {
                $content .= "," . $key . "= ?";
                $params[] = $set;
                ++$count;
            }
        }
        if (!empty($data)) {
            $content .= " where ";
            $count = 0;
            foreach ($data as $key => $value) {
                if ($value === null) continue;
                $value = is_array($value) ? '[' . (stripslashes(implode(',', $value))) . ']' : (stripslashes($value));
                if ($count == 0) {
                    $content .= $key . " = ?";
                    $params[] = $value;
                    ++$count;
                } else {
                    $content .= " {$logic} " . $key . " = ? ";
                    $params[] = $value;
                    ++$count;
                }
            }
        }
        $query .= $content;
        try {
            $statement=$this->connection->prepare($query);
            $statement->execute($params);

            if ($statement->rowCount()>0) {
                return $this->getADataByParam($table_name, $data);
            } else {
                return (
                (new RestfulResponse(400, 'Failed to Update', $data, 1))->expose()
                );
            }
        } catch (PDOException $e) {
            return (
            (new RestfulResponse(400, 'Unable to perform request with error message: ' . $e->getMessage(), $data, 0))->expose()
            );
        }
    }

    /**
     * @param $table_name
     * @param array $data
     * @param array $sets
     * @param string $logic
     * @return string
     * This is used to update multiple data and return list of updated data asynchronously --DONE
     */
    public function updateAll($table_name, array $data, array $sets,  $logic='&&'):string
    {
        // TODO: Implement update() method.
        if (empty($sets)) return (new RestfulResponse(400, 'No Set Data Passed', $sets, 0))->expose();

        $query = "Update {$table_name} set ";
        $content = '';
        $count = 0;
        $params =[];
        foreach ($sets as $key => $set) {
            if ($set === null) continue;
            $set = is_array($set) ? '[' . (stripslashes(implode(',', $set))) . ']' : ($set);
            if ($count == 0) {
                $content .= $key . "= ?";
                $params[] = $set;
                ++$count;
            } else {
                $content .= "," . $key . "= ?";
                $params[] = $set;
                ++$count;
            }
        }
        if (!empty($data)) {
            $content .= " Where ";
            $count = 0;
            foreach ($data as $key => $value) {
                if ($value === null) continue;
                $in=[];
                $ops=is_array($value)?1:0; // denote if array to use in operator
                if(is_array($value)) {
                  foreach ($value as $item){
                      $params[] = $item;
                      $in[] = '?';
                  }
                }
                if ($count == 0) {
                    if ($ops==0) {
                        $content .= $key . " like ? ";
                        $params[] = $value;
                    }
                    else {
                        $in = implode(',',$in);
                        $content .= $key . " in ({$in}) ";
                    }
                    ++$count;
                } else {
                    if ($ops==0) {
                        $content .= " {$logic} " . $key . " like ? ";
                    }
                    else {
                        $in = implode(',',$in);
                        $content .= " {$logic} " . $key . " in ({$in}) ";
                    }
                    ++$count;
                }
            }
            /* $whereContent=implode(',',$data);
             $content.=" id in (".$whereContent.")";*/
        }
        $query .= $content;
        try {
            $statement = $this->connection->prepare($query);
            $statement->execute($params);
            if ($statement->rowCount()>0) {
                return (
                (new RestfulResponse(200, 'Update All data Successfully', $data, 1))->expose()
                );
            } else {
                return (
                (new RestfulResponse(400, 'Failed to Update All data', $data, 0))->expose()
                );
            }
        } catch (PDOException $e) {
            return (
            (new RestfulResponse(400, 'Unable to perform request with error message: ' . $e->getMessage(), $data, 0))->expose()
            );
        }
    }

    /**
     * @param $table_name
     * @param $data
     * @param $logic
     * @param $fields
     * @return string
     * This is used to list any related data searched for.--DONE
     */
    public function getAll($table_name, array $data,$logic='&&',array $fields=['*']):string
    {
        // TODO: Implement getAll() method.
        $fields=$this->implodeFields($fields);
        $query = "Select {$fields} from {$table_name}";
        if (!empty($data)) {
            $query = "Select {$fields}  from {$table_name} where ";
            $content = '';
            $count = 0;
            $params = [];
            foreach ($data as $key => $value) {
                $value = is_array($value) ? '[' . (stripslashes(implode(',', $value))) . ']' : (stripslashes($value));
                if ($value == null) continue;
                if ($count == 0) {
                        if($key=='id' || strpos($key,'_id')!==false)
                        $content .= $key . " = ?  ";
                        else {
                            $content .= $key . " LIKE CONCAT ('%', ?, '%') ";
                        }
                        $params[] = $value;
                        ++$count;
                } else {
                        if($key=='id' || strpos($key,'_id')!==false)
                            $content .= " {$logic} " . $key . " = ? ";
                        else {
                            $content .= " {$logic} " . $key . " LIKE CONCAT ('%', ?, '%')";
                        }
                        $params[] = $value;
                        ++$count;
                }
            }
           $query .= $content;
        }
        try {
                $statement= $this->connection->prepare($query);
                $statement->execute($params);
                if ($statement->rowCount() > 0) {
                    $result = $this->implodeListDataToListArray($statement->fetchAll(PDO::FETCH_ASSOC));
                    return (
                    (new RestfulResponse(200, 'Get all Successfully', $result, 1))->expose()
                    );
                } else return (
                (new RestfulResponse(400, 'Failed to get all', $data, 0))->expose()
                );
        } catch (PDOException $e) {
            return (
            (new RestfulResponse(400, 'Unable to perform request with error message: ' . $e->getMessage(), $data, 0))->expose()
            );
        }
    }

    /**
     * @param $table_name
     * @param $data
     * @param $fields
     * @return string
     * This is used to get data by single value passed --DONE
     */
    public function getADataByParam($table_name, array $data,array $fields=['*']):string
    {
        // TODO: Implement getADataByParam() method.
        $fields=$this->implodeFields($fields);

        if (empty($data)) return (new RestfulResponse(400, 'No Data Passed', $data, 0))->expose();

        $query = "Select {$fields} from {$table_name} where ";
        $content = '';
        $count = 0;
        $params=[];
        foreach ($data as $key => $value) {
            $value = is_array($value) ? '[' . (stripslashes(implode(',', $value))) . ']' : (stripslashes($value));
            if ($value == null) continue;
            if ($count == 0) {
                $content .= $key . " = ?";
                $params[]=$value;
                ++$count;
            } else {
                $content .= " && " . $key . " = ? ";
                $params[]=$value;
                ++$count;
            }
        }
        $query .= $content;
        $statement = $this->connection->prepare($query);
        try {
            $statement->execute($params);
                if ($statement->rowCount() > 0) {
                    $result = $this->implodeDataToArray($statement->fetch(PDO::FETCH_ASSOC));
                    return (
                    (new RestfulResponse(200, 'Fetched Successfully', $result, 1))->expose()
                    );
                } else return (
                (new RestfulResponse(400, 'Failed to fetched Information', $data, 0))->expose()
                );
        } catch (PDOException $e) {
            return (
            (new RestfulResponse(400, 'Unable to perform request with error message: ' . $e->getMessage(), $data, 0))->expose()
            );
        }
    }

    /**
     * @param $table_name
     * @param $data
     * @param $logic
     * @param $fields
     * @return string
     * This is used to get users based on role passed --DONE
     */
    public function getByRole($table_name, array $data,$logic='&&',array $fields=['*']):string
    {
        // TODO: Implement getByRole() method.
        $fields=$this->implodeFields($fields);
        if (empty($data)) return (new RestfulResponse(400, 'No Data Passed', $data, 0))->expose();

        $query = "Select {$fields} from {$table_name} where ";
        $content = '';
        $count = 0;
        $params = [];
        foreach ($data as $value) {
            if ($value == null) continue;
            $value = is_array($value) ? '[' . (stripslashes(implode(',', $value))) . ']' : (stripslashes($value));
            if ($count == 0) {
                $content .= 'roles' . " LIKE CONCAT('%', ?, '%') ";
                $params[] =$value;
                ++$count;
            } else {
                $content .= " {$logic} " . 'roles' . " LIKE CONCAT('%', ?, '%') ";
                $params[] =$value;
                ++$count;
            }
        }
        $query .= $content;
        try {
            $statement= $this->connection->prepare($query);
            $statement->execute($params);
            if ($statement->rowCount() > 0) {
                    $result = $this->implodeListDataToListArray($statement->fetchAll(PDO::FETCH_ASSOC));
                    return (
                    (new RestfulResponse(200, 'Fetched by Roles Successfully', $result, 1))->expose()
                    );
                } else return (
                (new RestfulResponse(400, 'Failed to fetch by roles', $data, 0))->expose()
                );
        } catch (PDOException $e) {
            return (
            (new RestfulResponse(400, 'Unable to perform request with error message: ' . $e->getMessage(), $data, 0))->expose()
            );
        }
    }

    /**
     * @param $table_name
     * @param $data
     * @param $logic
     * @param $fields
     * @return string
     * This is used to validate the existence of a user by return its object --DONE
     */
    public function validate($table_name, array $data, $logic='&&',array $fields=['*']):string
    {
        // TODO: Implement validate() method.
        $fields=$this->implodeFields($fields);
        if (empty($data)) return (new RestfulResponse(400, 'No Data Passed', $data, 0))->expose();

        $query = "Select {$fields} from {$table_name} where ";
        $content = '';
        $count = 0;
        $params= [];
        foreach ($data as $key => $value) {
            $value = is_array($value) ? '[' . (stripslashes(implode(',', $value))) . ']' : (stripslashes($value));
            if ($value == null) continue;
            if ($count == 0) {
                $content .= $key . " = ? ";
                $params[] = $value;
                ++$count;
            } else {
                $content .= " {$logic} " . $key . " = ? ";
                $params[] = $value;
                ++$count;
            }
        }
        $query .= $content;
        try {
                $statement = $this->connection->prepare($query);
                $statement->execute($params);
                if ($statement->rowCount() > 0) {
                    $result = $this->implodeDataToArray($statement->fetch(PDO::FETCH_ASSOC));
                    return (
                    (new RestfulResponse(200, 'Validated Successfully', $result, 1))->expose()
                    );
                } else return (
                (new RestfulResponse(400, 'Failed to Validate', $data, 0))->expose()
                );

        } catch (PDOException $e) {
            return (
            (new RestfulResponse(400, 'Unable to perform request with error message: ' . $e->getMessage(), $data, 0))->expose()
            );
        }
    }

    /**
     * @param $table_name
     * @param $data
     * @return string
     * This is used to save multiple record at a time synchronously. --DONE
     */
    public function saveMultiple($table_name,array $data):string
    {
        // TODO: Implement saveMultiple() method.
        if (empty($data)) return (new RestfulResponse(400, 'No Data Passed', $data, 0))->expose();

        // TODO: Implement save() method.
        $query = "insert Ignore  into {$table_name} (";
        $content = '';
        $count = 0;
        $params = [];
        foreach ($data[0] as $key => $value) {
            $value = is_array($value) ? '[' . (stripslashes(implode(',', $value))) . ']' : (stripslashes($value));
            if ($value == null) continue;
            if ($count == 0) {
                $content .= $key;
                ++$count;
            } else {
                $content .= ",{$key}";
                ++$count;
            }
        }
        $content .= ") values(";
        $lastDataIndex = 0;
        $dataLen = count($data);
        foreach ($data as $k => $item) {
            $count = 0;
            foreach ($item as $key => $value) {
                $value = is_array($value) ? '[' . (stripslashes(implode(',', $value))) . ']' : (stripslashes($value));
                if ($value == null) continue;
                if ($count == 0) {
                    $content .= " ?";
                    $params[] =$value;
                    ++$count;
                } else {
                    $content .= ",?";
                    $params[] =$value;
                    ++$count;
                }
            }
            if ($dataLen == 1) {
                $content .= ")";
                break;
            }
            if ($dataLen - 1 > $lastDataIndex) $content .= "),(";
            if ($dataLen - 1 == $lastDataIndex) $content .= ")";

            $lastDataIndex++;
        }
        $query .= $content;
        //echo $query;
        //return;
        try {
            $statement = $this->connection->prepare($query);
            $statement->execute($params);
            if ($statement->rowCount() > 0) {
                if (isset($data[0]['key_fetch']))
                    return $this->list($table_name, array('key_fetch' => $data[0]['key_fetch']), '||');
                else
                    return (
                    (new RestfulResponse(200, 'Multiple Data Saved Successfully', $data, 1))->expose()
                    );

            } else {
                return (
                (new RestfulResponse(400, 'Failed to Save', $data, 0))->expose()
                );
            }
        } catch (PDOException $e) {
            return (
            (new RestfulResponse(400, 'Unable to perform request with error message: ' . $e->getMessage(), $data, 0))->expose()
            );
        }
    }

    /**
     * @param $table_name
     * @param $data
     * @param $logic
     * @param $page
     * @param int $limit
     * @param $fields
     * @return string
     * This is used to list data based on speculated limit with offset and condition passed --DONE
*/
    public function listByLimit($table_name,array $data,$logic='&&',$page=1,$limit=200,array $fields=['*']):string
    {
        // TODO: Implement listByLimit() method.
        $fields=$this->implodeFields($fields);
        $page = (int)$page;
        $total = 0;
        if ($page == 1) {
            $params= [];
            $getTotal = "Select COUNT(DISTINCT id) as totalLength from {$table_name}";
            if (!empty($data)) {
                $getTotal = "Select COUNT(DISTINCT id) as totalLength from {$table_name} where ";
                $content = '';
                $count = 0;
                foreach ($data as $key => $value) {
                    $value = is_array($value) ? '[' . (stripslashes(implode(',', $value))) . ']' : (stripslashes($value));
                    if ($value == null) continue;
                    if ($count == 0) {

                        if($key=='id' || strpos($key,'_id')!==false)
                            $content .= $key . " = ? ";
                        else
                            $content .= $key . " LIKE CONCAT('%', ?, '%') ";

                        $params[] = $value;
                        ++$count;
                    } else {

                        if($key=='id' || strpos($key,'_id')!==false)
                            $content .= " {$logic} " . $key . " = ? ";
                        else
                            $content .= " {$logic} " . $key . " LIKE CONCAT('%', ?, '%') ";

                        $params[] = $value;
                        ++$count;
                    }
                }
                $getTotal .= $content;
            }
            $statement = $this->connection->prepare($getTotal);
            $statement->execute($params);
            $total = (int)($statement->fetch(PDO::FETCH_ASSOC)['totalLength']);
        }
        $offset = ($page - 1) * $limit;
        $query = "Select {$fields} from {$table_name} LIMIT {$limit}  OFFSET {$offset} ";
        $params= [];
        if (!empty($data)) {
            $query = "Select {$fields} from {$table_name} where ";
            $content = '';
            $count = 0;
            foreach ($data as $key => $value) {
                $value = is_array($value) ? '[' . (stripslashes(implode(',', $value))) . ']' : (stripslashes($value));
                if ($value == null) continue;
               if ($count == 0) {
                    if($key=='id' || strpos($key,'_id')!==false)
                        $content .= $key . " = ? ";
                    else
                        $content .= $key . " LIKE CONCAT('%', ?, '%') ";

                   $params[] = $value;
                   ++$count;
                } else {
                       if($key=='id' || strpos($key,'_id')!==false)
                            $content .= " {$logic} " . $key . " = ? ";
                       else
                            $content .= " {$logic} " . $key . " LIKE CONCAT('%', ?, '%') ";
                    $params[] = $value;
                    ++$count;
                }
            }
            $content .= " LIMIT {$limit}  OFFSET {$offset} ";
            $query .= $content;
        }
        //echo $query; return;
        try {
            $statement = $this->connection->prepare($query);
            $statement->execute($params);
            if ($statement->rowCount() > 0) {
                    $result = $this->implodeListDataToListArray($statement->fetchAll(PDO::FETCH_ASSOC));
                    //print_r($total);
                    if ($page == 1) {
                        return (
                        (new RestfulResponse(200, 'Information Fetched Successfully', $result, 1, $total))->expose());
                    } else {
                        return ((new RestfulResponse(200, 'Information Fetched Successfully', $result, 1))->expose());
                    }
                } else {
                    $data = array(0);
                    return (
                    (new RestfulResponse(400, 'Failed to List', $data, 0))->expose()
                    );
                }
        } catch (PDOException $e) {
            return (
            (new RestfulResponse(400, 'Unable to perform request with error message: ' . $e->getMessage(), $data, 0))->expose()
            );
        }
    }

    /**
     * @param $table_name
     * @param $data
     * @param $logic
     * @param $fields
     * @return string --DONE
     */
    public function getLastIndex($table_name,array $data,$logic='&&',array $fields=['*']):string
    {
        // TODO: Implement getLastIndex() method.
        $fields=$this->implodeFields($fields);
        $params = [];
        $query = "Select {$fields} from {$table_name} ORDER BY id DESC LIMIT 1 ";
        if (!empty($data)) {
            $query = "Select {$fields} from {$table_name} where ";
            $content = '';
            $count = 0;
            foreach ($data as $key => $value) {
                $value = is_array($value) ? '[' . (stripslashes(implode(',', $value))) . ']' : (stripslashes($value));
                if ($value == null) continue;
                if ($count == 0) {
                    $content .= $key . " = ?";
                    $params[] =$value;
                    ++$count;
                } else {
                    $content .= " {$logic} " . $key . " = ?";
                    $params[] =$value;
                    ++$count;
                }
            }
            $query .= $content;
            $query .= " ORDER BY id  DESC LIMIT 1";
        }
        try {
            $statement = $this->connection->prepare($query);
            $statement->execute($params);
            if ($statement->rowCount() > 0) {
                $result = $this->implodeDataToArray($statement->fetch(PDO::FETCH_ASSOC));
                if (empty($result)) $result = array();
                return (
                (new RestfulResponse(200, 'Last Index data fetched successfully', $result, 0))->expose()
                );
            } else {
                $result = array();
                return (
                (new RestfulResponse(400, 'No data', $result, 0))->expose()
                );
            }
        } catch (PDOException $e) {
            return (
            (new RestfulResponse(400, 'Unable to perform request with error message: ' . $e->getMessage(), $data, 0))->expose()
            );
        }
    }

    /**
     * @param $table_name
     * @param array $data
     * @param string $logic
     * @return string
     * This is used to perform count operation on table based on $data supplied
     *
     */
    public function countByParam($table_name,array $data,$logic='&&'):string
    {
        // TODO: Implement count() method.
        $result = array();
        $getTotal = "Select COUNT(DISTINCT id) as totalLength from {$table_name}";
        $params = [];
        if (!empty($data)) {
            $getTotal = "Select COUNT(DISTINCT id) as totalLength from {$table_name} where ";
            $content = '';
            $count = 0;
            foreach ($data as $key => $value) {
                $value = is_array($value) ? '[' . (stripslashes(implode(',', $value))) . ']' : (stripslashes($value));
                if ($value == null) continue;
                if ($count == 0) {
                    if($key=='id' || strpos($key,'_id')!==false)
                        $content .= $key . " = ? ";
                    else
                        $content .= $key . " LIKE CONCAT('%', ?, '%')  ";

                    $params[] = $value;
                    ++$count;
                } else {
                    if($key=='id' || strpos($key,'_id')!==false)
                        $content .= " {$logic} " . $key . " = ? ";
                    else
                        $content .= " {$logic} " . $key . " LIKE CONCAT('%', ?, '%')  ";

                    $params[] = $value;
                    ++$count;
                }
            }
            $getTotal .= $content;
        }
        // echo $getTotal;
        try {
            $statement = $this->connection->prepare($getTotal);
            $statement->execute($params);
            if ($statement->rowCount() > 0) {
                    $total = (int)($statement->fetch(PDO::FETCH_ASSOC)['totalLength']);
                    $result[] = $total;
                    return (
                    (new RestfulResponse(200, 'Count Successfully', $result, 1))->expose());
                } else {
                    $data = array(0);
                    return (
                    (new RestfulResponse(400, 'Failed to Count', $data, 0))->expose()
                    );
                }
        } catch (PDOException $e) {
            return (
            (new RestfulResponse(400, 'Unable to perform request with error message: ' . $e->getMessage(), $data, 0))->expose()
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

    /**
     * @param array $fields
     * @return string
     * This is used to convert array to string
     */
    public function implodeFields(array $fields):string{
        return implode(',',$fields);
    }

    /**
     * End connection Destructor
     */
    public function __destruct(){
        $this->connection=null;
    }
}