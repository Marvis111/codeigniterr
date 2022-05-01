<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Getalluser extends CI_Controller {

	public function index()
	{
        echo "welcome to the users page";
	}
    public function find(){
        function filterError($field){
            return $field['msg'] != "";
        }
        $errorArray = [];
     if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $token = $_POST['token'];
        array_push($errorArray,['field'=>"token","msg" => (empty($token) ? "Token is required" :"") ]);
        $errors = array_filter($errorArray,'filterError');
        if(count($errors) == 0){
            // do the get here.. here 
            $sql = "SELECT start_time, finish_time FROM user WHERE login_token = '$token' ";
            $results = $this->db->query($sql);
            if($results){
                foreach ($results->result() as $row) {
                    $start = $row -> start_time;
                    $finish = $row -> finish_time;
                }
                $time = time();
                if($time < $finish){
                    $info = array();
                    $sql = "SELECT * FROM users where login_token != '$token' ";
                    $results = $this->db->query($sql);
                    if($results){
                        foreach ($results->result() as $row) {
                            $id = $row -> user_id;
                            $name = $row -> username;
                            $email = $row -> email;
                            array_push($info, array('user_id'=> $id, 'name'=> $name,'email' => $email, 'status'=> 'active'));
                        }
                    echo json_encode($info);
                }
                else{
                    array_push($errorArray,['field'=>"token","msg" => "Token Expired, please login again."]);
                    $errors = array_filter($errorArray,'filterError');
                }
            }
        
        }else{
            echo json_encode($errors);
        }

        }
    
    }
}
}