<?php

abstract class Admin_Model_Model {

    protected $mapper;

    /**
     * Overloading: allow property access
     *
     * @param  string $name
     * @param  mixed $value
     * @return void
     */
    public function __set($name, $value) {
        $method = 'set' . $name;
        if ('mapper' == $name || !method_exists($this, $method)) {
            throw Exception('Invalid property specified');
        }
        $this->$method($value);
    }

    public function  __construct($options = array()) {
        if(count($options)){
           $this->setOptions($options) ;
        }
    }


    /**
     * Overloading: allow property access
     *
     * @param  string $name
     * @return mixed
     */
    public function __get($name) {
       try{
        $method = 'get' . $name;
        
        if ('mapper' == $name || !method_exists($this, $method)) {            
            throw new Exception('Invalid property specified');
        }
        return $this->$method();
       }
       catch(Exception $e){
            echo $name;
            echo $e->getTraceAsString();
       }
    }

    /**
     * Set object state
     *
     * @param  array $options
     * @return Electra_Model_Model
     */
    public function setOptions(array $options) {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);                
            }
        }

        return $this;
    }

    public function toArray() {
        $methods = get_class_methods($this);
        $ret_ary = array();        
        foreach ($methods as $method) {
            if((strpos($method,'get')) === 0) {
                $val = $this->$method();
                if(is_array($val) || is_object($val)) continue;
                $ret_ary[strtolower(str_replace("get", "", $method))] = $val;
            }
        }
        return $ret_ary;
    }

    /**
     * Set data mapper
     */
    //abstract public function setMapper($mapper);


    /**
     * Get data mapper
     *
     */
    //abstract public function getMapper();

}

