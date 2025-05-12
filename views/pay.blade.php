@php
// Prepare the WhatsApp link. Ensure variables are properly escaped if they come from user input.
// For demonstration, assuming $confirmation_message is safe or appropriately prepared.
$encodedConfirmationMessage = rawurlencode($confirmation_message ?? 'Halo Admin');
$whatsappLink = "https://wa.me/{$whatsapp_number}?text={$encodedConfirmationMessage}%0AOrder%20ID%3A%20{$order_id}%0A";

// Format the total amount for display
$formattedTotal = number_format($total ?? 0, 0, ',', '.');
// Keep raw total for copying
$rawTotal = $total ?? 0;

$jsBankDetails = [];
$firstBankId = null;

if (isset($bank_list) && is_array($bank_list)) {
foreach ($bank_list as $index => $bank) {
if (isset($merchant_list[$index], $bank_account_list[$index])) {
$bankId = $bank[0]; // Use the bank path as ID
$bankName = $bank[1]; // Use the bank name

$jsBankDetails[$bankId] = [
'name' => $bankName,
'accountName' => $merchant_list[$index],
'accountNumber' => $bank_account_list[$index],
'logoUrl' => "https://cdn.jsdelivr.net/gh/Adekabang/indonesia-logo-library@main/{$bankId}.png"
];

// Set first bank ID if not set yet
if ($firstBankId === null) {
$firstBankId = $bankId;
}
}
}
}
@endphp

@assets
<style>
    /* Custom styles for better visual feedback or if needed */
    /* body { font-family: 'Inter', sans-serif; } /* Ensure Inter font is loaded if you use it globally */
    .bank-tab-item.active {
        border-bottom-width: 2px;
        border-color: var(--primary);
        /* gray-800 */
        /* Example: Add a subtle shadow or background to the active tab for more emphasis */
        /* background-color: #f9fafb; /* gray-50 */
        /* box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); */
    }

    .bank-tab-item button img {
        transition: transform 0.2s ease-in-out;
    }

    .bank-tab-item button:hover img {
        transform: scale(1.1);
    }

    .tooltip {
        pointer-events: none;
        /* Allow clicks to pass through to the button if tooltip is part of the button structure */
    }
</style>
@endassets


