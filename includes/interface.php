<?php
interface moduleInterface
{
    public function index();
    public function menu();
    public function content();
    public function get();
    public function post();
    public function put();
    public function delete();
}