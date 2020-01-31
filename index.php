<?php
require 'vendor/autoload.php';


class TryMongoDBClass {

    private $mongodb;
    private $db;

    private $auth = '';
    private $host = '127.0.0.1';
    private $port = '27017';
    private $authDB = '';
    private $dbName = 'demo';

    public function __construct() {

        // check if mongodb is installed or not
        if (!extension_loaded('mongodb')) {
            return [
                'success' => false,
                'message' => 'Connection fails, MongoDB is not installed!',
                'data' => null
            ];
        }

        try {
            $this->mongodb = new MongoDB\Client('mongodb://' . $this->auth . $this->host . ':' . $this->port . $this->authDB);
            $this->db = $this->mongodb->selectDatabase($this->dbName);

            # Test function, added due to errors

            $this->mongodb->WIOC->settings->updateOne(array('_id' => 'xxx'), array('$set' => array('test' => 'yes')));
        } catch (MongoConnectionException $e) {
            throw $e;
        }
    }

    public function insert( $collection, $arrDocumentObject ) {

        // select the collection you want to insert document in
        $collection = $this->db->selectCollection($collection);

        $result = $collection->insertOne($arrDocumentObject);
        
        if( $result->getInsertedId() ) {
            $arr = [
                'success' => true,
                'message' => 'Object inserted successfully',
                'data' => ['id' => json_decode(json_encode($result->getInsertedId()),true), 'object' => $arrDocumentObject]
            ];
        } else {
            $arr = [
                'success' => false,
                'message' => 'Fails to insert Object',
                'data' => ['id' => null, 'object' => $arrDocumentObject]
            ];
        }

        return $arr;
    }

    public function search($collection, $arrSearchObject) {

        // select the collection you want to insert document in
        $collection = $this->db->selectCollection($collection);

        $result = $collection->find($arrSearchObject);

        $numberOfRecords = count($result);

        $i = 0;

        if ( $numberOfRecords ) {
            $arr = [
                'success' => true,
                'message' => $numberOfRecords . ' record(s) found',
                'data' => ['result' => $result]
            ];
        } else {
            $arr = [
                'success' => false,
                'message' => 'no record(s) found',
                'data' => ['result' => null]
            ];
        }

        return $arr;
    }
};


$myMongo = new TryMongoDBClass;

echo '<pre>';

/* --- CREATE -- */
$res = $myMongo->insert('beers',['name' => 'Hitesh', 'brewery' => 'BrewDog']);
print_r( $res );


/* --- READ -- */
$res = $myMongo->search('beers',['name'=> 'Hitesh', 'brewery' => 'BrewDog']);

if( $res['success'] ) {
    $cursor = $res['data'];
    foreach ($cursor as $document) {
        print_r($document);
    }
}

/* --- UPDATE -- */
/* --- DELETE -- */
