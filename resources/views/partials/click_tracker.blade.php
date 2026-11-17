{{--
    クリック計測(施策の効果測定用)。data-track 属性を持つリンクのクリックを /t/click に送る
    - data-track: hw_apply / line_apply / form_apply / pr_job / direct_job
    - data-target-job: 送客枠でクリックされた求人ID
    - ページ側: data-page-job(求人ID) / data-page-type(lp/article/top/search) を持つ要素を置く
    送客枠(pr_job / direct_job)のカードは、画面に半分以上映ったときに1回だけ /t/impression に記録する
    複数回 include されても1回だけ初期化する
--}}
<script>
(function () {
    if (window.__ceClickTracker) return;
    window.__ceClickTracker = true;
    document.addEventListener('click', function (e) {
        var el = e.target.closest ? e.target.closest('[data-track]') : null;
        if (!el || !navigator.sendBeacon) return;
        var ctx = document.querySelector('[data-page-type]');
        var fd = new FormData();
        fd.append('type', el.getAttribute('data-track'));
        if (el.getAttribute('data-target-job')) fd.append('target_job_id', el.getAttribute('data-target-job'));
        if (ctx) {
            fd.append('page', ctx.getAttribute('data-page-type') || '');
            if (ctx.getAttribute('data-page-job')) fd.append('job_id', ctx.getAttribute('data-page-job'));
        }
        try { navigator.sendBeacon('{{ route('click.track') }}', fd); } catch (err) {}
    }, true);
})();
</script>

<script>
(function () {
    // 送客枠の表示回数: カードが画面に半分以上映ったら1回(同じページ内の同じ枠・求人は1回だけ)
    if (window.__ceImpressionTracker || !('IntersectionObserver' in window) || !navigator.sendBeacon) return;
    window.__ceImpressionTracker = true;
    var seen = {}, queue = [], timer = null;
    function flush() {
        if (!queue.length) return;
        var ctx = document.querySelector('[data-page-type]');
        var fd = new FormData();
        fd.append('page', ctx ? (ctx.getAttribute('data-page-type') || '') : '');
        queue.forEach(function (k) { fd.append('items[]', k); });
        queue = [];
        try { navigator.sendBeacon('{{ route('impression.track') }}', fd); } catch (err) {}
    }
    var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (en) {
            if (!en.isIntersecting) return;
            var el = en.target;
            io.unobserve(el);
            var key = el.getAttribute('data-track') + ':' + el.getAttribute('data-target-job');
            if (seen[key]) return;
            seen[key] = true;
            queue.push(key);
        });
        clearTimeout(timer);
        timer = setTimeout(flush, 1500);
    }, { threshold: 0.5 });
    function scan() {
        document.querySelectorAll('[data-track="pr_job"][data-target-job], [data-track="direct_job"][data-target-job]')
            .forEach(function (el) { io.observe(el); });
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', scan); else scan();
    document.addEventListener('visibilitychange', function () { if (document.visibilityState === 'hidden') flush(); });
    window.addEventListener('pagehide', flush);
})();
</script>
