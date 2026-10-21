@extends('layouts.admin')

@section('title', 'E-Wallet Settings')
@section('subtitle', 'Manage your digital payment methods')

@section('content')

<div style="max-width: 1200px; margin: 0 auto;">

    <!-- Toast Triggers -->
    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', () => { showToast('Success', '{{ session("success") }}', 'success'); });
    </script>
    @endif
    @if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', () => { showToast('Error', '{{ $errors->first() }}', 'error'); });
    </script>
    @endif

    @php
        $existingCodes = isset($wallets) ? $wallets->pluck('code')->toArray() : [];
    @endphp

    <div style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 24px; align-items: start;">

        <!-- LEFT: ADD E-WALLET FORM -->
        <div style="background: #16161d; border: 1px solid rgba(255,255,255,0.06); border-radius: 20px; padding: 24px; box-shadow: 0 10px 30px -10px rgba(0,0,0,0.5);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px dashed rgba(255,255,255,0.1);">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(232, 163, 61, 0.1); color: #E8A33D; display: flex; align-items: center; justify-content: center;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 18px; font-weight: 800; color: #F5F3F0;">Add New Wallet</h3>
                    <p style="margin: 2px 0 0; font-size: 12px; color: #7A7A85;">Setup a new payment method</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.e-wallets.store') }}" enctype="multipart/form-data">
                @csrf
                
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 11px; font-weight: 800; color: #A1A1AA; text-transform: uppercase; margin-bottom: 8px;">Wallet Name</label>
                    <input type="text" name="name" placeholder="e.g. GCash, Maya" required style="width: 100%; padding: 12px 16px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; color: #FFF; font-size: 14px; outline: none; transition: 0.2s;" onfocus="this.style.borderColor='#E8A33D'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 11px; font-weight: 800; color: #A1A1AA; text-transform: uppercase; margin-bottom: 8px;">System Code (Logo)</label>
                    <select name="code" required style="width: 100%; padding: 12px 16px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; color: #FFF; font-size: 14px; outline: none; cursor: pointer;">
                        <option value="gcash" {{ in_array('gcash', $existingCodes) ? 'disabled' : '' }} style="color: {{ in_array('gcash', $existingCodes) ? '#666' : '#000' }};">
                            GCash {{ in_array('gcash', $existingCodes) ? '(Already Added)' : '(Pre-loaded)' }}
                        </option>
                        <option value="maya" {{ in_array('maya', $existingCodes) ? 'disabled' : '' }} style="color: {{ in_array('maya', $existingCodes) ? '#666' : '#000' }};">
                            Maya {{ in_array('maya', $existingCodes) ? '(Already Added)' : '(Pre-loaded)' }}
                        </option>
                        <option value="grabpay" {{ in_array('grabpay', $existingCodes) ? 'disabled' : '' }} style="color: {{ in_array('grabpay', $existingCodes) ? '#666' : '#000' }};">
                            GrabPay {{ in_array('grabpay', $existingCodes) ? '(Already Added)' : '(Pre-loaded)' }}
                        </option>
                        <option value="shopeepay" {{ in_array('shopeepay', $existingCodes) ? 'disabled' : '' }} style="color: {{ in_array('shopeepay', $existingCodes) ? '#666' : '#000' }};">
                            ShopeePay {{ in_array('shopeepay', $existingCodes) ? '(Already Added)' : '(Pre-loaded)' }}
                        </option>
                        <option value="other" style="color: #000;">Other (Generic Icon)</option>
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; font-size: 11px; font-weight: 800; color: #A1A1AA; text-transform: uppercase; margin-bottom: 8px;">Account Name</label>
                        <input type="text" name="account_name" placeholder="e.g. Juan Dela Cruz" style="width: 100%; padding: 12px 16px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; color: #FFF; font-size: 14px; outline: none; transition: 0.2s;" onfocus="this.style.borderColor='#E8A33D'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
                    </div>
                    <div>
                        <label style="display: block; font-size: 11px; font-weight: 800; color: #A1A1AA; text-transform: uppercase; margin-bottom: 8px;">Account Number</label>
                        <input type="text" name="account_number" placeholder="e.g. 0917 123 4567" style="width: 100%; padding: 12px 16px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; color: #FFF; font-size: 14px; outline: none; transition: 0.2s;" onfocus="this.style.borderColor='#E8A33D'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
                    </div>
                </div>

                <!-- UPLOAD QR CODE -->
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 11px; font-weight: 800; color: #A1A1AA; text-transform: uppercase; margin-bottom: 8px;">QR Code Image</label>
                    <div class="upload-dropzone" style="position: relative; border: 1.5px dashed rgba(255,255,255,0.2); background: rgba(0,0,0,0.2); border-radius: 12px; padding: 20px; text-align: center; transition: 0.2s; overflow: hidden; cursor: pointer;" onmouseover="this.style.borderColor='#E8A33D'" onmouseout="this.style.borderColor='rgba(255,255,255,0.2)'">
                        <input type="file" name="qr_code" accept="image/*" onchange="previewImage(this, 'add_qr_preview')" style="position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%;">
                        <div id="add_qr_placeholder" style="display: flex; flex-direction: column; align-items: center; gap: 8px; color: #A1A1AA; pointer-events: none;">
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            <span style="font-size: 12px; font-weight: 600;">Click to upload QR Code</span>
                        </div>
                        <img id="add_qr_preview" style="display: none; width: 100%; max-height: 150px; object-fit: contain; border-radius: 8px;">
                    </div>
                </div>

                <!-- UPLOAD LOGO -->
                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-size: 11px; font-weight: 800; color: #A1A1AA; text-transform: uppercase; margin-bottom: 8px;">Custom Logo (Optional)</label>
                    <div class="upload-dropzone" style="position: relative; border: 1.5px dashed rgba(255,255,255,0.2); background: rgba(0,0,0,0.2); border-radius: 12px; padding: 20px; text-align: center; transition: 0.2s; overflow: hidden; cursor: pointer;" onmouseover="this.style.borderColor='#E8A33D'" onmouseout="this.style.borderColor='rgba(255,255,255,0.2)'">
                        <input type="file" name="logo_path" accept="image/*" onchange="previewImage(this, 'add_logo_preview')" style="position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%;">
                        <div id="add_logo_placeholder" style="display: flex; flex-direction: column; align-items: center; gap: 8px; color: #A1A1AA; pointer-events: none;">
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                            <span style="font-size: 12px; font-weight: 600;">Click to upload Logo</span>
                        </div>
                        <img id="add_logo_preview" style="display: none; width: 100%; max-height: 100px; object-fit: contain; border-radius: 8px;">
                    </div>
                </div>

                <button type="submit" style="width: 100%; padding: 14px; background: linear-gradient(135deg, #E8A33D, #D4901F); color: #1A1A1A; font-size: 14px; font-weight: 900; border: none; border-radius: 10px; cursor: pointer; box-shadow: 0 4px 15px rgba(232, 163, 61, 0.3); transition: 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                    Save E-Wallet
                </button>
            </form>
        </div>

        <!-- RIGHT: ACTIVE E-WALLETS LIST -->
        <div style="background: #16161d; border: 1px solid rgba(255,255,255,0.06); border-radius: 20px; padding: 24px; box-shadow: 0 10px 30px -10px rgba(0,0,0,0.5);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px dashed rgba(255,255,255,0.1);">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(59, 130, 246, 0.1); color: #60A5FA; display: flex; align-items: center; justify-content: center;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 18px; font-weight: 800; color: #F5F3F0;">Active Wallets</h3>
                    <p style="margin: 2px 0 0; font-size: 12px; color: #7A7A85;">List of available payment methods</p>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 12px;">
                @forelse($wallets as $w)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); border-radius: 14px; transition: 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.04)'" onmouseout="this.style.background='rgba(255,255,255,0.02)'">
                        
                        <div style="display: flex; align-items: center; gap: 16px;">
                            <!-- Logo Preview -->
                            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FFF; border: 1px solid rgba(255,255,255,0.1); overflow: hidden; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                @if($w->logo_path)
                                    <img src="{{ asset('storage/' . $w->logo_path) }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <span style="font-size: 11px; font-weight: 900; color: #1A1A1A;">{{ strtoupper($w->code) }}</span>
                                @endif
                            </div>
                            <div>
                                <div style="font-weight: 800; font-size: 15px; color: #F5F3F0; margin-bottom: 2px;">{{ $w->name }}</div>
                                <div style="font-size: 12px; color: #7A7A85;">{{ $w->account_number ?? 'No number' }} · {{ $w->account_name ?? 'No name' }}</div>
                            </div>
                        </div>
                        
                        <div style="display: flex; gap: 10px; align-items: center; justify-content: flex-end;">
                            <!-- TOGGLE BUTTON (WITH ARROW) -->
                            <form method="POST" action="{{ route('admin.e-wallets.toggle', $w->id) }}" style="margin: 0;">
                                @csrf 
                                <button type="submit" style="background: {{ $w->is_active ? 'rgba(34, 197, 94, 0.1)' : 'rgba(239, 68, 68, 0.1)' }}; color: {{ $w->is_active ? '#4ade80' : '#f87171' }}; border: 1px solid {{ $w->is_active ? 'rgba(34, 197, 94, 0.3)' : 'rgba(239, 68, 68, 0.3)' }}; padding: 6px 10px; border-radius: 8px; font-size: 11px; font-weight: 800; cursor: pointer; transition: 0.2s; display: flex; align-items: center; gap: 6px;">
                                    {{ $w->is_active ? 'ACTIVE' : 'INACTIVE' }}
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
                                </button>
                            </form>

                            <!-- 3-DOTS ACTION MENU -->
                            <div style="position: relative;" class="action-dropdown-container">
                                <button type="button" onclick="toggleDropdown(event, {{ $w->id }})" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #94A3B8; padding: 6px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/></svg>
                                </button>
                                
                                <!-- DROPDOWN MENU -->
                                <div id="dropdown-{{ $w->id }}" class="action-dropdown-menu" style="display: none; position: absolute; right: 0; top: 100%; margin-top: 6px; background: #1E293B; border: 1px solid #334155; border-radius: 8px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.8); z-index: 100; min-width: 130px; overflow: hidden; flex-direction: column;">
                                    <!-- EDIT -->
                                    <button type="button" onclick="closeAllDropdowns(); openEditModal({{ $w->id }}, '{{ $w->name }}', '{{ $w->code }}', '{{ $w->account_name }}', '{{ $w->account_number }}')" style="display: flex; align-items: center; gap: 10px; width: 100%; text-align: left; padding: 10px 14px; background: transparent; border: none; color: #F8FAFC; font-size: 12px; font-weight: 600; cursor: pointer; border-bottom: 1px solid #334155;" onmouseover="this.style.background='#334155'" onmouseout="this.style.background='transparent'">
                                        <svg width="14" height="14" fill="none" stroke="#60A5FA" stroke-width="2.5" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        Edit
                                    </button>
                                    
                                    <!-- DELETE -->
                                    <form method="POST" action="{{ route('admin.e-wallets.destroy', $w->id) }}" onsubmit="event.preventDefault(); closeAllDropdowns(); openDeleteModal(this);" style="margin: 0;">
                                        @csrf 
                                        @method('DELETE') 
                                        <button type="submit" style="display: flex; align-items: center; gap: 10px; width: 100%; text-align: left; padding: 10px 14px; background: transparent; border: none; color: #f87171; font-size: 12px; font-weight: 600; cursor: pointer;" onmouseover="this.style.background='#334155'" onmouseout="this.style.background='transparent'">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 40px; color: #7A7A85;">
                        <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin: 0 auto 12px; display: block; opacity: 0.5;"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                        <p style="font-size: 14px; font-weight: 600; margin: 0;">No e-wallets added yet.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

