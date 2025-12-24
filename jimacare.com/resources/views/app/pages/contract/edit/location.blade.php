<div class="form-group col-12 cb-must-select">
    <label>Your prefered location for {{ ucfirst($type) }} </label>
    <div class="row location-autofill">

        <div class="form-group col-6 col-md px-1">
            <label for="radius">Within</label>
            @php
                $radiuses = [
                    1 => '1 Mile',
                    2 => '2 Miles',
                    3 => '3 Miles',
                    4 => '4 Miles',
                    5 => '5 Miles',
                    7 => '7 Miles',
                    10 => '10 Miles',
                ];
            @endphp
            <select name="radius" id="radius" class="radius custom-select">
                @foreach ($radiuses as $key => $title)
                    <option value="{{ $key }}" @if ($key == $contract->radius ?? 5) selected @endif>
                        {{ ucfirst($title) }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-6 col-md px-1">
            <label for="address">Location</label>
            <input type="text" name="address" value="{{ $contract->address }}" id="address"
                class="address form-control" placeholder="Location" />
            <input type="hidden" name="lat" class="lat" value="{{ $contract->lat }}" />
            <input type="hidden" name="long" class="long" value="{{ $contract->long }}" />
        </div>
    </div>
</div>
