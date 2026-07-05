<!DOCTYPE html>
<html lang="ja">
<head>
@if(app()->environment('production'))
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-KLXGD5FL');</script>
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-18106143411"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'AW-18106143411');
    </script>
@endif
    <meta charset="UTF-8">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>LINE応募｜{{ $job->seo_title }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://static.line-scdn.net/liff/edge/2/sdk.js"></script>
    <style>
        *{box-sizing:border-box;-webkit-tap-highlight-color:transparent;}
        html,body{margin:0;padding:0;height:100%;font-family:'Hiragino Kaku Gothic ProN','Hiragino Sans','Meiryo',sans-serif;}
        body{background:#8caeca;color:#333;overflow:hidden;}
        .chat-wrap{max-width:480px;margin:0 auto;background:#8caeca;height:100vh;display:flex;flex-direction:column;position:relative;overflow:hidden;}

        /* Header */
        .chat-header{position:sticky;top:0;background:#06C755;color:#fff;padding:10px 14px;display:flex;align-items:center;gap:10px;box-shadow:0 1px 3px rgba(0,0,0,0.15);z-index:10;}
        .chat-header-logo{width:36px;height:36px;background:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
        .chat-header-meta{flex:1;min-width:0;}
        .chat-header-name{font-weight:700;font-size:0.92rem;line-height:1.2;}
        .chat-header-sub{font-size:0.7rem;opacity:0.9;line-height:1.2;margin-top:2px;}

        /* Scroll area */
        .chat-area{flex:1;overflow-y:auto;padding:14px 10px 20px;-webkit-overflow-scrolling:touch;}

        /* Rows & bubbles */
        .row{display:flex;align-items:flex-end;gap:6px;margin-bottom:6px;opacity:0;transform:translateY(6px);animation:pop .22s forwards;}
        .row.bot{justify-content:flex-start;}
        .row.user{justify-content:flex-end;}
        .avatar{width:34px;height:34px;border-radius:50%;background:#06C755;display:flex;align-items:center;justify-content:center;color:#fff;flex-shrink:0;font-size:0.9rem;}
        .avatar svg{width:22px;height:22px;}
        .bubble{max-width:72%;padding:9px 13px;border-radius:16px;font-size:0.92rem;line-height:1.55;word-wrap:break-word;white-space:pre-wrap;}
        .bubble.bot{background:#fff;color:#333;border-top-left-radius:4px;box-shadow:0 1px 2px rgba(0,0,0,0.05);}
        .bubble.user{background:#89e089;color:#1a1a1a;border-top-right-radius:4px;box-shadow:0 1px 2px rgba(0,0,0,0.05);}

        @keyframes pop{to{opacity:1;transform:translateY(0);}}

        /* Typing indicator */
        .typing{display:inline-flex;gap:4px;padding:14px 16px;background:#fff;border-radius:16px;border-top-left-radius:4px;align-items:center;box-shadow:0 1px 2px rgba(0,0,0,0.05);}
        .typing i{width:6px;height:6px;background:#999;border-radius:50%;display:inline-block;animation:blink 1.2s infinite;}
        .typing i:nth-child(2){animation-delay:.2s;}
        .typing i:nth-child(3){animation-delay:.4s;}
        @keyframes blink{0%,80%,100%{opacity:.25;}40%{opacity:1;}}

        /* Job card bubble */
        .job-card{max-width:88%;background:#fff;border-radius:12px;border-top-left-radius:4px;padding:14px;box-shadow:0 1px 3px rgba(0,0,0,0.08);}
        .job-card-title{font-weight:800;font-size:0.96rem;color:#1a1a2e;margin:0 0 4px;line-height:1.4;}
        .job-card-company{font-size:0.78rem;color:#666;margin:0 0 10px;}
        .job-card-badges{display:flex;flex-wrap:wrap;gap:5px;margin-bottom:8px;}
        .job-badge{font-size:0.72rem;font-weight:700;padding:3px 8px;border-radius:10px;}
        .badge-area{background:#e6f4ea;color:#137333;}
        .badge-type{background:#e8f0fe;color:#1967d2;}
        .badge-emp{background:#fef7e0;color:#8a6d00;}
        .job-card-salary{font-weight:800;color:#e65100;font-size:0.95rem;margin-top:8px;}
        .job-card-desc{font-size:0.78rem;color:#555;line-height:1.6;margin-top:8px;padding-top:8px;border-top:1px solid #eef1f5;}

        /* Action buttons */
        .actions{display:flex;flex-direction:column;gap:6px;padding:6px 44px 6px 0;margin-bottom:8px;align-items:flex-end;opacity:0;animation:pop .22s forwards;}
        .btn-choice{padding:10px 22px;border-radius:22px;border:none;font-weight:700;font-size:0.92rem;cursor:pointer;min-width:180px;}
        .btn-primary{background:#06C755;color:#fff;box-shadow:0 2px 4px rgba(6,199,85,0.3);}
        .btn-primary:active{background:#05a847;}
        .btn-secondary{background:#fff;color:#555;border:1.5px solid #ccc;}
        .btn-secondary:active{background:#f5f5f5;}

        /* Input bar */
        .input-bar{background:#f0f0f0;padding:8px 10px 12px;display:none;gap:8px;align-items:flex-end;border-top:1px solid #ddd;}
        .input-bar.show{display:flex;}
        .input-wrap{flex:1;display:flex;flex-direction:column;gap:4px;}
        .input-hint{font-size:0.72rem;color:#888;padding:0 4px;}
        .chat-input{width:100%;border:1.5px solid #ccc;border-radius:20px;padding:10px 14px;background:#fff;font-size:0.95rem;outline:none;}
        .chat-input:focus{border-color:#06C755;box-shadow:0 0 0 2px rgba(6,199,85,0.15);}
        .chat-input.is-invalid{border-color:#dc3545;animation:shake .3s;}
        @keyframes shake{0%,100%{transform:translateX(0);}25%{transform:translateX(-4px);}75%{transform:translateX(4px);}}
        .chat-send{background:#06C755;color:#fff;border:none;border-radius:50%;width:38px;height:38px;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:1.1rem;}
        .chat-send:disabled{background:#ccc;}
        .skip-link{font-size:0.8rem;color:#1a73e8;text-decoration:none;padding:4px 8px;align-self:center;}

        /* Loading overlay */
        .loading{display:none;position:fixed;inset:0;background:rgba(255,255,255,0.9);z-index:999;align-items:center;justify-content:center;flex-direction:column;gap:12px;}
        .loading.show{display:flex;}
        .loading-spinner{width:32px;height:32px;border:3px solid #ccc;border-top-color:#06C755;border-radius:50%;animation:spin 1s linear infinite;}
        @keyframes spin{to{transform:rotate(360deg);}}

        /* Error */
        .error-toast{position:fixed;top:60px;left:50%;transform:translateX(-50%);background:#dc3545;color:#fff;padding:10px 18px;border-radius:8px;font-size:0.88rem;z-index:100;box-shadow:0 2px 8px rgba(0,0,0,0.2);display:none;}
        .error-toast.show{display:block;animation:pop .2s;}
    </style>
</head>
<body>
@if(app()->environment('production'))
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KLXGD5FL" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
@endif

<div class="chat-wrap">
    <div class="chat-header">
        <div class="chat-header-logo">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="#06C755"><path d="M12 2C6.477 2 2 6.057 2 11.08c0 4.512 3.996 8.29 9.39 9.04.366.078.862.24.987.551.113.281.074.722.036 1.007l-.16.957c-.05.28-.228 1.098.964.599 1.193-.5 6.43-3.785 8.77-6.48C23.24 14.87 24 13.06 24 11.08 24 6.057 19.523 2 12 2z"/></svg>
        </div>
        <div class="chat-header-meta">
            <div class="chat-header-name">Care Entry(ケアエントリー)</div>
            <div class="chat-header-sub">介護・福祉求人 応募窓口</div>
        </div>
    </div>

    <div class="chat-area" id="chatArea"></div>

    <div class="input-bar" id="inputBar">
        <div class="input-wrap">
            <div class="input-hint" id="inputHint"></div>
            <input type="text" id="chatInput" class="chat-input" autocomplete="off">
        </div>
        <a href="#" class="skip-link" id="skipLink" style="display:none;">スキップ</a>
        <button type="button" id="chatSend" class="chat-send" aria-label="送信">
            <i class="bi bi-arrow-up"></i>
        </button>
    </div>
</div>

<div class="loading" id="loading">
    <div class="loading-spinner"></div>
    <div style="font-size:0.9rem;color:#555;">送信中...</div>
</div>

<div class="error-toast" id="errorToast"></div>

@php
    $areaNames = $job->jobAreas->take(3)->map(fn($ja) => $ja->area?->name)->filter()->values();
    $typeNames = $job->jobJobTypes->take(3)->map(fn($jt) => $jt->jobType?->name)->filter()->values();
    $empNames  = $job->jobEmploymentTypes->take(3)->map(fn($et) => $et->employmentType?->name)->filter()->values();
    $descShort = $job->free_text ? mb_strimwidth($job->free_text, 0, 160, '…') : null;
@endphp

<script>
const LIFF_ID    = '{{ $liffId }}';
const STORE_URL  = @json(route('liff.apply.store', ['token' => $job->token]));
const THANKS_URL = @json(route('liff.thanks', ['token' => $job->token]));
const LP_URL     = @json(route('lp.show', ['token' => $job->token]));

const JOB = {
    title:   @json($job->seo_title ?: $job->title),
    company: @json($job->company_name),
    areas:   @json($areaNames),
    types:   @json($typeNames),
    emps:    @json($empNames),
    salary:  @json($job->salaryText()),
    desc:    @json($descShort),
};

const state = {
    profile: null,
    answers: { applicant_name: '', phone: '', email: '', line_user_id: '', line_display_name: '', line_session_id: '' },
    inputResolver: null,
    choiceResolver: null,
};

const chatArea = document.getElementById('chatArea');
const inputBar = document.getElementById('inputBar');
const chatInput = document.getElementById('chatInput');
const inputHint = document.getElementById('inputHint');
const chatSend = document.getElementById('chatSend');
const skipLink = document.getElementById('skipLink');
const loading = document.getElementById('loading');
const errorToast = document.getElementById('errorToast');

function sleep(ms) { return new Promise(r => setTimeout(r, ms)); }
function scrollBottom() { chatArea.scrollTop = chatArea.scrollHeight; }
function showError(msg) {
    errorToast.textContent = msg;
    errorToast.classList.add('show');
    setTimeout(() => errorToast.classList.remove('show'), 2500);
}

function addTyping() {
    const el = document.createElement('div');
    el.className = 'row bot';
    el.innerHTML = '<div class="avatar"><svg viewBox="0 0 24 24" fill="#fff"><path d="M12 2C6.477 2 2 6.057 2 11.08c0 4.512 3.996 8.29 9.39 9.04.366.078.862.24.987.551.113.281.074.722.036 1.007l-.16.957c-.05.28-.228 1.098.964.599 1.193-.5 6.43-3.785 8.77-6.48C23.24 14.87 24 13.06 24 11.08 24 6.057 19.523 2 12 2z"/></svg></div><div class="typing"><i></i><i></i><i></i></div>';
    el.id = 'typing-' + Date.now();
    chatArea.appendChild(el);
    scrollBottom();
    return el.id;
}
function removeTyping(id) {
    const el = document.getElementById(id);
    if (el) el.remove();
}

function addBotBubble(text) {
    const el = document.createElement('div');
    el.className = 'row bot';
    el.innerHTML = `<div class="avatar"><svg viewBox="0 0 24 24" fill="#fff"><path d="M12 2C6.477 2 2 6.057 2 11.08c0 4.512 3.996 8.29 9.39 9.04.366.078.862.24.987.551.113.281.074.722.036 1.007l-.16.957c-.05.28-.228 1.098.964.599 1.193-.5 6.43-3.785 8.77-6.48C23.24 14.87 24 13.06 24 11.08 24 6.057 19.523 2 12 2z"/></svg></div><div class="bubble bot">${escapeHtml(text)}</div>`;
    chatArea.appendChild(el);
    scrollBottom();
}

function addUserBubble(text) {
    const el = document.createElement('div');
    el.className = 'row user';
    el.innerHTML = `<div class="bubble user">${escapeHtml(text)}</div>`;
    chatArea.appendChild(el);
    scrollBottom();
}

function addJobCard() {
    const el = document.createElement('div');
    el.className = 'row bot';
    el.innerHTML = `
        <div class="avatar"><svg viewBox="0 0 24 24" fill="#fff"><path d="M12 2C6.477 2 2 6.057 2 11.08c0 4.512 3.996 8.29 9.39 9.04.366.078.862.24.987.551.113.281.074.722.036 1.007l-.16.957c-.05.28-.228 1.098.964.599 1.193-.5 6.43-3.785 8.77-6.48C23.24 14.87 24 13.06 24 11.08 24 6.057 19.523 2 12 2z"/></svg></div>
        <div class="job-card">
            <div class="job-card-title">${escapeHtml(JOB.title)}</div>
            <div class="job-card-company"><i class="bi bi-building"></i> ${escapeHtml(JOB.company || '')}</div>
            <div class="job-card-badges">
                ${JOB.areas.map(a => `<span class="job-badge badge-area"><i class="bi bi-geo-alt-fill"></i> ${escapeHtml(a)}</span>`).join('')}
                ${JOB.types.map(t => `<span class="job-badge badge-type">${escapeHtml(t)}</span>`).join('')}
                ${JOB.emps.map(e => `<span class="job-badge badge-emp">${escapeHtml(e)}</span>`).join('')}
            </div>
            ${JOB.salary ? `<div class="job-card-salary">${escapeHtml(JOB.salary)}</div>` : ''}
            ${JOB.desc ? `<div class="job-card-desc">${escapeHtml(JOB.desc)}</div>` : ''}
        </div>`;
    chatArea.appendChild(el);
    scrollBottom();
}

function addActionButtons(options) {
    return new Promise(resolve => {
        const container = document.createElement('div');
        container.className = 'actions';
        options.forEach(opt => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn-choice ' + (opt.primary ? 'btn-primary' : 'btn-secondary');
            btn.textContent = opt.label;
            btn.addEventListener('click', () => {
                container.remove();
                resolve(opt.value ?? opt.label);
            });
            container.appendChild(btn);
        });
        chatArea.appendChild(container);
        scrollBottom();
    });
}

async function botSay(text, delay = 500) {
    const id = addTyping();
    await sleep(delay);
    removeTyping(id);
    addBotBubble(text);
    await sleep(150);
}

function showInput({ placeholder = '', hint = '', type = 'text', prefill = '', allowSkip = false, validate = null }) {
    return new Promise(resolve => {
        inputHint.textContent = hint;
        chatInput.value = prefill;
        chatInput.placeholder = placeholder;
        chatInput.type = type;
        chatInput.classList.remove('is-invalid');
        chatInput.inputMode = type === 'tel' ? 'numeric' : (type === 'email' ? 'email' : 'text');
        skipLink.style.display = allowSkip ? 'inline' : 'none';
        inputBar.classList.add('show');
        scrollBottom();
        setTimeout(() => chatInput.focus(), 100);

        const commit = (raw) => {
            const value = raw.trim();
            if (!allowSkip && !value) {
                chatInput.classList.add('is-invalid');
                showError('入力してください');
                setTimeout(() => chatInput.classList.remove('is-invalid'), 1500);
                return;
            }
            if (validate) {
                const err = validate(value);
                if (err) {
                    chatInput.classList.add('is-invalid');
                    showError(err);
                    setTimeout(() => chatInput.classList.remove('is-invalid'), 1500);
                    return;
                }
            }
            cleanup();
            resolve(value);
        };

        const onSend = () => commit(chatInput.value);
        const onKey = (e) => { if (e.key === 'Enter') { e.preventDefault(); commit(chatInput.value); } };
        const onSkip = (e) => {
            e.preventDefault();
            cleanup();
            resolve('');
        };

        const cleanup = () => {
            inputBar.classList.remove('show');
            chatSend.removeEventListener('click', onSend);
            chatInput.removeEventListener('keydown', onKey);
            skipLink.removeEventListener('click', onSkip);
            chatInput.value = '';
        };

        chatSend.addEventListener('click', onSend);
        chatInput.addEventListener('keydown', onKey);
        skipLink.addEventListener('click', onSkip);
    });
}

function escapeHtml(str) {
    return String(str).replace(/[&<>"']/g, ch => ({
        '&': '&amp;','<': '&lt;','>': '&gt;','"': '&quot;',"'": '&#39;'
    })[ch]);
}

async function initLiff() {
    try {
        await liff.init({ liffId: LIFF_ID });
        if (!liff.isLoggedIn()) {
            liff.login({ redirectUri: location.href });
            return false;
        }
        const profile = await liff.getProfile();
        state.profile = profile;
        state.answers.line_user_id = profile.userId;
        state.answers.line_display_name = profile.displayName;
        const ctx = liff.getContext();
        state.answers.line_session_id = ctx?.utouId || ctx?.roomId || ctx?.groupId || '';
        return true;
    } catch (e) {
        console.error(e);
        showError('LINEログインに失敗しました');
        return false;
    }
}

async function submitApplication() {
    loading.classList.add('show');
    try {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const formData = new URLSearchParams();
        Object.entries(state.answers).forEach(([k, v]) => { if (v) formData.append(k, v); });
        formData.append('_token', csrf);
        const res = await fetch(STORE_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
            },
            body: formData.toString(),
        });
        if (!res.ok) throw new Error('HTTP ' + res.status);
        const json = await res.json();
        if (json.redirect) {
            location.href = json.redirect;
        } else {
            throw new Error('no redirect');
        }
    } catch (e) {
        console.error(e);
        loading.classList.remove('show');
        showError('送信に失敗しました。もう一度お試しください');
    }
}

async function main() {
    // Initialize LIFF first (needs LINE login)
    const ok = await initLiff();
    if (!ok) return;

    // 1. Greeting
    await botSay('こんにちは!Care Entry(ケアエントリー)です👋', 400);
    await botSay('以下の求人へのご応募でお間違いないでしょうか?', 500);
    await sleep(200);
    addJobCard();
    await sleep(500);

    // 2. Confirm intent
    const decision = await addActionButtons([
        { label: '応募する', primary: true, value: 'apply' },
        { label: '見送る', value: 'decline' },
    ]);
    addUserBubble(decision === 'apply' ? '応募する' : '見送る');

    if (decision === 'decline') {
        await sleep(300);
        await botSay('かしこまりました。またの機会にお待ちしております。', 500);
        setTimeout(() => {
            if (liff.isInClient()) liff.closeWindow();
            else location.href = LP_URL;
        }, 2000);
        return;
    }

    // 3. Name
    await sleep(200);
    const defaultName = state.profile?.displayName || '';
    if (defaultName) {
        await botSay(`ありがとうございます😊\nお名前は「${defaultName}」で承ります。異なる場合は修正して送信してください。`, 500);
    } else {
        await botSay('お名前を教えてください', 400);
    }
    const name = await showInput({
        placeholder: 'お名前(例: 山田 太郎)',
        prefill: defaultName,
        validate: v => v.length > 100 ? 'お名前は100文字以内で入力してください' : null,
    });
    state.answers.applicant_name = name;
    addUserBubble(name);

    // 4. Phone
    await sleep(200);
    await botSay('お電話番号を教えてください(ハイフンなし)', 500);
    const phone = await showInput({
        placeholder: '例: 09012345678',
        hint: 'ハイフンなしの数字10〜11桁',
        type: 'tel',
        validate: v => /^[0-9]{10,11}$/.test(v) ? null : 'ハイフンなしの数字10〜11桁で入力してください',
    });
    state.answers.phone = phone;
    addUserBubble(phone);

    // 5. Email (optional)
    await sleep(200);
    await botSay('応募控えをメールでお送りできます。メールアドレスをご入力ください(スキップも可)', 600);
    const email = await showInput({
        placeholder: 'you@example.com(任意)',
        hint: '応募内容の控えをお送りします(任意)',
        type: 'email',
        allowSkip: true,
        validate: v => (!v || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) ? null : 'メールアドレスの形式が正しくありません',
    });
    state.answers.email = email;
    addUserBubble(email || '(スキップ)');

    // 6. Summary + submit
    await sleep(200);
    const summaryLines = [
        '以下の内容で応募いたします。よろしいですか?',
        '',
        `・お名前: ${name}`,
        `・電話番号: ${phone}`,
        email ? `・メール: ${email}` : '・メール: (なし)',
    ];
    await botSay(summaryLines.join('\n'), 600);

    const confirm = await addActionButtons([
        { label: 'この内容で応募する', primary: true, value: 'submit' },
        { label: 'やり直す', value: 'restart' },
    ]);
    addUserBubble(confirm === 'submit' ? 'この内容で応募する' : 'やり直す');

    if (confirm === 'restart') {
        await sleep(300);
        await botSay('やり直します。少々お待ちください', 400);
        setTimeout(() => location.reload(), 1200);
        return;
    }

    // 7. Submit
    await sleep(200);
    await botSay('応募内容を送信しています...', 300);
    await submitApplication();
}

// CSRF meta for the request
(function() {
    if (!document.querySelector('meta[name="csrf-token"]')) {
        const m = document.createElement('meta');
        m.name = 'csrf-token';
        m.content = @json(csrf_token());
        document.head.appendChild(m);
    }
})();

main();
</script>
</body>
</html>
