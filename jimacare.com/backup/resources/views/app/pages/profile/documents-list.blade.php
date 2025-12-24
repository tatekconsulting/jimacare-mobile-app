<div class="col-12 mb-5">
	<h3 class="bg-white text-dark p-3">Uploaded Document</h3>
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
			@foreach($documents ?? [] as $doc)
				<tr>
					<td>{{ $doc->name }}</td>
					<td>{{ $doc->expiration->format('Y-m-d') }}</td>
					<td class="text-right">
						<a href="{{ asset($doc->path) }}" class="btn btn-sm btn-primary" download>Download</span></a>
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>
	</div>
</div>
