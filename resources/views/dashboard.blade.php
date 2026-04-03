@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="flex flex-col gap-8">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Dashboard Overview</h1>
        <button type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm shadow-blue-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            <span>New Project</span>
        </button>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Active Projects -->
        <div class="bg-blue-600 rounded-2xl p-6 text-white shadow-lg shadow-blue-100 flex flex-col justify-between min-h-[160px]">
            <div>
                <h3 class="text-blue-100 text-sm font-bold uppercase tracking-wider mb-1">Active Projects</h3>
                <p class="text-white/80 text-xs">Currently running</p>
            </div>
            <div class="text-5xl font-bold tracking-tight">—</div>
        </div>

        <!-- Tasks Completed -->
        <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm flex flex-col justify-between min-h-[160px]">
            <div>
                <h3 class="text-gray-400 text-sm font-bold uppercase tracking-wider mb-1">Tasks Completed</h3>
                <p class="text-gray-400 text-xs">This week</p>
            </div>
            <div class="text-5xl font-bold text-blue-600 tracking-tight">—</div>
        </div>

        <!-- Productivity Score -->
        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-6 text-white shadow-lg shadow-indigo-100 lg:col-span-1 flex flex-col justify-between min-h-[160px]">
            <div>
                <h3 class="text-blue-100 text-sm font-bold uppercase tracking-wider mb-1">Productivity Score</h3>
                <p class="text-white/80 text-xs">Last 7 days</p>
            </div>
            <div class="text-5xl font-bold tracking-tight">—</div>
        </div>
    </div>

    <!-- Recent Activity Section (Placeholder) -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/30">
            <h2 class="font-bold text-gray-900">Recent Activity</h2>
            <a href="#" class="text-sm font-bold text-blue-600 hover:text-blue-700">View All</a>
        </div>
        <div class="p-12 flex flex-col items-center justify-center text-center">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-gray-100">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">No activity yet</h3>
            <p class="text-gray-400 max-w-xs">When projects are started or members are updated, you'll see the activity log here.</p>
        </div>
    </div>
</div>
@endsection
