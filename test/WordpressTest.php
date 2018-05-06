<?php
/**
 * Project: inksandnibs.
 * User: ruben
 * Date: 17/08/17
 * Time: 18:04
 */

namespace Inks;

use PHPUnit\Framework\TestCase;

class WordpressTest extends TestCase
{
    private $data = [];
    public function setUp()
    {
        $this->data = [
            'fpBrand' => '',
            'fpModel' => '',
            'fpNib' => '',
            'inkBrand' => '',
            'inkModel' => '',
            'inkColor' => '',
            'paperBrand' => '',
            'paperModel' => '',
            'imgOrigin' => '',
            'imgUrl' => ''
        ];
    }

    public function testGenerateFountainPenContent()
    {

        $wordpress = new Wordpress('test');
        $this->data['fpBrand'] = 'Visconti';
        $this->data['fpModel'] = 'Homo Sapiens Dark Age';
        $response = $wordpress->addContent($this->data);
        $this->assertFalse($response);
        $content = $wordpress->getContent();
        $this->assertCount(2, $content['categories']);
    }
    public function testGenerateFountainPenContentWithNib()
    {
        $wordpress = new Wordpress('test');
        $this->data['fpBrand'] = 'Visconti';
        $this->data['fpModel'] = 'Homo Sapiens Dark Age';
        $this->data['fpNib'] = 'F';
        $response = $wordpress->addContent($this->data);
        $this->assertFalse($response);
        $content = $wordpress->getContent();

        $this->assertCount(3, $content['categories']);
    }

    public function testGenerateFountainPenContentWithNibAndImage()
    {
        $wordpress = new Wordpress('test');
        $this->data['fpBrand'] = 'Visconti';
        $this->data['fpModel'] = 'Homo Sapiens Dark Age';
        $this->data['fpNib'] = 'F';
        $this->data['imgOrigin'] = 'instagram';
        $this->data['imgUrl'] = 'https://instagram.com/wherever';
        $response = $wordpress->addContent($this->data);
        $this->assertFalse($response);
        $content = $wordpress->getContent();

        $this->assertCount(3, $content['categories']);
    }

    public function testGenerateInkContent()
    {
        $wordpress = new Wordpress('test');
        $this->data['inkBrand'] = 'Private Reserve';
        $this->data['inkModel'] = 'Electric DC Blue';
        $this->data['inkColor'] = 'Blue';
        $this->data['imgOrigin'] = 'pinterest';
        $this->data['imgUrl'] = 'https://pinterest.com/wherever';
        $response = $wordpress->addContent($this->data);
        $this->assertFalse($response);
        $content = $wordpress->getContent();
        $this->assertCount(3, $content['categories']);
    }
    public function testGenerateContentWithoutPaper()
    {
        $wordpress = new Wordpress('test');
        $this->data['fpBrand'] = 'Visconti';
        $this->data['fpModel'] = 'Homo Sapiens Dark Age';
        $this->data['fpNib'] = 'F';
        $this->data['inkBrand'] = 'Private Reserve';
        $this->data['inkModel'] = 'Electric DC Blue';
        $this->data['inkColor'] = 'Blue';
        $this->data['imgOrigin'] = 'instagram';
        $this->data['imgUrl'] = 'https://instagram.com/wherever';
        $response = $wordpress->addContent($this->data);
        $this->assertFalse($response);
        $content = $wordpress->getContent();
        $this->assertCount(6, $content['categories']);
    }

    public function testGenerateContentWithPaper()
    {
        $wordpress = new Wordpress('test');
        $this->data['fpBrand'] = 'Visconti';
        $this->data['fpModel'] = 'Homo Sapiens Dark Age';
        $this->data['fpNib'] = 'F';
        $this->data['inkBrand'] = 'Private Reserve';
        $this->data['inkModel'] = 'Electric DC Blue';
        $this->data['inkColor'] = 'Blue';
        $this->data['paperBrand'] = 'Rhodia';
        $this->data['paperModel'] = 'Ice Dot Pad';
        $this->data['imgOrigin'] = 'instagram';
        $this->data['imgUrl'] = 'https://instagram.com/wherever';
        $response = $wordpress->addContent($this->data);
        $this->assertFalse($response);
        $content = $wordpress->getContent();
        $this->assertCount(8, $content['categories']);
    }
    public function testInsertAndDeleteNewPost()
    {
        $wordpress = new Wordpress();
        $this->data['fpBrand'] = 'Stipula';
        $this->data['fpModel'] = 'Speed';
        $this->data['fpNib'] = 'F';
        $this->data['inkBrand'] = 'Iroshizuku';
        $this->data['inkModel'] = 'Tsuki-yo';
        $this->data['inkColor'] = 'Blue';
        $this->data['imgOrigin'] = 'instagram';
        $this->data['imgUrl'] = 'https://instagram.com/p/BTB1fSbjApM/';
        $response = $wordpress->addContent($this->data);
        $recordId = $response->ID;
        $this->assertNotEquals(0, $recordId);
        $response = $wordpress->deleteContent($response->ID);
        $this->assertEquals($recordId, $response->ID);
    }

    public function testGetCategories()
    {

    }


}
