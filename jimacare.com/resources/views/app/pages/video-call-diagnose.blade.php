@extends('app.template.layout')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fa fa-stethoscope mr-2"></i>Video Call Configuration Diagnostics</h4>
                </div>
                <div class="card-body">
                    <h5 class="mb-4">Twilio Video API Configuration Status</h5>
                    
                    <!-- Account SID -->
                    <div class="mb-4">
                        <h6>Account SID (TWILIO_ACCOUNT_SID)</h6>
                        <div class="alert alert-{{ $diagnostics['account_sid']['configured'] && $diagnostics['account_sid']['format_valid'] ? 'success' : 'danger' }}">
                            @if($diagnostics['account_sid']['configured'])
                                <strong>Status:</strong> 
                                @if($diagnostics['account_sid']['format_valid'])
                                    ✅ Configured and Valid
                                @else
                                    ⚠️ Configured but Invalid Format
                                @endif
                                <br>
                                <strong>Length:</strong> {{ $diagnostics['account_sid']['length'] }} characters<br>
                                <strong>Preview:</strong> {{ $diagnostics['account_sid']['preview'] }}
                            @else
                                <strong>Status:</strong> ❌ Not Configured<br>
                                <strong>Action:</strong> Add <code>TWILIO_ACCOUNT_SID</code> to your <code>.env</code> file
                            @endif
                        </div>
                    </div>
                    
                    <!-- API Key -->
                    <div class="mb-4">
                        <h6>API Key (TWILIO_API_KEY)</h6>
                        <div class="alert alert-{{ $diagnostics['api_key']['configured'] && $diagnostics['api_key']['format_valid'] ? 'success' : 'danger' }}">
                            @if($diagnostics['api_key']['configured'])
                                <strong>Status:</strong> 
                                @if($diagnostics['api_key']['format_valid'])
                                    ✅ Configured and Valid
                                @else
                                    ⚠️ Configured but Invalid Format
                                @endif
                                <br>
                                <strong>Length:</strong> {{ $diagnostics['api_key']['length'] }} characters<br>
                                <strong>Preview:</strong> {{ $diagnostics['api_key']['preview'] }}
                            @else
                                <strong>Status:</strong> ❌ Not Configured<br>
                                <strong>Action:</strong> Add <code>TWILIO_API_KEY</code> to your <code>.env</code> file<br>
                                <strong>How to get:</strong> Twilio Console → Account → API Keys & Tokens → Create API Key
                            @endif
                        </div>
                    </div>
                    
                    <!-- API Secret -->
                    <div class="mb-4">
                        <h6>API Secret (TWILIO_API_SECRET)</h6>
                        <div class="alert alert-{{ $diagnostics['api_secret']['configured'] && $diagnostics['api_secret']['format_valid'] ? 'success' : 'danger' }}">
                            @if($diagnostics['api_secret']['configured'])
                                <strong>Status:</strong> 
                                @if($diagnostics['api_secret']['format_valid'])
                                    ✅ Configured and Valid
                                @else
                                    ⚠️ Configured but Invalid Format
                                @endif
                                <br>
                                <strong>Length:</strong> {{ $diagnostics['api_secret']['length'] }} characters<br>
                                <strong>Preview:</strong> {{ $diagnostics['api_secret']['preview'] }}
                            @else
                                <strong>Status:</strong> ❌ Not Configured<br>
                                <strong>Action:</strong> Add <code>TWILIO_API_SECRET</code> to your <code>.env</code> file<br>
                                <strong>Note:</strong> API Secret is shown only once when created in Twilio Console
                            @endif
                        </div>
                    </div>
                    
                    <!-- Overall Status -->
                    <div class="mb-4">
                        <h6>Overall Configuration Status</h6>
                        <div class="alert alert-{{ $diagnostics['all_configured'] ? 'success' : 'warning' }}">
                            @if($diagnostics['all_configured'])
                                ✅ All credentials are configured
                            @else
                                ⚠️ Some credentials are missing or invalid
                            @endif
                        </div>
                    </div>
                    
                    <!-- Token Generation Test -->
                    <div class="mb-4">
                        <h6>Token Generation Test</h6>
                        <div class="alert alert-{{ $diagnostics['can_generate_token'] ? 'success' : 'danger' }}">
                            @if($diagnostics['can_generate_token'])
                                ✅ Token generation successful!<br>
                                <strong>Test Token:</strong> {{ $diagnostics['test_token'] }}
                            @else
                                ❌ Token generation failed<br>
                                @if($diagnostics['test_error'])
                                    <strong>Error:</strong> {{ $diagnostics['test_error'] }}
                                @else
                                    <strong>Error:</strong> Cannot generate token. Check credentials above.
                                @endif
                            @endif
                        </div>
                    </div>
                    
                    <!-- Recommendations -->
                    <div class="mt-4">
                        <h5>Recommendations</h5>
                        <ul>
                            @if(!$diagnostics['all_configured'])
                                <li>Add missing credentials to your <code>.env</code> file</li>
                                <li>Run <code>php artisan config:clear</code> after updating <code>.env</code></li>
                            @endif
                            @if($diagnostics['all_configured'] && !$diagnostics['can_generate_token'])
                                <li>Check Twilio Console to ensure API Key is active</li>
                                <li>Verify API Secret is correct (it's shown only once when created)</li>
                                <li>Check server logs for detailed error messages</li>
                            @endif
                            @if($diagnostics['can_generate_token'])
                                <li>If video calls still fail, check browser console for WebSocket errors</li>
                                <li>Verify network/firewall allows WebSocket connections</li>
                                <li>Ensure site uses HTTPS in production</li>
                            @endif
                        </ul>
                    </div>
                    
                    <!-- Actions -->
                    <div class="mt-4">
                        <a href="{{ route('inbox') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left mr-2"></i>Back to Inbox
                        </a>
                        <button onclick="location.reload()" class="btn btn-primary ml-2">
                            <i class="fa fa-refresh mr-2"></i>Refresh Diagnostics
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

