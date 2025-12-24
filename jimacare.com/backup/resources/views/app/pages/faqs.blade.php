@extends('app.template.layout')

@section('content')
	<div class="helpdesk-head mt-5">
		<div class="container">
			<div class="row">
				<div class="col-6"><h3>Helpdesk</h3></div>
				<div class="col-6">
					<div class="d-flex float-right">
						<ul role="tablist" class="helptabs">
							@foreach($roles ?? [] as $role)
								<li role="presentation">
									<a @if($role->slug == 'client') class="active" @endif href="#{{ $role->slug }}">{{ ucfirst($role->title) }}</a>
								</li>
							@endforeach
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="tab-content" id="myTabContent">
		@foreach($roles ?? [] as $role)
			<div role="tabpanel" id="{{ $role->slug }}" aria-labelledby="{{ $role->slug }}-tab" @if($role->slug != 'client') class="d-none" @endif>
				<div class="help-accordian">
					<div class="container">
						<div class="row">
							<div class="col-md-12 col-sm-12 col-12">
								<ul>
									@foreach($role->faqs ?? [] as $faq)
										<li>
											<h3>{!! $faq->title !!}</h3>
											<div>{!! $faq->desc !!}</div>
										</li>
									@endforeach
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		@endforeach
	</div>
@endsection
