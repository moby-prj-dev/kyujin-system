{{--
    クリック計測(施策の効果測定用)。data-track 属性を持つリンクのクリックを /t/click に送る
    - data-track: hw_apply / line_apply / form_apply / pr_job / direct_job
    - data-target-job: 送客枠でクリックされた求人ID
    - ページ側: data-page-job(求人ID) / data-page-type(lp/article/top) を持つ要素を置く
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
