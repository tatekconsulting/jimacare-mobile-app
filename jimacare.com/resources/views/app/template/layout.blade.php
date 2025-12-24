<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<meta name="user-token" content="{{ auth()->id() ?? '0' }}">

		<!-- PWA Meta Tags -->
		<meta name="theme-color" content="#4e73df">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="default">
		<meta name="apple-mobile-web-app-title" content="Jimacare">
		<meta name="description" content="Connect with trusted carers, babysitters, and cleaners in your area">

		<!-- PWA Manifest -->
		<link rel="manifest" href="{{ asset('manifest.json') }}">

		<!-- Apple Touch Icons -->
		<link rel="apple-touch-icon" href="{{ asset('img/icons/icon-152x152.png') }}">
		<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/icons/icon-192x192.png') }}">

		<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
		<title>{{ config('app.name', 'Laravel') }}</title>
		<link href="{{ asset('css/app.css') }}" rel="stylesheet">
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAemE-RYiok4C3WvIOuLLo3nhmMNaffl6s&libraries=places&ver=1.0"></script>
	</head>
	<body>
	@include('app.template.header')
	<main>
		@yield('content')
	</main>
	@include('app.template.footer')





<!-- Enhanced AI Chatbot with Full Job Posting Flow -->
<div id="jimacare-chatbot">
    <button id="chat-toggle" onclick="toggleChat()">💬</button>
    <div id="chat-window" style="display:none;">
        <div id="chat-header">
            <span>🤖 Jimacare Assistant</span>
            <button onclick="toggleChat()">×</button>
        </div>
        <div id="chat-progress" style="display:none;">
            <div class="progress-steps">
                <span class="step" data-step="1">1</span>
                <span class="step" data-step="2">2</span>
                <span class="step" data-step="3">3</span>
                <span class="step" data-step="4">4</span>
                <span class="step" data-step="5">5</span>
            </div>
            <div class="progress-label">Step <span id="current-step">1</span> of 5</div>
        </div>
        <div id="chat-messages">
            <div class="bot-msg">Hello! 👋 I can help you find the perfect carer or post a job. What would you like to do?</div>
        </div>
        <div id="chat-options"></div>
        <div id="chat-input">
            <input type="text" id="user-input" placeholder="Type your message..." onkeypress="if(event.key==='Enter')sendUserMessage()">
            <button onclick="sendUserMessage()">Send</button>
        </div>
    </div>
</div>

