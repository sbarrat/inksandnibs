<?php
/**
 * Created by PhpStorm.
 * User: ruben
 * Date: 14/8/17
 * Time: 17:08
 */
namespace Inks;

class Wordpress
{
    private $clientId = ''; //Id cliente Wordpress
    private $clientSecret = ''; // Secreto cliente Wordpress
    private $grantType =  'password'; // Tipo de acceso
    private $username = ''; // Usuario Wordpress
    private $password = ''; // Contraseña Wordpress
    private $siteId = ''; // ID Sitio Wordpress
    private $baseUri = "https://public-api.wordpress.com/rest/v1.1/sites/";
    private $accessKey = null;
    private $content = [
        'tags' => [],
        'categories' => [],
        'title' => '',
        'text' => ''
    ];
    private $envionment = 'real';

    /**
     * Wordpress constructor.
     *
     * @param string $environment
     */
    public function __construct($environment = 'real')
    {
        $this->envionment = $environment;
        if ($this->envionment === 'real') {
            $curl = curl_init('https://public-api.wordpress.com/oauth2/token');
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt(
                $curl,
                CURLOPT_POSTFIELDS,
                [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'grant_type' => $this->grantType,
                    'username' => $this->username,
                    'password' => $this->password,
                ]
            );
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $auth = curl_exec($curl);
            $auth = json_decode($auth);
            $this->accessKey = $auth->access_token;
            $this->baseUri = $this->baseUri.$this->siteId;
        }
    }

    /**
     * Agrega el contenido
     *
     * @param $data
     * @return bool|mixed|string
     */
    public function addContent($data)
    {
        $this->generateFountainPenContent($data);
        $this->generateInkContent($data);
        $this->generatePaperContent($data);
        $this->generateImageContent($data);
        $response = false;
        if ($this->envionment === 'real') {
            $options = [
                'http' =>
                    [
                        'ignore_errors' => true,
                        'method' => 'POST',
                        'header' =>
                            [
                                0 => 'authorization: Bearer ' . $this->accessKey,
                                1 => 'Content-Type: application/x-www-form-urlencoded',
                            ],
                        'content' =>
                            http_build_query([
                                'title' => $this->content['title'],
                                'content' => $this->content['text'],
                                'tags' => $this->content['tags'],
                                'categories' => $this->content['categories'],
                                'format' => 'image',
                                'media_url' => $data['imgUrl']
                            ]),
                    ],
            ];
            $context = stream_context_create($options);
            $response = file_get_contents(
                $this->baseUri . '/posts/new/',
                false,
                $context
            );
            $response = json_decode($response);
        }
        return $response;
    }

    /**
     * Genera el contenido formateado de la Pluma
     *
     * @param $data
     */
    public function generateFountainPenContent($data)
    {
        if ($data['fpBrand'] != '') {
            $this->content['tags'][] = 'Fountain Pen';
            $this->content['categories'][] = $data['fpBrand'];
            $this->content['categories'][] = $data['fpModel'];
            $this->content['title'] .= $data['fpBrand'] . " " . $data['fpModel'];
            $this->content['text'] .= "This is a " . $data['fpBrand'] . " " . $data['fpModel'];
            if ($data['fpNib'] != '') {
                $this->content['categories'][] = $data['fpNib'];
                $this->content['title'] .= " ".$data['fpNib'] . " nib";
                $this->content['text'] .= " with " . $data['fpNib'] . " nib";
            }
        }
    }

    /**
     * Genera el contenido formateado de la tinta
     *
     * @param $data
     */
    public function generateInkContent($data)
    {
        if ($data['inkBrand'] != '') {
            $this->content['tags'][] = 'Ink';
            $this->content['categories'][] = $data['inkBrand'];
            $this->content['categories'][] = $data['inkModel'];
            $this->content['categories'][] = $data['inkColor'];
            if (strlen($this->content['title']) > 0) {
                $this->content['title'] .= " with ";
                $this->content['text'] .= " sample write with ";
            }
            $this->content['title'] .= $data['inkBrand'] . " " . $data['inkModel'];
            $this->content['text'] .= $data['inkBrand'] . " " . $data['inkModel']. " ink";
        }
    }

    /**
     * Genera el contenido formateado del papel
     *
     * @param $data
     */
    public function generatePaperContent($data)
    {
        if ($data['paperBrand'] != '') {
            $this->content['tags'][] = 'Paper';
            $this->content['categories'][] = $data['paperBrand'];
            $this->content['categories'][] = $data['paperModel'];
            $this->content['text'] .= " in " . $data['paperBrand'] . " " . $data['paperModel']. " paper";
        }
    }

    /**
     * Genera el contenido formateado de la imagen
     *
     * @param $data
     */
    public function generateImageContent($data)
    {
        $alt = $this->content['text'];
        $this->content['text'] = "<p>". $this->content['text']."</p>";
        if ($data['imgUrl'] != '') {
            $image = "<img src='" . $data['imgUrl'] . "' alt='".$alt.">";
            if ($data['imgOrigin'] === 'instagram') {
                $image = "[instagram url=" . $data['imgUrl'] . "]";
            } else if ($data['imgOrigin'] === 'pinterest') {
                $image = $data['imgUrl'];
            }
            $this->content['text'] .= $image;
        }
    }

    /**
     * Devuelve el contenido
     *
     * @return array
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Borra el post creado
     *
     * @param $postID
     * @return mixed
     */
    public function deleteContent($postID)
    {
        $options = array(
            'http' =>
                array(
                    'ignore_errors' => true,
                    'method' => 'POST',
                    'header' =>
                        array(
                            0 => 'authorization: Bearer ' . $this->accessKey,
                        ),
                ),
        );
        $context = stream_context_create($options);
        $response = file_get_contents(
            $this->baseUri . '/posts/' . $postID . '/delete/',
            false,
            $context
        );
        return json_decode($response);
    }

    /**
     * Devuelve las categorias del sitio
     *
     * @return mixed
     */
    public function getCategories()
    {
        $options  = array (
            'http' =>
                array (
                    'ignore_errors' => true,
                ),
        );
        $context  = stream_context_create($options);
        $response = file_get_contents(
            $this->baseUri.'/categories/',
            false,
            $context
        );
        return json_decode($response);
    }
}
