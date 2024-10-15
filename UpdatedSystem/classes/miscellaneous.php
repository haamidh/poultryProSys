<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of miscellaneous
 *
 * @author user
 */
class miscellaneous {
    
    private $conn;
    private $table_name = "miscellaneous_category";
    private $user_id;
    private $category_id;
    private $category_name;
    private $category_description;
    
    function __construct($conn) {
        $this->conn = $conn;
        
    }
    function getUser_id() {
        return $this->user_id;
    }

    function getCategory_id() {
        return $this->category_id;
    }

    function getCategory_name() {
        return $this->category_name;
    }

    function getCategory_description() {
        return $this->category_description;
    }

    function setUser_id($user_id) {
        $this->user_id = $user_id;
    }

    function setCategory_id($category_id) {
        $this->category_id = $category_id;
    }

    function setCategory_name($category_name) {
        $this->category_name = $category_name;
    }

    function setCategory_description($category_description) {
        $this->category_description = $category_description;
    }

    

}
