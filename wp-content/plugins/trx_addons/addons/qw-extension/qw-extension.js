jQuery( document ).ready(function() {
	"use strict";

	var $window              = jQuery( window ),
		$document            = jQuery( document ),
		$body                = jQuery( 'body' );

	// Init elements
	//------------------------------------------
	
	$document.on( 'action.init_hidden_elements', function(e, cont) {

		// Featured image as panel
		jQuery('.sc_services_qw-panel .sc_services_item:not(.switch_qw_panel_inited)').addClass('switch_qw_panel_inited').each(function() {
			var $self = jQuery(this);
			var $text = $self.find('.sc_services_item_text');
			$self.on('mouseenter', function() {
				var num = $self.data('item-number'),
					$posts = $self.parents('.sc_item_posts_container'),
					$old_panel = $posts.find('.sc_qw_panel_thumb_active').removeClass('sc_qw_panel_thumb_active'),
					$new_panel = $posts.find('.sc_qw_panel_thumb[data-thumb-number="' + num + '"]').addClass('sc_qw_panel_thumb_active');
				$document.trigger( 'action.init_hidden_elements', [$new_panel] );
			});

			function assign_hover($self, $text) {
				$self
					.off('.qw-panel')
					.on('mouseenter.qw-panel', function() {
						if ($text.css('display') == 'none') {
							$text.stop().slideDown(300);
						}
					})
					.on('mouseleave.qw-panel', function() {
						$text.stop().slideUp(300);
					});
			}
			var reassign_hover = trx_addons_throttle(function() {
				assign_hover($self, $text);
			}, 1100);
			assign_hover($self, $text);
			$document.on( 'action.resize_trx_addons', function() {
				reassign_hover($self, $text);
			});
		});


		// Masonry for testimonials Date
		cont.find( '.sc_testimonials_masonry_wrap').each( function() {
			var testimonials = jQuery(this);
			if ( testimonials.parents( 'div:hidden,article:hidden' ).length > 0 ) return;
			if ( ! testimonials.hasClass( 'inited' ) ) {
				trx_addons_when_images_loaded( testimonials, function() {
					setTimeout( function() {
						testimonials.masonry( {
							itemSelector: '.sc_testimonials_masonry_item',
							columnWidth: '.sc_testimonials_masonry_item',
							percentPosition: true
						} );
						testimonials.addClass( 'inited' );
						// Trigger events after masonry layout is finished
						jQuery( window ).trigger( 'resize' );
						jQuery( window ).trigger( 'scroll' );
					}, 10 );
				});
			} else {
				// Relayout after 
				//setTimeout( function() { testimonials.masonry(); }, 310 );
			}
		});

	});


	// Change param "slides per" in exactly sliders
	trx_addons_add_filter( 'trx_addons_filter_slider_init_args', function( $param, $init ) {
		if( $init.parents('.slider_width_auto').length > 0 ) {
			$param.slidesPerView = 'auto';
		}
		return $param;
	});


	// QW Case
	jQuery('.sc_portfolio_qw-case:not(.qw_case_inited)').addClass('qw_case_inited').each(function() {
		var items = jQuery(this).find('.sc_portfolio_item');
		items.first().addClass('is-active');
		items.each( function() {
			var $item = jQuery(this);
			$item.off('.qw-case')
			.on('mouseenter.qw-case', function() {
				items.removeClass('is-active');
				$item.addClass('is-active');
			});
		});
	});

});