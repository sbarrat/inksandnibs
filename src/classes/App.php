<?php
/**
 * Created by PhpStorm.
 * User: ruben
 * Date: 14/8/17
 * Time: 17:11
 */
namespace Inks;

class App
{

    /**
     * @param string $email
     * @param string $password
     * @return array
     */
    public function login($email, $password)
    {
        $connection = new MongoConnection();
        $result = $connection->query(
            'users',
            ['email' => $email, 'password' => sha1($password)]
        );
        return $result->toArray();
    }

    /**
     * @param array $data
     * @return int
     */
    public function insertRecord($data)
    {
        $connection = new MongoConnection();
        $result = $connection->insert('posts', $data);
        return $result;
    }
    public function deleteRecord($recordId)
    {

    }

    /**
     * Actualiza el registro
     *
     * @param array $filter
     * @param array $data
     * @return int
     */
    public function updateRecord($filter, $data)
    {
        $connection = new MongoConnection();
        $result = $connection->update('posts', $filter, $data);
        return $result;
    }
}