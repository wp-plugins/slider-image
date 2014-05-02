jQuery(document).ready(function () {
	
});

  $(function() {
    $( "#images-list" ).sortable({
      stop: function() {
			jQuery("#images-list li").removeClass('has-background');
			count=jQuery("#images-list li").length;
			for(var i=0;i<=count;i+=2){
					jQuery("#images-list li").eq(i).addClass("has-background");
			}
			jQuery("#images-list li").each(function(){
				jQuery(this).find('.order_by').val(jQuery(this).index());
			});
      },
      revert: true
    });
    $( "ul, li" ).disableSelection();
  });