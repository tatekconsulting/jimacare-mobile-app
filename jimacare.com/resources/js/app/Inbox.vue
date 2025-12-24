<template>
	<div class="tile tile-alt" id="messages-main">
		<div class="ms-menu">
			<div class="ms-user clearfix">
				<img src="{{ asset($auth->profile ?? 'img/undraw_profile.svg') }}" alt="" class="img-avatar float-left mr-2">
				<div>Signed in as <br> {{ $auth->firstname ?? '' }} {{ $auth->lastname[0] ?? '' }}</div>
			</div>

			<div class="list-group lg-alt contact-list">
				@foreach($inboxes as $ib)
				@php $u = $ib->{ ($ib->client_id == $auth->id) ? 'seller' : 'client' };  @endphp
				<a class="list-group-item media" href="{{ route('inbox.show', ['user' => $u->id]) }}">
					<div class="float-left mr-2">
						<img src="{{ asset($u->profile ?? 'img/undraw_profile.svg') }}" alt="" class="img-avatar">
					</div>
					<div class="media-body">
						<small class="list-group-item-heading font-weight-bold">{{ $u->firstname ?? '' }} {{ $u->lastname[0] ?? '' }}</small><br/>
						<small class="list-group-item-text c-gray">Fierent fastidii recteque</small>
					</div>
				</a>
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

			<div class="message-list">
				@foreach($messages as $message)
				<div class="message-feed @if($message->from_id != $auth->id) media @else right @endif">
					<div class="@if($message->from_id != $auth->id) float-left mr-3 @else float-right ml-3 @endif ">
						<img src="{{ asset($message->from->profile ?? 'img/undraw_profile.svg') }}" alt="" class="img-avatar">
					</div>
					<div class="media-body">
						<div class="mf-content @if($message->from_id != $auth->id) bg-primary @endif">
							{{ $message->message ?? '' }}
						</div>
						<small class="mf-date"><i class="fa fa-clock-o"></i> {{ $message->created_at->format('d/m/Y \a\t H:i') }}</small>
					</div>
				</div>
				@endforeach
			</div>
			<form method="POST" action="{{ route('message', [ 'user' => $user->id]) }}" class="msb-reply">
				@csrf
				<textarea name="message" id="message"
						  placeholder="What's on your mind..."
				></textarea>
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
</template>

<script>
	export default {
		name: "Inbox",
		data() {
			return {
				auth: [],
				users: []
			};
		},

		props: {
			users: {
				type: Array,
				default: [],
			}
		}
	}
</script>
