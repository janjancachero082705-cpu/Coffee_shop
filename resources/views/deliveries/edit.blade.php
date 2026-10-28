@extends('layouts.admin')
@section('title', 'Deliveries — edit')
@section('content')
<style>
    /* ==========================================
       MOBILE DISCOUNT SECTION ARRANGEMENT
       ========================================== */
    @media (max-width: 767px) {
        .discount-row {
            flex-direction: column !important;
            align-items: stretch !important;
            padding: 14px !important;
            gap: 10px !important;
            background-color: #FAF9F7 !important; /* Subtle background */
            border-radius: 12px !important;
        }

        /* Ibabaw: Label */
        .discount-row > label {
            font-size: 11px !important;
            color: #78716A !important;
            margin-bottom: 2px !important;
        }

        /* Ubos: Ang input ug ang Total */
        .discount-row > div {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            width: 100% !important;
            gap: 12px !important;
        }

        /* Ang input container */
        .discount-row .relative.w-16 {
            width: 50% !important; /* Himuong 50% ang gilapdon */
            flex: 1;
        }

        /* Ang input box mismo */
        .discount-row input[type="number"] {
            height: 48px !important;
            font-size: 18px !important; /* Dako-dako para sayon basahon */
            padding: 0 35px 0 15px !important; /* Space sa too para sa % */
            border-radius: 10px !important;
            border: 1.5px solid #E8E5E0 !important;
            background: #FFFFFF !important;
            color: #E86100 !important;
            width: 100% !important;
        }

        /* Ang % sign */
        .discount-row .absolute.right-2 {
            right: 12px !important;
            font-size: 14px !important;
        }

        /* Ang peso display (Total Discount) */
        .discount-row .w-20 {
            width: 50% !important;
            text-align: right !important;
            font-size: 18px !important; /* Dako-dako nga text */
            padding-right: 4px !important;
        }
    }
</style>
<style>
    /* ==========================================
       FINAL MASTER LAYOUT (MOBILE DELIVERY FORM)
       ========================================== */
    
    /* Pugson og light mode ang tanang dropdown ug inputs */
    select, option, input, textarea {
        color-scheme: light !important;
        background-color: #FFFFFF !important;
        color: #1A1715 !important;
    }
    
    option {
        background-color: #FFFFFF !important;
        color: #1A1715 !important;
        font-weight: 600;
    }

    @media (max-width: 767px) {
        /* 1. Tagoan ang Table Header para dili maglutaw ang text */
        #productsTable thead {
            display: none !important;
        }

        /* 2. I-reset ang Table into Card Layout */
        #productsTable, 
        #productsTable tbody {
            display: block !important;
            width: 100% !important;
            border-collapse: collapse;
        }

        /* 3. Ang Card Container */
        .product-row {
            display: flex !important;
            flex-wrap: wrap !important;
            border: 1px solid #E8E5E0 !important;
            border-radius: 12px !important;
            padding: 16px !important;
            margin-bottom: 16px !important;
            background: #FFFFFF !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02) !important;
            position: relative;
            gap: 12px 8px;
            width: 100% !important;
        }

        .product-row td {
            display: block !important;
            border: none !important;
            padding: 0 !important;
        }

        /* --- ROW 1: PRODUCT SELECT UG REMOVE BUTTON --- */
        .product-row td:nth-child(1) {
            width: 100% !important;
            padding-right: 40px !important; /* Space para sa X button */
        }
        
        .product-row td:nth-child(6) {
            position: absolute !important;
            top: 16px !important;
            right: 16px !important;
            width: 32px !important;
            height: 32px !important;
        }

        /* --- ROW 2: STOCK, PRICE, QTY (Magtapad sa tunga) --- */
        .product-row td:nth-child(2),
        .product-row td:nth-child(3),
        .product-row td:nth-child(4) {
            width: calc(33.333% - 6px) !important; /* 3 columns */
            flex: none !important;
            display: flex !important;
            flex-direction: column !important;
        }

        /* Labels sa ibabaw sa matag input */
        .product-row td:nth-child(2)::before { content: "STOCK"; display: block; font-size: 9px; color: #78716A; font-weight: 800; margin-bottom: 4px; text-align: center; letter-spacing: 0.05em; }
        .product-row td:nth-child(3)::before { content: "PRICE"; display: block; font-size: 9px; color: #78716A; font-weight: 800; margin-bottom: 4px; text-align: center; letter-spacing: 0.05em; }
        .product-row td:nth-child(4)::before { content: "QTY"; display: block; font-size: 9px; color: #78716A; font-weight: 800; margin-bottom: 4px; text-align: center; letter-spacing: 0.05em; }

        /* --- ROW 3: SUBTOTAL --- */
        .product-row td:nth-child(5) {
            width: 100% !important;
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            margin-top: 4px !important;
            padding-top: 12px !important;
            border-top: 1px dashed #E8E5E0 !important;
        }
        .product-row td:nth-child(5)::before {
            content: "SUBTOTAL";
            font-size: 11px;
            color: #78716A;
            font-weight: 800;
        }

        /* --- INPUTS UG SELECTS STYLING --- */
        .variant-select, .price-input, .qty-input {
            width: 100% !important;
            height: 44px !important;
            padding: 0 10px !important;
            font-size: 15px !important;
            border-radius: 8px !important;
            border: 1px solid #D3CEC6 !important;
            text-align: center;
            font-weight: 700;
        }

        .variant-select {
            text-align: left !important;
            padding-right: 30px !important;
            /* Custom arrow para wala nay native look */
            background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23A29B90%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: right 10px center !important;
            background-size: 10px !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            appearance: none !important;
        }
        
        .stock-text {
            width: 100% !important;
            height: 44px !important;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px !important;
            border-radius: 8px !important;
            background: #F3F1EE !important;
        }
    }
</style>







<div class="card p-12 text-center">
    <i class="fas fa-tools text-6xl text-gray-300 mb-4"></i>
    <h3 class="text-xl font-bold text-gray-700 mb-2">Deliveries — edit</h3>
    <p class="text-gray-500">Kini nga page kay under construction pa. Sa sunod nga commands, ato i-build ang full functionality.</p>
</div>
<!-- CUSTOM DROPDOWN SCRIPT -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Choices === 'undefined') return;

        // Function para i-convert ang native select ngadto sa nindot nga Choices.js
        function initChoices(select) {
            if (select.classList.contains('choices-initialized')) return;
            new Choices(select, {
                searchEnabled: true,
                itemSelectText: '',
                shouldSort: false,
                classNames: { containerOuter: 'choices w-full' }
            });
            select.classList.add('choices-initialized');
        }

        // 1. I-apply sa mga existing dropdowns inig-load sa page
        document.querySelectorAll('.variant-select').forEach(initChoices);

        // 2. I-apply pud sa mga bag-ong dropdown inig-click sa "Add Another Item"
        const productsTable = document.getElementById('productRows');
        if (productsTable) {
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1 && node.classList.contains('product-row')) {
                            const newSelect = node.querySelector('.variant-select');
                            if (newSelect) initChoices(newSelect);
                        }
                    });
                });
            });
            observer.observe(productsTable, { childList: true });
        }
    });
</script>
@endsection