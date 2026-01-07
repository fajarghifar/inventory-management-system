<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Dashboard
        </h2>
    </x-slot>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <!-- Card 1: Total Products -->
        <div class="relative overflow-hidden rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5 transition-all hover:shadow-md">
            <dt>
                <div class="absolute rounded-md bg-indigo-50 px-2 py-2">
                    <x-heroicon-o-cube class="h-6 w-6 text-indigo-600" />
                </div>
                <p class="ml-14 truncate text-sm font-medium text-gray-500">Total Products</p>
            </dt>
            <dd class="ml-14 flex items-baseline pb-1">
                <p class="text-2xl font-semibold text-gray-900">1,248</p>
                <p class="ml-2 flex items-baseline text-xs font-semibold text-green-600">
                    <x-heroicon-m-arrow-up class="h-3 w-3 flex-shrink-0 self-center text-green-500 mr-1" />
                    4.5%
                </p>
            </dd>
        </div>

        <!-- Card 2: Total Revenue -->
        <div class="relative overflow-hidden rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5 transition-all hover:shadow-md">
            <dt>
                <div class="absolute rounded-md bg-emerald-50 px-2 py-2">
                    <x-heroicon-o-currency-dollar class="h-6 w-6 text-emerald-600" />
                </div>
                <p class="ml-14 truncate text-sm font-medium text-gray-500">Total Revenue</p>
            </dt>
            <dd class="ml-14 flex items-baseline pb-1">
                <p class="text-2xl font-semibold text-gray-900">$24,500</p>
                <p class="ml-2 flex items-baseline text-xs font-semibold text-green-600">
                    <x-heroicon-m-arrow-up class="h-3 w-3 flex-shrink-0 self-center text-green-500 mr-1" />
                    12%
                </p>
            </dd>
        </div>

        <!-- Card 3: Active Users -->
        <div class="relative overflow-hidden rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5 transition-all hover:shadow-md">
            <dt>
                <div class="absolute rounded-md bg-purple-50 px-2 py-2">
                    <x-heroicon-o-users class="h-6 w-6 text-purple-600" />
                </div>
                <p class="ml-14 truncate text-sm font-medium text-gray-500">Active Users</p>
            </dt>
            <dd class="ml-14 flex items-baseline pb-1">
                <p class="text-2xl font-semibold text-gray-900">42</p>
                <p class="ml-2 flex items-baseline text-xs font-semibold text-gray-500">
                    Current session
                </p>
            </dd>
        </div>

        <!-- Card 4: Low Stock -->
        <div class="relative overflow-hidden rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5 transition-all hover:shadow-md">
            <dt>
                <div class="absolute rounded-md bg-red-50 px-2 py-2">
                    <x-heroicon-o-exclamation-triangle class="h-6 w-6 text-red-600" />
                </div>
                <p class="ml-14 truncate text-sm font-medium text-gray-500">Low Stock Items</p>
            </dt>
            <dd class="ml-14 flex items-baseline pb-1">
                <p class="text-2xl font-semibold text-gray-900">8</p>
                <p class="ml-2 flex items-baseline text-xs font-semibold text-red-600">
                    Action needed
                </p>
            </dd>
        </div>
    </div>

    <!-- Main Content: Activity & Charts (Placeholder) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Recent Activity Table (Span 2) -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-900/5 overflow-hidden">
                <div class="border-b border-gray-100 px-6 py-4 flex items-center justify-between">
                    <h3 class="text-base font-semibold leading-6 text-gray-900">Recent Activity</h3>
                    <a href="#" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">View all</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product/Action</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    MacBook Pro M2
                                    <span class="block text-xs font-normal text-gray-500">Stock updated</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Admin</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2 mins ago</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                                    <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Success</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    New Category: Electronics
                                    <span class="block text-xs font-normal text-gray-500">Created</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Manager</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">1 hour ago</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                                    <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">Info</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Order #2309
                                    <span class="block text-xs font-normal text-gray-500">Processed</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">System</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">3 hours ago</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                                    <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Completed</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Quick Actions / Mini Widget (Span 1) -->
        <div class="lg:col-span-1 space-y-8">
            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-900/5 p-6">
                 <h3 class="text-base font-semibold leading-6 text-gray-900 mb-4">Quick Actions</h3>
                 <div class="space-y-3">
                     <x-button class="w-full gap-x-2">
                        <x-heroicon-m-plus class="-ml-0.5 h-5 w-5" aria-hidden="true" />
                        Add New Product
                     </x-button>
                     <x-button variant="secondary" class="w-full gap-x-2">
                        <x-heroicon-m-document-arrow-down class="-ml-0.5 h-5 w-5 text-gray-400" aria-hidden="true" />
                        Export Report
                     </x-button>
                 </div>
            </div>

            <!-- Server Status (Example) -->
             <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-900/5 p-6">
                 <h3 class="text-base font-semibold leading-6 text-gray-900 mb-4">System Status</h3>
                 <div class="flex items-center justify-between">
                     <span class="text-sm text-gray-500">Database</span>
                     <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Operational</span>
                 </div>
                 <div class="mt-4 flex items-center justify-between">
                     <span class="text-sm text-gray-500">API Gateway</span>
                     <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Operational</span>
                 </div>
            </div>
        </div>

    </div>

</x-app-layout>
