<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CIDAS')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Global CSS -->
    <link rel="stylesheet" href="/css/table-ui.css">

    <!-- Page-specific CSS -->
    @stack('styles')
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #F8FAFC;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #E2E8F0;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #CBD5E1;
        }
    </style>
</head>

<body class="min-h-screen">
    <div class="flex flex-col min-h-screen">
        <x-navbar />

        <main class="flex-1 overflow-y-auto p-8">
            <div class="max-w-7xl mx-auto">
                @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-5 py-4">
                    <div class="text-sm font-bold text-red-800">Please fix the highlighted issues</div>
                    <ul class="mt-2 list-disc pl-5 text-sm text-red-700 space-y-1">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if (session('status'))
                @php
                $statusKey = session('status');
                $statusMessages = [
                'party-created' => 'Party saved successfully.',
                'party-updated' => 'Party updated successfully.',
                'party-deleted' => 'Party deleted successfully.',
                'product-created' => 'Product saved successfully.',
                'product-updated' => 'Product updated successfully.',
                'product-deleted' => 'Product deleted successfully.',
                'variant-created' => 'Variant saved successfully.',
                'variant-updated' => 'Variant updated successfully.',
                'variant-deleted' => 'Variant deleted successfully.',
                'tour-created' => 'Tour saved successfully.',
                'tour-updated' => 'Tour updated successfully.',
                'tour-deleted' => 'Tour deleted successfully.',
                'member-created' => 'Saved successfully.',
                'member-updated' => 'Updated successfully.',
                'member-deleted' => 'Deleted successfully.',
                'transport-created' => 'Transport saved successfully.',
                'transport-updated' => 'Transport updated successfully.',
                'transport-deleted' => 'Transport deleted successfully.',
                'profile-updated' => 'Profile updated successfully.',
                'password-updated' => 'Password updated successfully.',
                'verification-link-sent' => 'Verification link sent.',
                ];
                $statusText = $statusMessages[$statusKey] ?? 'Done.';
                @endphp
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-800">
                    {{ $statusText }}
                </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <!-- Global JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
    <script src="/js/table-ui.js"></script>

    <!-- Page-specific JS -->
    @stack('scripts')
</body>

</html>