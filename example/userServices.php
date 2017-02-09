<?php
/**
 * Created by PhpStorm.
 * User: SQ05
 * Date: 08/02/2017
 * Time: 11:43 PM
 */
require_once ('../interface/BeereOperations.php'); //load dependencies
echo"
/*** 
 * This 'Beere' Class is of PHP 7, other lower version might have problem to use the query methods.<br>
 * This 'Beere' class reduces many mysqli query you need to do on every service request<br>
 * this 'Beere' class helps to structure all operations of your app in one file.<br>
 * it make use of array of key and value of which the key correspond to table field and value is content to save.<br>
 * request are sent in array form and response is send in Json to the caller.<br>
 * There are many operations this class of BeereOperations can perform to help develop a restful response service synchronously and asynchronously.<br>
 * To save nested array of level 2, use saveMultiples.<br>
 * The response returns json of five category of data, which are:<br>
 * Status which is Integer to denote 200 for success and 400 for failure.<br>
 * Message which is string, <br>
 * Data which  can either be json of data queried<br>
 * Code is bool to denote success<br>
 * Total: this is majorly used while using list based on limit. <br>
 * We will cover a limited execution of queries with CRUD using our class.<br>
 * Kindly change the connection in utils folder to connect with your own database and create a table user with email, first_name, * status fields.<br>
 * <br><b>Study it well to see how powerful it is by using one or more together to achieve your goal. it is well optimised.</b>
 */
"."<br>";
echo "<br>";echo "<br>";echo "<br>";
$_POST=array('first_name'=>'Kazeem Olanipekun','email'=>'wally@gmail.com','status'=>'Mr'); //Assuming this is a post request

function saveUser(){
    echo "<br>";
    echo "<b>Saving with Beere Class Asynchronously</b>"."<br>";
$beere= new BeereOperations();
 $save=$beere->save('user', $_POST); //request with asynchronous response without the saved id;
    echo"<b>Asynchronous save of data</b>"."<br>";echo "<br>";

   print_r(json_decode($save,true)); //convert json back to array
    echo "<br>";echo "<br>";

  $_POST['key_fetch']=md5(mt_rand(0,999).date('YmdHis',time()).uniqid('key').mt_rand(0,99999));
    //load syncronously with key_fetch
    echo"<b>always create key_fetch of the same type and pass to each row of nested array or to an array to receive response
   synchronously.</b>"."<br>";echo "<br>";
    echo "<b>Saving with Beere Class Synchronously</b>"."<br>";echo "<br>";
    $save=$beere->save('user', $_POST);
   $_GET['user']=json_decode($save,true);
       print_r($_GET['user']); //convert json back to array
    echo "<br>";echo "<br>";
}
function updateUser(){
    echo"<b>Update Data using parameters</b>"."<br>";echo "<br>";echo "<br>";
    $delete= new BeereOperations();
    $where=array('id'=>1);
    $sets=array('first_name'=>'Fola Olawale','email'=>'kezyolanipekun@gmail.com','status'=>'Mr');
    $result= $delete->update('user', $where, $sets);
    //You pass a single conditional operator such as AND etc as 3rd parameter in update method, default is AND
    print_r(json_decode($result,true));
}
function listUser(){
    echo "<br>";echo "<br>";echo "<br>";
    echo"<b>Listing respond with list of arrays</b>"."<br>";
    echo"List Data using empty data array"."<br>";
$list=new BeereOperations();
 //to list all, pass empty array, while to list based on some data, pass array of data.
    $data=array();
    $res=$list->list('user', $data);
    print_r(json_decode($res,true));
    //You pass a single conditional operator such as AND etc as 3rd parameter in update method, default is AND
    echo "<br>";echo "<br>";
    echo"<b>List Data using Parameter(s)</b>"."<br>";echo "<br>";
    $data=array('id'=>1);
    $res=$list->list('user', $data);
    print_r(json_decode($res,true));
}
function validateUser(){
    echo "<br>";echo "<br>";echo "<br>";
    echo "<b>Validate User by Parameter</b>"."<br>";echo "<br>";
    $val=new BeereOperations();
    $data=array('email'=>'kezyolanipekun@gmail.com');
    $res=$val->validate('user', $data);
    print_r(json_decode($res,true)); //convert json back to array
    echo "<br>";echo "<br>";
}

