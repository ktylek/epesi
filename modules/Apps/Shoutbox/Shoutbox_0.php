<?php
/**
 * @author Paul Bukowski <pbukowski@telaxus.com>
 * @copyright Copyright &copy; 2008, Telaxus LLC
 * @license MIT
 * @version 1.0
 * @package epesi-apps
 * @subpackage shoutbox
 */
defined("_VALID_ACCESS") || die('Direct access forbidden');

class Apps_Shoutbox extends Module {

	public function body() {
		/* Do not delete anything - all messages are kept in history
		// to allow delete by sdmin uncomment below lines
		// if i am admin add "clear shoutbox" actionbar button
		if(Base_AclCommon::i_am_admin())
			Base_ActionBarCommon::add('delete','Clear shoutbox',$this->create_callback_href(array($this,'delete_all')));
		*/
		
		$tb = & $this->init_module('Utils/TabbedBrowser');
		$tb->set_tab('Chat', array($this,'chat'),true,null);
		$tb->set_tab('History', array($this,'history'),null);
		$this->display_module($tb);
    }
	
	public function history($uid=null) {	
		$th = $this->init_module('Base/Theme');
		$th->assign('header', $this->t('Shoutbox History'));

		$gb = & $this->init_module('Utils/GenericBrowser',null,'shoutbox_history');

		$gb->set_table_columns(array(
						array('name'=>$this->t('From'),'width'=>10),
						array('name'=>$this->t('To'),'width'=>10),
						array('name'=>$this->t('Message'),'width'=>64),
						array('name'=>$this->t('Date'),'width'=>16)
						));

        // $gb->set_default_order(array($this->t('Date')=>'DESC'));

		$query = 'SELECT base_user_login_id, to_user_login_id, message, posted_on FROM apps_shoutbox_messages ORDER BY posted_on DESC';
        $query_qty = 'SELECT count(id) FROM apps_shoutbox_messages';

		$ret = $gb->query_order_limit($query, $query_qty);

        if($ret)
			while(($row=$ret->FetchRow())) {
				$ulogin = Base_UserCommon::get_user_login($row['base_user_login_id']);
				if($row['to_user_login_id']!==null)
    				$tologin = Base_UserCommon::get_user_login($row['to_user_login_id']);
    		    else
    		        $tologin = $this->t('-- all --');
                $gb->add_row('<span class="author">'.$ulogin.'</span>','<span class="author">'.$tologin.'</span>',Utils_BBCodeCommon::parse($row['message']),Base_RegionalSettingsCommon::time2reg($row['posted_on']));
			}

		$th->assign('messages',$this->get_html_of_module($gb));
		$th->display();
            return true;
	}

	//delete_all callback (on "clear shoutbox" button)
	public function delete_all() {
		DB::Execute('DELETE FROM apps_shoutbox_messages');
	}

	public function applet($conf,$opts) {
		$opts['go'] = true;
		$this->chat();
    }
    
    public function chat($big=false,$uid=null) {
		Base_ThemeCommon::load_css($this->get_type());
		$to = & $this->get_module_variable('to',"all");
		eval_js('shoutbox_uid="'.$to.'"');
		if(Acl::is_user()) {
			//initialize HTML_QuickForm
			$qf = & $this->init_module('Libs/QuickForm');
			$g = array();
			if($big) {
                $myid = Acl::get_user();
			    if(ModuleManager::is_installed('CRM_Contacts')>=0) {
    			    $emps = DB::GetAssoc('SELECT l.id,IF(cd.f_last_name!=\'\',CONCAT(cd.f_last_name,\' \',cd.f_first_name),l.login) FROM user_login l LEFT JOIN contact_data_1 cd ON (cd.f_login=l.id AND cd.active=1) WHERE l.active=1 AND l.id!=%d',array($myid));			    
			    } else
    			    $emps = DB::GetAssoc('SELECT id,login FROM user_login WHERE active=1 AND l.id!=%d',array($myid));
    			$g[] = HTML_QuickForm::createElement('select','to',$this->t('To'),array('all'=>$this->ht('-- all --'))+$emps,array('id'=>'shoutbox_to','onChange'=>'shoutbox_uid=this.value;shoutbox_refresh_big()'));
			}
			//create text box
			$g[] = HTML_QuickForm::createElement('text','post',$this->t('Post'),'id="shoutbox_text'.($big?'_big':'').'"');
			//create submit button
			$g[] = HTML_QuickForm::createElement('submit','button',$this->ht('Submit'), 'id="shoutbox_button'.($big?'_big':'').'"');
			//add it
			$qf->addGroup($g,'post');
			$qf->addGroupRule('post',$this->t('Field required'),'required',null,$big?3:2);
			$qf->setRequiredNote(null);
			$qf->setDefaults(array('post'=>array('to'=>$to)));

			//if submited
			if($qf->validate()) {
				 //get post group
				$msg = $qf->exportValue('post');
				$to = $msg['to'];
				//get msg from post group
				$msg = Utils_BBCodeCommon::optimize($msg['post']);
				//get logged user id
				$user_id = Acl::get_user();
				//clear text box and focus it
				eval_js('$(\'shoutbox_text'.($big?'_big':'').'\').value=\'\';focus_by_id(\'shoutbox_text'.($big?'_big':'').'\');shoutbox_uid="'.$to.'"');

				//insert to db
				DB::Execute('INSERT INTO apps_shoutbox_messages(message,base_user_login_id,to_user_login_id) VALUES(%s,%d,%d)',array(htmlspecialchars($msg,ENT_QUOTES,'UTF-8'),$user_id,$to==="all"?null:$to));
			}
			//display form
			$qf->display();
		} else {
			print($this->t('Please log in to post message').'<br>');
		}

		print('<div id=\'shoutbox_board'.($big?'_big':'').'\'></div>');

		//if shoutbox is diplayed, call myFunctions->refresh from refresh.php file every 5s
		eval_js_once('shoutbox_refresh'.($big?'_big':'').' = function(){if(!$(\'shoutbox_board'.($big?'_big':'').'\')) return;'.
			'new Ajax.Updater(\'shoutbox_board'.($big?'_big':'').'\',\'modules/Apps/Shoutbox/refresh.php\',{method:\'get\', parameters: { uid: shoutbox_uid }});'.
			'};setInterval(\'shoutbox_refresh'.($big?'_big':'').'()\','.($big?'10000':'30000').')');
		eval_js('shoutbox_refresh'.($big?'_big':'').'()');
	}

	public function caption() {
		return "Shoutbox";
	}
}

?>
