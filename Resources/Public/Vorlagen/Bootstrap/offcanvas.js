jQuery( document ).ready(function() {
	$('.navbar-fixed-top *').tooltip(
		{
			container: 'body',
			placement: 'bottom'
		}
	);
	$('.local_topspace *').tooltip(
		{
			container: 'body',
			placement: 'top'
		}
	);
});
