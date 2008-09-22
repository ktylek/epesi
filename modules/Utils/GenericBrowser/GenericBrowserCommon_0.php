<?php
/**
 * @author Arkadiusz Bisaga <abisaga@telaxus.com>, Kuba Slawinski <kslawinski@telaxus.com> and Paul Bukowski <pbukowski@telaxus.com>
 * @copyright Copyright &copy; 2006, Telaxus LLC
 * @version 0.9
 * @license SPL
 * @package epesi-utils
 * @subpackage generic-browser
 */
defined("_VALID_ACCESS") || die('Direct access forbidden');

class Utils_GenericBrowserCommon extends ModuleCommon {
	public static function user_settings(){
		return array('Browsing tables'=>array(
			array('name'=>'per_page','label'=>'Records per page','type'=>'select','values'=>array(5=>5,10=>10,20=>20,50=>50,100=>100),'default'=>20),
			array('name'=>'actions_position','label'=>'Position of \'Actions\' column','type'=>'radio','values'=>array(0=>'Left',1=>'Right'),'default'=>0),
			array('name'=>'adv_search','label'=>'Advanced search by default','type'=>'bool','default'=>0),
			array('name'=>'adv_history','label'=>'Advanced order history','type'=>'bool','default'=>0),
			array('name'=>'display_no_records_message','label'=>'Hide \'No records found\' message','type'=>'bool','default'=>0)
			));
	}
	
	public static function mobile_table($cols,$data) {
		$th = Base_ThemeCommon::init_smarty();

		$all_width = 0;
		foreach($cols as $v) {
			if (array_key_exists('display', $v) && $v['display']==false)
				continue;
			$all_width+=$v['width'];
		}
		$i=0;
		$headers = array();
		foreach($cols as $v) {
			if (array_key_exists('display', $v) && $v['display']==false) {
				$i++;
				continue;
			}
			if(isset($v['order'])) $is_order = true;
			if(!isset($headers[$i])) $headers[$i] = array('label'=>'');
			if (isset($_GET['order']) && isset($_GET['order_dir']) && $i==$_GET['order']) {
				$sort = 'style="padding-right: 12px; background-image: url('.Base_ThemeCommon::get_template_file('Utils_GenericBrowser','sort-'.strtolower($_GET['order_dir']).'ending.png').'); background-repeat: no-repeat; background-position: right;"';
				$sort_direction = ($_GET['order_dir']=='desc')?'asc':'desc';
			} else {
				$sort = '';
				$sort_direction = 'asc';
			}
			$headers[$i]['label'] .= (isset($v['preppend'])?$v['preppend']:'').(isset($v['order'])?'<a href="mobile.php?'.http_build_query(array_merge($_GET,array('order'=>$i,'order_dir'=>$sort_direction))).'">' . '<span '.$sort.'>' . $v['name'] . '</span></a>':'<span>'.$v['name'].'</span>').(isset($v['append'])?$v['append']:'');
			$headers[$i]['attrs'] = 'style="width: '.intval(100*$v['width']/$all_width).'%" ';
			$headers[$i]['attrs'] .= 'nowrap="1" ';
			$i++;
		}
		$th->assign('cols',array_values($headers));

		//sort data
		if(isset($_GET['order']) && isset($_GET['order_dir'])) {
			$col = array();
			foreach($data as $j=>$d)
				foreach($d as $i=>$c)
					if(isset($cols[$i]['order']) && $i==$_GET['order']) {
						if(is_array($c)) {
							if(isset($c['order_value']))
								$xxx = $c['order_value'];
							else
								$xxx = $c['value'];
						} else $xxx = $c;
						if(isset($cols[$i]['order_eregi'])) {
							$ret = array();
							eregi($cols[$i]['order_eregi'],$xxx, $ret);
							$xxx = isset($ret[1])?$ret[1]:'';
						}
						$xxx = strip_tags(strtolower($xxx));
						$col[$j] = $xxx;
					}

			asort($col);
			$data2 = array();
			foreach($col as $j=>$v) {
				$data2[] = $data[$j];
			}
			if($_GET['order_dir']!='asc') {
				$data2 = array_reverse($data2);
			}
			$data = $data2;
		}

		$out_data = array();
		foreach($data as $row) {
			foreach($row as $k=>$cell) {
				if (!isset($cols[$k]) || (array_key_exists('display', $cols[$k]) && $cols[$k]['display']==false)) 
					continue;
				if(!$cell) $cell='&nbsp;';
				if(is_string($cell)) $out_data[] = array('label'=>$cell,'attrs'=>'');
					else $out_data[] = $cell;
			}
		}
		unset($data);
		$th->assign('data',$out_data);

		Base_ThemeCommon::display_smarty($th,'Utils/GenericBrowser','mobile');
	}
}

?>
