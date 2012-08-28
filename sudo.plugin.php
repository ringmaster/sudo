<?php

class sudoPlugin extends Plugin
{
	public function show_sudo()
	{
		$user = User::identify();
		if($user->can('super_user') || (isset($_SESSION['sudo']) && $_SESSION['sudo'] != '')) {
			return true;
		}
		return false;
	}

	public function action_init_theme_any()
	{
		if($this->show_sudo()) {
			Stack::add('template_header_javascript', Site::get_url('vendor', '/jquery.js'), 'jquery');
			Stack::add('template_header_javascript', Site::get_url('vendor', '/jquery-ui.min.js'), 'jquery-ui');
			Stack::add('template_header_javascript', $this->get_url('/sudo.js'), 'sudo', 'jquery');
		}
	}

	public function theme_footer($theme)
	{
		$out = '';
		if($this->show_sudo()) {
			$user = User::identify();
			$username = $user->displayname;
			$out = <<< FOOTER_DIV
<div id="sudo" style="position:fixed;top:30px;left:0px;background:#666;color:#fff;border-radius:0px 5px 5px 0px;font-size:small;">
<div id="sudo_controls" style="display:none;">
Hi!
</div>
<span id="sudo_handle" style="padding:5px;cursor:pointer;display:block;color:white;text-decoration:underline;">{$username}</span>
</div>
FOOTER_DIV;
		}

		return $out;
	}

	public function action_init()
	{
		$this->add_rule('"admin"/"sudo"', 'sudo');
		$this->add_rule('"admin"/"change_sudo"', 'change_sudo');
	}

	public function theme_route_sudo($theme)
	{
		$form = $this->get_form();
		$form->out();
	}

	public function get_form()
	{
		$users = Users::get_all();
		$users = Utils::array_map_field($users, 'username', 'id');

		$form = new FormUI('sudo');
		$form->append(new FormControlSelect('userlist', 'null:null', 'Become User:', $users ));
		$form->userlist->value = User::identify()->id;
		$form->append(new FormControlSubmit('submit', 'Submit'));
		$form->set_option('form_action', URL::get('sudo'));
		$form->onsubmit = 'return dosudo.setuser();';

		return $form;
	}

	public function theme_route_change_sudo()
	{
		$form = $this->get_form();
		$user_id = $form->userlist->value;
		$user = User::get_by_id($user_id);
		if($_SESSION['user_id'] == $user->id) {
			unset($_SESSION['sudo']);
		}
		else {
			$_SESSION['sudo'] = $user->id;
		}

		$ar = new AjaxResponse(200, 'Ok.');
		$ar->html('#sudo_handle', $user->displayname);
		$ar->out();
	}
}

?>