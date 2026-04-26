<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;

class HelloworkJobController extends Controller
{
    public function index()
    {
        $jobs = Job::where('source', 'hellowork')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.hellowork.index', compact('jobs'));
    }

    public function edit(Job $job)
    {
        abort_if($job->source !== 'hellowork', 404);
        return view('admin.hellowork.edit', compact('job'));
    }

    public function update(Request $request, Job $job)
    {
        abort_if($job->source !== 'hellowork', 404);

        $request->validate([
            'title'                => ['required', 'string', 'max:100'],
            'seo_title'            => ['nullable', 'string', 'max:100'],
            'meta_description'     => ['nullable', 'string', 'max:320'],
            'description_generated'=> ['nullable', 'string'],
        ]);

        $job->update($request->only([
            'title', 'seo_title', 'meta_description', 'description_generated',
        ]));

        return redirect()->route('admin.hellowork.index')
            ->with('success', "「{$job->title}」を更新しました。");
    }

    public function destroy(Job $job)
    {
        abort_if($job->source !== 'hellowork', 404);
        $title = $job->title;
        $job->delete();

        return back()->with('success', "「{$title}」を削除しました。");
    }
}
