<?php
/* SVN FILE: $Id: xml.test.php 7118 2008-06-04 20:49:29Z gwoo $ */
/**
 * Short description for file.
 *
 * Long description for file
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) Tests <https://trac.cakephp.org/wiki/Developement/TestSuite>
 * Copyright 2005-2008, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 *  Licensed under The Open Group Test Suite License
 *  Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright 2005-2008, Cake Software Foundation, Inc.
 * @link				https://trac.cakephp.org/wiki/Developement/TestSuite CakePHP(tm) Tests
 * @package			cake.tests
 * @subpackage		cake.tests.cases.libs.view.helpers
 * @since			CakePHP(tm) v 1.2.0.4206
 * @version			$Revision: 7118 $
 * @modifiedby		$LastChangedBy: gwoo $
 * @lastmodified	$Date: 2008-06-04 13:49:29 -0700 (Wed, 04 Jun 2008) $
 * @license			http://www.opensource.org/licenses/opengroup.php The Open Group Test Suite License
 */
if (!defined('CAKEPHP_UNIT_TEST_EXECUTION')) {
	define('CAKEPHP_UNIT_TEST_EXECUTION', 1);
}

uses('view'.DS.'helpers'.DS.'app_helper', 'controller'.DS.'controller', 'model'.DS.'model', 'view'.DS.'helper', 'view'.DS.'helpers'.DS.'xml');

class TestXml extends Object {
	var $content = '';

	function __construct($content) {
		$this->content = $content;
	}

	function toString() {
		return $this->content;
	}
}
/**
 * Short description for class.
 *
 * @package		cake.tests
 * @subpackage	cake.tests.cases.libs.view.helpers
 */
class XmlHelperTest extends UnitTestCase {

	function setUp() {
		$this->Xml =& new XmlHelper();
		$this->Xml->beforeRender();
	}

	function testAddNamespace() {
		$this->Xml->addNs('custom', 'http://example.com/dtd.xml');
		$manager =& XmlManager::getInstance();

		$expected = array('custom' => 'http://example.com/dtd.xml');
		$this->assertEqual($manager->namespaces, $expected);
	}

	function testRemoveNamespace() {
		$this->Xml->addNs('custom', 'http://example.com/dtd.xml');
		$this->Xml->addNs('custom2', 'http://example.com/dtd2.xml');
		$manager =& XmlManager::getInstance();

		$expected = array('custom' => 'http://example.com/dtd.xml', 'custom2' => 'http://example.com/dtd2.xml');
		$this->assertEqual($manager->namespaces, $expected);

		$this->Xml->removeNs('custom');
		$expected = array('custom2' => 'http://example.com/dtd2.xml');
		$this->assertEqual($manager->namespaces, $expected);
	}

	function testRenderZeroElement() {
		$result = $this->Xml->elem('count', null, 0);
		$expected = '<count>0</count>';
		$this->assertEqual($result, $expected);
	}

	function testRenderElementWithNamespace() {
		$result = $this->Xml->elem('count', array('namespace' => 'myNameSpace'), 'content');
		$expected = '<myNameSpace:count>content</myNameSpace:count>';
		$this->assertEqual($result, $expected);

		$result = $this->Xml->elem('count', array('namespace' => 'myNameSpace'), 'content', false);
		$expected = '<myNameSpace:count>content';
		$this->assertEqual($result, $expected);
		
		$expected .= '</myNameSpace:count>';
		$result .= $this->Xml->closeElem();
		$this->assertEqual($result, $expected);
	}
	
	function testRenderElementWithComplexContent() {
		$result = $this->Xml->elem('count', array('namespace' => 'myNameSpace'), array('contrived' => 'content'));
		$expected = '<myNameSpace:count><content /></myNameSpace:count>';
		$this->assertEqual($result, $expected);

		$result = $this->Xml->elem('count', array('namespace' => 'myNameSpace'), array('cdata' => true, 'value' => 'content'));
		$expected = '<myNameSpace:count><![CDATA[content]]></myNameSpace:count>';
		$this->assertEqual($result, $expected);
	}

	function testSerialize() {
		$data = array(
			'test1' => 'test with no quotes',
			'test2' => 'test with "double quotes"'
		);
		$result = $this->Xml->serialize($data);
		$expected = '<std_class test1="test with no quotes" test2="test with \"double quotes\"" />';
		$this->assertIdentical($result, $expected);

		$data = array(
			'test1' => 'test with no quotes',
			'test2' => 'test without double quotes'
		);
		$result = $this->Xml->serialize($data);
		$expected = '<std_class test1="test with no quotes" test2="test without double quotes" />';
		$this->assertIdentical($result, $expected);
	}

	function testHeader() {
		$expectedDefaultEncoding = Configure::read('App.encoding');
		if (empty($expectedDefaultEncoding)) {
			$expectedDefaultEncoding = 'UTF-8';
		}
		$attrib = array(); 
		$result = $this->Xml->header($attrib); 
		$expected = '<?xml version="1.0" encoding="'.$expectedDefaultEncoding.'" ?>'; 
		$this->assertIdentical($result, $expected);

		$attrib = array(
			'encoding' => 'UTF-8',
			'version' => '1.1'
		);
		$result = $this->Xml->header($attrib);
		$expected = '<?xml version="1.1" encoding="UTF-8" ?>';
		$this->assertIdentical($result, $expected);

		$attrib = array(
			'encoding' => 'UTF-8',
			'version' => '1.2',
			'additional' => 'attribute'
		);
		$result = $this->Xml->header($attrib);
		$expected = '<?xml version="1.2" encoding="UTF-8" additional="attribute" ?>';
		$this->assertIdentical($result, $expected);

		$attrib = 'encoding="UTF-8" someOther="value"';
		$result = $this->Xml->header($attrib);
		$expected = '<?xml encoding="UTF-8" someOther="value" ?>';
		$this->assertIdentical($result, $expected);
	}

	function tearDown() {
		unset($this->Xml);
	}
}

?>