@extends('layouts.master')
@section('title', 'Website Settings')
@section('contents')
<div class="col-12">
    <form action="{{ route('website.settings.update') }}" method="POST" class="card">
        @csrf
        <div class="card-header"><h3 class="card-title">Website Settings</h3></div>
        <div class="card-body row">
            <div class="form-group col-md-6"><label for="settings-edit-school-name">School Name</label><input id="settings-edit-school-name" class="form-control" name="school_name" value="{{ old('school_name', $settings['school_name'] ?? '') }}" required></div>
            <div class="form-group col-md-6"><label for="settings-edit-tagline">Tagline</label><input id="settings-edit-tagline" class="form-control" name="tagline" value="{{ old('tagline', $settings['tagline'] ?? '') }}"></div>
            <div class="form-group col-md-6"><label for="settings-edit-contact-email">Contact Email</label><input id="settings-edit-contact-email" class="form-control" name="contact_email" value="{{ old('contact_email', $settings['contact_email'] ?? '') }}"></div>
            <div class="form-group col-md-6"><label for="settings-edit-contact-phone">Contact Phone</label><input id="settings-edit-contact-phone" class="form-control" name="contact_phone" value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}"></div>
            <div class="form-group col-12"><label for="settings-edit-address">Address</label><input id="settings-edit-address" class="form-control" name="address" value="{{ old('address', $settings['address'] ?? '') }}"></div>
            <div class="form-group col-12"><label for="settings-edit-footer-about">Footer About</label><textarea id="settings-edit-footer-about" class="form-control" name="footer_about" rows="3">{{ old('footer_about', $settings['footer_about'] ?? '') }}</textarea></div>
            <div class="form-group col-md-6"><label for="settings-edit-social-facebook">Facebook URL</label><input id="settings-edit-social-facebook" class="form-control" name="social_facebook" value="{{ old('social_facebook', $settings['social_facebook'] ?? '') }}" placeholder="https://facebook.com/..."></div>
            <div class="form-group col-md-6"><label for="settings-edit-social-instagram">Instagram URL</label><input id="settings-edit-social-instagram" class="form-control" name="social_instagram" value="{{ old('social_instagram', $settings['social_instagram'] ?? '') }}" placeholder="https://instagram.com/..."></div>
            <div class="form-group col-md-6"><label for="settings-edit-social-youtube">YouTube URL</label><input id="settings-edit-social-youtube" class="form-control" name="social_youtube" value="{{ old('social_youtube', $settings['social_youtube'] ?? '') }}" placeholder="https://youtube.com/..."></div>
            <div class="form-group col-md-6"><label for="settings-edit-social-linkedin">LinkedIn URL</label><input id="settings-edit-social-linkedin" class="form-control" name="social_linkedin" value="{{ old('social_linkedin', $settings['social_linkedin'] ?? '') }}" placeholder="https://linkedin.com/..."></div>
        </div>
        <div class="card-footer"><button class="btn btn-primary">Save Settings</button></div>
    </form>
</div>
@endsection
