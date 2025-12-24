@if(in_array($role->id ?? 0, [3, 5]) )
        <div class="form-group col-12 cb-must-select">
                <label>
                        @if(($role->id ?? 0) == 3)
                                Working Day
                        @else
                                What are the best days for cleaning?
                        @endif
                </label>
                <div>
                        @php $dayes = $contract->days ? $contract->days->pluck('id')->toArray() : []; @endphp
                        @foreach($days ?? [] as $day)
                                <label class="btn btn-outline-primary" data-toggle="button"
                                           @if(in_array($day->id, $dayes )) aria-pressed="true"
                                           @else aria-pressed="false" @endif
                                >
                                        <input type="checkbox" name="day[]" value="{{ $day->id }}"
                                                   id="day_{{ $day->id }}"
                                                   class="day"
                                                   @if(in_array($day->id, $dayes)) checked @endif
                                                   @if( count($dayes) < 1 ) required @endif
                                        /> {{ $day->title }}
                                </label>
                        @endforeach
                </div>
        </div>
@endif