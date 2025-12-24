@extends('app.template.layout')

@section('content')
	<div class="container py-5">
		<div class="row">
			<div class="col-lg-6 mx-auto">
				<div class="card ">
					<div class="card-header">
						<div id="credit-card" class="tab-pane fade show active pt-3">
							<form role="form" action="{{ route('invoice.processPayment', ['invoice' => $invoice->id]) }}" method="post" class="validation"
								  data-cc-on-file="false"
								  data-stripe-publishable-key="{{ env('STRIPE_KEY') }}"
								  id="payment-form"
							>
								@csrf
								<div class="form-group required">
									<label for="username">
										<h6>Card Owner</h6>
									</label>
									<input type="text" name="username" placeholder="Card Owner Name" required class="form-control ">
								</div>
								<div class="form-group reuired">
									<label for="cardNumber">
										<h6>Card Number</h6>
									</label>
									<div class="input-group">
										<input type="text" name="cardNumber" placeholder="Card number" class="form-control card-num" required>
										<div class="input-group-append">
											<span class="input-group-text text-muted">
												<i class="fa fa-cc-visa mx-1"></i>
												<i class="fa fa-cc-mastercard mx-1"></i>
												<i class="fa fa-cc-amex mx-1"></i>
											</span>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-8">
										<div class="form-group">
											<label>
												<span class="hidden-xs">
													<h6>Expiration Date</h6>
												</span>
											</label>
											<div class="input-group">
												<input type='number' class='form-control card-expiry-month' size='2'
													   placeholder="MM" min="1" max="12" required
												/>
												<input type="number" class="form-control card-expiry-year"
													   placeholder="YY" min="{{ date('Y') }}" required
												/>
											</div>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group mb-4">
											<label data-toggle="tooltip" title="Three digit CV code on the back of your card">
												<h6>CVV</h6>
											</label>
											<input type='text' autocomplete='off' class='form-control card-cvc' placeholder='e.g 415' size='4'>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-12">
										<button type="submit" class="subscribe btn btn-primary btn-block shadow-sm"> Pay Now £{{ $invoice->price }} </button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	@push('scripts')
		<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
		<script type="text/javascript">
			jQuery(document).ready(function ($) {
				var form = $(".validation");

				function stripeHandleResponse(status, response) {
					if (response.error) {
						console.log(response.error);
						$('.error')
							.removeClass('hide')
							.find('.alert')
							.text(response.error.message)
						;
					} else {
						var token = response['id'];
						form.find('input[type=text]').empty();
						form.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
						form.get(0).submit();
					}
				}

				form.on('submit', function (e) {
					e.preventDefault();
					var that 			= $(this),
						inputVal		= ['input[type=email]', 'input[type=password]',
						'input[type=text]', 'input[type=file]',
						'textarea'].join(', '),
						inputs       	= that.find('.required').find(inputVal),
						errorStatus		= that.find('div.error'),
						valid         	= true
					;
					errorStatus.addClass('hide');
					that.find('.has-error').removeClass('has-error');

					inputs.each(function(i, el) {
						var input = $(el);
						if (input.val() === '') {
							input.parent().addClass('has-error');
							errorStatus.removeClass('hide');
							e.preventDefault();
						}
					});

					if (!that.data('cc-on-file')) {
						e.preventDefault();
						Stripe.setPublishableKey(that.data('stripe-publishable-key'));
						Stripe.createToken({
							number: that.find('.card-num').val(),
							cvc: that.find('.card-cvc').val(),
							exp_month: that.find('.card-expiry-month').val(),
							exp_year: that.find('.card-expiry-year').val()
						}, stripeHandleResponse);
					}


				});
			});
		</script>
	@endpush
@endsection