<!-- EDIT E-WALLET MODAL -->
<div id="editModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.8); backdrop-filter:blur(5px); z-index:999999; align-items:center; justify-content:center; padding: 20px;">
    <div style="background:#1E293B; padding:24px; border-radius:16px; width:100%; max-width:500px; border:1px solid #334155; box-shadow: 0 20px 40px rgba(0,0,0,0.5); max-height: 90vh; overflow-y: auto;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h3 style="color:#FFF; font-size:18px; font-weight:800; margin:0;">Edit E-Wallet</h3>
            <button type="button" onclick="document.getElementById('editModal').style.display='none'" style="background:none; border:none; color:#94A3B8; cursor:pointer;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        
        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 11px; font-weight: 800; color: #A1A1AA; text-transform: uppercase; margin-bottom: 8px;">Wallet Name</label>
                <input type="text" name="name" id="edit_name" required style="width: 100%; padding: 12px 16px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; color: #FFF; font-size: 14px; outline: none;">
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 11px; font-weight: 800; color: #A1A1AA; text-transform: uppercase; margin-bottom: 8px;">System Code</label>
                <select name="code" id="edit_code" required style="width: 100%; padding: 12px 16px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; color: #FFF; font-size: 14px; outline: none; cursor: pointer;">
                    <option value="gcash" style="color: #000;">GCash</option>
                    <option value="maya" style="color: #000;">Maya</option>
                    <option value="grabpay" style="color: #000;">GrabPay</option>
                    <option value="shopeepay" style="color: #000;">ShopeePay</option>
                    <option value="other" style="color: #000;">Other</option>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 11px; font-weight: 800; color: #A1A1AA; text-transform: uppercase; margin-bottom: 8px;">Account Name</label>
                    <input type="text" name="account_name" id="edit_account_name" style="width: 100%; padding: 12px 16px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; color: #FFF; font-size: 14px; outline: none;">
                </div>
                <div>
                    <label style="display: block; font-size: 11px; font-weight: 800; color: #A1A1AA; text-transform: uppercase; margin-bottom: 8px;">Account Number</label>
                    <input type="text" name="account_number" id="edit_account_number" style="width: 100%; padding: 12px 16px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; color: #FFF; font-size: 14px; outline: none;">
                </div>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 11px; font-weight: 800; color: #A1A1AA; text-transform: uppercase; margin-bottom: 8px;">Update QR Code</label>
                <div class="upload-dropzone" style="position: relative; border: 1.5px dashed rgba(255,255,255,0.2); background: rgba(0,0,0,0.2); border-radius: 12px; padding: 12px; text-align: center; transition: 0.2s; overflow: hidden; cursor: pointer;" onmouseover="this.style.borderColor='#E8A33D'" onmouseout="this.style.borderColor='rgba(255,255,255,0.2)'">
                    <input type="file" name="qr_code" accept="image/*" onchange="previewImage(this, 'edit_qr_preview')" style="position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%;">
                    <div id="edit_qr_placeholder" style="display: flex; flex-direction: column; align-items: center; gap: 4px; color: #A1A1AA; pointer-events: none;">
                        <span style="font-size: 11px; font-weight: 600;">Leave blank to keep current</span>
                    </div>
                    <img id="edit_qr_preview" style="display: none; width: 100%; max-height: 100px; object-fit: contain; border-radius: 8px;">
                </div>
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 11px; font-weight: 800; color: #A1A1AA; text-transform: uppercase; margin-bottom: 8px;">Update Logo</label>
                <div class="upload-dropzone" style="position: relative; border: 1.5px dashed rgba(255,255,255,0.2); background: rgba(0,0,0,0.2); border-radius: 12px; padding: 12px; text-align: center; transition: 0.2s; overflow: hidden; cursor: pointer;" onmouseover="this.style.borderColor='#E8A33D'" onmouseout="this.style.borderColor='rgba(255,255,255,0.2)'">
                    <input type="file" name="logo_path" accept="image/*" onchange="previewImage(this, 'edit_logo_preview')" style="position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%;">
                    <div id="edit_logo_placeholder" style="display: flex; flex-direction: column; align-items: center; gap: 4px; color: #A1A1AA; pointer-events: none;">
                        <span style="font-size: 11px; font-weight: 600;">Leave blank to keep current</span>
                    </div>
                    <img id="edit_logo_preview" style="display: none; width: 100%; max-height: 80px; object-fit: contain; border-radius: 8px;">
                </div>
            </div>

            <button type="submit" style="width: 100%; padding: 14px; background: linear-gradient(135deg, #E8A33D, #D4901F); color: #1A1A1A; font-size: 14px; font-weight: 900; border: none; border-radius: 10px; cursor: pointer;">
                Save Changes
            </button>
        </form>
    </div>