<style>
#jimacare-chatbot { position: fixed; bottom: 20px; right: 20px; z-index: 9999; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
#chat-toggle { width: 65px; height: 65px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; cursor: pointer; font-size: 28px; box-shadow: 0 4px 25px rgba(102,126,234,0.5); transition: all 0.3s ease; }
#chat-toggle:hover { transform: scale(1.1) rotate(10deg); }
#chat-window { position: absolute; bottom: 80px; right: 0; width: 380px; height: 550px; background: #fff; border-radius: 20px; box-shadow: 0 15px 50px rgba(0,0,0,0.2); display: flex; flex-direction: column; overflow: hidden; animation: slideUp 0.3s ease; }
@keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
#chat-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 18px 20px; display: flex; justify-content: space-between; align-items: center; font-weight: 600; font-size: 16px; }
#chat-header button { background: rgba(255,255,255,0.2); border: none; color: white; font-size: 20px; cursor: pointer; width: 30px; height: 30px; border-radius: 50%; transition: all 0.2s; }
#chat-header button:hover { background: rgba(255,255,255,0.3); }
#chat-progress { background: #f8f9fa; padding: 12px 15px; border-bottom: 1px solid #eee; }
.progress-steps { display: flex; justify-content: space-between; margin-bottom: 8px; }
.progress-steps .step { width: 28px; height: 28px; border-radius: 50%; background: #e0e0e0; color: #999; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; transition: all 0.3s; }
.progress-steps .step.active { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
.progress-steps .step.completed { background: #28a745; color: white; }
.progress-label { text-align: center; font-size: 12px; color: #666; }
#chat-messages { flex: 1; padding: 15px; overflow-y: auto; background: #f8f9fa; }
.bot-msg, .user-msg { padding: 12px 16px; border-radius: 18px; margin-bottom: 10px; max-width: 85%; line-height: 1.4; font-size: 14px; }
.bot-msg { background: white; color: #333; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-bottom-left-radius: 4px; }
.user-msg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; margin-left: auto; border-bottom-right-radius: 4px; }
#chat-options { padding: 10px 15px; background: white; border-top: 1px solid #eee; display: flex; flex-wrap: wrap; gap: 8px; max-height: 150px; overflow-y: auto; }
.chat-option { padding: 10px 16px; background: linear-gradient(135deg, #f0f0f0 0%, #e8e8e8 100%); border: none; border-radius: 20px; cursor: pointer; font-size: 13px; transition: all 0.2s; font-weight: 500; }
.chat-option:hover { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; transform: translateY(-2px); }
.chat-option.primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
.chat-option.selected { background: #28a745; color: white; }
#chat-input { display: flex; padding: 12px; border-top: 1px solid #eee; background: white; }
#chat-input input { flex: 1; padding: 12px 16px; border: 2px solid #eee; border-radius: 25px; outline: none; font-size: 14px; transition: border-color 0.2s; }
#chat-input input:focus { border-color: #667eea; }
#chat-input button { margin-left: 10px; padding: 12px 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 25px; cursor: pointer; font-weight: 600; transition: all 0.2s; }
#chat-input button:hover { transform: scale(1.05); }
.typing-indicator { display: flex; gap: 4px; padding: 12px 16px; }
.typing-indicator span { width: 8px; height: 8px; background: #667eea; border-radius: 50%; animation: bounce 1.4s infinite ease-in-out; }
.typing-indicator span:nth-child(1) { animation-delay: -0.32s; }
.typing-indicator span:nth-child(2) { animation-delay: -0.16s; }
@keyframes bounce { 0%, 80%, 100% { transform: scale(0); } 40% { transform: scale(1); } }
.carer-card { background: white; border-radius: 12px; padding: 15px; margin: 10px 0; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
.carer-card-header { display: flex; align-items: center; gap: 12px; margin-bottom: 10px; }
.carer-card img { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; }
.carer-card-info h4 { margin: 0; font-size: 15px; color: #333; }
.carer-card-info .score { color: #667eea; font-weight: 600; font-size: 14px; }
.carer-card-reasons { font-size: 12px; color: #666; margin-bottom: 10px; }
.carer-card-reasons span { display: inline-block; background: #f0f0f0; padding: 4px 10px; border-radius: 12px; margin: 2px 4px 2px 0; }
.carer-card-actions { display: flex; gap: 8px; }
.carer-card-actions a { flex: 1; padding: 8px; text-align: center; border-radius: 8px; font-size: 13px; text-decoration: none; font-weight: 500; }
.carer-card-actions .view-btn { background: #f0f0f0; color: #333; }
.carer-card-actions .msg-btn { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
.form-group-chat { margin: 10px 0; }
.form-group-chat label { display: block; font-size: 12px; color: #666; margin-bottom: 5px; }
.form-group-chat input, .form-group-chat select, .form-group-chat textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; }
.form-group-chat textarea { min-height: 60px; resize: none; }
@media (max-width: 480px) { #chat-window { width: 100vw; height: 100vh; bottom: 0; right: 0; border-radius: 0; } #chat-toggle { bottom: 15px; right: 15px; } }
</style>

<script>


// Check auth status dynamically
function checkAuthStatus() {
    return fetch('/api/chatbot/auth-check', {
        credentials: 'same-origin'
    })
    .then(r => r.json())
    .then(data => {
        chatState.isLoggedIn = data.authenticated;
        return data.authenticated;
    })
    .catch(() => false);
}



var chatState = {
    step: 'initial',
    postJobStep: 0,
    jobData: {
        service_type: '',
        title: '',
        company: '',
        gender: 'nopreferences',
        start_type: 'immediately',
        start_date: '',
        end_type: 'on-going',
        end_date: '',
        start_time: '',
        end_time: '',
        hourly_rate: '',
        daily_rate: '',
        weekly_rate: '',
        description: '',
        days: [],
        address: '',
        postcode: ''
    },
    isLoggedIn: {{ auth()->check() ? 'true' : 'false' }}
};

var jobTypes = [];
var days = [];

function toggleChat() {
    var w = document.getElementById('chat-window');
    if (w.style.display === 'none') {
        w.style.display = 'flex';
        if (chatState.step === 'initial') {
            showInitialOptions();
            loadJobTypes();
            loadDays();
        }
    } else {
        w.style.display = 'none';
    }
}

function showProgress(step) {
    var progress = document.getElementById('chat-progress');
    if (step > 0) {
        progress.style.display = 'block';
        document.getElementById('current-step').textContent = step;
        document.querySelectorAll('.progress-steps .step').forEach(function(el, idx) {
            el.classList.remove('active', 'completed');
            if (idx + 1 < step) el.classList.add('completed');
            if (idx + 1 === step) el.classList.add('active');
        });
    } else {
        progress.style.display = 'none';
    }
}

function showInitialOptions() {
    showProgress(0);
    setOptions([
        { text: '🔍 Find a Carer', action: () => startFindCarer() },
        { text: '📝 Post a Job', action: () => startPostJob() },
        { text: '❓ How it Works', action: () => showHowItWorks() },
        { text: '💬 Ask a Question', action: () => startFreeChat() }
    ]);
}

function loadJobTypes() {
    fetch('/api/chatbot/job-types')
        .then(r => r.json())
        .then(data => { if(data.success) jobTypes = data.data; });
}

function loadDays() {
    fetch('/api/chatbot/days')
        .then(r => r.json())
        .then(data => { if(data.success) days = data.data; });
}

function addMessage(text, isUser) {
    var messages = document.getElementById('chat-messages');
    var div = document.createElement('div');
    div.className = isUser ? 'user-msg' : 'bot-msg';
    div.innerHTML = text;
    messages.appendChild(div);
    messages.scrollTop = messages.scrollHeight;
}

function showTyping() {
    var messages = document.getElementById('chat-messages');
    var div = document.createElement('div');
    div.className = 'bot-msg typing-indicator';
    div.id = 'typing';
    div.innerHTML = '<span></span><span></span><span></span>';
    messages.appendChild(div);
    messages.scrollTop = messages.scrollHeight;
}

function hideTyping() {
    var typing = document.getElementById('typing');
    if (typing) typing.remove();
}

function setOptions(options) {
    var container = document.getElementById('chat-options');
    container.innerHTML = '';
    options.forEach(function(opt) {
        var btn = document.createElement('button');
        btn.className = 'chat-option' + (opt.primary ? ' primary' : '') + (opt.selected ? ' selected' : '');
        btn.textContent = opt.text;
        btn.onclick = opt.action;
        container.appendChild(btn);
    });
}

// ============ FIND CARER FLOW ============
function startFindCarer() {
    chatState.step = 'find_select_type';
    addMessage('🔍 Find a Carer', true);
    addMessage('Great! What type of carer are you looking for?', false);
    
    var options = [
        { text: '👴 Carer', action: () => selectCarerType({id: 3, title: 'Carer'}) },
        { text: '👶 Childminder', action: () => selectCarerType({id: 4, title: 'Childminder'}) },
        { text: '🏠 Housekeeper', action: () => selectCarerType({id: 5, title: 'Housekeeper'}) }
    ];
    setOptions(options);
}

function selectCarerType(type) {
    chatState.step = 'find_select_days';
    chatState.jobData.role_id = type.id;
    chatState.jobData.service_type = type.title;
    
    addMessage(type.title, true);
    addMessage('When do you need a ' + type.title.toLowerCase() + '? Select the days:', false);
    
    chatState.jobData.days = [];
    showDaySelection(false);
}

function showDaySelection(isPosting) {
    var daysList = [
        {id: 1, title: 'Monday'},
        {id: 2, title: 'Tuesday'},
        {id: 3, title: 'Wednesday'},
        {id: 4, title: 'Thursday'},
        {id: 5, title: 'Friday'},
        {id: 6, title: 'Saturday'},
        {id: 7, title: 'Sunday'}
    ];
    
    var options = daysList.map(function(day) {
        var isSelected = chatState.jobData.days && chatState.jobData.days.includes(day.id);
        return { 
            text: (isSelected ? '✓ ' : '') + day.title, 
            action: () => toggleDay(day, isPosting),
            selected: isSelected
        };
    });
    options.push({ text: '✅ Done', primary: true, action: () => finishDaySelection(isPosting) });
    setOptions(options);
}

function toggleDay(day, isPosting) {
    if (!chatState.jobData.days) chatState.jobData.days = [];
    var idx = chatState.jobData.days.indexOf(day.id);
    if (idx > -1) {
        chatState.jobData.days.splice(idx, 1);
    } else {
        chatState.jobData.days.push(day.id);
    }
    showDaySelection(isPosting);
}

function finishDaySelection(isPosting) {
    var dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    var selectedDays = chatState.jobData.days.map(id => dayNames[id-1]).join(', ');
    addMessage(selectedDays || 'Any day', true);
    
    if (isPosting) {
        postJobStep5Other();
    } else {
        findCarers();
    }
}

function findCarers() {
    addMessage('🔍 Finding the best matches for you...', false);
    showTyping();
    
    fetch('/api/chatbot/recommendations', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        },
        body: JSON.stringify(chatState.jobData)
    })
    .then(r => r.json())
    .then(data => {
        hideTyping();
        if (data.success && data.matches && data.matches.length > 0) {
            addMessage('🎉 Found ' + data.matches.length + ' great matches! Here are your top recommendations:', false);
            showCarerMatches(data.matches);
        } else {
            addMessage('Sorry, no carers found matching your criteria. Try broadening your search or <a href="/post-a-job" style="color:#667eea;font-weight:600;">post a job</a> to receive applications.', false);
        }
        setOptions([
            { text: '🔄 New Search', action: () => resetChat() },
            { text: '📝 Post a Job', action: () => startPostJob() }
        ]);
    })
    .catch(err => {
        hideTyping();
        addMessage('Sorry, something went wrong. <a href="/sellers" style="color:#667eea;font-weight:600;">Browse carers here</a>.', false);
        showInitialOptions();
    });
}

function showCarerMatches(matches) {
    var messages = document.getElementById('chat-messages');
    matches.forEach(function(match) {
        var card = document.createElement('div');
        card.className = 'carer-card';
        card.innerHTML = 
            '<div class="carer-card-header">' +
                '<img src="' + match.profile + '" alt="' + match.name + '">' +
                '<div class="carer-card-info">' +
                    '<h4>' + match.name + (match.verified ? ' ✓' : '') + '</h4>' +
                    '<div class="score">⭐ ' + match.score + '% Match</div>' +
                '</div>' +
            '</div>' +
            '<div class="carer-card-reasons">' +
                match.reasons.map(function(r) { return '<span>✓ ' + r + '</span>'; }).join('') +
            '</div>' +
            '<div class="carer-card-actions">' +
                '<a href="' + match.url + '" class="view-btn">View Profile</a>' +
                '<a href="/inbox/' + match.id + '" class="msg-btn">Message</a>' +
            '</div>';
        messages.appendChild(card);
    });
    messages.scrollTop = messages.scrollHeight;
}

// ============ POST JOB FLOW (5 Steps) ============
function startPostJob() {
    addMessage('📝 Post a Job', true);
    addMessage('Checking login status...', false);
    
    // Check auth status dynamically
    fetch('/chatbot-auth-check', { credentials: 'same-origin' })
    .then(r => r.json())
    .then(data => {
        // Remove "Checking..." message
        var messages = document.getElementById('chat-messages');
        messages.removeChild(messages.lastChild);
        
        if (!data.authenticated) {
            chatState.isLoggedIn = false;
            addMessage('🔐 You need to be logged in to post a job.<br><br><a href="/login" style="display:inline-block;padding:12px 24px;background:linear-gradient(135deg,#667eea,#764ba2);color:white;border-radius:25px;text-decoration:none;font-weight:600;margin:5px 5px 5px 0;">Login</a> <a href="/register" style="display:inline-block;padding:12px 24px;background:#f0f0f0;color:#333;border-radius:25px;text-decoration:none;font-weight:600;margin:5px;">Register</a>', false);
            setOptions([
                { text: '🔍 Find Carers Instead', action: () => startFindCarer() },
                { text: '🏠 Main Menu', action: () => resetChat() }
            ]);
        } else {
            chatState.isLoggedIn = true;
            chatState.step = 'post_job';
            chatState.postJobStep = 1;
            postJobStep1Service();
        }
    })
    .catch(() => {
        addMessage('Error checking login. Please <a href="/login" style="color:#667eea;">login here</a> and try again.', false);
        showInitialOptions();
    });
}

// Step 1: Choose Service
function postJobStep1Service() {
    showProgress(1);
    addMessage('<b>Step 1: Choose Service</b><br>Who do you want to hire?', false);
    setOptions([
        { text: '👴 Carer', action: () => selectJobService('carer', 3) },
        { text: '👶 Childminder', action: () => selectJobService('childminder', 4) },
        { text: '🏠 Housekeeper', action: () => selectJobService('housekeeper', 5) }
    ]);
}

function selectJobService(type, roleId) {
    chatState.jobData.service_type = type;
    chatState.jobData.role_id = roleId;
    addMessage(type.charAt(0).toUpperCase() + type.slice(1), true);
    chatState.postJobStep = 2;
    postJobStep2Requirements();
}

// Step 2: Requirements
function postJobStep2Requirements() {
    showProgress(2);
    addMessage('<b>Step 2: ' + chatState.jobData.service_type.charAt(0).toUpperCase() + chatState.jobData.service_type.slice(1) + ' Requirements</b><br>Please provide a job title:', false);
    chatState.step = 'post_job_title';
    setOptions([]);
}

// Step 3: Time Requirements
function postJobStep3Time() {
    showProgress(3);
    addMessage('<b>Step 3: Time Requirements</b><br>When would you like to start?', false);
    setOptions([
        { text: 'Immediately', action: () => selectStartType('immediately') },
        { text: 'Not Sure', action: () => selectStartType('not-sure') },
        { text: 'Specific Date', action: () => selectStartType('specific-date') }
    ]);
}

function selectStartType(type) {
    chatState.jobData.start_type = type;
    addMessage(type.charAt(0).toUpperCase() + type.slice(1).replace('-', ' '), true);
    
    addMessage('What time do you need the ' + chatState.jobData.service_type + '?<br><br>Type start and end time (e.g., "9am to 5pm"):', false);
    chatState.step = 'post_job_time';
    setOptions([
        { text: 'Morning (6am-12pm)', action: () => setTime('06:00', '12:00') },
        { text: 'Afternoon (12pm-6pm)', action: () => setTime('12:00', '18:00') },
        { text: 'Evening (6pm-10pm)', action: () => setTime('18:00', '22:00') },
        { text: 'Full Day (8am-6pm)', action: () => setTime('08:00', '18:00') }
    ]);
}

function setTime(start, end) {
    chatState.jobData.start_time = start;
    chatState.jobData.end_time = end;
    var startFormatted = formatTime(start);
    var endFormatted = formatTime(end);
    addMessage(startFormatted + ' to ' + endFormatted, true);
    chatState.postJobStep = 4;
    postJobStep4Rate();
}

function formatTime(time) {
    var parts = time.split(':');
    var hour = parseInt(parts[0]);
    var ampm = hour >= 12 ? 'pm' : 'am';
    hour = hour % 12 || 12;
    return hour + ampm;
}

// Step 4: Rate Settlement
function postJobStep4Rate() {
    showProgress(4);
    addMessage('<b>Step 4: Rate Settlement</b><br>What hourly rate are you offering? (in £)', false);
    chatState.step = 'post_job_rate';
    setOptions([
        { text: '£10/hour', action: () => setRate(10) },
        { text: '£12/hour', action: () => setRate(12) },
        { text: '£15/hour', action: () => setRate(15) },
        { text: '£20/hour', action: () => setRate(20) },
        { text: 'Custom', action: () => { addMessage('Type your hourly rate (number only):', false); } }
    ]);
}

function setRate(rate) {
    chatState.jobData.hourly_rate = rate;
    chatState.jobData.daily_rate = rate * 8;
    chatState.jobData.weekly_rate = rate * 40;
    addMessage('£' + rate + '/hour', true);
    
    addMessage('Any additional details about the job?<br><br>Describe what you need (experience, requirements, etc.):', false);
    chatState.step = 'post_job_description';
    setOptions([]);
}

// Step 5: Other Requirements
function postJobStep5Other() {
    showProgress(5);
    addMessage('<b>Step 5: Final Details</b><br>What\'s your location/postcode?', false);
    chatState.step = 'post_job_location';
    setOptions([]);
}

function submitJob() {
    addMessage('📤 Posting your job and finding matches...', false);
    showTyping();
    showProgress(0);
    
    fetch('/chatbot-post-job', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            'Accept': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify({
            role_id: chatState.jobData.role_id,
            title: chatState.jobData.title,
            description: chatState.jobData.description || chatState.jobData.title,
            days: chatState.jobData.days,
            address: chatState.jobData.address,
            postcode: chatState.jobData.postcode || chatState.jobData.address,
            hourly_rate: chatState.jobData.hourly_rate,
            daily_rate: chatState.jobData.daily_rate,
            weekly_rate: chatState.jobData.weekly_rate
        })
            })
    .then(r => r.json())
    .then(data => {
        hideTyping();
        if (data.success) {
            addMessage('✅ <b>Job Posted Successfully!</b><br><br>Your job for a <b>' + chatState.jobData.service_type + '</b> has been posted.<br><br>💰 Rate: £' + chatState.jobData.hourly_rate + '/hour<br>📍 Location: ' + (chatState.jobData.address || 'Not specified'), false);
            
            if (data.matches && data.matches.length > 0) {
                addMessage('🎯 Here are your best matches based on our ML algorithm:', false);
                showCarerMatches(data.matches);
            } else {
                addMessage('We\'ll notify matching carers. Check your inbox for responses!', false);
            }
            setOptions([
                { text: '📝 Post Another Job', action: () => resetChat() },
                { text: '🔍 Browse Carers', action: () => { window.location.href = '/sellers'; } },
                { text: '🏠 Main Menu', action: () => resetChat() }
            ]);
        } else if (data.redirect || (data.message && data.message.includes('login'))) {
            addMessage('🔐 You need to be logged in to post a job.<br><br><a href="/login" style="display:inline-block;padding:12px 24px;background:linear-gradient(135deg,#667eea,#764ba2);color:white;border-radius:25px;text-decoration:none;font-weight:600;margin:5px;">Login</a> <a href="/register" style="display:inline-block;padding:12px 24px;background:#f0f0f0;color:#333;border-radius:25px;text-decoration:none;font-weight:600;margin:5px;">Register</a>', false);
            setOptions([
                { text: '🔍 Find Carers Instead', action: () => findCarers() },
                { text: '🏠 Main Menu', action: () => resetChat() }
            ]);
        } else {
            addMessage((data.message || 'Sorry, could not post the job.') + '<br><br><a href="/post-a-job" style="color:#667eea;font-weight:600;">Post job manually here</a>', false);
            setOptions([
                { text: '🔄 Try Again', action: () => startPostJob() },
                { text: '🏠 Main Menu', action: () => resetChat() }
            ]);
        }
    })
    .catch(err => {
        hideTyping();
        addMessage('Sorry, something went wrong. <a href="/post-a-job" style="color:#667eea;font-weight:600;">Click here to post a job manually</a>.', false);
        showInitialOptions();
    });
}

// Handle text input
function sendUserMessage() {
    var input = document.getElementById('user-input');
    var msg = input.value.trim();
    if (!msg) return;
    
    addMessage(msg, true);
    input.value = '';
    
    switch(chatState.step) {
        case 'post_job_title':
            chatState.jobData.title = msg;
            addMessage('Got it! Any specific requirements or preferences? (e.g., gender preference, experience needed)', false);
            chatState.step = 'post_job_gender';
            setOptions([
                { text: 'No preference', action: () => { chatState.jobData.gender = 'nopreferences'; postJobStep3Time(); } },
                { text: 'Male', action: () => { chatState.jobData.gender = 'male'; postJobStep3Time(); } },
                { text: 'Female', action: () => { chatState.jobData.gender = 'female'; postJobStep3Time(); } }
            ]);
            break;
            
        case 'post_job_time':
            var times = msg.match(/(\d{1,2}(?::\d{2})?(?:am|pm)?)/gi);
            if (times && times.length >= 2) {
                chatState.jobData.start_time = times[0];
                chatState.jobData.end_time = times[1];
            }
            addMessage('Time noted!', false);
            chatState.postJobStep = 4;
            postJobStep4Rate();
            break;
            
        case 'post_job_rate':
            var rate = parseInt(msg.replace(/[^0-9]/g, ''));
            if (rate > 0) {
                setRate(rate);
            } else {
                addMessage('Please enter a valid number for the hourly rate:', false);
            }
            break;
            
        case 'post_job_description':
            chatState.jobData.description = msg;
            chatState.postJobStep = 5;
            addMessage('Great description! Now, which days do you need help?', false);
            chatState.jobData.days = [];
            showDaySelection(true);
            break;
            
        case 'post_job_location':
            chatState.jobData.address = msg;
            chatState.jobData.postcode = msg;
            submitJob();
            break;
            
        default:
            handleFreeChat(msg);
    }
}

function handleFreeChat(msg) {
    var lowerMsg = msg.toLowerCase();
    var response = '';
    
    if (lowerMsg.includes('find') || lowerMsg.includes('search') || lowerMsg.includes('carer') || lowerMsg.includes('babysitter')) {
        setTimeout(() => startFindCarer(), 500);
        return;
    } else if (lowerMsg.includes('post') || lowerMsg.includes('job') || lowerMsg.includes('hire')) {
        setTimeout(() => startPostJob(), 500);
        return;
    } else if (lowerMsg.includes('how') || lowerMsg.includes('work')) {
        response = '✨ <b>How Jimacare Works:</b><br><br>1️⃣ <b>Search or Post</b> - Find carers or post a job<br>2️⃣ <b>AI Matching</b> - We find the best matches<br>3️⃣ <b>Connect</b> - Message or video call<br>4️⃣ <b>Book & Pay</b> - Securely through our platform<br>5️⃣ <b>Review</b> - Help others by leaving feedback';
    } else if (lowerMsg.includes('price') || lowerMsg.includes('cost') || lowerMsg.includes('fee')) {
        response = '💰 <b>Pricing:</b><br><br>• Carers set their own rates (typically £10-25/hour)<br>• You agree directly with them<br>• Small service fee applies to bookings';
    } else if (lowerMsg.includes('hello') || lowerMsg.includes('hi') || lowerMsg.includes('hey')) {
        response = 'Hello! 👋 How can I help you today?';
    } else {
        response = 'I can help you with:<br><br>🔍 <b>Finding carers</b><br>📝 <b>Posting jobs</b><br>❓ <b>Questions</b><br><br>What would you like to do?';
    }
    
    setTimeout(() => {
        addMessage(response, false);
        showInitialOptions();
    }, 500);
}

function showHowItWorks() {
    addMessage('❓ How it Works', true);
    addMessage('✨ <b>How Jimacare Works:</b><br><br>1️⃣ <b>Choose Service</b> - Carer, Childminder, or Housekeeper<br>2️⃣ <b>Set Requirements</b> - Job details & preferences<br>3️⃣ <b>Time & Schedule</b> - When you need help<br>4️⃣ <b>Rate Settlement</b> - Set your budget<br>5️⃣ <b>Get Matched</b> - AI finds the best carers<br><br>Our ML algorithm matches you with carers based on skills, experience, availability, and location!', false);
    showInitialOptions();
}

function startFreeChat() {
    addMessage('💬 Ask a Question', true);
    addMessage('Sure! Type your question below and I\'ll help you.', false);
    chatState.step = 'free_chat';
    setOptions([]);
}

function resetChat() {
    chatState = { step: 'initial', postJobStep: 0, jobData: { service_type: '', title: '', company: '', gender: 'nopreferences', start_type: 'immediately', start_date: '', end_type: 'on-going', end_date: '', start_time: '', end_time: '', hourly_rate: '', daily_rate: '', weekly_rate: '', description: '', days: [], address: '', postcode: '' }, isLoggedIn: chatState.isLoggedIn };
    document.getElementById('chat-messages').innerHTML = '<div class="bot-msg">Hello! 👋 I can help you find the perfect carer or post a job. What would you like to do?</div>';
    showProgress(0);
    showInitialOptions();
}
</script>


	@if(session()->has('notice'))
		<div class='alert alert-{{session('type')??'success'}} alert-notice alert-dismissible fade show' role='alert'>
			{{ session('notice') }}
			<button type='button' class='close' data-dismiss='alert' aria-label='Close'>
				<span aria-hidden='true'>&times;</span>
			</button>
		</div>
	@endif
	<script src="{{ asset('js/app.js') }}"></script>

	<!-- Service Worker Registration -->
	<script>
		if ('serviceWorker' in navigator) {
			window.addEventListener('load', function() {
				navigator.serviceWorker.register('/sw.js')
					.then(function(registration) {
						console.log('ServiceWorker registered:', registration.scope);
					})
					.catch(function(error) {
						console.log('ServiceWorker registration failed:', error);
					});
			});
		}
	</script>

	@stack('scripts')
	
	</body>

</html>
