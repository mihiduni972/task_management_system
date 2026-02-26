@extends('frontend.layouts.app')

@section('content')
<div style="max-width: 700px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; position: relative;">
    
    {{-- WHITE TOAST MESSAGE - BOTTOM RIGHT --}}
    @if(session('toast'))
        <div id="status-toast" style="
            position: fixed; 
            bottom: 30px; 
            right: 30px; 
            background: #ffffff; 
            color: black; 
            padding: 15px 25px; 
            border-radius: 10px; 
            border-left: 5px solid #28a745; 
            z-index: 9999; 
            display: flex; 
            align-items: center; 
            gap: 12px;">
            
            <div style="background: #e9f7ef; color: black; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">✓</div>
            <div>
                <div style="font-weight: bold; color: #28a745; font-size: 14px;">Success</div>
                <div style="font-size: 13px; color: #666;">{{ session('toast') }}</div>
            </div>
        </div>

        <script>
            setTimeout(() => {
                const toast = document.getElementById('status-toast');
                if(toast) {
                    toast.style.transition = '0.5s';
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateX(20px)';
                    setTimeout(() => toast.remove(), 500);
                }
            }, 4000);
        </script>
    @endif

    <h2 style="margin-bottom: 25px; color: #333; border-bottom: 2px solid #f4f4f4; padding-bottom: 10px; display: flex; align-items: center; gap: 10px;">
        <span style="background: #800000; width: 10px; height: 30px; border-radius: 4px;"></span>
        My Profile
    </h2>

    {{-- USER DETAILS FORM --}}
    <form action="{{ route('profile.update') }}" method="POST" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; align-items: end;">
        @csrf
        <div style="display: flex; flex-direction: column; gap: 8px;">
            <label style="font-weight: 600; color: #555;">Full Name</label>
            <input type="text" name="name" value="{{ auth()->user()->name }}" style="padding: 12px; border: 1px solid #ddd; border-radius: 8px; outline: none; transition: border 0.3s;" onfocus="this.style.border='1px solid #800000'" onblur="this.style.border='1px solid #ddd'">
        </div>

        <div style="display: flex; flex-direction: column; gap: 8px;">
            <label style="font-weight: 600; color: #555;">Username</label>
            <input type="text" name="username" value="{{ auth()->user()->username }}" style="padding: 12px; border: 1px solid #ddd; border-radius: 8px; outline: none; background-color: #f9f9f9; color: #777;" readonly title="Username cannot be changed">
        </div>

        <div style="grid-column: span 2; margin-top: 10px;">
            <button type="submit" style="background-color: #800000; color: white; border: none; padding: 12px 30px; border-radius: 8px; cursor: pointer; font-weight: bold; transition: 0.3s; box-shadow: 0 4px 6px rgba(128,0,0,0.2);" onmouseover="this.style.backgroundColor='#a00000'" onmouseout="this.style.backgroundColor='#800000'">
                Update Profile
            </button>
        </div>
    </form>

    <hr style="margin: 40px 0; border: 0; border-top: 1px solid #eee;">

    {{-- CHANGE PASSWORD FORM --}}
    <h3 style="margin-bottom: 20px; color: #333; font-size: 1.2rem; display: flex; align-items: center; gap: 10px;">
        <span style="background: #444; width: 8px; height: 20px; border-radius: 4px;"></span>
        Security Settings
    </h3>
    
    <form action="{{ route('profile.password') }}" method="POST" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; align-items: end;">
        @csrf
        <div style="display: flex; flex-direction: column; gap: 8px;">
            <label style="font-weight: 600; color: #555;">New Password</label>
            <input type="password" name="password" placeholder="••••••••" style="padding: 12px; border: 1px solid #ddd; border-radius: 8px; outline: none; transition: border 0.3s;" onfocus="this.style.border='1px solid #444'" onblur="this.style.border='1px solid #ddd'">
        </div>

        <div style="display: flex; flex-direction: column; gap: 8px;">
            <label style="font-weight: 600; color: #555;">Confirm Password</label>
            <input type="password" name="password_confirmation" placeholder="••••••••" style="padding: 12px; border: 1px solid #ddd; border-radius: 8px; outline: none; transition: border 0.3s;" onfocus="this.style.border='1px solid #444'" onblur="this.style.border='1px solid #ddd'">
        </div>

        <div style="grid-column: span 2; margin-top: 10px;">
            <button type="submit" style="background-color: #222; color: white; border: none; padding: 12px 30px; border-radius: 8px; cursor: pointer; font-weight: bold; transition: 0.3s; box-shadow: 0 4px 6px rgba(0,0,0,0.1);" onmouseover="this.style.backgroundColor='#222'" onmouseout="this.style.backgroundColor='#444'">
                Change Password
            </button>
        </div>
    </form>
</div>

<style>
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
</style>
@endsection