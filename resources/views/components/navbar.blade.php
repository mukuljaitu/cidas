@php
$createItems = [
['label' => 'Tour', 'url' => route('tours.create'), 'active' => request()->is('create-tour*')],
['label' => 'Order', 'url' => route('orders.index'), 'active' => request()->is('orders*')],
];

$analyseItems = [
['label' => 'View Tours', 'url' => route('tours.index'), 'active' => request()->is('view-tours*')],
['label' => 'Employee Analyzer', 'url' => route('employees.analyze'), 'active' => request()->is('view-employees*')],
['label' => 'Order Analyzer', 'url' => route('orders.analyze'), 'active' => request()->is('view-orders*')],
];

$assetsItems = [
['label' => 'Employees', 'url' => url('/employees'), 'active' => request()->is('employees*')],
['label' => 'Parties', 'url' => url('/parties'), 'active' => request()->is('parties*')],
['label' => 'Cities', 'url' => url('/cities'), 'active' => request()->is('cities*')],
['label' => 'Products', 'url' => url('/products'), 'active' => request()->is('products*')],
['label' => 'Variants', 'url' => url('/variants'), 'active' => request()->is('variants*')],
['label' => 'Transports', 'url' => url('/transports'), 'active' => request()->is('transports*')],
];

$financialsItems = [
['label' => 'Banking', 'url' => route('banks.index'), 'active' => request()->is('banks*')],
];

$groups = [$createItems, $analyseItems, $assetsItems, $financialsItems];

$switchItems = [];
foreach ($groups as $items) {
foreach ($items as $it) {
if (!empty($it['active'])) {
$switchItems = $items;
break 2;
}
}
}

$user = Auth::user();
$isAdmin = $user?->isAdmin() ?? false;
$unreadCount = $user ? $user->unreadNotifications()->count() : 0;
$latestNotifications = $user ? $user->notifications()->latest()->limit(8)->get() : collect();
@endphp

<header class="h-16 bg-white border-b border-gray-200 px-8 flex items-center justify-between sticky top-0 z-30">
    <div class="flex items-center gap-4 flex-1 min-w-0">
        <div class="flex-1 min-w-0 max-w-xl">
            <div class="google-search">
                <span class="google-search-icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </span>
                <input
                    id="globalSearch"
                    type="search"
                    placeholder="Search index..."
                    class="google-search-input" />
                <button type="button" class="google-search-clear" aria-label="Clear search" onclick="const i=this.previousElementSibling; if(i){ i.value=''; i.dispatchEvent(new Event('input')); i.focus(); }">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Right Actions -->
    <div class="flex items-center gap-4">
        <a href="{{ url('/') }}" class="inline-flex items-center h-9 px-4 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition-colors shrink-0">Dashboard</a>

        @if(!empty($switchItems))
        <div class="hidden lg:flex items-center gap-2 shrink-0">
            @foreach($switchItems as $item)
            <a
                href="{{ $item['url'] }}"
                class="inline-flex items-center h-9 px-3 rounded-lg text-sm font-semibold transition-colors {{ $item['active'] ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' }}">
                {{ $item['label'] }}
            </a>
            @endforeach
        </div>
        @endif

        <div class="relative">
            <button type="button" data-notifications-trigger class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded-lg transition-all relative" aria-haspopup="true" aria-expanded="false">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                @if($unreadCount > 0)
                <span class="absolute top-2 right-2 min-w-2 h-2 bg-red-500 border-2 border-white rounded-full"></span>
                @endif
            </button>

            <div id="notificationsMenu" class="hidden absolute right-0 mt-2 w-[420px] max-w-[90vw] bg-white border border-gray-200 rounded-2xl shadow-xl overflow-hidden z-40">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <div class="text-sm font-bold text-gray-900">Notifications</div>
                        <div class="text-xs text-gray-400">{{ $unreadCount }} unread</div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        @if($unreadCount > 0)
                        <form method="POST" action="{{ route('notifications.markAllRead') }}">
                            @csrf
                            <button type="submit" class="text-xs font-bold text-blue-600 hover:text-blue-700">Mark all read</button>
                        </form>
                        @endif
                    </div>
                </div>

                @if($isAdmin)
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/40">
                    <button type="button" data-notification-compose-toggle class="inline-flex items-center gap-2 text-xs font-bold text-gray-700 hover:text-gray-900">
                        <span>New update</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </button>

                    <form method="POST" action="{{ route('admin.notifications.store') }}" class="mt-3 hidden space-y-3" data-notification-compose-form>
                        @csrf
                        <input name="title" type="text" class="block w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Title" maxlength="120" required />
                        <textarea name="message" rows="3" class="block w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all resize-none" placeholder="Write an update..." maxlength="1000" required></textarea>
                        <div class="flex items-center justify-end">
                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition-colors shadow-sm shadow-blue-200">
                                Send
                            </button>
                        </div>
                    </form>
                </div>
                @endif

                <div class="max-h-[420px] overflow-y-auto">
                    @if($latestNotifications->isEmpty())
                    <div class="p-8 text-center">
                        <div class="text-sm font-semibold text-gray-900">No notifications</div>
                        <div class="mt-1 text-xs text-gray-400">Updates from admin show up here.</div>
                    </div>
                    @else
                    <div class="divide-y divide-gray-100">
                        @foreach($latestNotifications as $notification)
                        @php
                        $data = is_array($notification->data) ? $notification->data : [];
                        $title = $data['title'] ?? 'Update';
                        $message = $data['message'] ?? '';
                        $isUnread = $notification->read_at === null;
                        @endphp

                        @if($isUnread)
                        <form method="POST" action="{{ route('notifications.read', ['notificationId' => $notification->id]) }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-5 py-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-start gap-3">
                                    <span class="mt-1 w-2 h-2 rounded-full bg-blue-600 shrink-0"></span>
                                    <div class="min-w-0">
                                        <div class="text-sm font-bold text-gray-900 truncate">{{ $title }}</div>
                                        <div class="mt-1 text-xs text-gray-500">{{ \Illuminate\Support\Str::limit($message, 120) }}</div>
                                        <div class="mt-2 text-[11px] font-semibold text-gray-400">{{ optional($notification->created_at)->diffForHumans() }}</div>
                                    </div>
                                </div>
                            </button>
                        </form>
                        @else
                        <div class="px-5 py-4">
                            <div class="flex items-start gap-3">
                                <span class="mt-1 w-2 h-2 rounded-full bg-gray-200 shrink-0"></span>
                                <div class="min-w-0">
                                    <div class="text-sm font-semibold text-gray-900 truncate">{{ $title }}</div>
                                    <div class="mt-1 text-xs text-gray-500">{{ \Illuminate\Support\Str::limit($message, 120) }}</div>
                                    <div class="mt-2 text-[11px] font-semibold text-gray-400">{{ optional($notification->created_at)->diffForHumans() }}</div>
                                </div>
                            </div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="w-8 h-8 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center overflow-hidden">
            @php
            $initial = strtoupper(substr($user?->name ?: 'U', 0, 1));
            @endphp
            <span class="text-xs font-bold text-gray-600">{{ $initial }}</span>
        </div>
    </div>
</header>
