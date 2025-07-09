@extends('admin.layouts.admin_master')

@section('title', 'Default Setting')
<style>
    .toggle-container {
    position: relative;
}

.toggle-checkbox {
    display: none;
}

.toggle-label {
    display: inline-block;
    width: 38px;
    height: 18px;
    background-color: #ccc;
    border-radius: 50px;
    position: relative;
    cursor: pointer;
    transition: background-color 0.3s ease;
}
.ak-upload {
    display: flex;
    align-items: center;
    gap: 8px;
}
.toggle-label::after {
    content: '';
    width: 14px;
    height: 14px;
    background-color: white;
    border-radius: 50%;
    position: absolute;
    top: 2px;
    left: 2px;
    transition: left 0.3s ease;
}

.toggle-checkbox:checked + .toggle-label {
    background-color: #4caf50;
}

.toggle-checkbox:checked + .toggle-label::after {
    left: 21px;
}
</style>
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Default Setting</h4>
                <p class="card-text">Body</p>
            </div>
            <div class="card-body">
                <form action="{{route('default.setting.update', $default_setting->id)}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label>Logo Photo</label>
                            <input type="file" class="form-control" name="logo_photo" id="logoImage">
                            <img src="{{ asset('uploads/default_photo') }}/{{ $default_setting->logo_photo }}" width="100" height="100" id="logoImagePreview">
                            @error('logo_photo')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label>Favicon</label>
                            <input type="file" class="form-control" name="favicon" id="favicon">
                            <img src="{{ asset('uploads/default_photo') }}/{{ $default_setting->favicon }}" width="100" height="100" id="faviconPreview">
                            @error('favicon')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                        <div class="col-lg-4 mb-3">
                            <label>App Name</label>
                            <input type="text" class="form-control" name="app_name" value="{{$default_setting->app_name}}">
                            @error('app_name')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                        <div class="col-lg-4 mb-3">
                            <label>App Url</label>
                            <input type="text" class="form-control" name="app_url" value="{{$default_setting->app_url}}">
                            @error('app_url')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                        <div class="col-lg-4 mb-3">
                            <label>Time Zone</label>
                            <select name="time_zone" class="form-control">
                                <option value="">--Select--</option>
                                <option value="UTC" @selected($default_setting->time_zone == 'UTC')>UTC</option>
                                <option value="Asia/Dhaka" @selected($default_setting->time_zone == 'Asia/Dhaka')>Asia/Dhaka</option>
                            </select>
                            @error('time_zone')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label>Main Phone Number</label>
                            <input type="text" class="form-control" name="main_phone" value="{{$default_setting->main_phone}}" placeholder="Main Phone Number">
                            @error('main_phone')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label>Support Phone Number</label>
                            <input type="text" class="form-control" name="support_phone" value="{{$default_setting->support_phone}}" placeholder="Support Phone Number">
                            @error('support_phone')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label>Main Email Address</label>
                            <input type="text" class="form-control" name="main_email" value="{{$default_setting->main_email}}" placeholder="Main Email Address">
                            @error('main_email')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label>Support Email Address</label>
                            <input type="text" class="form-control" name="support_email" value="{{$default_setting->support_email}}" placeholder="Support Email Address">
                            @error('support_email')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                        <div class="col-lg-12 mb-3">
                            <label>Address</label>
                            <textarea name="address" class="form-control" placeholder="Address">{{$default_setting->address}}</textarea>
                        </div>
                        <div class="col-lg-12 mb-3">
                            <label>Google Map Link</label>
                            <textarea name="google_map_link" class="form-control" placeholder="Google Map Link">{{$default_setting->google_map_link}}</textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <label>Facebook Link</label>
                            <input type="text" class="form-control" name="facebook_link" value="{{$default_setting->facebook_link}}" placeholder="Facebook Link">
                        </div>
                        <div class="col-lg-12 mb-3">
                            <label>Twitter Link</label>
                            <input type="text" class="form-control" name="twitter_link" value="{{$default_setting->twitter_link}}" placeholder="Twitter Link">
                        </div>
                        <div class="col-lg-12 mb-3">
                            <label>Instagram Link</label>
                            <input type="text" class="form-control" name="instagram_link" value="{{$default_setting->instagram_link}}" placeholder="Instagram Link">
                        </div>
                        <div class="col-lg-12 mb-3">
                            <label>Linkedin Link</label>
                            <input type="text" class="form-control" name="linkedin_link" value="{{$default_setting->linkedin_link}}" placeholder="Linkedin Link">
                        </div>
                        <div class="col-lg-12 mb-3">
                            <label>Youtube Link</label>
                            <input type="text" class="form-control" name="youtube_link" value="{{$default_setting->youtube_link}}" placeholder="Youtube Link">
                        </div>
                        <div class="col-lg-12 mb-3 ak-upload">
            <label>Building Auto Approve</label>
            <!-- Toggle Checkbox -->
            <input type="checkbox" name="building_approval" id="building_approval" 
                   class="toggle-checkbox" @checked($default_setting->building_approval == 1)>
            <label for="building_approval" class="toggle-label"></label>
            @error('building_approval')
            <span class="text-danger">{{$message}}</span>
            @enderror
        </div>
                    <div class="m-3">
                        <button class="btn btn-info" type="submit">Update Default Setting</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function(){
        // Logo Image Preview
        $('#logoImage').change(function(){
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#logoImagePreview').attr('src', e.target.result).show();
            }
            reader.readAsDataURL(this.files[0]);
        });
        // Favicon Preview
        $('#favicon').change(function(){
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#faviconPreview').attr('src', e.target.result).show();
            }
            reader.readAsDataURL(this.files[0]);
        });
    })
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const toggle = document.getElementById("building_approval");

        // Initial log based on the toggle state
        if (toggle.checked) {
            console.log("Slider is ON");
        } else {
            console.log("Slider is OFF");
        }

        // When the user changes the toggle state
        toggle.addEventListener("change", function() {
            if (this.checked) {
                // If checked, the value will be 1
                console.log("Slider is ON");
            } else {
                // If unchecked, the value will be 0
                console.log("Slider is OFF");
            }
        });
    });
</script>

@endsection