</div>

<!-- CUSTOM DELETE MODAL -->
<div id="deleteModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.8); backdrop-filter:blur(5px); z-index:999999; align-items:center; justify-content:center;">
    <div style="background:#1E293B; padding:24px; border-radius:16px; width:100%; max-width:400px; border:1px solid #334155; text-align:center; box-shadow: 0 20px 40px rgba(0,0,0,0.5);">
        <div style="width:48px; height:48px; border-radius:50%; background:rgba(239,68,68,0.1); color:#ef4444; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </div>
        <h3 style="color:#FFF; font-size:18px; font-weight:800; margin:0 0 8px;">Delete E-Wallet?</h3>
        <p style="color:#94A3B8; font-size:13px; margin-bottom:24px;">Are you sure you want to delete this e-wallet? This action cannot be undone.</p>
        <div style="display:flex; gap:12px;">
            <button type="button" onclick="document.getElementById('deleteModal').style.display='none'" style="flex:1; padding:12px; background:transparent; border:1px solid #475569; color:#94A3B8; border-radius:8px; font-weight:700; cursor:pointer;">Cancel</button>
            <button type="button" onclick="submitDeleteForm()" style="flex:1; padding:12px; background:#EF4444; border:none; color:#FFF; border-radius:8px; font-weight:700; cursor:pointer;">Yes, Delete</button>
        </div>
    </div>
