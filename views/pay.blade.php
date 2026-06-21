@php
$bankId = $bank_list[0][0] ?? '';
$bankName = $bank_list[0][1] ?? '';
$accountName = $merchant_list[0] ?? '';
$accountNumber = $bank_account_list[0] ?? '';

$logoUrls = [
    'atlantic-bank' => 'https://www.atlabank.com/images/logo.jpg',
    'belize-bank' => 'https://static.wixstatic.com/media/c0de4d_cb59fbad81cc4a3c93fa6dd49715e406~mv2.png',
    'heritage-bank' => 'https://www.heritageibt.com/wp-content/uploads/2015/11/HB_NEW_logo_FINAL.jpg',
    'national-bank-of-belize' => 'https://www.nbbl.bz/wp-content/uploads/2022/07/National-Bank-of-Belize-Logo.png',
    'digiwallet' => 'https://www.digiwallet.bz/wp-content/uploads/2021/11/DWL-web-logo.png',
];

$logoUrl = $logoUrls[$bankId] ?? '';
$isDigiWallet = ($bankId === 'digiwallet');

$formattedTotal = number_format($total ?? 0, 2, '.', ',');
$rawTotal = $total ?? 0;

$encodedMsg = rawurlencode(($confirmation_message ?? 'Payment confirmation') . "\nOrder ID: " . $order_id);
$whatsappLink = "https://wa.me/{$whatsapp_number}?text={$encodedMsg}";
@endphp

<style>
    .ba-card {
        background: var(--color-background, #fff);
        border: 1px solid var(--color-line, #e5e7eb);
        border-radius: 12px;
    }
    .ba-label {
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--color-muted, #6b7280);
        margin-bottom: 4px;
    }
    .ba-value {
        font-size: 1rem;
        font-weight: 600;
        color: var(--color-text, #111827);
        word-break: break-all;
    }
    .ba-amount {
        font-size: 2rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: var(--color-text, #111827);
    }
    .ba-bank-logo {
        height: 48px;
        width: auto;
        object-fit: contain;
    }
    .ba-divider {
        border: none;
        border-top: 1px solid var(--color-line, #e5e7eb);
        margin: 16px 0;
    }
    .ba-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 12px 20px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.95rem;
        text-decoration: none;
        transition: opacity 0.15s, transform 0.1s;
        cursor: pointer;
        border: none;
    }
    .ba-btn:active { transform: scale(0.98); }
    .ba-btn-primary {
        background: #25D366;
        color: #fff;
    }
    .ba-btn-primary:hover { opacity: 0.9; color: #fff; }
    .ba-copy-wrap {
        position: relative;
    }
    .ba-copy-btn {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        background: transparent;
        border: 1px solid var(--color-line, #e5e7eb);
        border-radius: 6px;
        padding: 6px 8px;
        cursor: pointer;
        color: var(--color-muted, #6b7280);
        transition: background 0.15s, color 0.15s;
    }
    .ba-copy-btn:hover {
        background: var(--color-background-secondary, #f3f4f6);
        color: var(--color-text, #111827);
    }
    .ba-copy-btn svg {
        width: 14px;
        height: 14px;
        display: block;
    }
    .ba-info-banner {
        background: var(--color-background-secondary, #f9fafb);
        border-radius: 10px;
        padding: 14px 16px;
        font-size: 0.875rem;
        color: var(--color-muted, #6b7280);
        line-height: 1.5;
    }
</style>

<div style="max-width: 480px; margin: 32px auto; padding: 0 16px; font-family: inherit;">

    {{-- Amount --}}
    <div style="text-align: center; margin-bottom: 24px;">
        <div style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-muted, #6b7280); margin-bottom: 4px;">Amount to Pay</div>
        <div class="ba-amount" style="cursor: pointer;" id="ba-amount" title="Click to copy">
            {{ $prefix ?? '' }}{{ $formattedTotal }}{{ $suffix ?? '' }}
        </div>
        <div id="ba-amount-toast" style="font-size: 0.75rem; color: #25D366; margin-top: 4px; opacity: 0; transition: opacity 0.2s;">Copied</div>
    </div>

    {{-- Bank Details Card --}}
    <div class="ba-card" style="padding: 24px; margin-bottom: 16px;">
        @if($bankName)
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
            @if($logoUrl)
            <img src="{{ $logoUrl }}" alt="{{ $bankName }}" class="ba-bank-logo" onerror="this.style.display='none'" />
            @endif
            <div>
                <div class="ba-label">Send payment to</div>
                <div style="font-size: 1.1rem; font-weight: 700; color: var(--color-text, #111827);">{{ $bankName }}</div>
            </div>
        </div>
        @endif

        <div style="display: flex; flex-direction: column; gap: 16px;">
            {{-- Account Name --}}
            <div>
                <div class="ba-label">Account Name</div>
                <div class="ba-value">{{ $accountName }}</div>
            </div>

            <hr class="ba-divider" />

            {{-- Account Number / Phone Number --}}
            <div>
                <div class="ba-label">{{ $isDigiWallet ? 'Phone Number' : 'Account Number' }}</div>
                <div class="ba-copy-wrap">
                    <input
                        type="text"
                        id="ba-account-number"
                        value="{{ $accountNumber }}"
                        readonly
                        disabled
                        aria-label="{{ $isDigiWallet ? 'Phone Number' : 'Account Number' }}"
                        style="
                            width: 100%;
                            padding: 10px 44px 10px 12px;
                            border: 1px solid var(--color-line, #e5e7eb);
                            border-radius: 8px;
                            background: var(--color-background, #fff);
                            font-size: 1rem;
                            font-weight: 600;
                            font-family: inherit;
                            color: var(--color-text, #111827);
                            box-sizing: border-box;
                        "
                    />
                    <button class="ba-copy-btn" id="ba-copy-btn" title="Copy">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Order Info --}}
    <div class="ba-card" style="padding: 16px 24px; margin-bottom: 16px;">
        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.875rem;">
            <span style="color: var(--color-muted, #6b7280);">Order ID</span>
            <span style="font-weight: 600; color: var(--color-text, #111827);">{{ $order_id ?? 'N/A' }}</span>
        </div>
    </div>

    {{-- WhatsApp Button --}}
    <a href="{{ $whatsappLink }}" target="_blank" rel="noopener" class="ba-btn ba-btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
        </svg>
        Confirm via WhatsApp
    </a>

    {{-- Footer Info --}}
    <div class="ba-info-banner" style="margin-top: 16px;">
        <p style="margin: 0 0 4px 0;">Your payment is processed manually. Please allow time for confirmation after transferring.</p>
        @if(!empty($payment_confirmation_eta))
        <p style="margin: 0; font-weight: 600;">Estimated confirmation: {{ $payment_confirmation_eta }}</p>
        @endif
    </div>
</div>

<script>
(function() {
    var amountEl = document.getElementById('ba-amount');
    var toastEl = document.getElementById('ba-amount-toast');
    if (amountEl) {
        amountEl.addEventListener('click', function() {
            var val = amountEl.textContent.trim();
            navigator.clipboard.writeText(val).then(function() {
                if (toastEl) { toastEl.style.opacity = '1'; setTimeout(function() { toastEl.style.opacity = '0'; }, 1500); }
            }).catch(function() {});
        });
    }

    var copyBtn = document.getElementById('ba-copy-btn');
    var accountInput = document.getElementById('ba-account-number');
    if (copyBtn && accountInput) {
        copyBtn.addEventListener('click', function() {
            accountInput.select();
            accountInput.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(accountInput.value).catch(function() {
                document.execCommand('copy');
            });
        });
    }
})();
</script>
