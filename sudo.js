var dosudo = {
	sudo: false,
	init: function(){
		self = this;
		self.sudo = $('#sudo');
		$('#sudo_handle')
			.live('click', function(){
				if($('#sudo_controls:visible').length > 0) {
					$('#sudo_controls').hide('slide', {}, 500);
				}
				else {
					$('#sudo_controls').load('/admin/sudo', function() {
						$('#sudo_controls').show('slide', {}, 500);
					});
				}
			});
	},
	setuser: function(a1, a2){
		$.post(
			'/admin/change_sudo',
			$('#sudo form').serialize(),
			function(response) {
				$('#sudo_handle').text(response.html['#sudo_handle']);
			}
		);
		return false;
	}
}

$(dosudo.init);