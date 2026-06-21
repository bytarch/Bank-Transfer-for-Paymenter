@php
$encodedConfirmationMessage = rawurlencode($confirmation_message ?? 'Hello, here is my payment confirmation');
$whatsappLink = "https://wa.me/{$whatsapp_number}?text={$encodedConfirmationMessage}%0AOrder%20ID%3A%20{$order_id}%0A";

$formattedTotal = number_format($total ?? 0, 2, '.', ',');
$rawTotal = $total ?? 0;

$logoUrls = [
'atlantic-bank' => 'https://www.atlabank.com/images/logo.jpg',
'belize-bank' => 'https://static.wixstatic.com/media/c0de4d_cb59fbad81cc4a3c93fa6dd49715e406~mv2.png',
'heritage-bank' => 'https://www.heritageibt.com/wp-content/uploads/2015/11/HB_NEW_logo_FINAL.jpg',
'national-bank-of-belize' => 'https://www.nbbl.bz/wp-content/uploads/2022/07/National-Bank-of-Belize-Logo.png',
'digiwallet' => 'https://www.digiwallet.bz/wp-content/uploads/2021/11/DWL-web-logo.png',
];

$jsBankDetails = [];
$firstBankId = null;

if (isset($bank_list) && is_array($bank_list)) {
    foreach ($bank_list as $index => $bank) {
        if (isset($merchant_list[$index], $bank_account_list[$index])) {
            $bankId = $bank[0];
            $bankName = $bank[1];

            $jsBankDetails[$bankId] = [
                'name' => $bankName,
                'accountName' => $merchant_list[$index],
                'accountNumber' => $bank_account_list[$index],
                'logoUrl' => isset($logoUrls[$bankId]) ? $logoUrls[$bankId] : '',
            ];

            if ($firstBankId === null) {
                $firstBankId = $bankId;
            }
        }
    }
}
@endphp

<style>
    .bank-tab-item.active {
        border-bottom-width: 2px;
        border-color: var(--primary);
    }
    .bank-tab-item button img {
        transition: transform 0.2s ease-in-out;
    }
    .bank-tab-item button:hover img {
        transform: scale(1.1);
    }
</style>

