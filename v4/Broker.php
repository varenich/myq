<?php
namespace myq\v4;

/*
Myq broker. Controls FIFO queues
Use it to perform any operation wih your queue

Uses PHP PDO to access Database
This version is only using MySQL to store events and subscribers. 
To use other DB please change the connection string at __construct

*/

use myq\v4\EventAbstract;

class Broker {
    public $configPath;
    private $dbh;

    /*
    Creates an object of broker

    configPath : string - Configuration JSON file with DB connection params

    Config file example:
    "myq" : {
        "db_host" : "db host",
        "db_name" : "db name",
        "db_user" : "db user",
        "db_pass" : "db password"
    }
    */
    function __construct($configPath='') {
        $this->configPath = $configPath;

        // Reads the DB config from a file
        $jsonconfigstr = file_get_contents($configPath);
        if (!$jsonconfigstr) throw new \Exception('DB config file reading error', 500);

        $jsonconfig = json_decode($jsonconfigstr,true);

        $host = $jsonconfig["myq"]["db_host"];
        $db = $jsonconfig["myq"]["db_name"];
        $user = $jsonconfig["myq"]["db_user"];
        $pass = $jsonconfig["myq"]["db_pass"];  
        
        try {
            $this->dbh = new \PDO("mysql:host=$host;dbname=$db", $user, $pass);
        } catch (\PDOException $e) {
            throw $e;
        }
    } // __construct

    /* 
        Publishes event

        publisherName : string - Publisher name
        e : EventAbstract - Event object to publish. Create your own event based on EventAbstract class. Check EventAbstract class for details.
    */
    public function publish($publisherName='', EventAbstract $e) {
        try {
            $subscibers = $this->getSubscribers($e->getName());

            foreach ($subscibers as $subsciber) {
                $sql = 'INSERT INTO EVENT (event_name, publisher_name, subscriber_name, data) values (:event_name, :publisher_name, :subscriber_name, :data)';  
                $sth = $this->dbh->prepare($sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);
                $sth->execute([
                    'event_name' => $e->getName(),
                    'publisher_name' => $publisherName,
                    'subscriber_name' => $subsciber,
                    'data' => $e->getData()
                ]);
            }
        } catch (\PDOException $e) {
            throw $e;
        }
    } // publish

    /* 
        Returns the subscribers list of a givent event

        eventName : string - Event name
    */
    public function getSubscribers($eventName='') {
        try {
            $sql = 'SELECT subscriber_name FROM SUBSCRIPTION WHERE event_name = :event_name';
            $sth = $this->dbh->prepare($sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);
            $sth->execute([
                'event_name' => $eventName
            ]);

            $subscibers = array_column($sth->fetchAll(\PDO::FETCH_ASSOC), 'subscriber_name');
            return $subscibers;
        } catch (\PDOException $e) {
            throw $e;
        }
    } // getSubscribers

    /* 
        Subscribes to given event

        subscriberName : string - Subsriber name
        eventName : string - Event name
    */
    public function subscribe($subscriberName='', $eventName='') {
        try {
            $sql = 'INSERT INTO SUBSCRIPTION (event_name, subscriber_name) values (:event_name, :subscriber_name)';  
            $sth = $this->dbh->prepare($sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);
            $sth->execute([
                'event_name' => $eventName,
                'subscriber_name' => $subscriberName
            ]);
        } catch (\PDOException $e) {
            throw $e;
        }
    } // subscribe

    /* 
        Unsubscribes from receiving given event

        subscriberName : string - Subsriber name
        eventName : string - Event name
    */
    public function unsubscribe($subscriberName='', $eventName='') {
        try {
            $sql = 'DELETE FROM SUBSCRIPTION WHERE event_name = :event_name AND subscriber_name = :subscriber_name';  
            $sth = $this->dbh->prepare($sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);
            $sth->execute([
                'event_name' => $eventName,
                'subscriber_name' => $subscriberName
            ]);
        } catch (\PDOException $e) {
            throw $e;
        }
    } // unsubscribe

    /* 
        Returns one FIFO event

        subscriberName : string - Subsriber name
        eventName : string - Event name
    */
    public function getEvent($subscriberName='',$eventName='') {
        try {
            $sql = 'SELECT * FROM EVENT WHERE subscriber_name = :subscriber_name AND event_name = :event_name ORDER BY registration_datetime ASC';
            $sth = $this->dbh->prepare($sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);
            $sth->execute([
                'subscriber_name' => $subscriberName,
                'event_name' => $eventName
            ]);
            $row = $sth->fetch(\PDO::FETCH_ASSOC);

            if (@$row['event_id']) {
                $e = new EventAbstract;
                $e->setID(@$row['event_id']);
                $e->setName($eventName);
                $e->setData(@$row['data']);
                return $e;
            }
        } catch (\PDOException $e) {
            throw $e;
        }
    } // getEvent

    /* 
        Moves queue pointer to next FIFO event

        currentEvent : EventAbstract - Currently loaded event to move from
    */
    public function movePointer(EventAbstract $currentEvent) {
        try {
            $sql = 'DELETE FROM EVENT WHERE event_id = :event_id'; 
            $sth = $this->dbh->prepare($sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);
            $sth->execute([
                'event_id' => $currentEvent->getID()
            ]);
        } catch (\PDOException $e) {
            throw $e;
        }
    } // movePointer
} // class