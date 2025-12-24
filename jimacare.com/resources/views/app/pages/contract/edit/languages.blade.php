<div class="form-group col-12 cb-must-select">
        <label>Language</label>
        <div>
                @php $langs = $contract->languages ? $contract->languages->pluck('id')->toArray() : []; @endphp
                @foreach($languages ?? [] as $lang)
                        <label class="btn btn-outline-primary" data-toggle="button"
                                   @if(in_array($lang->id, $langs )) aria-pressed="true" @else aria-pressed="false" @endif
                        >
                                <input type="checkbox" name="language[]" value="{{ $lang->id }}"
                                           id="language_{{ $lang->id }}" class="language"
                                           @if(in_array($lang->id, $langs )) checked @endif
                                           @if( count($langs) < 1 ) required @endif
                                /> {{ $lang->title }}
                        </label>
                @endforeach
        </div>
</div>
