@extends('layouts.app')

@section('title', 'Manage Users')

@section('orbs')
    <div class="orb orb-purple"></div>
    <div class="orb orb-teal"></div>
    <div class="orb orb-rose" style="opacity:0.3;"></div>
@endsection

@push('styles')
    <style>
        .admin-container {
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
            padding: 2rem 2rem 4rem;
        }

        .admin-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .admin-title {
            font-size: 2rem;
            color: #f0e6ee;
            margin-bottom: 0.3rem;
        }

        .filter-bar {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }

        .filter-btn {
            font-size: 0.78rem;
            color: rgba(169, 180, 194, 0.55);
            text-decoration: none;
            padding: 6px 18px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .filter-btn:hover {
            color: #f0e6ee;
            background: rgba(255, 255, 255, 0.06);
        }

        .filter-btn.active {
            background: linear-gradient(135deg, #c0587a, #7c3fa0);
            color: #fff;
            border-color: transparent;
        }

        .search-wrapper {
            display: flex;
            gap: 0.5rem;
            margin-left: auto;
            position: relative;
        }

        .search-input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.09);
            border-radius: 999px;
            padding: 0.6rem 1.2rem;
            color: #f0e6ee;
            font-size: 0.85rem;
            min-width: 260px;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: rgba(212, 165, 181, 0.4);
            background: rgba(255, 255, 255, 0.08);
        }

        .search-input::placeholder {
            color: rgba(169, 180, 194, 0.4);
        }

        .search-loading {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            border: 2px solid rgba(212, 165, 181, 0.2);
            border-top-color: var(--bloom);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: translateY(-50%) rotate(360deg);
            }
        }

        .admin-users-table {
            width: 100%;
            border-collapse: collapse;
        }

        .admin-users-table th {
            text-align: left;
            padding: 1.2rem 1rem;
            color: var(--bloom);
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .admin-users-table td {
            padding: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            color: rgba(240, 230, 238, 0.8);
            font-size: 0.85rem;
        }

        .admin-users-table tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }

        .user-cell {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-avatar-sm {
            width: 36px;
            height: 36px;
            background: rgba(192, 88, 122, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 500;
        }

        .status-active {
            background: rgba(78, 205, 196, 0.12);
            color: #4ecdc4;
            border: 1px solid rgba(78, 205, 196, 0.25);
        }

        .status-pending {
            background: rgba(244, 162, 97, 0.12);
            color: #f4a261;
            border: 1px solid rgba(244, 162, 97, 0.25);
        }

        .status-blocked {
            background: rgba(224, 82, 99, 0.12);
            color: #e05263;
            border: 1px solid rgba(224, 82, 99, 0.25);
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .action-btn {
            padding: 0.35rem 0.8rem;
            border-radius: 8px;
            font-size: 0.7rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
            display: inline-block;
        }

        .action-view {
            background: rgba(167, 139, 250, 0.12);
            color: #a78bfa;
            border: 1px solid rgba(167, 139, 250, 0.2);
        }

        .action-view:hover {
            background: rgba(167, 139, 250, 0.2);
            transform: translateY(-1px);
        }

        .action-approve {
            background: rgba(78, 205, 196, 0.12);
            color: #4ecdc4;
            border: 1px solid rgba(78, 205, 196, 0.2);
        }

        .action-approve:hover {
            background: rgba(78, 205, 196, 0.2);
            transform: translateY(-1px);
        }

        .action-block {
            background: rgba(224, 82, 99, 0.12);
            color: #e05263;
            border: 1px solid rgba(224, 82, 99, 0.2);
        }

        .action-block:hover {
            background: rgba(224, 82, 99, 0.2);
            transform: translateY(-1px);
        }

        .action-unblock {
            background: rgba(78, 205, 196, 0.12);
            color: #4ecdc4;
            border: 1px solid rgba(78, 205, 196, 0.2);
        }

        .action-unblock:hover {
            background: rgba(78, 205, 196, 0.2);
            transform: translateY(-1px);
        }

        .action-delete {
            background: rgba(224, 82, 99, 0.08);
            color: #e05263;
            border: 1px solid rgba(224, 82, 99, 0.15);
        }

        .action-delete:hover {
            background: rgba(224, 82, 99, 0.15);
            transform: translateY(-1px);
        }

        .entries-count {
            font-weight: 600;
            color: var(--bloom);
        }

        .empty-state {
            text-align: center;
            padding: 4rem;
            color: rgba(169, 180, 194, 0.45);
        }

        .empty-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }

        .pagination-container {
            margin-top: 2rem;
            display: flex;
            justify-content: center;
        }

        .pagination {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .pagination a,
        .pagination span {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 0.75rem;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: rgba(169, 180, 194, 0.6);
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.2s;
            cursor: pointer;
        }

        .pagination a:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #f0e6ee;
        }

        .pagination .active span {
            background: linear-gradient(135deg, #c0587a, #7c3fa0);
            color: #fff;
            border-color: transparent;
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .modal-content {
            max-width: 450px;
            width: 90%;
            padding: 2rem;
        }

        .modal-title {
            font-size: 1.5rem;
            color: #f0e6ee;
            margin-bottom: 0.5rem;
        }

        .modal-text {
            color: rgba(169, 180, 194, 0.6);
            margin-bottom: 1.5rem;
        }

        .modal-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }

        .btn-modal-cancel {
            padding: 0.75rem 1.5rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: rgba(169, 180, 194, 0.6);
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-modal-cancel:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #f0e6ee;
        }

        .btn-modal-confirm {
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #e05263, #c0587a);
            border: none;
            border-radius: 10px;
            color: #fff;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-modal-confirm:hover {
            transform: translateY(-1px);
        }

        textarea.modal-reason {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.09);
            border-radius: 12px;
            padding: 0.75rem;
            color: #f0e6ee;
            margin-bottom: 1rem;
            resize: vertical;
        }

        textarea.modal-reason:focus {
            outline: none;
            border-color: rgba(212, 165, 181, 0.4);
        }

        @media (max-width: 768px) {
            .admin-container {
                padding: 1rem;
            }

            .admin-users-table th,
            .admin-users-table td {
                padding: 0.75rem 0.5rem;
            }

            .action-buttons {
                flex-direction: column;
                gap: 0.3rem;
            }

            .search-wrapper {
                margin-left: 0;
                width: 100%;
            }

            .search-input {
                flex: 1;
                min-width: auto;
            }

            .filter-bar {
                flex-direction: column;
            }
        }
    </style>
@endpush

@section('content')
    <div class="admin-container">

        <div class="admin-header">
            <div>
                <p class="eyebrow">Admin Panel</p>
                <h1 class="admin-title">Manage Users</h1>
                <p class="muted" style="font-size:0.85rem;">Approve, block, or manage user accounts</p>
            </div>

            <a href="{{ route('admin.notifications.create') }}" class="btn-primary"
                style="width: auto; padding: 0.85rem 1.8rem;">
                📨 Send Notification
            </a>
        </div>

        {{-- Filters and Search --}}
        <div class="filter-bar">
            <button data-status="" class="filter-btn {{ !request('status') ? 'active' : '' }}">All</button>
            <button data-status="pending"
                class="filter-btn {{ request('status') === 'pending' ? 'active' : '' }}">Pending</button>
            <button data-status="active"
                class="filter-btn {{ request('status') === 'active' ? 'active' : '' }}">Active</button>
            <button data-status="blocked"
                class="filter-btn {{ request('status') === 'blocked' ? 'active' : '' }}">Blocked</button>

            <div class="search-wrapper">
                <input type="text" id="searchInput" placeholder="Search by name or email..." class="search-input"
                    value="{{ request('search') }}">
                <div id="searchLoading" class="search-loading" style="display: none;"></div>
            </div>
        </div>

        {{-- Users Table --}}
        <div class="glass-card" style="overflow-x: auto; padding: 0;">
            <table class="admin-users-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Entries</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                    @include('admin.users.partials.users-table', ['users' => $users])
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div id="paginationContainer" class="pagination-container">
            {{ $users->links() }}
        </div>
    </div>

    {{-- Block Modal --}}
    <div id="blockModal" class="modal-overlay" style="display: none;">
        <div class="glass-card modal-content">
            <h3 class="modal-title">Block User</h3>
            <p class="modal-text" id="blockUserName"></p>
            <form method="POST" id="blockForm">
                @csrf
                <textarea name="reason" class="modal-reason" rows="3" placeholder="Reason for blocking..."
                    required></textarea>
                <div class="modal-actions">
                    <button type="button" onclick="closeBlockModal()" class="btn-modal-cancel">Cancel</button>
                    <button type="submit" class="btn-modal-confirm">Block User</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div id="deleteModal" class="modal-overlay" style="display: none;">
        <div class="glass-card modal-content">
            <h3 class="modal-title">Delete User</h3>
            <p class="modal-text" id="deleteUserName"></p>
            <p class="modal-text" style="font-size:0.8rem; color:#e05263;">⚠️ This action cannot be undone. All user data
                including mood entries will be permanently deleted.</p>
            <form method="POST" id="deleteForm">
                @csrf
                @method('DELETE')
                <div class="modal-actions">
                    <button type="button" onclick="closeDeleteModal()" class="btn-modal-cancel">Cancel</button>
                    <button type="submit" class="btn-modal-confirm"
                        style="background: linear-gradient(135deg, #e05263, #b91c1c);">Delete User</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        // Real-time search and filter ONLY - using POST data
        let searchTimeout;
        let currentStatus = '{{ request('status') }}';
        let currentSearch = '{{ request('search') }}';
        let currentPage = 1;

        const searchInput = document.getElementById('searchInput');
        const searchLoading = document.getElementById('searchLoading');
        const usersTableBody = document.getElementById('usersTableBody');
        const paginationContainer = document.getElementById('paginationContainer');

        // Function to fetch users using POST data
        async function fetchUsers() {
            try {
                searchLoading.style.display = 'block';

                const response = await axios.post('{{ route("admin.users.search") }}', {
                    status: currentStatus,
                    search: currentSearch,
                    page: currentPage
                }, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    }
                });

                if (response.data.success) {
                    // Update table body
                    usersTableBody.innerHTML = response.data.html;

                    // Update pagination
                    paginationContainer.innerHTML = response.data.pagination;
                }

            } catch (error) {
                console.error('Error fetching users:', error);
            } finally {
                searchLoading.style.display = 'none';
            }
        }

        // Function to attach modal handlers to dynamic buttons
        function attachModalHandlers() {
            // Block buttons
            document.querySelectorAll('.action-block').forEach(btn => {
                if (!btn.hasAttribute('data-listener')) {
                    btn.setAttribute('data-listener', 'true');
                    const userId = btn.getAttribute('data-user-id');
                    const userName = btn.getAttribute('data-user-name');
                    btn.onclick = () => showBlockModal(userId, userName);
                }
            });

            // Delete buttons
            document.querySelectorAll('.action-delete').forEach(btn => {
                if (!btn.hasAttribute('data-listener')) {
                    btn.setAttribute('data-listener', 'true');
                    const userId = btn.getAttribute('data-user-id');
                    const userName = btn.getAttribute('data-user-name');
                    btn.onclick = () => showDeleteModal(userId, userName);
                }
            });
        }

        // Search input handler (debounced)
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                currentSearch = e.target.value;
                currentPage = 1;

                searchTimeout = setTimeout(() => {
                    fetchUsers();
                }, 500);
            });
        }

        // Filter button handlers
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const status = btn.dataset.status || '';

                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                currentStatus = status;
                currentPage = 1;
                fetchUsers();
            });
        });

        

        // Modal functions
        function showBlockModal(userId, userName) {
            document.getElementById('blockUserName').textContent = `Are you sure you want to block ${userName}?`;
            document.getElementById('blockForm').action = `/admin/users/${userId}/block`;
            document.getElementById('blockModal').style.display = 'flex';
        }

        function closeBlockModal() {
            document.getElementById('blockModal').style.display = 'none';
        }

        function showDeleteModal(userId, userName) {
            document.getElementById('deleteUserName').textContent = `Delete ${userName}?`;
            document.getElementById('deleteForm').action = `/admin/users/${userId}`;
            document.getElementById('deleteModal').style.display = 'flex';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        // Close modals on outside click
        document.getElementById('blockModal')?.addEventListener('click', function (e) {
            if (e.target === this) closeBlockModal();
        });

        document.getElementById('deleteModal')?.addEventListener('click', function (e) {
            if (e.target === this) closeDeleteModal();
        });

        // Make modal functions global
        window.showBlockModal = showBlockModal;
        window.closeBlockModal = closeBlockModal;
        window.showDeleteModal = showDeleteModal;
        window.closeDeleteModal = closeDeleteModal;
    </script>
@endpush