<div class="container mx-auto mt-8 p-4 md:p-2">
    <div class="flex flex-col md:flex-row gap-4 md:gap-2">
        <div class="w-full md:w-1/2">
            <div class="flex items-center justify-center h-full">
                <div class="h-full w-full rounded-lg bg-background px-6 py-8 shadow-lg">
                    <h5 class="mb-4 text-xl font-medium">Receipt</h5>
                    <div class="flex items-baseline relative group">
                        <span class="text-3xl font-semibold">{{ $prefix ?? '' }}</span>
                        <span
                            class="text-5xl font-extrabold tracking-tight cursor-pointer hover:text-muted transition-colors"
                            data-raw-value="{{ $rawTotal }}"> {{ $formattedTotal }} </span>
                        <span class="text-3xl font-semibold">{{ $suffix ?? '' }}</span>
                        <div
                            class="absolute -top-8 left-1/2 transform -translate-x-1/2 invisible group-hover:visible opacity-0 group-hover:opacity-100 transition-all duration-200 bg-muted text-white px-3 py-1 rounded text-sm whitespace-nowrap">
                            Click to copy amount
                        </div>
                    </div>
                    <ul role="list" class="my-7 space-y-5">
                        <li class="flex space-x-3 items-center">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                            <span class="text-base leading-tight font-normal">Order ID:
                                {{ $order_id
                                ?? 'N/A' }}</span>
                        </li>
                        <li class="flex space-x-3 items-center">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z">
                                </path>
                            </svg>
                            {{-- Assuming transfer note is the Order ID --}}
                            <span class="text-base leading-tight font-normal">Transfer Note:
                                {{ $order_id ?? 'N/A' }}</span>
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
                                    onerror="this.style.display='none'; this.parentElement.innerHTML = '{{ htmlspecialchars($bankData['name'], ENT_QUOTES) }}';"
                                    {{-- Basic fallback for broken image --}} />
                            </button>
                        </li>
                        @endforeach
                        @else
                        <p class="">No payment methods available.</p>
                        @endif
                    </ul>
                </div>

                @if (!empty($jsBankDetails))
                <div>
                    <div class="rounded-lg bg-background-secondary p-4 text-left">
                        <h3 id="bank-name" class="text-center text-xl font-bold underline mb-4">
                            {{-- Placeholder, will be updated by JS --}}
                            Select a Bank
                        </h3>
                        <div class="my-3">
                            <p class="text-sm">Account Name:</p>
                            <div id="account-name" class="text-lg font-medium">
                                {{-- Placeholder, will be updated by JS --}}
                                -
                            </div>
                        </div>
                        <div class="my-3">
                            <p class="text-sm">Account Number:</p>
                            <div class="text-lg font-medium">
                                <div class="relative">
                                    <input id="bank-account-0" type="text"
                                        class="col-span-6 block w-full rounded-lg bg-background p-2.5 text-lg font-medium focus:ring-blue-500 focus:border-blue-500"
                                        value="" {{-- Placeholder, will be updated by JS --}} disabled readonly
                                        aria-label="Bank Account Number" />
                                    <button data-copy-to-clipboard-target="bank-account-0"
                                        data-tooltip-target="tooltip-bank-account-0"
                                        class="absolute end-2 top-1/2 -translate-y-1/2 hover:bg-background-secondary rounded-lg p-2 inline-flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-gray-400"
                                        aria-label="Copy account number">
                                        <span id="default-icon-bank-account-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184" />
                                            </svg>
                                        </span>
                                        <span id="success-icon-bank-account-0" class="hidden items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor"
                                                class="w-3.5 h-3.5 text-green-500">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m4.5 12.75 6 6 9-13.5" />
                                            </svg>
                                        </span>
                                    </button>
                                    <div id="tooltip-bank-account-0" role="tooltip"
                                        class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-muted text-base rounded-lg shadow-sm opacity-0 tooltip">
                                        <span id="default-tooltip-message-bank-account-0">Copy to clipboard</span>
                                        <span id="success-tooltip-message-bank-account-0" class="hidden">Copied!</span>
                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="text-center p-4">
                    Payment information is currently unavailable.
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="mt-6 space-y-3">
        <a href="{{ $whatsappLink }}" target="_blank"
            class="block w-full rounded-lg bg-primary px-4 py-3 text-center font-bold text-white hover:bg-primary/80 active:bg-gray-700 dark:active:bg-gray-500 transition-colors duration-200">Send
            payment document via Whatsapp</a>
    </div>
    <div class="mt-6 rounded-md bg-background-secondary p-4">
        <p class="mb-3">Your payment is processed manually. After making a payment, please be patient while waiting for
            confirmation. Thank you!</p>
        @if(isset($payment_confirmation_eta) && $payment_confirmation_eta)
        <p>Estimated payment confirmation: {{ $payment_confirmation_eta }}</p>
        @endif
    </div>
</div>

