<nav class="bg-white shadow-sm border-b">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center space-x-8">
                <a href="{{ route('dashboard') }}" class="text-xl font-bold text-gray-900">
                    TODO Application
                </a>
                
                @auth
                    <div class="flex space-x-4">
                        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                            Dashboard
                        </a>
                        <a href="{{ route('todos.index') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                            Todos
                        </a>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('invitations.index') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                                Invitations
                            </a>
                            <a href="{{ route('invitations.create') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                                Invite Employee
                            </a>
                        @endif
                    </div>
                @endauth
            </div>

            @auth
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-700">
                        {{ auth()->user()->name }} 
                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
                            {{ auth()->user()->getRoleName() }}
                        </span>
                    </span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Logout
                        </button>
                    </form>
                </div>
            @else
                <a href="{{ route('login') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium">
                    Login
                </a>
            @endauth
        </div>
    </div>
</nav>

