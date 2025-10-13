function moduleLoad(element, spinner) {
	if (spinner) {
		element.find('.quickcheckout-content').html('');
	} else {
		moduleLoaded(element, spinner);

		var width = element.width();
		var height = element.height();
		var margin = height / 2 - 30;

		
	}
}

function moduleLoaded(element, spinner) {
	if (!spinner) {
		element.find('.overlay').remove();

		element.removeAttr('style');
	}
}

function disableCheckout() {
	$('#quickcheckout-disable').css('opacity', '0.5');

	var width = $('#quickcheckout-disable').width();
	var height = $('#quickcheckout-disable').height();

	html = '<div class="disable-overlay" style="position:absolute;top:0;left:0;z-index:99999;background:none;width:' + width + 'px;height:' + height + 'px;text-align:center;"></div>';

	$('#quickcheckout-disable').css('position', 'relative').append(html);
}
