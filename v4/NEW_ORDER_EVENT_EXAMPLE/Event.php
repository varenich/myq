<?php
namespace myq\v4\NEW_ORDER_EVENT_EXAMPLE;

use myq\v4\EventAbstract;

/*
    The only diffence beetwen events is the name
    Check other operations of an event in the EventAbstract class
*/
class Event extends EventAbstract {
    
    function __construct() {
        $this->_name = 'NEW_ORDER_EVENT_EXAMPLE';
    }

}
?>