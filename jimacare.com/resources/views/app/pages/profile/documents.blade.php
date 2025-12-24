@extends('app.template.layout-profile')

@section('content')
	<div class="about-you">
		@include('app.pages.profile.documents-list', compact('documents'))

		<div class="step-wrap">
			<form method="POST" action="{{ route('documents') }}" enctype="multipart/form-data" id="about1" class="row about-form novalidate">
				@csrf

				<div class="col-12 pb-3">
					<h3 class="bg-white text-dark p-3">My Document</h3>
				</div>

				<div class="col-12 mb-5">
					<div class="table-responsive">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th>Name</th>
									<th>Expiration Date</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								@php $i = 1; @endphp
								@foreach($documents ?? [] as $doc)
									<tr>
										<td>
											<input type="hidden" name="doc[{{$i}}][id]" value="{{ $doc->id }}">
											<div class="input-group">
												<input type="text" name="doc[{{$i}}][name]" value="{{ $doc->name }}" class="form-control form-control-sm"/>
											</div>
										</td>
										<td>
											<div class="input-group">
												<input type="date" name="doc[{{$i}}][expiration]" value="{{ $doc->expiration->format('Y-m-d') }}"
													   class="form-control form-control-sm"/>
											</div>
										</td>
										<td class="text-right">
											<a href="{{ route('document.show', ['document' => $doc->id]) }}" class="btn btn-sm btn-primary" >Download</a>

												

											<input type="file" name="doc[{{$i}}][file]" class="d-none"/>
											<a href="javascript:void(0)" class="btn btn-sm btn-success btn-upload">Upload</a>
											<a href="{{ route('document.destroy', ['document' => $doc->id]) }}"
												class="btn btn-sm btn-danger btn-delete"

											 >Delete</a>
										</td>
									</tr>
									@php $i++; @endphp
								@endforeach

								@for($i; $i < 6; $i++)
									<tr>
										<td>
											<div class="input-group">
												<input type="text" name="doc[{{$i}}][name]" class="form-control form-control-sm"/>
											</div>
										</td>
										<td>
											<div class="input-group">
												<input type="date" name="doc[{{$i}}][expiration]" class="form-control form-control-sm"/>
											</div>
										</td>
										<td class="text-right">
											<input type="file" name="doc[{{$i}}][file]" class="d-none"/>
											<a href="javascript:void(0)" class="btn btn-sm btn-success btn-upload">Upload</a>
										</td>
									</tr>
								@endfor
							</tbody>
						</table>
					</div>
				</div>


				<div class="col-12 form-group">
					<button class="btn btn-primary btn-block">Submit</button>
				</div>

			</form>
		</div>
	</div>
@endsection
