{{-- Shared Styles for Home, For Clients, and For Providers Pages --}}
<style>
	/* Hero Section */
	.hero-section {
		position: relative;
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		background-image: url('{{ asset("img/slider-banner.png") }}');
		background-size: cover;
		background-position: center;
		padding: 100px 0;
		min-height: 600px;
		display: flex;
		align-items: center;
	}
	.hero-overlay {
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background: linear-gradient(135deg, rgba(30, 55, 72, 0.85) 0%, rgba(216, 71, 39, 0.75) 100%);
	}
	.hero-content {
		position: relative;
		z-index: 2;
	}
	.hero-subtitle {
		color: #fff;
		font-size: 18px;
		font-weight: 400;
		margin-bottom: 15px;
		opacity: 0.9;
	}
	.hero-title {
		color: #fff;
		font-size: 48px;
		font-weight: 700;
		line-height: 1.2;
		margin-bottom: 20px;
	}
	.hero-description {
		color: rgba(255, 255, 255, 0.9);
		font-size: 18px;
		margin-bottom: 40px;
	}
	.min-vh-75 {
		min-height: 75vh;
	}

	/* Search Box Modern */
	.search-box-modern {
		background: rgba(255, 255, 255, 0.95);
		border-radius: 20px;
		padding: 30px;
		box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
		margin-top: 30px;
	}
	.form-group-modern {
		margin-bottom: 0;
	}
	.form-label-modern {
		display: block;
		font-size: 12px;
		font-weight: 600;
		color: #1E3748;
		margin-bottom: 8px;
		text-transform: uppercase;
		letter-spacing: 0.5px;
	}
	.form-label-modern i {
		margin-right: 5px;
		color: #D84727;
	}
	.form-control-modern {
		width: 100%;
		padding: 12px 15px;
		border: 2px solid #e0e0e0;
		border-radius: 10px;
		font-size: 16px;
		transition: all 0.3s ease;
	}
	.form-control-modern:focus {
		border-color: #D84727;
		outline: none;
		box-shadow: 0 0 0 3px rgba(216, 71, 39, 0.1);
	}
	.btn-search-modern {
		width: 100%;
		padding: 12px 20px;
		background: linear-gradient(135deg, #D84727 0%, #c0392b 100%);
		color: #fff;
		border: none;
		border-radius: 10px;
		font-size: 16px;
		font-weight: 600;
		cursor: pointer;
		transition: all 0.3s ease;
		margin-top: 28px;
	}
	.btn-search-modern:hover {
		transform: translateY(-2px);
		box-shadow: 0 5px 15px rgba(216, 71, 39, 0.4);
	}
	.popular-locations {
		margin-top: 20px;
		padding-top: 20px;
		border-top: 1px solid rgba(0, 0, 0, 0.1);
	}
	.location-tags {
		display: flex;
		flex-wrap: wrap;
		gap: 10px;
	}
	.location-tag {
		display: inline-block;
		padding: 6px 15px;
		background: rgba(216, 71, 39, 0.1);
		color: #D84727;
		border-radius: 20px;
		font-size: 13px;
		text-decoration: none;
		transition: all 0.3s ease;
	}
	.location-tag:hover {
		background: #D84727;
		color: #fff;
		text-decoration: none;
	}

	/* Trust Section */
	.trust-section {
		background: #fff;
		padding: 30px 0;
		box-shadow: 0 -5px 15px rgba(0, 0, 0, 0.05);
	}
	.trust-badges {
		display: flex;
		justify-content: space-around;
		flex-wrap: wrap;
		gap: 20px;
	}
	.trust-badge-item {
		display: flex;
		align-items: center;
		gap: 10px;
		font-weight: 600;
		color: #1E3748;
	}
	.trust-badge-item i {
		font-size: 24px;
		color: #D84727;
	}
	.cqc-badge {
		position: relative;
	}
	.cqc-logo-container {
		display: flex;
		align-items: center;
		justify-content: center;
		min-width: 40px;
		height: 45px;
	}
	.cqc-logo {
		height: 45px;
		width: auto;
		max-width: 140px;
		object-fit: contain;
		display: none;
	}
	.cqc-fallback {
		display: inline-block;
		font-size: 24px;
		color: #D84727;
	}
	@media (max-width: 768px) {
		.cqc-logo-container {
			height: 35px;
		}
		.cqc-logo {
			height: 35px;
			max-width: 100px;
		}
	}

	/* Section Titles */
	.section-title {
		font-size: 42px;
		font-weight: 700;
		color: #1E3748;
		margin-bottom: 15px;
	}
	.section-subtitle {
		font-size: 18px;
		color: #666;
		margin-bottom: 0;
	}

	/* How It Works */
	.how-it-works-section {
		background: #fff;
	}
	.step-card {
		text-align: center;
		padding: 30px 20px;
		border-radius: 15px;
		background: #fff;
		box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
		transition: all 0.3s ease;
		position: relative;
		height: 100%;
	}
	.step-card:hover {
		transform: translateY(-10px);
		box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
	}
	.step-icon {
		width: 80px;
		height: 80px;
		margin: 0 auto 20px;
		background: linear-gradient(135deg, #D84727 0%, #c0392b 100%);
		border-radius: 50%;
		display: flex;
		align-items: center;
		justify-content: center;
		color: #fff;
		font-size: 32px;
	}
	.step-number {
		position: absolute;
		top: -15px;
		right: -15px;
		width: 40px;
		height: 40px;
		background: #1E3748;
		color: #fff;
		border-radius: 50%;
		display: flex;
		align-items: center;
		justify-content: center;
		font-weight: 700;
		font-size: 18px;
	}
	.step-title {
		font-size: 20px;
		font-weight: 600;
		color: #1E3748;
		margin-bottom: 10px;
	}
	.step-description {
		color: #666;
		font-size: 14px;
		line-height: 1.6;
	}

	/* Why Choose Section */
	.why-choose-section {
		background: #f8f9fa;
	}
	.feature-list {
		margin-top: 30px;
	}
	.feature-item {
		display: flex;
		gap: 15px;
		margin-bottom: 25px;
	}
	.feature-item i {
		font-size: 24px;
		margin-top: 5px;
	}
	.feature-item h5 {
		font-weight: 600;
		color: #1E3748;
		margin-bottom: 5px;
	}
	.feature-item p {
		color: #666;
		margin: 0;
	}
	.verification-box {
		background: #fff;
		padding: 30px;
		border-radius: 15px;
		box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
	}
	.verification-steps {
		margin-top: 20px;
	}
	.verification-step {
		display: flex;
		gap: 20px;
		margin-bottom: 25px;
		padding-bottom: 25px;
		border-bottom: 1px solid #e0e0e0;
	}
	.verification-step:last-child {
		border-bottom: none;
		margin-bottom: 0;
		padding-bottom: 0;
	}
	.step-icon-box {
		width: 50px;
		height: 50px;
		background: rgba(216, 71, 39, 0.1);
		border-radius: 10px;
		display: flex;
		align-items: center;
		justify-content: center;
		color: #D84727;
		font-size: 20px;
		flex-shrink: 0;
	}
	.step-content h5 {
		font-weight: 600;
		color: #1E3748;
		margin-bottom: 5px;
	}
	.step-content p {
		color: #666;
		margin: 0;
		font-size: 14px;
	}

	/* CTA Section */
	.cta-section {
		background: linear-gradient(135deg, #1E3748 0%, #2c5f7d 100%);
	}
	.cta-buttons .btn {
		transition: all 0.3s ease;
	}
	.cta-buttons .btn:hover {
		transform: translateX(5px);
	}

	/* Responsive */
	@media (max-width: 768px) {
		.hero-title {
			font-size: 32px;
		}
		.hero-description {
			font-size: 16px;
		}
		.search-box-modern {
			padding: 20px;
		}
		.section-title {
			font-size: 32px;
		}
		.trust-badges {
			flex-direction: column;
			align-items: center;
		}
	}
</style>

