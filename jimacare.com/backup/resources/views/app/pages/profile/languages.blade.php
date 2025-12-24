<div class="form-group col-12 cb-must-select">
	<label>Language</label>
	<div>
		@foreach(\App\Models\Language::all() as $lang)
			<label class="btn btn-outline-primary" data-toggle="button"
				   @if(in_array($lang->id, old('language',$profile->languages->pluck('id')->toArray())) )) aria-pressed="true" @else aria-pressed="false" @endif
			>
				<input type="checkbox" name="language[]" value="{{ $lang->id }}"
					   id="language_{{ $lang->id }}" class="language"
					   @if(in_array($lang->id, old('language',$profile->languages->pluck('id')->toArray()))) checked @endif
					   @if( count($profile->languages->pluck('id')) < 1 && $loop->iteration==1) required @endif
				/> {{ $lang->title }}
			</label>
		@endforeach
	</div>
</div>
