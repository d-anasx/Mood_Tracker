@extends('layouts.app')

@section('title', 'Send Notification')

@section('orbs')
    <div class="orb orb-purple"></div>
    <div class="orb orb-teal"></div>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    
    <a href="{{ route('admin.users.index') }}" class="text-bloom hover:opacity-80 mb-4 inline-block">← Back to Users</a>

    <div class="glass-card p-8">
        <div class="text-center mb-6">
            <div class="text-4xl mb-2">📨</div>
            <h1 class="text-2xl font-display text-white">Send Notification</h1>
            <p class="text-mist mt-1">Send announcements or personal messages to users</p>
        </div>

        <form method="POST" action="{{ route('admin.notifications.send') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm text-mist mb-2">Send to</label>
                <div class="flex gap-4 mb-3">
                    <label class="flex items-center gap-2">
                        <input type="radio" name="send_to_all" value="0" id="singleUserRadio" checked onchange="toggleUserSelect()">
                        <span>Single User</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="radio" name="send_to_all" value="1" id="allUsersRadio" onchange="toggleUserSelect()">
                        <span>All Active Users</span>
                    </label>
                </div>

                <div id="userSelectDiv">
                    <select name="user_id" class="w-full p-3 rounded-lg bg-white/5 border border-white/10 text-white">
                        <option value="">Select a user...</option>
                        @foreach($users = \App\Models\User::where('status', 'active')->orderBy('name')->get() as $user)
                            <option class="bg-[#211021be]" value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm text-mist mb-2">Title</label>
                <input type="text" name="title" class="w-full p-3 rounded-lg bg-white/5 border border-white/10 text-white" placeholder="e.g., Welcome to MoodTrace!" required>
            </div>

            <div class="mb-6">
                <label class="block text-sm text-mist mb-2">Message</label>
                <textarea name="message" rows="5" class="w-full p-3 rounded-lg bg-white/5 border border-white/10 text-white" placeholder="Write your message here..." required></textarea>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('admin.users.index') }}" class="btn-secondary flex-1 text-center">Cancel</a>
                <button type="submit" class="btn-primary flex-1">Send Notification</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleUserSelect() {
        const singleUser = document.getElementById('singleUserRadio');
        const userSelect = document.getElementById('userSelectDiv');
        
        if (singleUser.checked) {
            userSelect.style.display = 'block';
        } else {
            userSelect.style.display = 'none';
        }
    }
    
    // Initialize on page load
    toggleUserSelect();
</script>
@endsection