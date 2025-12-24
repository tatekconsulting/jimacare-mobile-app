window._ = require('lodash');
try {
	window.Popper = require('popper.js').default;
	window.$ = window.jQuery = require('jquery');
	//window.Vue = require('vue').default;
	window.axios = require('axios');
	window.Pusher = require('pusher-js');
	require('bootstrap');
	require('owl.carousel');
	require('raty-js');
	require('jquery-datetimepicker');
} catch (e) {}

import Echo from 'laravel-echo';
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.Echo = new Echo({
	broadcaster: 'pusher',
	key: process.env.MIX_PUSHER_APP_KEY,
	cluster: process.env.MIX_PUSHER_APP_CLUSTER,
	forceTLS: true
});

jQuery(document).ready(function ($) {

	var body = $('body'),
		autocomplete = false,
		autofill = body.find('.location-autofill')
	;

	window.Echo.private('inbox-' + parseInt($("meta[name='user-token']").attr('content')) )
		.listen('MessageEvent', (e) => {

			if( $(".message-list[data-message-user='" + e['id'] + "']").length > 0 ){

				var t = "" +
					"<div class='message-feed media'>" +
					"   <div class='float-left mr-3'>" +
					"       <img src='" + e['profile'] + "' alt='' class='img-avatar'>" +
					"   </div>" +
					"   <div class='media-body'>" +
					"       <div class='mf-content bg-primary'>" + e['message'] + "</div>" +
					"       <small class='mf-date'><i class='fa fa-clock-o'></i> " + e['sent_at'] + "</small>" +
					"   </div>" +
					"</div>"
				;

				$(t).appendTo(".message-list");
				$('.message-list').scrollTop(1000000);

			}else{
				var tag = $(".contact-list [data-ib-user=" + e['id']  + "] .latest-msg");
				tag.addClass('font-weight-bold');
				tag.text(e.message);
			}
		})
	;

	if ($('#ms-menu-trigger')[0]) {
		body.on('click', '#ms-menu-trigger', function() {
			$('.ms-menu').toggleClass('toggled');
		});
	}

	$('.raty.readable').raty({
		starType: 'i',
		readOnly: true
	});

	autocomplete = new google.maps.places.Autocomplete(
		$('.location-autofill .address')[0]
	);
	autocomplete.addListener("place_changed", () => {
		var place   = autocomplete.getPlace(),
			parts   = {
				postal_code: 'postcode',
				postal_town: 'city',
				country: 'country'
				//administrative_area_level_1: 'state',
			},
			res     = {
				lat: place.geometry.location.lat(),
				long: place.geometry.location.lng(),
				postcode: '',
				city: '',
				//state: '',
				country: ''
			}
		;

		for (let component of place.address_components) {
			var type='undefined';
			if (component['types'].indexOf('postal_code')>-1){
				type = component.types[component['types'].indexOf('postal_code')];
			}else if (component['types'].indexOf('postal_town')>-1){
				type = component.types[component['types'].indexOf('postal_town')];
			}else if (component['types'].indexOf('country')>-1){
				type = component.types[component['types'].indexOf('country')];
			}
			if (typeof parts[type] !== 'undefined') {
				res[ parts[type]  ] = component['long_name'];
			}
		}

		for(const [k, v] of Object.entries(res)){
			autofill.find('.' + k).val(v);
		}

	});

	if ($('.client-carousel').length > 0) {
		$('.client-carousel').owlCarousel({
			loop: true,
			margin: 0,
			nav: true,
			dots: false,
			animateOut: 'fadeOut',
			autoplay: true,
			autoplayHoverPause: true,
			navText : ["<i class='fa fa-chevron-left'></i>","<i class='fa fa-chevron-right'></i>"],
			responsive: {
				0: {
					items: 1
				},
				600: {
					items: 1
				},
				1000: {
					items: 1
				}
			}
		});
	}

	if ($('.team-carousel').length > 0) {
		$('.team-carousel').owlCarousel({
			loop: false,
			margin: 8,
			nav: true,
			dots: false,
			animateOut: 'fadeOut',
			autoplay: true,
			autoplayHoverPause: true,
			responsive: {
				0: {
					items: 1
				},
				600: {
					items: 2
				},
				1000: {
					items: 4
				}
			}
		});
	}

	if ($('.flexlible-carousel').length > 0) {
		$('.flexlible-carousel').owlCarousel({
			loop: true,
			margin: 0,
			nav: false,
			dots: true,
			autoplay: true,
			dotsContainer: '#carousel-custom-dots',
			responsive: {
				0: {
					items: 1
				},
				600: {
					items: 1
				},
				1000: {
					items: 1
				}
			}
		});
	}

	$(".accordian ul li h3").click(function (e) {
		e.preventDefault();
		$(this).toggleClass('active');
	});

	$(".help-accordian ul li h3").click(function (e) {
		e.preventDefault();
		$(this).toggleClass('active');
	});

	$(".filter-sidebar h3").click(function (e) {
		e.preventDefault();
		$(this).toggleClass('active');
	});

	$(".management-list a").click(function (e) {
		e.preventDefault();
		$(this).toggleClass('active');
	});

	body.on('click', '.helptabs > li > a', function(e){
		e.preventDefault();
		var that = $(this);
		$('.helptabs > li > a.active').removeClass('active');
		$('.tab-content > div:not(.d-none)').addClass('d-none');

		that.addClass('active');

		console.log(that.attr('href'));
		$(that.attr('href')).removeClass('d-none');

	});

	if( $('#video1').length > 0){
		var myVideo = $("#video1");
		var videoOverlay = $("#video-overlay");

		function playPause(){
			myVideo.play();
			videoOverlay.classList.add("toggle");
		}
	}

	body.on('change', "#profile", function(e) {
		var that = this;
		if (that.files && that.files[0]) {
			var form = $('.about-form'),
				formData = new FormData(form[0])
			;
			formData.append('profile', that.files[0]);
			$.ajax({
				url: form.attr('action'),
				type: form.attr('method'),
				data: formData,
				contentType: false,
				processData: false,
				success: function(res){
					if(res != 0){
						$('.upload-img img').attr('src', res);
					}else{
						console.log('file not uploaded');
					}
				},
			});
		}
	});

	body.on('change', "#video", function(e) {
		var that = this;
		if (that.files && that.files[0]) {
			var form = $('.about-form'),
				formData = new FormData(form[0])
			;
			formData.append('video', that.files[0]);
			$('progress').removeClass('d-none');
			$.ajax({
				xhr: function () {
					var xhr = new window.XMLHttpRequest();
					xhr.upload.addEventListener("progress", function (evt) {
						if (evt.lengthComputable) {
							var percentComplete = evt.loaded / evt.total;
							percentComplete = parseInt(percentComplete * 100);
							$('progress').attr('value', percentComplete);
						}
					}, false);
					return xhr;
				},
				url: form.attr('action'),
				type: form.attr('method'),
				data: formData,
				contentType: false,
				processData: false,
				success: function (res) {
					if (res.path !== 0) {
						$('.upload-video video').attr('src', res.path);
						alert('Success! Video uploaded successfully.')
					} else {
						alert('Whoops! Something went wrong video not uploaded.');
					}
				},
			});
		}
	});

	body.on('click', "#remove-video", function (e) {
		var that = this;
		var form = $('.about-form'), formData = new FormData(form[0]);
		formData.append('action', 'remove');
		$.ajax({
			url: form.attr('action'),
			type: form.attr('method'),
			data: formData,
			contentType: false,
			processData: false,
			success: function (res) {
				if (res !== 0) {
					$('.upload-video video').attr('src', '');
					$(that).remove();
					alert('Success! Video removed successfully.');
					window.location.reload;
				} else {
					alert('Whoops! Something went wrong video not uploaded.');
				}
			},
		});
	});

	/*body.on('change', '.cb-must-select input[type="checkbox"]', function (e) {
		var that = $(this),
			parent = that.closest('.cb-must-select') // parent().parent().parent()
		;
		if (parent.find('input[type="checkbox"]:checked').length > 0) {
			parent.find('input[type="checkbox"]').removeAttr('required');
		} else {
			parent.find('input[type="checkbox"]').attr('required', true);
		}

	});*/

	body.on('change', '.dbs', function(e){
		var checked = ($('.dbs:checked').val() == 'yes') ? true : false,
			inputs = $('.dbs_type, .dbs_issue, .dbs_cert')
		;
		if(checked){
			inputs.attr('required', true);
		}else{
			inputs.removeAttr('required');
		}

	});

	body.on('change', '.type_filter', function (e) {
		var that    = $(this),
			exp     = $('.experience_filter')
		;

		exp.find('option[data-type]:not(.d-none)').addClass('d-none');

		if( that.val() != '' ){
			exp.find("option[data-type='" + that.val() + "']").removeClass('d-none');
		}
		exp.val('');
	});

	body.on('submit', '.msb-reply', function (e) {
		e.preventDefault();
		var that = $(this);
		if(that.find('#message').val().length > 0){
			$.post(that.attr('action'), that.serialize(), function (res) {
				that.find('#message').val('');
				var temp = "" +
					"<div class='message-feed right'>" +
					"   <div class='float-right ml-3'>" +
					"       <img src='" +res['profile'] +  "' alt='' class='img-avatar'>" +
					"   </div>" +
					"   <div class='media-body'>" +
					"       <div class='mf-content'>" + res['message'] + "</div>" +
					"       <small class='mf-date'><i class='fa fa-clock-o'></i>" + res['sent_at'] + "</small>" +
					"   </div>" +
					"</div>"
				;
				$(temp).appendTo('.message-list');
				$('.message-list').scrollTop(1000000);
			});
		}else{
			that.find('#message')[0].focus();
		}
	});

	body.on('click', '.a-upload-photo, .a-upload-video', function (e) {
		var that = $(this);
		that.siblings('input').trigger('click');
	});

	body.on('click', '.btn-upload', function (e) {
		var that = $(this);
		that.siblings('input').trigger('click');
	});

	body.on('click', '.btn-trash', function (e) {
		var that = $(this);
		that.parent().parent().remove();
	});

	body.on('submit', '.mini-msg-box', function (e) {
		e.preventDefault();
		var that = $(this);
		$.post(that.attr('action'), that.serialize(), function () {
			that.addClass('d-none');
			$('.msg-sent-notice').removeClass('d-none');
		});
	});

	body.on('click', '.btn-interested', function (e) {
		$('.btn-interested, .btn-not-interested').addClass('d-none');
		$('.mini-msg-box').removeClass('d-none');
	});

	body.on('click', '.invoice-btn', function (e) {
		e.preventDefault();
		var that = $(this),
			model = $('#send-invoice-model'),
			form = model.find('form')
		;
		model.modal('show');
	});

	body.on('submit', '#send-invoice-model form', function (e) {
		e.preventDefault();
		var that = $(this),
			model = $('#send-invoice-model')
		;
		$.post(that.attr('action'), that.serialize(), function (res) {
			console.log(res);
			var temp = "" +
				"<div class='message-feed right'>" +
				"   <div class='float-right ml-3'>" +
				"       <img src='" +res['profile'] +  "' alt='' class='img-avatar'>" +
				"   </div>" +
				"   <div class='media-body'>" +
				"       <div class='mf-content invoice border border-primary'>" +
				"			<h5>Custom Invoice <span class='float-right'>£" + res['invoice']['price'] + "</span></h5>" +
				"			<p>" + res['message'] + "</p>" +
				"           <div class='btn-group w-100'>" +
				"               <a href='" + res['invoice']['cancel'] + "' class='btn btn-primary cancel-invoice-request'>Cancel</a>" +
				"           </div>" +
				"       </div>" +
				"       <small class='mf-date'><i class='fa fa-clock-o'></i>" + res['sent_at'] + "</small>" +
				"   </div>" +
				"</div>"
			;
			$(temp).appendTo('.message-list');
			$('.message-list').scrollTop(1000000);


			model.modal('hide');
		});

	});

	/* 1. Visualizing things on Hover - See next part for action on click */
	$('#stars li').on('mouseover', function(){
		var onStar = parseInt($(this).data('value'), 10); // The star currently mouse on
		// Now highlight all the stars that's not after the current hovered star
		$(this).parent().children('li.star').each(function(e){
			if (e < onStar) {
				$(this).addClass('hover');
			}
			else {
				$(this).removeClass('hover');
			}
		});

	}).on('mouseout', function(){
		$(this).parent().children('li.star').each(function(e){
			$(this).removeClass('hover');
		});
	});


	/* 2. Action to perform on click */
	$('#stars li').on('click', function(){
		var onStar = parseInt($(this).data('value'), 10); // The star currently selected
		var stars = $(this).parent().children('li.star');

		for (let i = 0; i < stars.length; i++) {
			$(stars[i]).removeClass('selected');
		}

		for (let i = 0; i < onStar; i++) {
			$(stars[i]).addClass('selected');
		}

		// JUST RESPONSE (Not needed)
		var ratingValue = parseInt($('#stars li.selected').last().data('value'), 10);
		$('input[name=stars]').val(ratingValue);
	});
});



/*const files = require.context('./app', true, /\.vue$/i)
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

//Vue.component('example-component', require('./components/ExampleComponent.vue').default);
const app = new Vue({
	el: '#app',
});*/
