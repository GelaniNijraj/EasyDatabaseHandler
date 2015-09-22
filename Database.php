<?php

/**
 * Created by Nijraj Gelani.
 * User: MaitreyaBuddha
 * On: 15/7/15, 7:06 PM
 * Copyright (c) 2015 Nijraj Gelani
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 */

class Database{
    var $server   = "";
    var $user     = "";
    var $pass     = "";
    var $database = "";
    var $error = 1;
    var $connect = 0;
    var $query_id = 0;
    var $query_run = 0;
    
    function Database($server, $user, $pass, $database){
        $this->server = $server;
        $this->user = $user;
        $this->pass = $pass;
        $this->database = $database;
        $this->connect = mysqli_connect($this->server, $this->user, $this->pass, $this->database);
        if (!$this->connect) {
            $this->oops("Could not connect to server: <b>$this->server</b>.");
        }
    }

    function query($query){
        $this->query_run = mysqli_query($this->connect, $query);
        if($this->query_run){
            return 1;
        }else{
            $this->oops("Failed to execute the query: <b>".$query."</b>");
        }
    }

    function insertRow($table, $data, $get_return_value="0"){
        $keys = array_keys($data);
        $count = 0;
        if($keys[0]===0){
            $query = "INSERT INTO ".$table." VALUES(";
            foreach($data as $value){
                $query .= "'".$value."'";
                $count += 1;
                if($count<count($data))
                    $query .= ", ";
                else
                    $query .= ");";
            }
        }else{
            $query = "INSERT INTO ".$table."(";
            foreach($keys as $key){
                $query .= $key;
                $count += 1;
                if($count==count($keys))
                    $query .= ") ";
                else
                    $query .= ", ";
            }
            $query .= "VALUES(";
            $count = 0;
            foreach($data as $key){
                $query .= "'".$key."'";
                $count += 1;
                if($count==count($data))
                    $query .= ")";
                else
                    $query .= ", ";
            }
        }
        $this->query($query);
        if ($get_return_value != "0") {
            return $this->getValue($table, $get_return_value, $this->primary_keys[$table] . "='" . mysqli_insert_id($this->connect) . "'");
        } else {
            return mysqli_insert_id($this->connect);
        }
    }

    function updateValue($table, $vals, $condition){
        $query = "UPDATE ".$table." SET ";
        $query .= $this->generateList($vals);
        $query .= " WHERE ".$condition.";";
        return $this->query($query);
    }

    function getArray($table, $column, $condition="1"){
        $query = "SELECT ".$column." FROM ".$table." WHERE ".$condition.";";
        $this->query($query);
        if($this->query_run){
            $arr = array();
            while($a = mysqli_fetch_assoc($this->query_run)){
                array_push($arr, $a);
            }
            return $arr;
        }else{
            return -1;
        }
    }

    function getRow($table, $column, $condition="1"){
        $arr = $this->getArray($table, $column, $condition);
        return $arr[0];
    }

    function getValue($table, $column, $condition){
        $query = "SELECT ".$column." FROM ".$table;
        $query .= " WHERE ".$condition.";";
        $this->query($query);
        $nums = mysqli_num_rows($this->query_run)>0;
        if($nums>0) {
            if($nums==1) {
                return mysqli_fetch_assoc($this->query_run)[$column];
            }else{
                $this->oops("Query returned more than one rows.<br >Use getArray() instead to fetch multiple rows.");
            }
        }else{
            return 0;
        }
    }

    function deleteRow($table, $condition){
        $query = "DELETE FROM " . $table . " WHERE " . $condition;
        return $this->query($query);
    }

    function count($table, $condition){
        $query = "SELECT * FROM ".$table." WHERE ".$condition.";";
        $this->query($query);
        return mysqli_num_rows($this->query_run);
    }

    function oops($error="0"){
        if($this->error) {
            if ($error=="0") {
                echo "Something went wrong...<br />";
            } else {
                echo $error."<br />";
            }
        }
        return -1;
    }

    function generateList($associative_array){
        $count = 0;
        $list_ = "";
        foreach($associative_array as $column=>$value){
            $list_ .= $column."='".$value."'";
            if($count!=count($associative_array)-1){
                $list_ .= ", ";
            }
            $count += 1;
        }
        return $list_;
    }

    function enableErrors(){
        $this->error = 1;
    }

    function disableErrors(){
        $this->error = 0;
    }
}
