<?php
/**
 * Created by PhpStorm.
 * User: ruben
 * Date: 11/04/16
 * Time: 11:16
 */
namespace Inks;

use MongoDB\Driver as MongoDriver;
use MongoDB\BSON as MongoBSON;
use PHPUnit\Runner\Exception;

class MongoConnection
{
    private $username = 'inksandnibs';
    private $password = '9tY8DqkgdtQyRVs4Kdqw';
    private $host = 'ds062178.mlab.com:62178';
    private $replicaSet = false;
    private $dbAuth = 'inks';
    private $client = null;

    /**
     * Connection constructor.
     *
     */
    public function __construct()
    {
        $options = array(
            'username' => $this->username,
            'password' => $this->password
        );
        if ($this->replicaSet && strlen($this->replicaSet) > 0) {
            $options['replicaSet'] = $this->replicaSet;
            $options['readPreference'] = MongoDriver\ReadPreference::RP_NEAREST;
            $options['w'] = 1;
        }
        $this->client = new MongoDriver\Manager(
            'mongodb://' . $this->host . '/' . $this->dbAuth,
            $options
        );
    }

    /**
     * @return MongoDriver\Manager|null
     */
    protected function getClient()
    {
        return $this->client;
    }

    /**
     * Consulta a la base de datos Mongo
     *
     * @param $collection
     * @param array $filter
     * @param array $options
     * @return MongoDriver\Cursor
     * @throws MongoDriver\Exception\Exception
     */
    public function query($collection, $filter = array(), $options = array())
    {
        $query = new MongoDriver\Query(
            $filter,
            $options
        );
        return $this->client->executeQuery(
            $this->dbAuth.".".$collection,
            $query,
            new MongoDriver\ReadPreference(MongoDriver\ReadPreference::RP_NEAREST)
        );
    }

    /**
     * Actualiza el registro de la base de datos
     *
     * @param $collection
     * @param $idField
     * @param $data
     * @return int
     */
    public function update($collection, $idField, $data)
    {
        $filter = $idField;
        if (!is_array($idField)) {
            $filter = ['_id' => new MongoBSON\ObjectID($idField)];
        }
        $bulk = new MongoDriver\BulkWrite();
        $bulk->update(
            $filter,
            array('$set' => $data),
            array('multi' => false, 'upsert' => false)
        );
        $result = $this->client->executeBulkWrite($this->dbAuth . "." . $collection, $bulk)->getModifiedCount();
        return $result;
    }

    /**
     * @param $collection
     * @param $data
     * @param bool $multi
     * @return int
     */
    public function insert($collection, $data, $multi = false)
    {
        $result = 0;
        try {
            $bulk = new MongoDriver\BulkWrite();
            if ($multi) {
                foreach ($data as $doc) {
                    $bulk->insert($doc);
                }
            } else {
                $bulk->insert($data);
            }
            $result =
                $this->client->executeBulkWrite($this->dbAuth . "." . $collection, $bulk)->getInsertedCount();
        } catch (MongoDriver\Exception\BulkWriteException $e) {

        }
        return $result;
    }

    /**
     * @param $collection
     * @param $filter
     * @param array $deleteOptions
     * @return int
     */
    public function delete($collection, $filter, $deleteOptions = array())
    {
        $bulk = new MongoDriver\BulkWrite();
        $bulk->delete($filter, $deleteOptions);
        return $this->client->executeBulkWrite($collection, $bulk)->getDeletedCount();
    }
}