<div class="form-group col-12">
    Profile video<br />

    <div class="custom-control custom-radio custom-control-inline">
        <input type="radio" name="verified_video" value="true" id="approve" class="gender custom-control-input"
            @if (old('verified_video', $profile->verified_video) == true) checked @endif required />
        <label class="custom-control-label" for="approve">Approve</label>
    </div>
    <div class="custom-control custom-radio custom-control-inline">
        <input type="radio" name="verified_video" value="false" id="block" class="gender custom-control-input"
            @if (old('verified_video', $profile->verified_video) == false) checked @endif required />
        <label class="custom-control-label" for="block">Block</label>
    </div>
    <div class="border mt-2">
        @if (Storage::disk('s3')->exists($profile->video))
            <video class="w-100"
                src="{{ Storage::disk('s3')->temporaryUrl($profile->video, Carbon\Carbon::now()->addMinutes(60)) ?: '' }}"
                controls muted autoplay height="250" width="250"></video>
        @endif
    </div>
</div>
