window._ = require('lodash');
try {
	window.Popper = require('popper.js').default;
	window.$ = window.jQuery = require('jquery');
	require('bootstrap');
	require('jquery.easing');
	require('chart.js');
	require('datatables.net');
	require('datatables.net-bs4');
	require('./chart-area-demo.js');
	require('./chart-pie-demo.js');
	require('./chart-bar-demo.js');
} catch (e) {}

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

jQuery(document).ready(function($){

	var body = $('body'),
		autocomplete = false,
		autofill = body.find('.location-autofill')
	;

	autocomplete = new google.maps.places.Autocomplete(
		$('.location-autofill .address')[0]
	);
	autocomplete.addListener("place_changed", () => {
		var place   = autocomplete.getPlace(),
			parts   = {
				postal_code: 'postcode',
				administrative_area_level_3: 'city',
				//administrative_area_level_1: 'state',
				country: 'country'
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
			var type = component.types[0];
			if (typeof parts[type] !== 'undefined') {
				res[ parts[type]  ] = component['long_name'];
			}
		}

		for(const [k, v] of Object.entries(res)){
			autofill.find('.' + k).val(v);
		}

	});


	$(document).on('change', '.status-autoupate', function(e){
		e.preventDefault();
		if (confirm('Are you sure to perform this action?') === false) {
			return false;
		}
		var that = $(this), form = that.parent();
		$.post(form.attr('action'), form.serialize(), function (res) {
			if (res === 'success') {
				alert('Action performed successfully');
			}
		});
	});


	$(document).on('click', '.upload-wrap', function(e){
		$(this).siblings('.upload-input').trigger('click');
	});

	$(document).on('change', '.upload-input', function(e){
		var preview = $(this).siblings('.upload-wrap'),
			file = $(this)[0].files[0],
			reader = new FileReader()
		;
		reader.addEventListener("load", function (e) {
			preview.empty();
			$("<image class='image-ph' src='" + reader.result + "'/>").appendTo(preview);
		}, false);

		reader.readAsDataURL(file);
	});

	$(document).on('click', '.upload-wrap', function(e){
		$(this).siblings('.upload-input').trigger('click');
	});


	if($('#dataTable').length > 0){
		$('#dataTable').DataTable();
	}

	// Toggle the side navigation
	$("#sidebarToggle, #sidebarToggleTop").on('click', function(e) {
		$("body").toggleClass("sidebar-toggled");
		$(".sidebar").toggleClass("toggled");
		if ($(".sidebar").hasClass("toggled")) {
			$('.sidebar .collapse').collapse('hide');
		}
	});

	// Close any open menu accordions when window is resized below 768px
	$(window).resize(function() {
		if ($(window).width() < 768) {
			$('.sidebar .collapse').collapse('hide');
		};

		// Toggle the side navigation when window is resized below 480px
		if ($(window).width() < 480 && !$(".sidebar").hasClass("toggled")) {
			$("body").addClass("sidebar-toggled");
			$(".sidebar").addClass("toggled");
			$('.sidebar .collapse').collapse('hide');
		};
	});

	// Prevent the content wrapper from scrolling when the fixed side navigation hovered over
	$('body.fixed-nav .sidebar').on('mousewheel DOMMouseScroll wheel', function(e) {
		if ($(window).width() > 768) {
			var e0 = e.originalEvent,
				delta = e0.wheelDelta || -e0.detail;
			this.scrollTop += (delta < 0 ? 1 : -1) * 30;
			e.preventDefault();
		}
	});

	// Scroll to top button appear
	$(document).on('scroll', function() {
		var scrollDistance = $(this).scrollTop();
		if (scrollDistance > 100) {
			$('.scroll-to-top').fadeIn();
		} else {
			$('.scroll-to-top').fadeOut();
		}
	});

	// Smooth scrolling using jQuery easing
	$(document).on('click', 'a.scroll-to-top', function(e) {
		var $anchor = $(this);
		$('html, body').stop().animate({
			scrollTop: ($($anchor.attr('href')).offset().top)
		}, 1000, 'easeInOutExpo');
		e.preventDefault();
	});
});
