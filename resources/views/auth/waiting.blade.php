@extends('layouts.app')

@section('title', 'Account Pending Approval')

@section('orbs')
    <div class="orb orb-purple"></div>
    <div class="orb orb-teal"></div>
    <div class="orb orb-amber" style="opacity:0.3;"></div>
@endsection

@push('styles')
    <style>
        .waiting-container {
            min-height: calc(100vh - 60px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .waiting-card {
            max-width: 500px;
            width: 100%;
            padding: 3rem 2.5rem;
            text-align: center;
            animation: float-up 0.7s cubic-bezier(.22,1,.36,1) forwards;
        }
        
        .waiting-icon {
            font-size: 5rem;
            margin-bottom: 1.5rem;
            animation: pulse-glow 2s ease-in-out infinite;
        }
        
        .waiting-title {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            color: #f0e6ee;
            margin-bottom: 1rem;
        }
        
        .waiting-message {
            color: rgba(169, 180, 194, 0.7);
            line-height: 1.7;
            margin-bottom: 2rem;
        }
        
        .waiting-status {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(244, 162, 97, 0.12);
            border: 1px solid rgba(244, 162, 97, 0.25);
            border-radius: 999px;
            padding: 0.5rem 1.2rem;
            font-size: 0.85rem;
            color: #f4a261;
            margin-bottom: 2rem;
        }
        
        .waiting-steps {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: left;
        }
        
        .step-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .step-item:last-child {
            border-bottom: none;
        }
        
        .step-number {
            width: 32px;
            height: 32px;
            background: rgba(212, 165, 181, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: var(--bloom);
        }
        
        .step-text {
            flex: 1;
            font-size: 0.9rem;
            color: rgba(240, 230, 238, 0.7);
        }
        
        .logout-btn {
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
        
        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #f0e6ee;
            border-color: rgba(212, 165, 181, 0.25);
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
<div class="waiting-container">
    <div class="glass-card waiting-card">
        
        <div class="waiting-icon">⏳</div>
        
        <h1 class="waiting-title">Account Pending Approval</h1>
        
        <div class="waiting-status">
            <span>🟡</span> Status: Pending Review
        </div>
        
        <p class="waiting-message">
            Thank you for creating an account with <strong>MoodTrace</strong>! 
            Your account is currently awaiting approval from an administrator.
        </p>
        
        <div class="waiting-steps">
            <div class="step-item">
                <div class="step-number">1</div>
                <div class="step-text">Admin reviews your registration</div>
            </div>
            <div class="step-item">
                <div class="step-number">2</div>
                <div class="step-text">You receive a notification email when approved</div>
            </div>
            <div class="step-item">
                <div class="step-number">3</div>
                <div class="step-text">Log in and start tracking your mood!</div>
            </div>
        </div>
        
        
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">
                ← Sign Out
            </button>
        </form>
        
    </div>
</div>
@endsection