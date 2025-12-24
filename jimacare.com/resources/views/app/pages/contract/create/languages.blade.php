<div class="form-group col-12 cb-must-select">
	<label>Language</label>
	<div>
		@foreach($languages as $lang)
			<label class="btn btn-outline-primary" data-toggle="button"
				   @if(in_array($lang->id, old('language', []) )) aria-pressed="true" @else aria-pressed="false" @endif
			>
				<input type="checkbox" name="language[]" value="{{ $lang->id }}"
					   id="language_{{ $lang->id }}" class="language"
					   @if(in_array($lang->id, old('language', []) )) checked @endif
					   @if( count(old('language', [])) < 1 ) required @endif
				/> {{ $lang->title }}
			</label>
		@endforeach
	</div>
</div>
