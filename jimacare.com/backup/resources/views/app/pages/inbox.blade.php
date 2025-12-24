@extends('app.template.layout-profile')

@section('content')
	<div class="tile tile-alt" id="messages-main">
		<div class="ms-menu">
			<div class="ms-user clearfix">
				<img src="{{ asset($auth->profile ?? 'img/undraw_profile.svg') }}" alt="" class="img-avatar float-left mr-2">
				<div>Signed in as <br> {{ $auth->firstname ?? '' }} {{ $auth->lastname[0] ?? '' }}</div>
			</div>

			<div class="list-group lg-alt contact-list">
				@foreach($inboxes as $ib)
					@php $u = $ib->{ ($ib->client_id == $auth->id) ? 'seller' : 'client' };  @endphp
				@if($u)
					<a
						class="list-group-item media"
						data-ib-user="{{ $u->id }}"
						href="{{ route('inbox.show', ['user' => $u->id]) }}"
					>
						<div class="float-left mr-2">
							<img src="{{ asset($u->profile ?? 'img/undraw_profile.svg') }}" alt="" class="img-avatar">
						</div>
						<div class="media-body">
							<small class="list-group-item-heading font-weight-bold">{{ $u->firstname ?? '' }} {{ $u->lastname[0] ?? '' }}</small><br/>
							<small class="list-group-item-text c-gray latest-msg">{{ $ib->messages()->latest()->first()->message ?? '' }}</small>
						</div>
					</a>
				@endif
				@endforeach
			</div>
		</div>

		<div class="ms-body">
			@if($inbox ?? false)
				<div class="action-header clearfix">
					<div class="d-block d-md-none" id="ms-menu-trigger">
						<i class="fa fa-bars"></i>
					</div>

					<div class="float-left mr-2">
						<img src="{{ asset($user->profile ?? 'img/undraw_profile.svg') }}" alt="" class="img-avatar mr-2">
						<span>{{ $user->firstname ?? '' }} {{ $user->lastname[0] ?? '' }}</span>
					</div>
					@if(auth()->user()->role_id > 2)
						<div class="float-right">
							<button class="btn btn-primary btn-sm invoice-btn" data-user="{{ $user->id }}">Send Invoice</button>
						</div>
					@endif
					{{--<ul class="ah-actions actions">
						<li>
							<a href="">
								<i class="fa fa-phone"></i>
							</a>
						</li>
						<li>
							<a href="">
								<i class="fa fa-video-camera"></i>
							</a>
						</li>
					</ul>--}}
				</div>

				<div class="message-list" data-message-user="{{ $user->id }}">
					@foreach($messages as $message)
						<div class="message-feed @if($message->from_id != $auth->id) media @else right @endif">
							<div class="@if($message->from_id != $auth->id) float-left mr-3 @else float-right ml-3 @endif ">
								<img src="{{ asset($message->from->profile ?? 'img/undraw_profile.svg') }}" alt="" class="img-avatar">
							</div>
							<div class="media-body">
								<div class="mf-content
									@if($message->type == 'invoice') invoice border border-primary @endif"
								>
									@if($message->type == 'invoice')
										<h5>Custom Invoice <span class='float-right'>£{{ $message->invoice->price }}</span></h5>
										<p>{{ $message->message }}</p>
										<div class="btn-group w-100">
											@if($message->from_id != $auth->id)
												@if($message->invoice->status == 'active')
													<a href="{{ route('invoice.pay', ['invoice' => $message->invoice->id ]) }}" class="btn btn-primary" >Pay</a>
													<a href="{{ route('invoice.reject', ['invoice' => $message->invoice->id ]) }}" class="btn btn-danger reject-invoice-request">Reject Request</a>
												@elseif($message->invoice->status == 'paid')
													<button class="btn btn-primary" disabled>Paid</button>
													<a href="{{ route('order.show', [ 'order' => $message->invoice->order->id ?? '' ]) }}" class="btn btn-danger">View Order</a>
												@else
													<button class="btn btn-danger" disabled>{{ ucwords($message->invoice->status) }}</button>
												@endif
											@else
												@if($message->invoice->status == 'active')
													<a href="{{ route('invoice.cancel', ['invoice' => $message->invoice->id ]) }}" class="btn btn-primary cancel-invoice-request">Cancel</a>
												@elseif($message->invoice->status == 'paid')
													<button class="btn btn-primary" disabled>Paid</button>
													<a href="{{ route('order.show', [ 'order' => $message->invoice->order->id ?? '' ]) }}" class="btn btn-danger" >View Order</a>
												@else
													<button class="btn btn-danger" disabled>{{ ucwords($message->invoice->status) }}</button>
												@endif
											@endif
										</div>
									@else
										{{ $message->message ?? '' }}
									@endif

								</div>

								<small class="mf-date"><i class="fa fa-clock-o"></i> {{ $message->created_at->format('d/m/Y \a\t H:i') }}</small>
							</div>
						</div>
					@endforeach
				</div>
				<form method="POST" action="{{ route('message', [ 'user' => $user->id]) }}" class="msb-reply">
					@csrf
					<input name="message" id="message"
						placeholder="What's on your mind..."
					/>
					<button type="submit"><i class="fa fa-paper-plane-o"></i></button>
				</form>
			@else
				<div class="message-info pt-5 text-center">
					<h2>You have messages</h2>
					<p style="font-size: 18px;">Select a converson to read<br/> from the list on the left</p>
				</div>
			@endif
		</div>
	</div>
	@if(($inbox ?? false) && (auth()->user()->role_id > 2) )
		<div class="modal fade" id="send-invoice-model" role="dialog" aria-labelledby="send-invoice-model-label" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<form method="POST" action="{{ route('message.invoice', ['user' => $user->id ])}}" class="modal-content">
					@csrf
					<div class="modal-header">
						<h5 class="modal-title" id="send-invoice-model-label">Send Invoice</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label for="invoice-message" class="col-form-label">Describe your offer</label>
							<textarea name="message"
									  class="form-control" id="invoice-message"
									  placeholder="Describe your offer" required
							></textarea>
						</div>
						<div class="form-group">
							<label for="invoice-price" class="col-form-label">Price</label>
							<input type="number" name="price" class="form-control" id="invoice-price" placeholder="Price">
						</div>
					</div>
					<div class="modal-footer">
						<button class="btn btn-primary">Send Invoice</button>
					</div>
				</form>
			</div>
		</div>
	@endif
@endsection