<div class="container mx-auto mt-8 p-4 md:p-2">
    <div class="flex flex-col md:flex-row gap-4 md:gap-2">
        <div class="w-full md:w-1/2">
            <div class="flex items-center justify-center h-full">
                <div class="h-full w-full rounded-lg bg-background px-6 py-8 shadow-lg">
                    <h5 class="mb-4 text-xl font-medium">Receipt</h5>
                    <div class="flex items-baseline relative group">
                        <span class="text-3xl font-semibold">{{ $prefix ?? '' }}</span>
                        <span class="text-5xl font-extrabold tracking-tight cursor-pointer hover:text-muted transition-colors" data-raw-value="{{ $rawTotal }}"> {{ $formattedTotal }} </span>
                        <span class="text-3xl font-semibold">{{ $suffix ?? '' }}</span>
                    </div>
                    <ul role="list" class="my-7 space-y-5">
                        <li class="flex space-x-3 items-center">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                            <span class="text-base leading-tight font-normal">Order ID: {{ $order_id ?? 'N/A' }}</span>
                        </li>
                        <li class="flex space-x-3 items-center">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                            </svg>
                            <span class="text-base leading-tight font-normal">Transfer Note: {{ $order_id ?? 'N/A' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="w-full md:w-1/2">
            <div class="h-full w-full rounded-lg bg-background px-6 py-8 shadow-lg">
                <div class="flex justify-center mb-6">
                    <ul class="flex justify-center gap-2 sm:gap-4" id="bank-tabs">
                        @if (!empty($jsBankDetails))
                            @foreach ($jsBankDetails as $bankId => $bankData)
                            <li class="bank-tab-item cursor-pointer p-1" data-bank-id="{{ $bankId }}">
                                <button class="focus:outline-none" aria-label="Select {{ $bankData['name'] }}">
                                    <img src="{{ $bankData['logoUrl'] }}" alt="{{ $bankData['name'] }} Logo"
                                        class="h-12 w-12 sm:h-16 sm:w-16 object-contain"
                                        onerror="this.style.display='none'; this.parentElement.innerHTML = '{{ addslashes($bankData['name']) }}';" />
                                </button>
                            </li>
                            @endforeach
                        @else
                            <p>No payment methods available.</p>
                        @endif
                    </ul>
                </div>

                @if (!empty($jsBankDetails))
                <div>
                    <div class="rounded-lg bg-background-secondary p-4 text-left">
                        <h3 id="bank-name" class="text-center text-xl font-bold underline mb-4">Select a Bank</h3>
                        <div class="my-3">
                            <p class="text-sm">Account Name:</p>
                            <div id="account-name" class="text-lg font-medium">-</div>
                        </div>
                        <div class="my-3" id="account-number-section">
                            <p class="text-sm" id="account-number-label">Account Number:</p>
                            <div class="text-lg font-medium">
                                <div class="relative">
                                    <input id="bank-account-0" type="text"
                                        class="col-span-6 block w-full rounded-lg bg-background p-2.5 text-lg font-medium focus:ring-blue-500 focus:border-blue-500"
                                        value="" disabled readonly aria-label="Bank Account Number" />
                                    <button id="copy-btn" class="absolute end-2 top-1/2 -translate-y-1/2 hover:bg-background-secondary rounded-lg p-2 inline-flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-gray-400" aria-label="Copy account number">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="text-center p-4">Payment information is currently unavailable.</div>
                @endif
            </div>
        </div>
    </div>

    <div class="mt-6 space-y-3">
        <a href="{{ $whatsappLink }}" target="_blank"
            class="block w-full rounded-lg bg-primary px-4 py-3 text-center font-bold text-white hover:bg-primary/80 transition-colors duration-200">Send payment confirmation via WhatsApp</a>
    </div>
    <div class="mt-6 rounded-md bg-background-secondary p-4">
        <p class="mb-3">Your payment is processed manually. After making a payment, please be patient while waiting for confirmation. Thank you!</p>
        @if(isset($payment_confirmation_eta) && $payment_confirmation_eta)
        <p>Estimated payment confirmation: {{ $payment_confirmation_eta }}</p>
        @endif
    </div>
</div>

<script>
(function() {
    const bankDetails = @json($jsBankDetails);
    const firstBankId = @json($firstBankId);

    const bankNameEl = document.getElementById('bank-name');
    const accountNameEl = document.getElementById('account-name');
    const accountNumberInputEl = document.getElementById('bank-account-0');
    const accountNumberLabel = document.getElementById('account-number-label');
    const bankTabsContainer = document.getElementById('bank-tabs');

    if (!bankTabsContainer) return;
    const bankTabItems = bankTabsContainer.querySelectorAll('.bank-tab-item');

    function updateAccountInfo(bankId) {
        if (!bankDetails || !bankDetails[bankId]) {
            if (bankNameEl) bankNameEl.textContent = 'Info Not Available';
            if (accountNameEl) accountNameEl.textContent = '-';
            if (accountNumberInputEl) accountNumberInputEl.value = '';
            return;
        }
        const selectedBank = bankDetails[bankId];

        if (bankNameEl) bankNameEl.textContent = selectedBank.name;
        if (accountNameEl) accountNameEl.textContent = selectedBank.accountName;

        const accountNumberSection = document.getElementById('account-number-section');
        if (accountNumberSection) accountNumberSection.classList.remove('hidden');
        if (accountNumberInputEl) accountNumberInputEl.value = selectedBank.accountNumber;

        if (bankId === 'digiwallet') {
            if (accountNumberLabel) accountNumberLabel.textContent = 'Phone Number:';
            if (accountNumberInputEl) accountNumberInputEl.setAttribute('aria-label', 'Phone Number');
        } else {
            if (accountNumberLabel) accountNumberLabel.textContent = 'Account Number:';
            if (accountNumberInputEl) accountNumberInputEl.setAttribute('aria-label', 'Bank Account Number');
        }

        bankTabItems.forEach(function(tab) {
            tab.classList.remove('active');
            if (tab.dataset.bankId === bankId) {
                tab.classList.add('active');
            }
        });
    }

    bankTabItems.forEach(function(tab) {
        tab.addEventListener('click', function() {
            updateAccountInfo(tab.dataset.bankId);
        });
    });

    if (firstBankId && bankDetails[firstBankId]) {
        updateAccountInfo(firstBankId);
    } else if (bankTabItems.length > 0) {
        updateAccountInfo(bankTabItems[0].dataset.bankId);
    }

    var copyBtn = document.getElementById('copy-btn');
    if (copyBtn) {
        copyBtn.addEventListener('click', function() {
            if (accountNumberInputEl && accountNumberInputEl.value !== '') {
                accountNumberInputEl.select();
                accountNumberInputEl.setSelectionRange(0, 99999);
                navigator.clipboard.writeText(accountNumberInputEl.value).catch(function() {});
            }
        });
    }

    var totalAmountEl = document.querySelector('[data-raw-value]');
    if (totalAmountEl) {
        totalAmountEl.addEventListener('click', function() {
            navigator.clipboard.writeText(totalAmountEl.dataset.rawValue).catch(function() {});
        });
    }
})();
</script>
