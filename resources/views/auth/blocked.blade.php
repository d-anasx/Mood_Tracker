@extends('layouts.app')

@section('title', 'Account Blocked')

@section('orbs')
    <div class="orb orb-purple"></div>
    <div class="orb orb-rose"></div>
    <div class="orb orb-amber" style="opacity:0.2;"></div>
@endsection

@push('styles')
    <style>
        .blocked-container {
            min-height: calc(100vh - 60px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .blocked-card {
            max-width: 500px;
            width: 100%;
            padding: 3rem 2.5rem;
            text-align: center;
            animation: float-up 0.7s cubic-bezier(.22,1,.36,1) forwards;
        }
        
        .blocked-icon {
            font-size: 5rem;
            margin-bottom: 1.5rem;
        }
        
        .blocked-title {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            color: #e05263;
            margin-bottom: 1rem;
        }
        
        .blocked-message {
            color: rgba(169, 180, 194, 0.7);
            line-height: 1.7;
            margin-bottom: 1.5rem;
        }
        
        .blocked-status {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(224, 82, 99, 0.12);
            border: 1px solid rgba(224, 82, 99, 0.25);
            border-radius: 999px;
            padding: 0.5rem 1.2rem;
            font-size: 0.85rem;
            color: #e05263;
            margin-bottom: 2rem;
        }
        
        .info-box {
            background: rgba(224, 82, 99, 0.05);
            border-left: 3px solid #e05263;
            border-radius: 12px;
            padding: 1.2rem;
            margin-bottom: 2rem;
            text-align: left;
        }
        
        .info-title {
            font-weight: 600;
            color: #e05263;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        
        .info-text {
            font-size: 0.85rem;
            color: rgba(169, 180, 194, 0.6);
            line-height: 1.6;
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 1rem;
        }
        
        .contact-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 0.85rem 1.5rem;
            color: rgba(169, 180, 194, 0.7);
            text-decoration: none;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .contact-btn:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #f0e6ee;
            border-color: rgba(212, 165, 181, 0.25);
        }
        
        .logout-btn-block {
            background: rgba(224, 82, 99, 0.1);
            border: 1px solid rgba(224, 82, 99, 0.3);
            border-radius: 12px;
            padding: 0.85rem 1.5rem;
            color: #e05263;
            text-decoration: none;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
        }
        
        .logout-btn-block:hover {
            background: rgba(224, 82, 99, 0.15);
            transform: translateY(-1px);
        }
        
        .contact-link {
            color: var(--bloom);
            text-decoration: none;
            font-weight: 500;
        }
        
        .contact-link:hover {
            text-decoration: underline;
        }
    </style>
@endpush

@section('content')
<div class="blocked-container">
    <div class="glass-card blocked-card">
        
        <div class="blocked-icon">🚫</div>
        
        <h1 class="blocked-title">Account Blocked</h1>
        
        <div class="blocked-status">
            <span>🔴</span> Status: Blocked
        </div>
        
        <p class="blocked-message">
            Your account has been blocked by an administrator. 
            You cannot access MoodTrace at this time.
        </p>
        
        <div class="action-buttons">
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="logout-btn-block">
                    ← Sign Out
                </button>
            </form>
        </div>
        
    </div>
</div>
@endsection