Simple messaging broker using a MySQL database as a storage.

Uses PHP PDO to connect to database.

Example of use
===============

Database configuration JSON file
===========================
```
"myq" : {
    "db_host" : "db host",
    "db_name" : "db name",
    "db_user" : "db user",
    "db_pass" : "db password"
}
```

Setting up
==========
```
use myq\v4\Broker;
$configPath = '/path/to/your/config.json';
$br = new Broker($configPath);
```

Reading event
=============
```
try {
    // 'order' is a subscriber name
    // 'NEW_ORDER_EVENT' is an event name
    $ev = $br->getEvent('order', 'NEW_ORDER_EVENT');

    if (@$ev) $data = $ev->getDataDeserialized();
    // data can be empty, so check it
    if (@$data) {
        // Its not empty so use it as normal
    }

    //Move pointer to next event in queue
    $br->movePointer($ev);
}
catch (\Exception $e) {
    // Do something with exception
}
```
Publishing event
================
```
use myq\v4\NEW_ORDER_EVENT_EXAMPLE\Event;
$ev = new Event;

// Order as an example
$evData = array(
    "orderID" => $orderID,
    "orderData" => $orderData
);
$ev->setData($evData);

try {
    // 'order' is a publisher name
    // Event is published only if there are any of subscribers in SUBSCRIPTION database table
    $br->publish('order', $ev);
}
catch (Exception $e) {
    // Do something with exception
}
```
