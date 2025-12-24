@extends('app.template.layout')

@section('content')
<style>
	/* Modern Helpdesk Design */
	.helpdesk-hero {
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		border-radius: 20px;
		padding: 4rem 2rem;
		color: white;
		margin-bottom: 3rem;
		box-shadow: 0 10px 40px rgba(0,0,0,0.1);
		text-align: center;
	}

	.helpdesk-hero h1 {
		font-size: 2.5rem;
		font-weight: 700;
		margin-bottom: 1rem;
	}

	.helpdesk-hero p {
		font-size: 1.2rem;
		opacity: 0.95;
		margin-bottom: 0;
	}

	/* Modern Tab Navigation */
	.helpdesk-tabs {
		background: white;
		border-radius: 16px;
		padding: 1.5rem;
		margin-bottom: 2rem;
		box-shadow: 0 4px 20px rgba(0,0,0,0.08);
		display: flex;
		flex-wrap: wrap;
		gap: 1rem;
		justify-content: center;
	}

	.helpdesk-tab {
		padding: 0.875rem 2rem;
		border-radius: 50px;
		font-weight: 600;
		font-size: 1rem;
		cursor: pointer;
		transition: all 0.3s ease;
		border: 2px solid #e2e8f0;
		background: white;
		color: #4a5568;
		display: flex;
		align-items: center;
		gap: 0.5rem;
		text-decoration: none;
	}

	.helpdesk-tab:hover {
		border-color: #667eea;
		color: #667eea;
		transform: translateY(-2px);
		box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
	}

	.helpdesk-tab.active {
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		border-color: #667eea;
		color: white;
		box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
	}

	.helpdesk-tab i {
		font-size: 1.1rem;
	}

	/* Search Bar */
	.helpdesk-search {
		background: white;
		border-radius: 16px;
		padding: 1.5rem;
		margin-bottom: 2rem;
		box-shadow: 0 4px 20px rgba(0,0,0,0.08);
	}

	.search-input-wrapper {
		position: relative;
	}

	.search-input-wrapper i {
		position: absolute;
		left: 1.25rem;
		top: 50%;
		transform: translateY(-50%);
		color: #a0aec0;
		font-size: 1.1rem;
	}

	.search-input {
		width: 100%;
		padding: 1rem 1rem 1rem 3.5rem;
		border: 2px solid #e2e8f0;
		border-radius: 12px;
		font-size: 1rem;
		transition: all 0.3s ease;
	}

	.search-input:focus {
		outline: none;
		border-color: #667eea;
		box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
	}

	/* Modern Accordion */
	.helpdesk-accordion {
		background: white;
		border-radius: 16px;
		padding: 2rem;
		box-shadow: 0 4px 20px rgba(0,0,0,0.08);
	}

	.faq-item {
		border-bottom: 1px solid #e2e8f0;
		margin-bottom: 1rem;
		padding-bottom: 1rem;
		transition: all 0.3s ease;
	}

	.faq-item:last-child {
		border-bottom: none;
		margin-bottom: 0;
		padding-bottom: 0;
	}

	.faq-header {
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding: 1.25rem 1.5rem;
		background: #f7fafc;
		border-radius: 12px;
		cursor: pointer;
		transition: all 0.3s ease;
		user-select: none;
	}

	.faq-header:hover {
		background: #edf2f7;
		transform: translateX(4px);
	}

	.faq-header.active {
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		color: white;
		box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
	}

	.faq-header.active:hover {
		transform: translateX(4px) scale(1.01);
	}

	.faq-title {
		font-size: 1.1rem;
		font-weight: 600;
		margin: 0;
		flex: 1;
		line-height: 1.5;
	}

	.faq-icon {
		font-size: 1.2rem;
		transition: transform 0.3s ease;
		margin-left: 1rem;
	}

	.faq-header.active .faq-icon {
		transform: rotate(180deg);
	}

	.faq-content {
		max-height: 0;
		overflow: hidden;
		transition: max-height 0.4s ease, padding 0.4s ease;
		padding: 0 1.5rem;
	}

	.faq-content.active {
		max-height: 2000px;
		padding: 1.5rem;
	}

	.faq-content-inner {
		font-size: 1rem;
		line-height: 1.8;
		color: #4a5568;
	}

	.faq-content.active .faq-content-inner {
		animation: fadeIn 0.4s ease;
	}

	@keyframes fadeIn {
		from {
			opacity: 0;
			transform: translateY(-10px);
		}
		to {
			opacity: 1;
			transform: translateY(0);
		}
	}

	/* Empty State */
	.empty-state {
		text-align: center;
		padding: 4rem 2rem;
		background: white;
		border-radius: 16px;
		box-shadow: 0 4px 20px rgba(0,0,0,0.08);
	}

	.empty-state-icon {
		font-size: 4rem;
		color: #cbd5e0;
		margin-bottom: 1.5rem;
	}

	.empty-state h3 {
		font-size: 1.5rem;
		font-weight: 700;
		color: #2d3748;
		margin-bottom: 1rem;
	}

	.empty-state p {
		color: #718096;
		font-size: 1.1rem;
	}

	/* No Results */
	.no-results {
		text-align: center;
		padding: 3rem 2rem;
		background: white;
		border-radius: 16px;
		box-shadow: 0 4px 20px rgba(0,0,0,0.08);
		display: none;
	}

	.no-results.show {
		display: block;
	}

	.no-results-icon {
		font-size: 3rem;
		color: #cbd5e0;
		margin-bottom: 1rem;
	}

	/* Contact Section */
	.helpdesk-contact {
		background: linear-gradient(135deg, #f6f8fb 0%, #e9ecef 100%);
		border-radius: 16px;
		padding: 2rem;
		margin-top: 3rem;
		text-align: center;
	}

	.helpdesk-contact h3 {
		font-size: 1.5rem;
		font-weight: 700;
		color: #2d3748;
		margin-bottom: 1.5rem;
	}

	.contact-info {
		display: flex;
		justify-content: center;
		gap: 3rem;
		flex-wrap: wrap;
	}

	.contact-item {
		display: flex;
		align-items: center;
		gap: 0.75rem;
		color: #4a5568;
		font-size: 1.1rem;
	}

	.contact-item i {
		color: #667eea;
		font-size: 1.3rem;
	}

	.contact-item a {
		color: #667eea;
		text-decoration: none;
		font-weight: 600;
		transition: all 0.3s ease;
	}

	.contact-item a:hover {
		color: #764ba2;
		text-decoration: underline;
	}

	/* Responsive */
	@media (max-width: 768px) {
		.helpdesk-hero {
			padding: 2.5rem 1.5rem;
		}

		.helpdesk-hero h1 {
			font-size: 2rem;
		}

		.helpdesk-tabs {
			flex-direction: column;
		}

		.helpdesk-tab {
			width: 100%;
			justify-content: center;
		}

		.contact-info {
			flex-direction: column;
			gap: 1.5rem;
		}

		.faq-header {
			padding: 1rem;
		}

		.faq-title {
			font-size: 1rem;
		}
	}

	/* Smooth scroll */
	html {
		scroll-behavior: smooth;
	}
</style>

<div class="pt-5 pb-5" style="background: #f7fafc; min-height: 100vh;">
	<div class="container">
		<!-- Hero Section -->
		<div class="helpdesk-hero">
			<h1>
				<i class="fa fa-question-circle mr-3"></i>Helpdesk
			</h1>
			<p>Find answers to frequently asked questions</p>
		</div>

		<!-- Tab Navigation -->
		<div class="helpdesk-tabs">
			@foreach($roles ?? [] as $role)
				<a href="#{{ $role->slug }}" 
				   class="helpdesk-tab @if($role->slug == 'client') active @endif" 
				   data-tab="{{ $role->slug }}">
					@if($role->slug == 'client')
						<i class="fa fa-user"></i>
					@elseif($role->slug == 'carer')
						<i class="fa fa-heart"></i>
					@elseif($role->slug == 'childminder')
						<i class="fa fa-child"></i>
					@elseif($role->slug == 'housekeeper')
						<i class="fa fa-home"></i>
					@else
						<i class="fa fa-users"></i>
					@endif
					{{ ucfirst($role->title) }}
				</a>
			@endforeach
		</div>

		<!-- Search Bar -->
		<div class="helpdesk-search">
			<div class="search-input-wrapper">
				<i class="fa fa-search"></i>
				<input type="text" 
				       id="faq-search" 
				       class="search-input" 
				       placeholder="Search for questions...">
			</div>
		</div>

		<!-- No Results Message -->
		<div class="no-results" id="no-results">
			<div class="no-results-icon">
				<i class="fa fa-search"></i>
			</div>
			<h3>No results found</h3>
			<p>Try adjusting your search terms or browse by category above.</p>
		</div>

		<!-- Tab Content -->
		<div class="tab-content" id="myTabContent">
			@foreach($roles ?? [] as $role)
				<div role="tabpanel" 
				     id="{{ $role->slug }}" 
				     class="tab-panel @if($role->slug != 'client') d-none @endif"
				     data-role="{{ $role->slug }}">
					@if(count($role->faqs ?? []) > 0)
						<div class="helpdesk-accordion">
							@foreach($role->faqs ?? [] as $index => $faq)
								<div class="faq-item" data-faq-index="{{ $index }}">
									<div class="faq-header @if($index == 0 && $role->slug == 'client') active @endif">
										<h3 class="faq-title">{!! $faq->title !!}</h3>
										<i class="fa fa-chevron-down faq-icon"></i>
									</div>
									<div class="faq-content @if($index == 0 && $role->slug == 'client') active @endif">
										<div class="faq-content-inner">
											{!! $faq->desc !!}
										</div>
									</div>
								</div>
							@endforeach
						</div>
					@else
						<div class="empty-state">
							<div class="empty-state-icon">
								<i class="fa fa-inbox"></i>
							</div>
							<h3>No FAQs Available</h3>
							<p>There are no frequently asked questions for {{ ucfirst($role->title) }} at the moment.</p>
						</div>
					@endif
				</div>
			@endforeach
		</div>

		<!-- Contact Section -->
		<div class="helpdesk-contact">
			<h3>Still have questions?</h3>
			<div class="contact-info">
				<div class="contact-item">
					<i class="fa fa-envelope"></i>
					<a href="mailto:support@jimacare.com">support@jimacare.com</a>
				</div>
				<div class="contact-item">
					<i class="fa fa-phone"></i>
					<a href="tel:01182303044">01182303044</a>
				</div>
			</div>
		</div>
	</div>
</div>

@push('scripts')
<script>
	(function() {
		// Initialize when DOM is ready
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', initHelpdesk);
		} else {
			initHelpdesk();
		}

		function initHelpdesk() {
			// Tab Switching
			var tabs = document.querySelectorAll('.helpdesk-tab');
			var panels = document.querySelectorAll('.tab-panel');
			var noResults = document.getElementById('no-results');

			tabs.forEach(function(tab) {
				tab.addEventListener('click', function(e) {
					e.preventDefault();
					var targetId = this.getAttribute('href');
					var targetTab = this.getAttribute('data-tab');

					// Update active tab
					tabs.forEach(function(t) {
						t.classList.remove('active');
					});
					this.classList.add('active');

					// Show/hide panels
					panels.forEach(function(panel) {
						panel.classList.add('d-none');
					});

					var targetPanel = document.querySelector(targetId);
					if (targetPanel) {
						targetPanel.classList.remove('d-none');
						// Scroll to top of content
						setTimeout(function() {
							targetPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
						}, 100);
					}

					// Clear search when switching tabs
					var searchInput = document.getElementById('faq-search');
					if (searchInput) {
						searchInput.value = '';
						performSearch('');
					}
				});
			});

			// Accordion Functionality
			function initAccordion() {
				var faqHeaders = document.querySelectorAll('.faq-header');
				faqHeaders.forEach(function(header) {
					header.addEventListener('click', function(e) {
						e.preventDefault();
						e.stopPropagation();

						var content = this.nextElementSibling;
						var isActive = this.classList.contains('active');

						// Close all in the same panel (optional - remove if you want multiple open)
						// var panel = this.closest('.tab-panel');
						// if (panel) {
						// 	var allHeaders = panel.querySelectorAll('.faq-header');
						// 	var allContents = panel.querySelectorAll('.faq-content');
						// 	allHeaders.forEach(function(h) {
						// 		h.classList.remove('active');
						// 	});
						// 	allContents.forEach(function(c) {
						// 		c.classList.remove('active');
						// 	});
						// }

						// Toggle current
						if (isActive) {
							this.classList.remove('active');
							content.classList.remove('active');
						} else {
							this.classList.add('active');
							content.classList.add('active');
						}
					});
				});
			}

			// Initialize accordion
			initAccordion();

			// Re-initialize accordion when tabs change
			var observer = new MutationObserver(function(mutations) {
				mutations.forEach(function(mutation) {
					if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
						var target = mutation.target;
						if (!target.classList.contains('d-none')) {
							initAccordion();
						}
					}
				});
			});

			panels.forEach(function(panel) {
				observer.observe(panel, { attributes: true, attributeFilter: ['class'] });
			});

			// Search Functionality
			var searchInput = document.getElementById('faq-search');
			if (searchInput) {
				searchInput.addEventListener('input', function(e) {
					performSearch(this.value.toLowerCase().trim());
				});

				searchInput.addEventListener('keydown', function(e) {
					if (e.key === 'Enter') {
						e.preventDefault();
					}
				});
			}

			function performSearch(query) {
				var activePanel = document.querySelector('.tab-panel:not(.d-none)');
				if (!activePanel) return;

				var faqItems = activePanel.querySelectorAll('.faq-item');
				var hasResults = false;

				faqItems.forEach(function(item) {
					var title = item.querySelector('.faq-title').textContent.toLowerCase();
					var content = item.querySelector('.faq-content-inner').textContent.toLowerCase();
					var matches = title.includes(query) || content.includes(query);

					if (query === '' || matches) {
						item.style.display = '';
						hasResults = true;
					} else {
						item.style.display = 'none';
					}
				});

				// Show/hide no results message
				if (query !== '' && !hasResults) {
					noResults.classList.add('show');
					if (activePanel.querySelector('.helpdesk-accordion')) {
						activePanel.querySelector('.helpdesk-accordion').style.display = 'none';
					}
				} else {
					noResults.classList.remove('show');
					if (activePanel.querySelector('.helpdesk-accordion')) {
						activePanel.querySelector('.helpdesk-accordion').style.display = '';
					}
				}
			}

			// Smooth scroll for anchor links
			document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
				anchor.addEventListener('click', function(e) {
					var href = this.getAttribute('href');
					if (href !== '#' && href.startsWith('#')) {
						var target = document.querySelector(href);
						if (target) {
							e.preventDefault();
							setTimeout(function() {
								target.scrollIntoView({ behavior: 'smooth', block: 'start' });
							}, 100);
						}
					}
				});
			});
		}
	})();
</script>
@endpush
@endsection
