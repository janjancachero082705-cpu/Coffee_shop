@extends('layouts.admin')

@section('content')
<div style="max-width: 720px; margin: 2rem auto; padding: 0 1rem;">
    <div style="background: #fff; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); padding: 2rem;">
        <h2 style="font-size: 1.5rem; font-weight: 700; color: #1a1a1a; margin: 0 0 1.5rem 0;">
            My Profile
        </h2>

        @if(session('success'))
            <div style="margin-bottom: 1rem; padding: 0.75rem 1rem; background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; border-radius: 8px;">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div style="margin-bottom: 1rem; padding: 0.75rem 1rem; background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; border-radius: 8px;">
                @foreach($errors->all() as $e)
                    <div>• {{ $e }}</div>
                @endforeach
            </div>
        @endif

        <div style="display: flex; align-items: center; gap: 1.5rem; padding-bottom: 1.5rem; margin-bottom: 1.5rem; border-bottom: 1px solid #f0f0f0;">
            @if(auth()->user()->profile_image)
                <img id="avatarPreview"
                     src="/storage/{{ auth()->user()->profile_image }}?v={{ now()->timestamp }}"
                     alt="Avatar"
                     style="width: 96px; height: 96px; border-radius: 50%; object-fit: cover; border: 4px solid #10b981;">
            @else
                <div id="avatarFallback"
                     style="width: 96px; height: 96px; border-radius: 50%; background: #10b981; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 700;">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <img id="avatarPreview" style="display: none; width: 96px; height: 96px; border-radius: 50%; object-fit: cover; border: 4px solid #10b981;" alt="Preview">
            @endif

            <div>
                <h3 style="font-size: 1.25rem; font-weight: 600; color: #1a1a1a; margin: 0 0 0.25rem 0;">
                    {{ auth()->user()->name }}
                </h3>
                <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">
                    {{ auth()->user()->email }}
                </p>
                <p style="font-size: 0.75rem; color: #9ca3af; margin: 0.25rem 0 0 0;">
                    Role: {{ auth()->user()->role ?? 'admin' }}
                </p>
            </div>
        </div>

        <form action="{{ route('admin.profile.avatar') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <label style="display: block; font-size: 0.875rem; font-weight: 700; color: #374151; margin-bottom: 0.5rem;">
                Change Profile Picture
            </label>

            <input type="file" name="profile_image" accept="image/*" required
                   onchange="document.getElementById('avatarPreview').src=window.URL.createObjectURL(this.files[0]);document.getElementById('avatarPreview').style.display='block';"
                   style="display: block; width: 100%; padding: 0.75rem; border: 2px dashed #10b981; border-radius: 12px; background: #f0fdf4; font-size: 0.875rem; color: #374151; margin-bottom: 0.75rem; cursor: pointer;">

            <div style="display: flex; gap: 0.5rem;">
                <button type="submit"
                        style="padding: 0.625rem 1.25rem; background: #10b981; color: #fff; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    Upload
                </button>
                @if(auth()->user()->profile_image)
                    <button type="button" onclick="if(confirm('Remove profile picture?')) document.getElementById('removeForm').submit();"
                            style="padding: 0.625rem 1.25rem; background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; border-radius: 8px; font-weight: 600; cursor: pointer;">
                        Remove
                    </button>
                @endif
            </div>
        </form>

        @if(auth()->user()->profile_image)
            <form id="removeForm" action="{{ route('admin.profile.avatar.remove') }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </div>
</div>
@endsection