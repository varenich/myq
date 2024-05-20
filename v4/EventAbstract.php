<?php
namespace myq\v4;

/**
 *   Abstract Event - defines an event interface
*/
class EventAbstract {
    protected $_name = '';
    protected $_data;
    protected $_id;

    /**
    * Returns the name of an event
    * 
    * @return string Event name
    */
    public function getName() {
        return $this->_name;
    } // getName

    /**
    * Sets the name of an event
    *
    * @param string $name  - The name of an event
    * 
    * @return void
    */
    public function setName($name='') {
        return $this->_name = $name;
    } // setName

    /**
    * Returns the event ID
    * 
    * @return int Event ID
    */
    public function getID() {
        return $this->_id;
    } // getID

    /**
    * Sets the event ID
    *
    * @param int $id  - The event ID
    * 
    * @return void
    */
    public function setID($id=null) {
        $this->_id = $id;
    } // setID

    /**
    * Sets and serialzes the data of an event
    *
    * @param mixed $data - Event data to store
    *
    * @return void
    */

    function setData($data=null) {
        $this->_data =  json_encode($data, JSON_UNESCAPED_UNICODE);
    } // setData

    /**
    * Returns deserialized event data
    * 
    * @return mixed Event data
    */
    public function getDataDeserialized() {
        $dt = json_decode(json_decode($this->_data,true),true);
        return $dt;
    } // getDataDeserialized

    /**
    * Returns raw event data
    * 
    * @return string Event data
    */
    public function getData() {
        return $this->_data;
    } // getData
} // class
?>