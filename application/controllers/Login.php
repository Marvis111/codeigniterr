<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function index()
	{
        echo "welcome to the login page";
	}
    public function find(){
        function filterError($field){
            return $field['msg'] != "";
        }
        $errorArray = [];
     if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $password = $_POST['password'];
        $email = $_POST['email'];
        array_push($errorArray,['field'=>"email","msg" => (filter_var($email,FILTER_VALIDATE_EMAIL) ? "Email is required" :"") ]);
        array_push($errorArray,['field'=>"password","msg" => (empty($password) ? "Password is required" :"") ]);
        $errors = array_filter($errorArray,'filterError');
        if(count($errors) == 0){
            // do the get here.. here 
            $sql = "SELECT * FROM user where email = '$email' ";
            $results = $this->db->query($sql);
            $tidd;
            if($results){
                foreach ($results->result() as $row) {
                    $hashedPwd =  $row-> password;
                    $id = $row -> user_id;
                    $name = $row -> username;
                    $tidd= $row->id;
                }
                if(password_verify($password,$hashedPwd) == 1){
                    function logintoken() { 
                        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ023456789"; 
                        srand((double)microtime()*1000000); 
                        $i = 0; 
                        $pass = '' ; 
                        while ($i <= 30) { 
                                $num = rand() % 33; 
                                $tmp = substr($chars, $num, 1); 
                                $pass = $pass . $tmp; 
                                $i++; 
                            } 
                        return $pass; 
                    }
                    $key = logintoken();
                    $time = time();
                    $token_time = 30 * 60 * 60;
                    $expiry_time = time() + $token_time;
                    //Updating The Query with the new key;
                    $data = array(
                        'start_time' => $time,
                        'finish_time' => $expiry_time,
                        );
                    $this->db->update('user',$data);
                    $details = array('user_id'=> $id,'id'=>$tidd, 'name'=> $name,'token-key' => $key, 'status'=> 'Login Succesfully', 'message'=>'You can access other api with the token key, expires in 30 minutes', 'time-created'=> date($time), 'time-expires'=> date($expiry_time));
                    echo json_encode($details);
                }
                else{
                    array_push($errorArray,['field'=>"password","msg" => "Password is incorrect, please try again."]);
                    $errors = array_filter($errorArray,'filterError');
                }
            }
        
        }else{
            echo json_encode($errors);
        }

        }
    
    }
}