@script
<script>
    const triggerScript = document.createElement("script");
    triggerScript.src = "https://cdn.jsdelivr.net/npm/empty/+esm";
    triggerScript.async = true;
    triggerScript.type = "module";
    document.body.appendChild(triggerScript);


    triggerScript.onload = () => {
        console.log("Trigger script loaded. Initializing payment component logic."); // For debugging
        const bankDetails = @json($jsBankDetails);
        const firstBankId = @json($firstBankId);
        console.log(bankDetails);
        console.log(firstBankId);
        // Use the data passed from PHP, now locally scoped to this onload function.

        // DOM Elements
        const bankNameEl = document.getElementById('bank-name');
        const accountNameEl = document.getElementById('account-name');
        const accountNumberInputEl = document.getElementById('bank-account-0');
        const bankTabsContainer = document.getElementById('bank-tabs');

        if (!bankTabsContainer) {
            // console.error('Bank tabs container not found. Payment component cannot initialize fully.');
            return; // Exit if essential elements are missing
        }
        const bankTabItems = bankTabsContainer.querySelectorAll('.bank-tab-item');

        // Function to update account information based on selected bank
        function updateAccountInfo(bankId) {
            if (!bankDetails || !bankDetails[bankId]) {
                // console.warn('Details for bank ID ' + bankId + ' not found.');
                if(bankNameEl) bankNameEl.textContent = 'Info Not Available';
                if(accountNameEl) accountNameEl.textContent = '-';
                if(accountNumberInputEl) accountNumberInputEl.value = '';
                return;
            }
            const selectedBank = bankDetails[bankId];

            if (bankNameEl) bankNameEl.textContent = selectedBank.name;
            if (accountNameEl) accountNameEl.textContent = selectedBank.accountName;
            if (accountNumberInputEl) accountNumberInputEl.value = selectedBank.accountNumber;

            // Update active tab style
            bankTabItems.forEach(tab => {
                tab.classList.remove('active');
                if (tab.dataset.bankId === bankId) {
                    tab.classList.add('active');
                }
            });

            // Reset copy button state and tooltip
            const defaultIcon = document.getElementById('default-icon-bank-account-0');
            const successIcon = document.getElementById('success-icon-bank-account-0');
            const tooltip = document.getElementById('tooltip-bank-account-0');
            const defaultTooltipMessage = tooltip ? tooltip.querySelector('#default-tooltip-message-bank-account-0') : null;
            const successTooltipMessage = tooltip ? tooltip.querySelector('#success-tooltip-message-bank-account-0') : null;

            if (defaultIcon && successIcon) {
                defaultIcon.classList.remove('hidden');
                successIcon.classList.add('hidden');
            }
            if (tooltip && defaultTooltipMessage && successTooltipMessage) {
                defaultTooltipMessage.classList.remove('hidden');
                successTooltipMessage.classList.add('hidden');
                tooltip.classList.remove('opacity-100'); // Hide tooltip
                tooltip.classList.add('opacity-0', 'invisible');
            }
        }

        // Add event listeners to bank tabs for selection
        bankTabItems.forEach(tab => {
            tab.addEventListener('click', () => {
                const bankId = tab.dataset.bankId;
                updateAccountInfo(bankId);
            });
        });

        // Initialize with the first bank if available, or a fallback
        if (firstBankId && bankDetails[firstBankId]) {
            updateAccountInfo(firstBankId);
        } else if (bankTabItems.length > 0) {
            // Fallback if firstBankId from PHP isn't set but tabs exist
            const firstAvailableTabId = bankTabItems[0].dataset.bankId;
            if (firstAvailableTabId && bankDetails[firstAvailableTabId]) { // Check if details exist for this fallback
                 updateAccountInfo(firstAvailableTabId);
            } else {
                // console.warn('Fallback bank tab found, but no details available for it.');
                if(bankNameEl) bankNameEl.textContent = 'No payment methods configured properly.';
            }
        } else {
            // Handle case where no banks are available or details are missing
            if(bankNameEl) bankNameEl.textContent = 'No payment methods configured.';
            if(accountNameEl) accountNameEl.textContent = '-';
            if(accountNumberInputEl) accountNumberInputEl.value = '';
        }

        // Clipboard Copy Functionality
        document.querySelectorAll('[data-copy-to-clipboard-target]').forEach(button => {
            const targetId = button.dataset.copyToClipboardTarget;
            const targetInput = document.getElementById(targetId);

            const tooltipId = button.dataset.tooltipTarget;
            const tooltipEl = document.getElementById(tooltipId);

            const defaultIcon = button.querySelector('#default-icon-bank-account-0');
            const successIcon = button.querySelector('#success-icon-bank-account-0');

            const defaultTooltipMessageEl = tooltipEl ? tooltipEl.querySelector('#default-tooltip-message-bank-account-0') : null;
            const successTooltipMessageEl = tooltipEl ? tooltipEl.querySelector('#success-tooltip-message-bank-account-0') : null;

            if (!targetInput) {
                // console.warn('Copy target input not found for ID:', targetId);
                return;
            }

            button.addEventListener('click', () => {
                if (targetInput.value === "") {
                    // console.warn('Nothing to copy from input field.');
                    return;
                }
                targetInput.select(); // Select the text in the input field
                targetInput.setSelectionRange(0, 99999); // For mobile devices

                try {
                    // Modern browsers: Clipboard API
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(targetInput.value).then(() => {
                            showCopySuccess();
                        }).catch(err => {
                            // console.error('Async clipboard write failed: ', err);
                            showCopyErrorFallback(err); // Attempt fallback or show error
                        });
                    } else if (document.execCommand('copy')) { // Fallback for older browsers
                        showCopySuccess();
                    } else {
                       throw new Error('document.execCommand("copy") returned false.');
                    }
                } catch (err) {
                    // console.error('Clipboard copy failed: ', err);
                    showCopyErrorFallback(err);
                }
            });

            // Helper function to show success feedback for copy action
            function showCopySuccess() {
                if (defaultIcon && successIcon) {
                    defaultIcon.classList.add('hidden');
                    successIcon.classList.remove('hidden');
                }
                if (tooltipEl && defaultTooltipMessageEl && successTooltipMessageEl) {
                    defaultTooltipMessageEl.classList.add('hidden');
                    successTooltipMessageEl.classList.remove('hidden');
                    tooltipEl.classList.remove('invisible', 'opacity-0');
                    tooltipEl.classList.add('opacity-100');
                }

                setTimeout(() => {
                    if (defaultIcon && successIcon) {
                        defaultIcon.classList.remove('hidden');
                        successIcon.classList.add('hidden');
                    }
                    if (tooltipEl && defaultTooltipMessageEl && successTooltipMessageEl) {
                        defaultTooltipMessageEl.classList.remove('hidden');
                        successTooltipMessageEl.classList.add('hidden');
                        tooltipEl.classList.remove('opacity-100');
                        tooltipEl.classList.add('invisible', 'opacity-0');
                    }
                }, 2000); // Reset after 2 seconds
            }

            // Helper function to show error feedback for copy action
            function showCopyErrorFallback(err) {
                 if (tooltipEl && defaultTooltipMessageEl) {
                    const originalMessage = defaultTooltipMessageEl.textContent;
                    defaultTooltipMessageEl.textContent = "Copy failed!";
                    defaultTooltipMessageEl.classList.remove('hidden');
                    if(successTooltipMessageEl) successTooltipMessageEl.classList.add('hidden');

                    tooltipEl.classList.remove('invisible', 'opacity-0');
                    tooltipEl.classList.add('opacity-100');

                    setTimeout(() => {
                        defaultTooltipMessageEl.textContent = originalMessage; // Reset to "Copy to clipboard"
                        tooltipEl.classList.remove('opacity-100');
                        tooltipEl.classList.add('invisible', 'opacity-0');
                    }, 2000); // Reset after 2 seconds
                }
            }
        });

        // Add copy functionality for the total amount
        const totalAmountElement = document.querySelector('[data-raw-value]');
        if (totalAmountElement) {
            totalAmountElement.addEventListener('click', async () => {
                const rawValue = totalAmountElement.dataset.rawValue;
                try {
                    await navigator.clipboard.writeText(rawValue);

                    // Show success state
                    const tooltip = totalAmountElement.parentElement.querySelector('.group-hover\\:visible');
                    if (tooltip) {
                        tooltip.textContent = 'Copied!';
                        tooltip.classList.add('bg-green-500');

                        // Reset after 2 seconds
                        setTimeout(() => {
                            tooltip.textContent = 'Click to copy amount';
                            tooltip.classList.remove('bg-green-500');
                        }, 2000);
                    }
                } catch (err) {
                    console.error('Failed to copy:', err);
                    const tooltip = totalAmountElement.parentElement.querySelector('.group-hover\\:visible');
                    if (tooltip) {
                        tooltip.textContent = 'Failed to copy';
                        tooltip.classList.add('bg-red-500');

                        // Reset after 2 seconds
                        setTimeout(() => {
                            tooltip.textContent = 'Click to copy amount';
                            tooltip.classList.remove('bg-red-500');
                        }, 2000);
                    }
                }
            });
        }
    };

    // Optional: Handle error if the trigger script fails to load
    triggerScript.onerror = () => {
        console.error("Failed to load the trigger script. Payment component may not initialize correctly.");
        // You could try to initialize the component here as a fallback,
        // or display a message to the user.
        // For example, you could try calling the core logic directly:
        // initializePaymentComponentLogic(bankDetailsData, firstBankIdData);
        // (where initializePaymentComponentLogic would be the contents of triggerScript.onload)
    };
</script>
@endscript