</div>

<script>
    // --- IMAGE PREVIEW LOGIC ---
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        const placeholder = input.parentElement.querySelector('div');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                if(placeholder) placeholder.style.display = 'none';
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.style.display = 'none';
            if(placeholder) placeholder.style.display = 'flex';
        }
    }

    // --- DROPDOWN LOGIC ---
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.action-dropdown-container')) {
                closeAllDropdowns();
            }
        });
    });

    function toggleDropdown(event, id) {
        event.stopPropagation();
        closeAllDropdowns();
        const dropdown = document.getElementById('dropdown-' + id);
        if (dropdown) {
            dropdown.style.display = dropdown.style.display === 'flex' ? 'none' : 'flex';
        }
    }

    function closeAllDropdowns() {
        document.querySelectorAll('.action-dropdown-menu').forEach(menu => {
            menu.style.display = 'none';
        });
    }

    // --- MODAL LOGIC ---
    function openEditModal(id, name, code, accountName, accountNumber) {
        document.getElementById('editForm').action = '/admin/e-wallets/' + id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_code').value = code;
        document.getElementById('edit_account_name').value = accountName;
        document.getElementById('edit_account_number').value = accountNumber;
        
        // Reset previews
        document.getElementById('edit_qr_preview').style.display = 'none';
        document.getElementById('edit_qr_placeholder').style.display = 'flex';
        document.getElementById('edit_logo_preview').style.display = 'none';
        document.getElementById('edit_logo_placeholder').style.display = 'flex';
        
        document.getElementById('editModal').style.display = 'flex';
    }

    let formToSubmit = null;
    function openDeleteModal(form) {
        formToSubmit = form;
        document.getElementById('deleteModal').style.display = 'flex';
    }
    function submitDeleteForm() {
        if(formToSubmit) formToSubmit.submit();
    }
</script>

@endsection