function getData(){
    echo "<br>";echo "<br>";echo "<br>";
    echo"<b>A get respond with an array unlike list with multiple arrays"."<br>";
    echo"Get Data using empty data array will throw error, cause daya must not be empty</b>"."<br>";
    $get=new BeereOperations();
    //to list all, pass empty array, while to list based on some data, pass array of data.
    $data=array(); //data must not be empty array
    $res=$get->getADataByParam('user', $data);
    print_r(json_decode($res,true));
    //You pass a single conditional operator such as AND etc as 3rd parameter in getADataByParam method, default is AND
    echo "<br>";echo "<br>";
    echo"<b>Get Data using Parameter(s) return a single array of data</b>"."<br>";
    $data=array('id'=>1);
    $res=$get->getADataByParam('user', $data);
    print_r(json_decode($res,true));
    echo "<br>";
}
function saveUsingMultiple(){
    echo "<br>";echo "<br>";
   $data=array(
       array('first_name'=>'Fola Olawale','email'=>'dennis@gmail.com','status'=>'Mr'),
       array('first_name'=>'Kunle Olawale','email'=>'d@gmail.com','status'=>'Mr'),
       array('first_name'=>'Kunle Loveth','email'=>'love@gmail.com','status'=>'Miss')
   );

    echo "<b>Saving using MULTIPLE Data with Beere Class Asynchronously</b>"."<br>";
    $beere= new BeereOperations();
    $save=$beere->saveMultiple('user', $data); //request with asynchronous response without the saved id;
    echo"Asynchronous save of data"."<br>";echo "<br>";

    print_r(json_decode($save,true)); //convert json back to array
    echo "<br>";echo "<br>";

    echo "<b>Saving using MULTIPLE Data with Beere Class Synchronously by adding key_fetch to request</b>"."<br>";

    $key_fetch=md5(mt_rand(0,999).date('YmdHis',time()).uniqid('key').mt_rand(0,99999));

    foreach ($data as $index => &$item) {
        $item['key_fetch']=$key_fetch;
    };

    //load syncronously with key_fetch
    echo"<b>always create key_fetch of the same type and pass to each row of nested array or to an array to receive response
   synchronously.</b>"."<br>";echo "<br>";

    $save=$beere->saveMultiple('user', $data);
    $_GET['userMultiple']=json_decode($save,true);
    print_r($_GET['userMultiple']); //convert json back to array
    echo "<br>";echo "<br>";
}
function listByLimitPager(){
    echo "<br>";echo "<br>";echo "<br>";
    echo "<b>List By Limit Pager</b>"."<br>";
    $val=new BeereOperations();
    //pass parameter or not, it is work
    $page=1; //default is one but make sure you increment page and send d next page to fetch the next data.
    $limit=10; //default is 200;
    $logic='&&'; //default is AND.
    while($page<3) {
        echo" List ".$page.'<br>';
        $res=$val->listByLimit('user', array(), $logic, $page, $limit);
        print_r(json_decode($res, true)); //convert json back to array
        echo "<br><br>";
        $page++;
    }
    echo "<br>";
}
function deleteUser(){
    echo "<br>";echo "<br>";echo "<br>";
    echo"<b>Delete Data using parameters</b>"."<br>";echo "<br>";
    $delete= new BeereOperations();
    $data=array('id'=>2);
    $result= $delete->delete('user', $data);
    //You pass a single conditional operator such as AND etc as 3rd parameter in delete method, default is AND
    print_r(json_decode($result,true));
    echo "<br>";echo "<br>";
}
//Call Functions
saveUser();
updateUser();
listUser();
validateUser();
getData();
saveUsingMultiple();
listByLimitPager();
deleteUser();
