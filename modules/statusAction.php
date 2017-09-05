<?php
class statusAction implements moduleInterface {
    public function index(){
        if($args = func_get_args())
            return 'Check system status by : '. json_encode($args);
        return 'Check system status !';
    }
    public function menu(){
        return array();
    }
    public function content(){
        if($args = func_get_args())
            return 'Check system status by : '. json_encode($args);
        return 'Check system status !';
    }
    public function get(){
        if($args = func_get_args())
            return 'Retrived get status by : '. json_encode($args);
        return 'Retrived get status !';
    }
    public function post(){
        if($args = func_get_args())
            return 'Received post status by : '. json_encode($args);
        return 'Received post status !';
    }
    public function put(){
        if($args = func_get_args())
            return 'Received put status by : '. json_encode($args);
        return 'Received put status !';
    }
    public function delete(){
        if($args = func_get_args())
            return 'Deleted status by : '. json_encode($args);
        return 'Deleted status !';
    }
}