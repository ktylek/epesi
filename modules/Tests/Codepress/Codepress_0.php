<?php
/**
 * 
 * @author pbukowski@telaxus.com
 * @copyright pbukowski@telaxus.com
 * @license SPL
 * @version 0.1
 * @package tests-codepress
 */
defined("_VALID_ACCESS") || die('Direct access forbidden');

class Tests_Codepress extends Module {

	public function body() {
		$qf = $this->init_module('Libs/QuickForm');
		$x = $qf->addElement('codepress','cd','CD');
		$x->setRows(15);
		$x->setCols(100);
		$x->setLineNumbers(false);
		//$x->setLang('php'); //default
		//$x->setAutocomplete(true); //default
		$qf->setDefaults(array('cd'=>file_get_contents($this->get_module_dir().'Codepress_0.php')));
		//$qf->freeze(array('cd'));
		$qf->display();
	}

}

?>